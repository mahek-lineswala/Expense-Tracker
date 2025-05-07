<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user']['id'];
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    // Insert the expense into the database
    $stmt = $pdo->prepare("INSERT INTO expenses (user_id, amount, category, description, date) VALUES (:user_id, :amount, :category, :description, :date)");
    $stmt->execute(['user_id' => $user_id, 'amount' => $amount, 'category' => $category, 'description' => $description, 'date' => $date]);

    // Redirect to the dashboard
    header("Location: addexpense.php");
    exit();
}
?>
