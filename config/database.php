<?php
/**
 * MTCare - Database Connection (PDO / MySQL)
 * -------------------------------------------------------------
 * Edit the credentials below to match your local MySQL server
 * (XAMPP / WAMP / LAMP defaults are already filled in).
 */

declare(strict_types=1);

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'mtcare_db');
define('DB_USER', 'root');
define('DB_PASS', '');          // XAMPP default is empty
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a shared PDO connection.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(
                '<div style="font-family:sans-serif;max-width:640px;margin:80px auto;padding:24px;'
                . 'border:1px solid #fecaca;background:#fef2f2;border-radius:12px;color:#991b1b">'
                . '<h2 style="margin-top:0">Database connection failed</h2>'
                . '<p>Could not connect to MySQL. Please confirm the credentials in '
                . '<code>config/database.php</code> and that you have imported '
                . '<code>database/mtcare.sql</code>.</p>'
                . '<p style="font-size:13px;color:#7f1d1d"><strong>Detail:</strong> '
                . htmlspecialchars($e->getMessage()) . '</p></div>'
            );
        }
    }
    return $pdo;
}
