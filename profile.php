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

// Get user statistics
$stats_query = "SELECT 
    COUNT(*) as total_expenses,
    SUM(amount) as total_spent,
    AVG(amount) as avg_expense,
    MIN(expense_date) as first_expense_date,
    MAX(expense_date) as last_expense_date
    FROM expenses WHERE user_id = ?";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute([$_SESSION['user_id']]);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    
    if (!empty($full_name) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check if email is already taken by another user
        $email_check_query = "SELECT id FROM users WHERE email = ? AND id != ?";
        $email_check_stmt = $db->prepare($email_check_query);
        $email_check_stmt->execute([$email, $_SESSION['user_id']]);
        
        if ($email_check_stmt->rowCount() == 0) {
            $update_query = "UPDATE users SET full_name = ?, email = ? WHERE id = ?";
            $update_stmt = $db->prepare($update_query);
            
            if ($update_stmt->execute([$full_name, $email, $_SESSION['user_id']])) {
                $_SESSION['user_name'] = $full_name;
                $_SESSION['user_email'] = $email;
                $_SESSION['success'] = "Profile updated successfully!";
                $user['full_name'] = $full_name;
                $user['email'] = $email;
            } else {
                $_SESSION['error'] = "Failed to update profile.";
            }
        } else {
            $_SESSION['error'] = "Email address is already taken.";
        }
    } else {
        $_SESSION['error'] = "Please provide valid name and email.";
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (password_verify($current_password, $user['password'])) {
        if (strlen($new_password) >= 6) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $password_query = "UPDATE users SET password = ? WHERE id = ?";
                $password_stmt = $db->prepare($password_query);
                
                if ($password_stmt->execute([$hashed_password, $_SESSION['user_id']])) {
                    $_SESSION['success'] = "Password changed successfully!";
                } else {
                    $_SESSION['error'] = "Failed to change password.";
                }
            } else {
                $_SESSION['error'] = "New passwords do not match.";
            }
        } else {
            $_SESSION['error'] = "New password must be at least 6 characters long.";
        }
    } else {
        $_SESSION['error'] = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Expense Tracker</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background: #f5f5f5;">
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">💰 Expense Tracker</div>
            <ul class="nav-links">
                <li><a href="homepage.php">Home</a></li>
                <li><a href="previous_savings.php">Previous Savings</a></li>
                <li><a href="profile.php" style="opacity: 0.8;">Profile</a></li>
                <li><a href="auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="homepage-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success" style="background: #4CAF50; margin-bottom: 20px; padding: 15px; border-radius: 10px; color: white;">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error" style="background: #f44336; margin-bottom: 20px; padding: 15px; border-radius: 10px; color: white;">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-bottom: 40px;">
            <h2>User Profile</h2>
            <p style="color: #666;">Manage your account settings and view your statistics</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px;">
            <!-- Profile Information -->
            <div style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px; color: #333;">Profile Information</h3>
                
                <form method="POST" style="margin-bottom: 30px;">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Full Name</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" 
                               style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;" required>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                               style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;" required>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Monthly Salary</label>
                        <input type="text" value="₹<?php echo number_format($user['monthly_salary'], 2); ?>" 
                               style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; background: #f5f5f5;" readonly>
                        <small style="color: #666;">Update salary from the homepage</small>
                    </div>
                    
                    <button type="submit" name="update_profile" 
                            style="width: 100%; padding: 12px; background: linear-gradient(45deg, #667eea, #764ba2); color: white; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer;">
                        Update Profile
                    </button>
                </form>
                
                <div style="padding-top: 20px; border-top: 1px solid #e0e0e0;">
                    <p style="color: #666; margin-bottom: 5px;"><strong>Member since:</strong> <?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
                    <p style="color: #666;"><strong>Last updated:</strong> <?php echo date('F d, Y g:i A', strtotime($user['updated_at'])); ?></p>
                </div>
            </div>

            <!-- Account Statistics -->
            <div style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px; color: #333;">Account Statistics</h3>
                
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #e0e0e0;">
                        <span style="font-weight: bold;">Total Expenses Recorded</span>
                        <span style="color: #667eea; font-size: 1.2rem; font-weight: bold;"><?php echo number_format($stats['total_expenses']); ?></span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #e0e0e0;">
                        <span style="font-weight: bold;">Total Amount Spent</span>
                        <span style="color: #ff6b6b; font-size: 1.2rem; font-weight: bold;">₹<?php echo number_format($stats['total_spent'], 2); ?></span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #e0e0e0;">
                        <span style="font-weight: bold;">Average Expense</span>
                        <span style="color: #4CAF50; font-size: 1.2rem; font-weight: bold;">₹<?php echo number_format($stats['avg_expense'], 2); ?></span>
                    </div>
                    
                    <?php if ($stats['first_expense_date']): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #e0e0e0;">
                        <span style="font-weight: bold;">First Expense</span>
                        <span style="color: #666; font-size: 1rem;"><?php echo date('M d, Y', strtotime($stats['first_expense_date'])); ?></span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0;">
                        <span style="font-weight: bold;">Latest Expense</span>
                        <span style="color: #666; font-size: 1rem;"><?php echo date('M d, Y', strtotime($stats['last_expense_date'])); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Change Password Section -->
        <div style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto;">
            <h3 style="margin-bottom: 20px; color: #333; text-align: center;">Change Password</h3>
            
            <form method="POST">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Current Password</label>
                    <input type="password" name="current_password" 
                           style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;" required>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">New Password</label>
                    <input type="password" name="new_password" 
                           style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;" 
                           minlength="6" required>
                    <small style="color: #666;">Minimum 6 characters</small>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Confirm New Password</label>
                    <input type="password" name="confirm_password" 
                           style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;" 
                           minlength="6" required>
                </div>
                
                <button type="submit" name="change_password" 
                        style="width: 100%; padding: 12px; background: linear-gradient(45deg, #ff6b6b, #ee5a24); color: white; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer;">
                    Change Password
                </button>
            </form>
        </div>
    </div>
</body>
</html>