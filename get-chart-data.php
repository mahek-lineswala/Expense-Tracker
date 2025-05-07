<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(['labels' => [], 'amounts' => []]);
    exit();
}

$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(date, '%M') AS month, SUM(amount) AS total
    FROM expenses
    WHERE user_id = :user_id
    GROUP BY MONTH(date)
    ORDER BY MONTH(date)
");
$stmt->execute(['user_id' => $user_id]);
$data = $stmt->fetchAll();

$labels = [];
$amounts = [];

foreach ($data as $row) {
    $labels[] = $row['month'];
    $amounts[] = $row['total'];
}

echo json_encode(['labels' => $labels, 'amounts' => $amounts]);
