<?php
// Database connection config for shared hosting (cPanel-style MySQL).
//
// Real credentials never go in this file (it's committed to git). Instead,
// create config/db.local.php — it's in .gitignore, so it stays only on
// your server / local machine — defining the same constants with real
// values. This file falls back to placeholders if that file is absent.

$localConfig = __DIR__ . '/db.local.php';
if (is_file($localConfig)) {
    require $localConfig;
} else {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'your_db_name');
    define('DB_USER', 'your_db_user');
    define('DB_PASS', 'your_db_password');
}

define('SITE_URL', 'https://whatsmyphoneprice.com');
define('CURRENT_YEAR', (int) date('Y'));

function get_db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}
