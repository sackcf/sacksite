<?php
// Load environment variables
$env = parse_ini_file(__DIR__ . '/.env');

$servername = $env['DB_SERVER'] ?? 'localhost';
$username   = $env['DB_USERNAME'] ?? 'root';
$password   = $env['DB_PASSWORD'] ?? '';
$dbname     = $env['DB_NAME'] ?? 'sacksite';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
