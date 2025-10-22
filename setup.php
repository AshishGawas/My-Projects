<?php
// Simple setup verification script
echo "<!DOCTYPE html>
<html>
<head>
    <title>Expense Tracker - Setup Check</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        .box { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success-box { background: #d4edda; border: 1px solid #c3e6cb; }
        .error-box { background: #f8d7da; border: 1px solid #f5c6cb; }
        .warning-box { background: #fff3cd; border: 1px solid #ffeaa7; }
        .info-box { background: #d1ecf1; border: 1px solid #bee5eb; }
    </style>
</head>
<body>";

echo "<h1>🚀 Expense Tracker Setup Verification</h1>";

// Check PHP version
echo "<h2>📋 System Requirements</h2>";

if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
    echo "<div class='box success-box'><strong class='success'>✓ PHP Version:</strong> " . PHP_VERSION . " (Good!)</div>";
} else {
    echo "<div class='box error-box'><strong class='error'>✗ PHP Version:</strong> " . PHP_VERSION . " (Requires PHP 7.4+)</div>";
}

// Check required extensions
$required_extensions = ['pdo', 'pdo_mysql', 'session', 'json'];
echo "<h3>Required PHP Extensions:</h3>";

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<div class='box success-box'><strong class='success'>✓ $ext:</strong> Available</div>";
    } else {
        echo "<div class='box error-box'><strong class='error'>✗ $ext:</strong> Not available</div>";
    }
}

// Test database connection
echo "<h2>🗄️ Database Connection Test</h2>";

try {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "<div class='box success-box'><strong class='success'>✓ Database Connection:</strong> Successful</div>";
        
        // Check if tables exist
        $tables = ['users', 'expenses', 'password_reset_tokens'];
        foreach ($tables as $table) {
            $stmt = $db->prepare("SHOW TABLES LIKE '$table'");
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                echo "<div class='box success-box'><strong class='success'>✓ Table '$table':</strong> Exists</div>";
            } else {
                echo "<div class='box warning-box'><strong class='warning'>! Table '$table':</strong> Not found (will be created automatically)</div>";
            }
        }
    } else {
        echo "<div class='box error-box'><strong class='error'>✗ Database Connection:</strong> Failed</div>";
    }
} catch (Exception $e) {
    echo "<div class='box error-box'><strong class='error'>✗ Database Error:</strong> " . $e->getMessage() . "</div>";
    echo "<div class='box info-box'><strong class='info'>💡 Tip:</strong> Make sure MySQL is running and check your database credentials in config/database.php</div>";
}

// Check file permissions
echo "<h2>📁 File Permissions</h2>";

$files_to_check = [
    'config/database.php',
    'css/style.css',
    'js/script.js',
    'index.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        if (is_readable($file)) {
            echo "<div class='box success-box'><strong class='success'>✓ $file:</strong> Readable</div>";
        } else {
            echo "<div class='box error-box'><strong class='error'>✗ $file:</strong> Not readable</div>";
        }
    } else {
        echo "<div class='box error-box'><strong class='error'>✗ $file:</strong> File not found</div>";
    }
}

// Final recommendations
echo "<h2>🎯 Next Steps</h2>";

echo "<div class='box info-box'>
    <h3>If everything looks good:</h3>
    <ol>
        <li><strong>Delete this setup.php file</strong> for security</li>
        <li><a href='index.php'>Go to the main application</a></li>
        <li>Register a new account</li>
        <li>Start tracking your expenses!</li>
    </ol>
</div>";

echo "<div class='box warning-box'>
    <h3>If you see errors:</h3>
    <ol>
        <li>Check that your web server (Apache/Nginx) is running</li>
        <li>Verify MySQL service is started</li>
        <li>Review database credentials in config/database.php</li>
        <li>Ensure proper file permissions</li>
        <li>Check the README.md for detailed setup instructions</li>
    </ol>
</div>";

echo "<div class='box success-box'>
    <h3>🎉 Welcome to Expense Tracker!</h3>
    <p>This application will help you track your expenses and save money. The intuitive interface makes it easy to:</p>
    <ul>
        <li>Set your monthly salary</li>
        <li>Track daily expenses by category</li>
        <li>Analyze spending patterns with charts</li>
        <li>Monitor your savings progress</li>
    </ul>
</div>";

echo "</body></html>";
?>