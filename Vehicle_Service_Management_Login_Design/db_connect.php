<?php
mysqli_report(MYSQLI_REPORT_OFF);

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'vehicle_service';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASSWORD') ?: '';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    http_response_code(500);
    die("Database connection is not ready. Please try again later.");
}
$conn->set_charset('utf8mb4');
// Create account table for this classroom project if it does not exist yet.
$conn->query("CREATE TABLE IF NOT EXISTS users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
?>
