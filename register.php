<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
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
    } elseif (strlen($password) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)');
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Ce pseudo ou cette adresse e-mail est déjà utilisé.';
        }
    }
}
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Inscription — CraftpickChat</title><link rel="stylesheet" href="style.css"><style>body{display:grid;place-items:center;min-height:100vh;background:#111827}.auth{width:min(420px,92vw);background:white;padding:30px;border-radius:18px}.auth h1{margin:0 0 8px}.auth p{color:#64748b}.auth label{display:block;margin:16px 0 6px;font-weight:700}.auth input{width:100%;box-sizing:border-box;padding:12px;border:1px solid #cbd5e1;border-radius:10px}.auth button{width:100%;margin-top:20px;padding:12px;border:0;border-radius:10px;background:#6d28d9;color:white;font-weight:800}.error{background:#fee2e2;color:#991b1b;padding:10px;border-radius:8px}.auth a{color:#6d28d9}</style></head><body><main class="auth"><h1>Créer un compte</h1><p>Rejoins CraftpickChat.</p><?php if ($error): ?><div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?><form method="post"><label for="username">Pseudo</label><input id="username" name="username" required maxlength="32" autocomplete="username"><label for="email">E-mail</label><input id="email" name="email" type="email" required autocomplete="email"><label for="password">Mot de passe</label><input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"><button type="submit">Créer mon compte</button></form><p>Déjà inscrit ? <a href="login.php">Se connecter</a></p></main></body></html>
