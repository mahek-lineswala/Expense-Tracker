<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    echo 'fail';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $user_id = $_SESSION['user']['id'];
    $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = :id AND user_id = :user_id");
    $success = $stmt->execute(['id' => $id, 'user_id' => $user_id]);

    echo $success ? 'success' : 'fail';
}
?>
