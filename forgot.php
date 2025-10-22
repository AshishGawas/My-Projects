<?php
require_once __DIR__ . '/config.php';
include __DIR__ . '/partials/header.php';
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if ($email) {
        $db = get_db();
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // Always respond the same to avoid email enumeration
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = time() + 3600; // 1 hour
            $ins = $db->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)');
            $ins->execute([(int)$user['id'], $token, $expires]);
            $resetLink = '/reset.php?token=' . urlencode($token);
            echo '<div class="card">Password reset link (demo): <a href="' . htmlspecialchars($resetLink) . '">' . htmlspecialchars($resetLink) . '</a></div>';
        }
        echo '<div class="card">If the email exists, a reset link is shown above.</div>';
    }
}
?>
<div class="card" style="max-width:480px;margin:24px auto;">
  <h3>Forgot password</h3>
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
    <label class="label" for="fp_email">Email</label>
    <input class="input" id="fp_email" name="email" type="email" required />
    <button class="btn block" type="submit">Send reset link</button>
  </form>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
