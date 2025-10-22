<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get user data
$user_query = "SELECT * FROM users WHERE id = ?";
$user_stmt = $db->prepare($user_query);
$user_stmt->execute([$_SESSION['user_id']]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Get selected month (default to current month)
$selected_month = $_GET['month'] ?? date('Y-m');

// Get monthly expenses by category
$category_query = "SELECT category, SUM(amount) as total 
                   FROM expenses 
                   WHERE user_id = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ? 
                   GROUP BY category 
                   ORDER BY total DESC";
$category_stmt = $db->prepare($category_query);
$category_stmt->execute([$_SESSION['user_id'], $selected_month]);
$category_expenses = $category_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all expenses for the month
$expense_query = "SELECT * FROM expenses 
                  WHERE user_id = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ? 
                  ORDER BY expense_date DESC, created_at DESC";
$expense_stmt = $db->prepare($expense_query);
$expense_stmt->execute([$_SESSION['user_id'], $selected_month]);
$monthly_expenses = $expense_stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$total_expenses = array_sum(array_column($category_expenses, 'total'));
$savings = $user['monthly_salary'] - $total_expenses;

// Get available months
$months_query = "SELECT DISTINCT DATE_FORMAT(expense_date, '%Y-%m') as month 
                 FROM expenses 
                 WHERE user_id = ? 
                 ORDER BY month DESC";
$months_stmt = $db->prepare($months_query);
$months_stmt->execute([$_SESSION['user_id']]);
$available_months = $months_stmt->fetchAll(PDO::FETCH_ASSOC);

// Add current month if not in list
$current_month = date('Y-m');
$has_current = false;
foreach ($available_months as $month) {
    if ($month['month'] == $current_month) {
        $has_current = true;
        break;
    }
}
if (!$has_current) {
    array_unshift($available_months, ['month' => $current_month]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previous Savings Analysis - Expense Tracker</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body style="background: #f5f5f5;">
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">💰 Expense Tracker</div>
            <ul class="nav-links">
                <li><a href="homepage.php">Home</a></li>
                <li><a href="previous_savings.php" style="opacity: 0.8;">Previous Savings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="homepage-container">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2>Savings Analysis</h2>
            
            <div style="margin: 20px 0;">
                <label for="monthSelect" style="font-weight: bold; margin-right: 10px;">Select Month:</label>
                <select id="monthSelect" onchange="changeMonth()" style="padding: 10px; border-radius: 5px; border: 2px solid #e0e0e0;">
                    <?php foreach ($available_months as $month): ?>
                        <option value="<?php echo $month['month']; ?>" <?php echo $month['month'] == $selected_month ? 'selected' : ''; ?>>
                            <?php echo date('F Y', strtotime($month['month'] . '-01')); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 40px; text-align: center;">
            <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <h3 style="color: #667eea; margin-bottom: 10px;">Monthly Salary</h3>
                <p style="font-size: 1.8rem; font-weight: bold; color: #333;">₹<?php echo number_format($user['monthly_salary'], 2); ?></p>
            </div>
            <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <h3 style="color: #ff6b6b; margin-bottom: 10px;">Total Expenses</h3>
                <p style="font-size: 1.8rem; font-weight: bold; color: #333;">₹<?php echo number_format($total_expenses, 2); ?></p>
            </div>
            <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <h3 style="color: <?php echo $savings >= 0 ? '#4CAF50' : '#ff6b6b'; ?>; margin-bottom: 10px;">
                    <?php echo $savings >= 0 ? 'Savings' : 'Overspent'; ?>
                </h3>
                <p style="font-size: 1.8rem; font-weight: bold; color: #333;">₹<?php echo number_format(abs($savings), 2); ?></p>
            </div>
        </div>

        <?php if (!empty($category_expenses)): ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px;">
                <!-- Pie Chart -->
                <div style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                    <h3 style="text-align: center; margin-bottom: 20px;">Expense Distribution</h3>
                    <canvas id="expenseChart" width="400" height="400"></canvas>
                </div>

                <!-- Category Breakdown -->
                <div style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                    <h3 style="text-align: center; margin-bottom: 20px;">Category Breakdown</h3>
                    <div style="max-height: 400px; overflow-y: auto;">
                        <?php foreach ($category_expenses as $category): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #e0e0e0;">
                                <div>
                                    <strong><?php echo htmlspecialchars($category['category']); ?></strong>
                                    <br><small><?php echo number_format(($category['total'] / $total_expenses) * 100, 1); ?>% of total</small>
                                </div>
                                <div style="font-size: 1.2rem; font-weight: bold; color: #667eea;">
                                    ₹<?php echo number_format($category['total'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Detailed Expense Table -->
        <div style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 20px;">Detailed Expenses for <?php echo date('F Y', strtotime($selected_month . '-01')); ?></h3>
            
            <?php if (empty($monthly_expenses)): ?>
                <p style="text-align: center; color: #666; margin: 40px 0;">No expenses recorded for this month.</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #e0e0e0;">Date</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #e0e0e0;">Category</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #e0e0e0;">Description</th>
                                <th style="padding: 15px; text-align: right; border-bottom: 2px solid #e0e0e0;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($monthly_expenses as $expense): ?>
                                <tr>
                                    <td style="padding: 12px 15px; border-bottom: 1px solid #e0e0e0;">
                                        <?php echo date('M d, Y', strtotime($expense['expense_date'])); ?>
                                    </td>
                                    <td style="padding: 12px 15px; border-bottom: 1px solid #e0e0e0;">
                                        <?php echo htmlspecialchars($expense['category']); ?>
                                    </td>
                                    <td style="padding: 12px 15px; border-bottom: 1px solid #e0e0e0;">
                                        <?php echo htmlspecialchars($expense['description'] ?: '-'); ?>
                                    </td>
                                    <td style="padding: 12px 15px; border-bottom: 1px solid #e0e0e0; text-align: right; font-weight: bold; color: #ff6b6b;">
                                        ₹<?php echo number_format($expense['amount'], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background: #f8f9fa; font-weight: bold;">
                                <td colspan="3" style="padding: 15px; border-top: 2px solid #e0e0e0;">Total</td>
                                <td style="padding: 15px; text-align: right; border-top: 2px solid #e0e0e0; color: #ff6b6b;">
                                    ₹<?php echo number_format($total_expenses, 2); ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function changeMonth() {
            const selectedMonth = document.getElementById('monthSelect').value;
            window.location.href = 'previous_savings.php?month=' + selectedMonth;
        }

        <?php if (!empty($category_expenses)): ?>
        // Create pie chart
        const ctx = document.getElementById('expenseChart').getContext('2d');
        const expenseChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($category_expenses, 'category')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($category_expenses, 'total')); ?>,
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                        '#FF6384',
                        '#C9CBCF',
                        '#4BC0C0',
                        '#FF6384'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ₹' + value.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>