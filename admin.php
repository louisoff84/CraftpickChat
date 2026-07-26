<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if (empty($_SESSION['is_admin'])) {
    http_response_code(403);
    exit('Accès refusé.');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

    if (!preg_match('/^[A-Za-z0-9_\-]{3,32}$/', $username)) {
        $error = 'Le pseudo doit contenir entre 3 et 32 caractères.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse e-mail invalide.';
    } elseif (strlen($password) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash, is_admin) VALUES (:username, :email, :password_hash, :is_admin)');
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'is_admin' => $isAdmin,
            ]);
            $success = 'Le compte a été créé.';
        } catch (PDOException $e) {
            $error = 'Ce pseudo ou cette adresse e-mail est déjà utilisé.';
        }
    }
}

$users = $pdo->query('SELECT id, username, email, is_admin, created_at FROM users ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Administration — CraftpickChat</title><style>*{box-sizing:border-box}body{font-family:Arial,sans-serif;background:#f1f5f9;margin:0;color:#0f172a}.top{background:#111827;color:#fff;padding:18px 5%;display:flex;justify-content:space-between;align-items:center}.top a{color:#fff}.wrap{width:min(1050px,92%);margin:30px auto;display:grid;grid-template-columns:380px 1fr;gap:24px}.card{background:#fff;padding:24px;border-radius:16px;box-shadow:0 8px 25px #0f172a12}.card h2{margin-top:0}label{display:block;margin:14px 0 6px;font-weight:700}input[type=text],input[type=email],input[type=password]{width:100%;padding:11px;border:1px solid #cbd5e1;border-radius:9px}button{width:100%;padding:12px;margin-top:18px;border:0;border-radius:9px;background:#6d28d9;color:#fff;font-weight:800}.check{display:flex;gap:8px;align-items:center;margin-top:14px}.error,.success{padding:10px;border-radius:8px;margin-bottom:12px}.error{background:#fee2e2;color:#991b1b}.success{background:#dcfce7;color:#166534}table{width:100%;border-collapse:collapse}th,td{text-align:left;padding:11px;border-bottom:1px solid #e2e8f0;font-size:14px}.badge{padding:4px 8px;border-radius:99px;background:#ede9fe;color:#5b21b6;font-size:12px}@media(max-width:800px){.wrap{grid-template-columns:1fr}.table{overflow-x:auto}}</style></head><body><header class="top"><strong>Administration CraftpickChat</strong><div><a href="index.php">Retour au chat</a> · <a href="logout.php">Déconnexion</a></div></header><main class="wrap"><section class="card"><h2>Créer un compte</h2><?php if ($error): ?><div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?><?php if ($success): ?><div class="success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?><form method="post"><label for="username">Pseudo</label><input id="username" name="username" type="text" maxlength="32" required><label for="email">E-mail</label><input id="email" name="email" type="email" required><label for="password">Mot de passe temporaire</label><input id="password" name="password" type="password" minlength="8" required><label class="check"><input name="is_admin" type="checkbox"> Donner les droits administrateur</label><button type="submit">Créer le compte</button></form></section><section class="card table"><h2>Comptes existants</h2><table><thead><tr><th>Pseudo</th><th>E-mail</th><th>Rôle</th><th>Créé le</th></tr></thead><tbody><?php foreach ($users as $user): ?><tr><td><?= htmlspecialchars((string) $user['username'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8') ?></td><td><?php if ($user['is_admin']): ?><span class="badge">Admin</span><?php else: ?>Membre<?php endif; ?></td><td><?= htmlspecialchars((string) $user['created_at'], ENT_QUOTES, 'UTF-8') ?></td></tr><?php endforeach; ?></tbody></table></section></main></body></html>
