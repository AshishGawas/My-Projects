<?php
require_once __DIR__ . '/config.php';
require_login();
$user = current_user();
include __DIR__ . '/partials/header.php';
?>
<div class="card" style="max-width:520px;margin:24px auto;">
  <h3>Your Profile</h3>
  <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name'] ?? ''); ?></p>
  <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
  <p><strong>Member since:</strong> <?php echo htmlspecialchars(date('M j, Y', strtotime($user['created_at']))); ?></p>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
