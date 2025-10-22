<?php
require_once __DIR__ . '/config.php';
require_login();
verify_csrf();
$db = get_db();
$user = current_user();

// Determine month
$now = new DateTime('first day of last month');
$month = $_GET['month'] ?? $now->format('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $month)) $month = $now->format('Y-m');

$start = $month . '-01';
$end = date('Y-m-t', strtotime($start));

// Salary
$sStmt = $db->prepare('SELECT amount FROM salaries WHERE user_id = ? AND month = ?');
$sStmt->execute([(int)$user['id'], $month]);
$salary = (float)($sStmt->fetchColumn() ?: 0);

// Expenses by category
$eStmt = $db->prepare('SELECT category, SUM(amount) as total FROM expenses WHERE user_id = ? AND date BETWEEN ? AND ? GROUP BY category ORDER BY total DESC');
$eStmt->execute([(int)$user['id'], $start, $end]);
$rows = $eStmt->fetchAll(PDO::FETCH_ASSOC);
$totalSpent = array_sum(array_map(fn($r) => (float)$r['total'], $rows));
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
      <button class="btn" type="submit">Show</button>
    </div>
  </form>
</div>

<div class="card pie-wrap">
  <h3>Spending by Category</h3>
  <canvas id="pie" width="560" height="560"></canvas>
  <p class="muted">Salary: <?php echo number_format($salary,2); ?> | Spent: <?php echo number_format($totalSpent,2); ?> | Remaining: <?php echo number_format($remaining,2); ?></p>
</div>

<div class="card" style="margin-top:16px; overflow-x:auto;">
  <h3>Details</h3>
  <table class="table">
    <thead>
      <tr><th>Category</th><th>Total</th><th>Share</th></tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): $t=(float)$r['total']; $pct = $totalSpent>0? round($t*100/$totalSpent,1):0; ?>
        <tr>
          <td><?php echo htmlspecialchars($r['category']); ?></td>
          <td><?php echo number_format($t,2); ?></td>
          <td><?php echo $pct; ?>%</td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($rows)): ?>
        <tr><td colspan="3" class="muted">No data.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
(function(){
  const data = <?php echo json_encode(array_map(fn($r)=>[(string)$r['category'], (float)$r['total']], $rows)); ?>;
  const canvas = document.getElementById('pie');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  const total = data.reduce((a, [,v]) => a + v, 0);
  const colors = ['#ef4444','#f59e0b','#10b981','#3b82f6','#8b5cf6','#ec4899','#14b8a6','#84cc16'];
  let start = -Math.PI/2;
  data.forEach(([label, value], idx) => {
    const angle = total>0 ? (value/total) * Math.PI*2 : 0;
    ctx.beginPath();
    ctx.moveTo(280,280);
    ctx.arc(280,280,220,start,start+angle);
    ctx.closePath();
    ctx.fillStyle = colors[idx % colors.length];
    ctx.fill();
    // label
    const mid = start + angle/2;
    const lx = 280 + Math.cos(mid)*150;
    const ly = 280 + Math.sin(mid)*150;
    ctx.fillStyle = '#111827';
    ctx.font = '14px system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif';
    const pct = total>0 ? Math.round(value*100/total) : 0;
    ctx.fillText(label + ' ('+pct+'%)', lx-30, ly);
    start += angle;
  });
})();
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
