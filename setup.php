<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/db.php';

$userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
if ($userCount > 0) {
    header('Location: login.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!preg_match('/^[A-Za-z0-9_\-]{3,32}$/', $username)) {
        $error = 'Le pseudo doit contenir entre 3 et 32 caractères.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse e-mail invalide.';
    } elseif (strlen($password) < 10) {
        $error = 'Le mot de passe doit contenir au moins 10 caractères.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash, is_admin) VALUES (:username, :email, :password_hash, 1)');
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Configuration — CraftpickChat</title><style>body{font-family:Arial,sans-serif;display:grid;place-items:center;min-height:100vh;background:#111827;margin:0}.box{width:min(440px,92vw);background:#fff;padding:30px;border-radius:18px;box-sizing:border-box}.box h1{margin-top:0}.box label{display:block;margin:15px 0 6px;font-weight:700}.box input{width:100%;box-sizing:border-box;padding:12px;border:1px solid #cbd5e1;border-radius:10px}.box button{width:100%;margin-top:20px;padding:12px;border:0;border-radius:10px;background:#6d28d9;color:#fff;font-weight:800}.error{background:#fee2e2;color:#991b1b;padding:10px;border-radius:8px}</style></head><body><main class="box"><h1>Premier administrateur</h1><p>Cette page est automatiquement désactivée dès qu'un compte existe.</p><?php if ($error): ?><div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?><form method="post"><label for="username">Pseudo</label><input id="username" name="username" required maxlength="32"><label for="email">E-mail</label><input id="email" name="email" type="email" required><label for="password">Mot de passe</label><input id="password" name="password" type="password" minlength="10" required><button type="submit">Créer l'administrateur</button></form></main></body></html>
