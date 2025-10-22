<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker - Save Money</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="landing-container">
        <div class="main-content">
            <?php
            session_start();
            if (isset($_SESSION['success'])): ?>
                <div class="alert success" style="background: #4CAF50; margin-bottom: 20px; padding: 15px; border-radius: 10px; color: white;">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert error" style="background: #f44336; margin-bottom: 20px; padding: 15px; border-radius: 10px; color: white;">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <h1 class="main-title">DO YOU WANT TO SAVE MONEY?</h1>
            
            <div class="slider-container">
                <div class="slider-track">
                    <div class="slider-thumb" id="sliderThumb"></div>
                    <span class="slider-label left">NO</span>
                    <span class="slider-label right">YES</span>
                </div>
            </div>
            
            <div class="auth-forms" id="authForms" style="display: none;">
                <div class="form-toggle">
                    <button class="toggle-btn active" onclick="showLogin()">Login</button>
                    <button class="toggle-btn" onclick="showSignup()">Sign Up</button>
                </div>
                
                <!-- Login Form -->
                <form class="auth-form" id="loginForm" action="auth/login.php" method="POST">
                    <h2>Login to Your Account</h2>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="auth-btn">Login</button>
                    <a href="#" class="forgot-password" onclick="showForgotPassword()">Forgot Password?</a>
                </form>
                
                <!-- Sign Up Form -->
                <form class="auth-form" id="signupForm" action="auth/register.php" method="POST" style="display: none;">
                    <h2>Create New Account</h2>
                    <div class="form-group">
                        <input type="text" name="full_name" placeholder="Full Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                    <button type="submit" class="auth-btn">Sign Up</button>
                </form>
                
                <!-- Forgot Password Form -->
                <form class="auth-form" id="forgotForm" action="auth/forgot_password.php" method="POST" style="display: none;">
                    <h2>Reset Password</h2>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                    <button type="submit" class="auth-btn">Send Reset Link</button>
                    <a href="#" class="back-to-login" onclick="showLogin()">Back to Login</a>
                </form>
            </div>
        </div>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>