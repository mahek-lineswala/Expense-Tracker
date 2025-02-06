<?php
$host = 'localhost';  // Database host
$dbname = 'expense_tracker';  // Database name
$username = 'root';  // Database username
$password = '';  // Database password (change if you have one)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
