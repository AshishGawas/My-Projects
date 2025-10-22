<?php
require_once __DIR__ . '/config.php';
verify_csrf();
$db = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    if (strlen($password) < 6) {
        header('Location: /reset.php?token=' . urlencode($token));
        exit;
    }
    $stmt = $db->prepare('SELECT user_id, expires_at FROM password_resets WHERE token = ?');
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && (int)$row['expires_at'] >= time()) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $up = $db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $up->execute([$hash, (int)$row['user_id']]);
        $del = $db->prepare('DELETE FROM password_resets WHERE token = ?');
        $del->execute([$token]);
        header('Location: /index.php');
        exit;
    }
    header('Location: /index.php');
    exit;
}

$token = $_GET['token'] ?? '';
include __DIR__ . '/partials/header.php';
?>
<div class="card" style="max-width:480px;margin:24px auto;">
  <h3>Reset password</h3>
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
    <label class="label" for="np">New password</label>
    <input class="input" id="np" name="password" type="password" minlength="6" required />
    <button class="btn block" type="submit">Update password</button>
  </form>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
