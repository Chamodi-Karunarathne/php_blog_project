<?php
$envPath = __DIR__ . '/.env';
if (is_readable($envPath)) {
    $env = parse_ini_file($envPath, false, INI_SCANNER_RAW);
    if ($env !== false) {
        foreach ($env as $key => $value) {
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
            }
        }
    }
}

$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';
$dbname = $_ENV['DB_NAME'] ?? 'php_blog';

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die('Database Connection Failed: ' . mysqli_connect_error());
}
?>