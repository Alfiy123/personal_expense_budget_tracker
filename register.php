<?php
session_start();
require __DIR__ . '/config/db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        $error = 'Enter your name, a valid email, and a password with at least 8 characters.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            header('Location: dashboard.php');
            exit;
        } catch (PDOException $e) {
            $error = $e->getCode() === '23000' ? 'That email is already registered.' : 'Could not create account. Check your database setup.';
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Create account</title><link rel="stylesheet" href="assets/style.css"><script defer src="assets/app.js"></script></head>
<body class="auth-page"><main class="auth-card"><a class="brand" href="login.php">PennyPlan</a><h1>Create account</h1><p class="muted">Start tracking your money.</p>
<?php if ($error): ?><p class="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
<form method="post" data-validate>
<label>Full name<input name="name" required maxlength="100" autocomplete="name"></label>
<label>Email<input type="email" name="email" required maxlength="190" autocomplete="email"></label>
<label>Password<input type="password" name="password" required minlength="8" autocomplete="new-password"><small>Use at least 8 characters.</small></label>
<button class="button full" type="submit">Create account</button></form><p class="switch">Already have an account? <a href="login.php">Log in</a></p></main></body></html>
