<?php
require_once __DIR__ . '/config.php';
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$name = trim($_POST['name'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
    header('Location: /index.php');
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$db = get_db();
try {
    $stmt = $db->prepare('INSERT INTO users (email, password_hash, name) VALUES (?, ?, ?)');
    $stmt->execute([$email, $hash, $name ?: null]);
    $_SESSION['user_id'] = (int)$db->lastInsertId();
    header('Location: /home.php');
} catch (PDOException $e) {
    // likely duplicate email
    header('Location: /index.php');
}
