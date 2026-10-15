<?php
require __DIR__ . '/includes/auth.php';
require_login();
require __DIR__ . '/config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare('DELETE FROM transactions WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, (int)$_SESSION['user_id']]);
        $_SESSION['notice'] = $stmt->rowCount() ? 'Transaction deleted.' : 'Transaction was not found.';
    }
}
header('Location: dashboard.php'); exit;
