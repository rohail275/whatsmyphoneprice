<?php
// Database connection config for shared hosting (cPanel-style MySQL).
// Fill in real credentials before deploying; keep this file out of version
// control in production (add config/db.php to .gitignore on the live server
// if you ever change these away from placeholders).

define('DB_HOST', 'localhost');
define('DB_NAME', 'whatsmyphoneprice');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

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
