<?php
require __DIR__ . '/includes/auth.php';
require_login();
require __DIR__ . '/config/db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: dashboard.php'); exit; }
$title = trim($_POST['title'] ?? '');
$type = $_POST['type'] ?? '';
$category = trim($_POST['category'] ?? '');
$amount = filter_var($_POST['amount'] ?? '', FILTER_VALIDATE_FLOAT);
$date = $_POST['transaction_date'] ?? '';
$allowedCategories = ['Food','Travel','Shopping','Bills','Health','Education','Salary','Gift','Other'];
$dateObj = DateTime::createFromFormat('!Y-m-d', $date);
$validDate = $dateObj && $dateObj->format('Y-m-d') === $date && $date <= date('Y-m-d');
if ($title === '' || strlen($title) > 120 || !in_array($type, ['income','expense'], true) || !in_array($category, $allowedCategories, true) || $amount === false || $amount <= 0 || !$validDate) {
    $_SESSION['notice'] = 'Please check the transaction details and try again.';
    header('Location: dashboard.php'); exit;
}
$stmt = $pdo->prepare('INSERT INTO transactions (user_id, title, category, type, amount, transaction_date) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute([(int)$_SESSION['user_id'], $title, $category, $type, $amount, $date]);
$_SESSION['notice'] = 'Transaction saved successfully.';
header('Location: dashboard.php'); exit;
