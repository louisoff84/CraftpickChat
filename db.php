<?php
declare(strict_types=1);

$configFile = __DIR__ . '/config.php';

if (!is_file($configFile)) {
    http_response_code(500);
    exit('Configuration manquante : copie config.example.php en config.php puis remplis les identifiants MySQL.');
}

$config = require $configFile;
$required = ['db_host', 'db_port', 'db_name', 'db_user', 'db_pass'];

foreach ($required as $key) {
    if (!isset($config[$key]) || trim((string) $config[$key]) === '' || $config[$key] === 'CHANGE_MOI') {
        http_response_code(500);
        exit('Configuration MySQL incomplète dans config.php : champ ' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . ' manquant.');
    }
}

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $config['db_host'],
        $config['db_port'],
        $config['db_name']
    );

    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(32) NOT NULL UNIQUE,
            email VARCHAR(190) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            is_admin TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $column = $pdo->query("SHOW COLUMNS FROM users LIKE 'is_admin'")->fetch();
    if (!$column) {
        $pdo->exec('ALTER TABLE users ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER password_hash');
    }
} catch (PDOException $e) {
    http_response_code(500);
    exit('Connexion MySQL impossible. Vérifie les identifiants dans config.php et que la base existe.');
}
