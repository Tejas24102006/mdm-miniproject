<?php
// Display errors for debugging (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database configuration
$host = "localhost";
$username = "root";       // Default XAMPP username
$password = "1234";           // Default XAMPP password (empty)
$database = "smart_local_services";

// Create MySQLi connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error
    ]));
}

// Set charset to utf8mb4 for full Unicode support
$conn->set_charset("utf8mb4");
?>
