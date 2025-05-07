<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $user_id = $_SESSION['user']['id'];
    $expense_id = $_POST['id'];

    // Safeguard: Delete only if it belongs to the user
    $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $expense_id, 'user_id' => $user_id]);
}

// Redirect back to transactions or dashboard
header("Location: transactions.php");
exit();
