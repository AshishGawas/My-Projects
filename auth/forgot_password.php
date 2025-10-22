<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $_SESSION['error'] = "Email is required";
        header("Location: ../index.php");
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header("Location: ../index.php");
        exit();
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if email exists
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Delete any existing tokens for this email
            $delete_query = "DELETE FROM password_reset_tokens WHERE email = ?";
            $delete_stmt = $db->prepare($delete_query);
            $delete_stmt->execute([$email]);
            
            // Insert new token
            $insert_query = "INSERT INTO password_reset_tokens (email, token, expires_at) VALUES (?, ?, ?)";
            $insert_stmt = $db->prepare($insert_query);
            $insert_stmt->execute([$email, $token, $expires_at]);
            
            // In a real application, you would send an email here
            // For now, we'll just show a success message
            $_SESSION['success'] = "Password reset instructions have been sent to your email address.";
        } else {
            // Don't reveal if email exists or not for security
            $_SESSION['success'] = "If an account with that email exists, password reset instructions have been sent.";
        }
        
        header("Location: ../index.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>