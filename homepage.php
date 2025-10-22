<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';

// Get user data
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get recent expenses
$expense_query = "SELECT * FROM expenses WHERE user_id = ? ORDER BY expense_date DESC, created_at DESC LIMIT 10";
$expense_stmt = $db->prepare($expense_query);
$expense_stmt->execute([$_SESSION['user_id']]);
$recent_expenses = $expense_stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total expenses for current month
$current_month = date('Y-m');
$monthly_expense_query = "SELECT SUM(amount) as total FROM expenses WHERE user_id = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ?";
$monthly_stmt = $db->prepare($monthly_expense_query);
$monthly_stmt->execute([$_SESSION['user_id'], $current_month]);
$monthly_expenses = $monthly_stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$remaining_budget = $user['monthly_salary'] - $monthly_expenses;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background: #f5f5f5;">
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">💰 Expense Tracker</div>
            <ul class="nav-links">
                <li><a href="homepage.php">Home</a></li>
                <li><a href="previous_savings.php">Previous Savings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="homepage-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success" style="background: #4CAF50; margin-bottom: 20px;">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error" style="background: #f44336; margin-bottom: 20px;">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="salary-section">
            <h2>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h2>
            
            <div style="margin: 30px 0;">
                <input type="number" 
                       id="salaryInput" 
                       class="salary-input" 
                       placeholder="Enter Your Monthly Salary" 
                       value="<?php echo $user['monthly_salary'] > 0 ? $user['monthly_salary'] : ''; ?>"
                       onchange="updateSalary()"
                       step="0.01">
            </div>
            
            <?php if ($user['monthly_salary'] > 0): ?>
                <div class="salary-display" id="salaryDisplay">
                    ₹<?php echo number_format($user['monthly_salary'], 2); ?>
                </div>
                
                <div style="display: flex; justify-content: space-around; margin: 30px 0; flex-wrap: wrap;">
                    <div style="text-align: center; margin: 10px;">
                        <h3 style="color: #667eea;">Monthly Salary</h3>
                        <p style="font-size: 1.5rem; font-weight: bold;">₹<?php echo number_format($user['monthly_salary'], 2); ?></p>
                    </div>
                    <div style="text-align: center; margin: 10px;">
                        <h3 style="color: #ff6b6b;">This Month's Expenses</h3>
                        <p style="font-size: 1.5rem; font-weight: bold;">₹<?php echo number_format($monthly_expenses, 2); ?></p>
                    </div>
                    <div style="text-align: center; margin: 10px;">
                        <h3 style="color: <?php echo $remaining_budget >= 0 ? '#4CAF50' : '#ff6b6b'; ?>">Remaining Budget</h3>
                        <p style="font-size: 1.5rem; font-weight: bold;">₹<?php echo number_format($remaining_budget, 2); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="expense-section">
            <h3>Add New Expense</h3>
            <div class="expense-form">
                <input type="number" 
                       id="expenseAmount" 
                       class="expense-input" 
                       placeholder="Amount (₹)" 
                       step="0.01" 
                       required>
                
                <select id="expenseCategory" class="category-select" required>
                    <option value="">Select Category</option>
                    <option value="Online Food Order">Online Food Order</option>
                    <option value="Street Food">Street Food</option>
                    <option value="Groceries">Groceries</option>
                    <option value="Online Shopping">Online Shopping</option>
                    <option value="Offline Shopping">Offline Shopping</option>
                    <option value="Friends & Family">Friends & Family</option>
                    <option value="Transportation">Transportation</option>
                    <option value="Entertainment">Entertainment</option>
                    <option value="Bills & Utilities">Bills & Utilities</option>
                    <option value="Other">Other</option>
                </select>
                
                <input type="text" 
                       id="expenseDescription" 
                       class="expense-input" 
                       placeholder="Description (Optional)">
                
                <button class="add-expense-btn" onclick="addExpense()">Add Expense</button>
            </div>

            <div class="expense-list">
                <h4>Recent Expenses</h4>
                <div id="expenseList">
                    <?php if (empty($recent_expenses)): ?>
                        <p style="text-align: center; color: #666; margin: 20px 0;">No expenses recorded yet.</p>
                    <?php else: ?>
                        <?php foreach ($recent_expenses as $expense): ?>
                            <div class="expense-item">
                                <div>
                                    <strong>₹<?php echo number_format($expense['amount'], 2); ?></strong> - <?php echo htmlspecialchars($expense['category']); ?>
                                    <?php if ($expense['description']): ?>
                                        <br><small><?php echo htmlspecialchars($expense['description']); ?></small>
                                    <?php endif; ?>
                                    <br><small><?php echo date('M d, Y', strtotime($expense['expense_date'])); ?></small>
                                </div>
                                <button class="delete-btn" onclick="deleteExpense(<?php echo $expense['id']; ?>)">Delete</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script>
        // Load expenses when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-update salary display if there's a value
            updateSalaryDisplay();
        });
    </script>
</body>
</html>