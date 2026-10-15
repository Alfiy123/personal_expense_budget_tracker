<?php
require __DIR__ . '/includes/auth.php';
require_login();
require __DIR__ . '/config/db.php';
$error = '';
$stmt = $pdo->prepare('SELECT monthly_budget FROM users WHERE id = ?');
$stmt->execute([(int)$_SESSION['user_id']]);
$currentBudget = (float)($stmt->fetchColumn() ?: 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = filter_var($_POST['monthly_budget'] ?? '', FILTER_VALIDATE_FLOAT);
    if ($amount === false || $amount < 0 || $amount > 999999999) {
        $error = 'Enter a valid budget amount (zero or more).';
    } else {
        $update = $pdo->prepare('UPDATE users SET monthly_budget = ? WHERE id = ?');
        $update->execute([$amount, (int)$_SESSION['user_id']]);
        $_SESSION['notice'] = 'Monthly budget updated.';
        header('Location: dashboard.php'); exit;
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Set budget | PennyPlan</title><link rel="stylesheet" href="assets/style.css"></head><body class="auth-page"><main class="auth-card"><a class="brand" href="dashboard.php">PennyPlan</a><h1>Monthly budget</h1><p class="muted">Choose the maximum amount you want to spend each month.</p><?php if ($error): ?><p class="alert"><?= h($error) ?></p><?php endif; ?><form method="post"><label>Budget amount (₹)<input name="monthly_budget" type="number" min="0" max="999999999" step="0.01" value="<?= h((string)$currentBudget) ?>" required></label><button class="button full" type="submit">Save budget</button></form><p class="switch"><a href="dashboard.php">← Back to dashboard</a></p></main></body></html>
