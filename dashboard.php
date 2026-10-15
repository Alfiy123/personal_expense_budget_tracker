<?php
require __DIR__ . '/includes/auth.php';
require_login();
require __DIR__ . '/config/db.php';
$userId = (int)$_SESSION['user_id'];
$month = date('Y-m');
$stmt = $pdo->prepare("SELECT type, COALESCE(SUM(amount),0) total FROM transactions WHERE user_id = ? AND DATE_FORMAT(transaction_date, '%Y-%m') = ? GROUP BY type");
$stmt->execute([$userId, $month]);
$totals = ['income' => 0.0, 'expense' => 0.0];
foreach ($stmt->fetchAll() as $row) $totals[$row['type']] = (float)$row['total'];
$budgetStmt = $pdo->prepare('SELECT monthly_budget FROM users WHERE id = ?');
$budgetStmt->execute([$userId]);
$budget = (float)($budgetStmt->fetchColumn() ?: 0);
$remaining = $budget - $totals['expense'];
$list = $pdo->prepare('SELECT id, title, category, type, amount, transaction_date FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC, id DESC LIMIT 50');
$list->execute([$userId]);
$transactions = $list->fetchAll();
$notice = $_SESSION['notice'] ?? ''; unset($_SESSION['notice']);
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dashboard | PennyPlan</title><link rel="stylesheet" href="assets/style.css"><script defer src="assets/app.js"></script></head><body>
<header class="topbar"><a class="brand" href="dashboard.php">PennyPlan</a><div class="top-actions"><span>Hello, <?= h($_SESSION['user_name'] ?? 'there') ?></span><a class="text-link" href="budget.php">Set budget</a><a class="text-link" href="logout.php">Log out</a></div></header>
<main class="container"><section class="welcome"><div><p class="eyebrow"><?= date('F Y') ?> OVERVIEW</p><h1>Your money, made simple.</h1><p class="muted">Keep an eye on what comes in and what goes out.</p></div></section>
<?php if ($notice): ?><p class="success"><?= h($notice) ?></p><?php endif; ?>
<section class="summary-grid">
<article class="summary-card"><p>Total income</p><h2>₹<?= number_format($totals['income'], 2) ?></h2><span class="positive">Money received this month</span></article>
<article class="summary-card"><p>Total expenses</p><h2>₹<?= number_format($totals['expense'], 2) ?></h2><span class="negative">Money spent this month</span></article>
<article class="summary-card"><p>Budget remaining</p><h2>₹<?= number_format($remaining, 2) ?></h2><span class="muted">Monthly budget: ₹<?= number_format($budget, 2) ?></span></article>
</section>
<section class="content-grid"><article class="panel"><div class="panel-heading"><div><h2>Add a transaction</h2><p class="muted">Record income or an expense.</p></div></div>
<form action="save_transaction.php" method="post" data-validate class="transaction-form">
<label>Title<input name="title" required maxlength="120" placeholder="e.g. Groceries"></label>
<div class="form-row"><label>Type<select name="type" required><option value="expense">Expense</option><option value="income">Income</option></select></label><label>Amount (₹)<input name="amount" type="number" min="0.01" step="0.01" required></label></div>
<div class="form-row"><label>Category<select name="category" required><option>Food</option><option>Travel</option><option>Shopping</option><option>Bills</option><option>Health</option><option>Education</option><option>Salary</option><option>Gift</option><option>Other</option></select></label><label>Date<input type="date" name="transaction_date" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required></label></div>
<button class="button" type="submit">+ Save transaction</button></form></article>
<article class="panel budget-panel"><h2>Monthly budget</h2><p class="muted">Your spending limit for this month.</p><div class="budget-number">₹<?= number_format($budget, 2) ?></div><div class="progress-track"><div class="progress-fill" style="width:<?= $budget > 0 ? min(100, ($totals['expense'] / $budget) * 100) : 0 ?>%"></div></div><p class="muted"><?= $budget > 0 ? number_format(($totals['expense'] / $budget) * 100, 1) . '% of budget used' : 'Set a budget to track your progress.' ?></p><a class="button secondary" href="budget.php">Update budget</a></article></section>
<section class="panel history-panel"><div class="panel-heading"><div><h2>Recent transactions</h2><p class="muted">Your latest 50 records.</p></div></div>
<?php if (!$transactions): ?><p class="empty">No transactions yet. Add your first one above.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Transaction</th><th>Category</th><th>Date</th><th>Type</th><th>Amount</th><th></th></tr></thead><tbody>
<?php foreach ($transactions as $t): ?><tr><td><?= h($t['title']) ?></td><td><?= h($t['category']) ?></td><td><?= h($t['transaction_date']) ?></td><td><span class="pill <?= h($t['type']) ?>"><?= ucfirst(h($t['type'])) ?></span></td><td class="<?= $t['type'] === 'income' ? 'positive' : 'negative' ?>"><?= $t['type'] === 'income' ? '+' : '−' ?>₹<?= number_format((float)$t['amount'], 2) ?></td><td><form action="delete_transaction.php" method="post" onsubmit="return confirm('Delete this transaction?')"><input type="hidden" name="id" value="<?= (int)$t['id'] ?>"><button class="delete-button" type="submit" aria-label="Delete transaction">Delete</button></form></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></section></main><footer>Made for better money habits · PennyPlan</footer></body></html>
