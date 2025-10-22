<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $salary = floatval($input['salary']);
    
    if ($salary <= 0) {
        echo json_encode(['success' => false, 'message' => 'Salary must be greater than 0']);
        exit();
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "UPDATE users SET monthly_salary = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$salary, $_SESSION['user_id']])) {
            $_SESSION['monthly_salary'] = $salary;
            echo json_encode(['success' => true, 'message' => 'Salary updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update salary']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>