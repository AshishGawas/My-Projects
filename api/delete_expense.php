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
    $expense_id = intval($input['id']);
    
    if ($expense_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid expense ID']);
        exit();
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Make sure the expense belongs to the current user
        $query = "DELETE FROM expenses WHERE id = ? AND user_id = ?";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$expense_id, $_SESSION['user_id']])) {
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Expense deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Expense not found or access denied']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete expense']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>