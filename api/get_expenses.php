<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM expenses WHERE user_id = ? ORDER BY expense_date DESC, created_at DESC LIMIT 20";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the expenses for display
    foreach ($expenses as &$expense) {
        $expense['date'] = date('M d, Y', strtotime($expense['expense_date']));
        $expense['amount'] = number_format($expense['amount'], 2);
    }
    
    echo json_encode(['success' => true, 'expenses' => $expenses]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>