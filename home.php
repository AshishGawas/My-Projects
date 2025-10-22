<?php
require_once __DIR__ . '/config.php';
require_login();
verify_csrf();
$db = get_db();
$user = current_user();

// Process salary submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salary_amount'], $_POST['salary_month'])) {
    $amount = (float)$_POST['salary_amount'];
    $month = preg_replace('/[^0-9\-]/', '', $_POST['salary_month']);
    if ($amount >= 0 && preg_match('/^\d{4}-\d{2}$/', $month)) {
        $stmt = $db->prepare('INSERT INTO salaries (user_id, month, amount) VALUES (?, ?, ?) ON CONFLICT(user_id, month) DO UPDATE SET amount = excluded.amount');
        $stmt->execute([(int)$user['id'], $month, $amount]);
    }
}

// Process new expense
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expense_amount'], $_POST['expense_category'], $_POST['expense_date'])) {
    $eAmount = (float)$_POST['expense_amount'];
    $eCat = trim($_POST['expense_category']);
    $eDate = preg_replace('/[^0-9\-]/', '', $_POST['expense_date']);
    $eDesc = trim($_POST['expense_description'] ?? '');
    if ($eAmount >= 0 && preg_match('/^\d{4}-\d{2}-\d{2}$/', $eDate)) {
        $stmt = $db->prepare('INSERT INTO expenses (user_id, date, category, description, amount) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([(int)$user['id'], $eDate, $eCat, $eDesc ?: null, $eAmount]);
    }
}

// Determine current month
$nowMonth = date('Y-m');
$month = $_GET['month'] ?? $nowMonth;
if (!preg_match('/^\d{4}-\d{2}$/', $month)) $month = $nowMonth;

// Load salary for month
$stmt = $db->prepare('SELECT amount FROM salaries WHERE user_id = ? AND month = ?');
$stmt->execute([(int)$user['id'], $month]);
$salary = (float)($stmt->fetchColumn() ?: 0);

// Load expenses for month
$start = $month . '-01';
$end = date('Y-m-t', strtotime($start));
$expStmt = $db->prepare('SELECT id, date, category, description, amount FROM expenses WHERE user_id = ? AND date BETWEEN ? AND ? ORDER BY date DESC, id DESC');
$expStmt->execute([(int)$user['id'], $start, $end]);
$expenses = $expStmt->fetchAll(PDO::FETCH_ASSOC);
$totalSpent = array_sum(array_map(fn($r) => (float)$r['amount'], $expenses));
$remaining = max(0, $salary - $totalSpent);

include __DIR__ . '/partials/header.php';
?>
<div class="card">
  <form method="get" class="grid" style="align-items:end;">
    <div>
      <label class="label" for="m">Month</label>
      <input class="input" id="m" name="month" type="month" value="<?php echo htmlspecialchars($month); ?>">
    </div>
    <div>
      <button class="btn" type="submit">Switch month</button>
    </div>
  </form>
</div>

<div class="center-big" id="salaryDisplay"><?php echo $salary ? number_format($salary, 2) : '<span class="muted">Enter Your Salary</span>'; ?></div>

<div class="grid" style="margin-top:16px;">
  <div class="card">
    <h3>Enter Your Salary</h3>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
      <label class="label" for="sm">Month</label>
      <input class="input" id="sm" name="salary_month" type="month" value="<?php echo htmlspecialchars($month); ?>" required>
      <label class="label" for="sa">Amount</label>
      <input class="input" id="sa" name="salary_amount" type="number" step="0.01" min="0" required>
      <button class="btn block" type="submit">Save Salary</button>
    </form>
  </div>

  <div class="card">
    <h3>Add Expense</h3>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
      <label class="label" for="ed">Date</label>
      <input class="input" id="ed" name="expense_date" type="date" value="<?php echo date('Y-m-d'); ?>" required>
      <label class="label" for="ec">Category</label>
      <select class="input" id="ec" name="expense_category" required>
        <option>online food order</option>
        <option>Street food</option>
        <option>Groceries</option>
        <option>online shopping</option>
        <option>offline shopping</option>
        <option>friends & family</option>
      </select>
      <label class="label" for="edesc">Description (optional)</label>
      <input class="input" id="edesc" name="expense_description" placeholder="Short note" />
      <label class="label" for="ea">Amount</label>
      <input class="input" id="ea" name="expense_amount" type="number" step="0.01" min="0" required>
      <button class="btn block" type="submit">Add Expense</button>
    </form>
  </div>
</div>

<div class="card" style="margin-top:16px;">
  <h3>Overview</h3>
  <p><strong>Salary:</strong> <?php echo number_format($salary, 2); ?> | <strong>Total Spent:</strong> <?php echo number_format($totalSpent, 2); ?> | <strong>Remaining:</strong> <?php echo number_format($remaining, 2); ?></p>
</div>

<div class="card" style="margin-top:16px; overflow-x:auto;">
  <h3>Expenses</h3>
  <table class="table">
    <thead>
      <tr>
        <th>Date</th><th>Category</th><th>Description</th><th>Amount</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($expenses as $e): ?>
        <tr>
          <td><?php echo htmlspecialchars($e['date']); ?></td>
          <td><?php echo htmlspecialchars($e['category']); ?></td>
          <td><?php echo htmlspecialchars($e['description'] ?? ''); ?></td>
          <td><?php echo number_format((float)$e['amount'], 2); ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($expenses)): ?>
        <tr><td colspan="4" class="muted">No expenses yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
