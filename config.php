<?php
$host = "localhost";
$dbname = "expense_tracker";
$username = "root"; // Default XAMPP MySQL username
$password = ""; // Default XAMPP MySQL password (leave empty)

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Database connected successfully!";
?>
