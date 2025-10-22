<?php
session_start();

// MySQL connection (override via environment variables)
// MYSQL_HOST, MYSQL_DB, MYSQL_USER, MYSQL_PASS
function get_db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $host = getenv('MYSQL_HOST') ?: '127.0.0.1';
        $db   = getenv('MYSQL_DB') ?: 'savemore';
        $user = getenv('MYSQL_USER') ?: 'root';
        $pass = getenv('MYSQL_PASS') ?: '';
        $charset = 'utf8mb4';

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        // Connect without specifying DB to ensure database exists
        $pdo = new PDO("mysql:host={$host};charset={$charset}", $user, $pass, $options);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}` DEFAULT CHARACTER SET {$charset} COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$db}`");
        // Enforce strict mode for safer numeric handling
        $pdo->exec("SET sql_mode = 'STRICT_ALL_TABLES'");
    }
    return $pdo;
}

function init_db(): void {
    $db = get_db();

    // Users table
    $db->exec(<<<SQL
    CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        name VARCHAR(255) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    SQL);

    // Password reset tokens
    $db->exec(<<<SQL
    CREATE TABLE IF NOT EXISTS password_resets (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        token VARCHAR(64) NOT NULL UNIQUE,
        expires_at INT NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    SQL);

    // Salaries per month
    $db->exec(<<<SQL
    CREATE TABLE IF NOT EXISTS salaries (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        month CHAR(7) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_user_month (user_id, month),
        CONSTRAINT fk_salaries_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    SQL);

    // Expenses
    $db->exec(<<<SQL
    CREATE TABLE IF NOT EXISTS expenses (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        date DATE NOT NULL,
        category VARCHAR(64) NOT NULL,
        description VARCHAR(255) NULL,
        amount DECIMAL(10,2) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_expenses_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    SQL);
}

init_db();

function require_login(): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /index.php');
        exit;
    }
}

function current_user(): ?array {
    if (!isset($_SESSION['user_id'])) return null;
    $db = get_db();
    $stmt = $db->prepare('SELECT id, email, name, created_at FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(400);
            echo 'Invalid CSRF token.';
            exit;
        }
    }
}
