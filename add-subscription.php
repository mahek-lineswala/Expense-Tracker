<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false]);
    exit();
}

$user_id = $_SESSION['user']['id'];
$title = $_POST['title'];
$date = $_POST['date'];
$amount = $_POST['amount'];

$stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, title, date, amount) VALUES (?, ?, ?, ?)");
$success = $stmt->execute([$user_id, $title, $date, $amount]);

echo json_encode(['success' => $success]);
