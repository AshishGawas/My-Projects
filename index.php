<?php
require_once __DIR__ . '/config.php';
verify_csrf();
include __DIR__ . '/partials/header.php';
?>
<section class="hero">
  <h1><strong>DO YOU WANT TO SAVE MONEY</strong>?</h1>
  <div class="slider-wrap">
    <label class="switch">
      <input id="saveToggle" type="checkbox" />
      <span class="slider"></span>
    </label>
  </div>

  <div id="authWrap" class="grid hidden" style="margin-top:24px;">
    <div class="card">
      <h3>Sign up</h3>
      <form method="post" action="/signup.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <label class="label" for="su_name">Name</label>
        <input class="input" id="su_name" name="name" required />
        <label class="label" for="su_email">Email</label>
        <input class="input" id="su_email" name="email" type="email" required />
        <label class="label" for="su_password">Password</label>
        <input class="input" id="su_password" name="password" type="password" required />
        <button class="btn block" type="submit">Create account</button>
      </form>
    </div>

    <div class="card">
      <h3>Login</h3>
      <form method="post" action="/login.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <label class="label" for="li_email">Email</label>
        <input class="input" id="li_email" name="email" type="email" required />
        <label class="label" for="li_password">Password</label>
        <input class="input" id="li_password" name="password" type="password" required />
        <div style="display:flex; justify-content: space-between; align-items:center; margin-top:8px;">
          <a href="/forgot.php">Forgot password?</a>
        </div>
        <button class="btn block" type="submit">Login</button>
      </form>
    </div>
  </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
