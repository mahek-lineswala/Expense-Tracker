<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("SELECT title, date, amount FROM subscriptions WHERE user_id = ? AND date >= CURDATE() ORDER BY date ASC");
$stmt->execute([$user_id]);
$subs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($subs);
?>