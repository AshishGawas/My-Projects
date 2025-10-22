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
    
    $amount = floatval($input['amount']);
    $category = trim($input['category']);
    $description = trim($input['description'] ?? '');
    
    if ($amount <= 0 || empty($category)) {
        echo json_encode(['success' => false, 'message' => 'Amount and category are required']);
        exit();
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "INSERT INTO expenses (user_id, amount, category, description, expense_date) VALUES (?, ?, ?, ?, CURDATE())";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$_SESSION['user_id'], $amount, $category, $description])) {
            echo json_encode(['success' => true, 'message' => 'Expense added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add expense']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>