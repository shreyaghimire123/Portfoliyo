<?php
// ========================
// DATABASE CONFIGURATION
// ========================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'portfolio_db');

// ========================
// SITE CONFIGURATION
// ========================
define('SITE_NAME', 'Shreya.G Portfolio');
define('SITE_URL', 'https://shreyaghimire.com');
define('ADMIN_EMAIL', 'ghimireshreya330@gmail.com');

// ========================
// ERROR REPORTING
// ========================
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// ========================
// SESSION
// ========================
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

session_start();
?>

