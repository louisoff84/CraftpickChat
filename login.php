<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $stmt = $pdo->prepare('SELECT id, username, email, password_hash, is_admin FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['username'] = (string) $user['username'];
        $_SESSION['is_admin'] = (bool) $user['is_admin'];
        header('Location: index.php');
        exit;
    }

    $error = 'Adresse e-mail ou mot de passe incorrect.';
}
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Connexion — CraftpickChat</title><link rel="stylesheet" href="style.css"><style>body{display:grid;place-items:center;min-height:100vh;background:#111827}.auth{width:min(420px,92vw);background:white;padding:30px;border-radius:18px}.auth h1{margin:0 0 8px}.auth p{color:#64748b}.auth label{display:block;margin:16px 0 6px;font-weight:700}.auth input{width:100%;box-sizing:border-box;padding:12px;border:1px solid #cbd5e1;border-radius:10px}.auth button,.setup{display:block;width:100%;box-sizing:border-box;margin-top:20px;padding:12px;border:0;border-radius:10px;background:#6d28d9;color:white;font-weight:800;text-align:center;text-decoration:none}.error{background:#fee2e2;color:#991b1b;padding:10px;border-radius:8px}.notice{background:#fef3c7;color:#92400e;padding:10px;border-radius:8px}</style></head><body><main class="auth"><h1>CraftpickChat</h1><p>Connecte-toi avec un compte créé par un administrateur.</p><?php if ($error): ?><div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?><?php if ($userCount === 0): ?><div class="notice">Aucun compte n'existe encore. Crée le premier administrateur.</div><a class="setup" href="setup.php">Configurer l'administrateur</a><?php else: ?><form method="post"><label for="email">E-mail</label><input id="email" name="email" type="email" required autocomplete="email"><label for="password">Mot de passe</label><input id="password" name="password" type="password" required autocomplete="current-password"><button type="submit">Se connecter</button></form><?php endif; ?></main></body></html>
