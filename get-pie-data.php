<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user']['id'];

try {
    $stmt = $pdo->prepare("SELECT category, SUM(amount) as total FROM expenses WHERE user_id = ? GROUP BY category");
    $stmt->execute([$user_id]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $totals = [];

    foreach ($data as $row) {
        $labels[] = $row['category'];
        $totals[] = (float)$row['total'];
    }

    echo json_encode([
        'labels' => $labels,
        'totals' => $totals
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
