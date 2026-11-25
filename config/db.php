<?php
// config/db.php
// Database connection using PDO (prepared statements = security)

$DB_HOST = 'localhost';
$DB_NAME = 'asset_register';   // <-- ask MySQL teammate
$DB_USER = 'root';             // <-- ask MySQL teammate
$DB_PASS = '';                 // <-- ask MySQL teammate

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // In production, don't show raw errors
    die("Database connection failed: " . $e->getMessage());
}