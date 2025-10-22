<?php
require_once __DIR__ . '/../config.php';
$user = current_user();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SaveMore - Expense Tracker</title>
  <link rel="stylesheet" href="/assets/style.css">
  <script defer src="/assets/app.js"></script>
</head>
<body>
  <nav class="navbar">
    <div class="brand"><a href="/index.php">SaveMore</a></div>
    <ul class="nav-links">
      <?php if ($user): ?>
      <li><a href="/home.php">Home</a></li>
      <li><a href="/previous.php">Previous Saving</a></li>
      <li><a href="/profile.php">Profile</a></li>
      <li>
        <form action="/logout.php" method="post" class="inline">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
          <button type="submit" class="linklike">Logout</button>
        </form>
      </li>
      <?php else: ?>
      <li><a href="/index.php">Welcome</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <main class="container">
