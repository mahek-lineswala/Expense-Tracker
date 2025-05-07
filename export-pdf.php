<?php
require 'db.php';
require 'vendor/autoload.php'; // Only if you're using Composer

use Dompdf\Dompdf;

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

$user = $_SESSION['user'];
$stmt = $pdo->prepare("SELECT * FROM expenses WHERE user_id = :user_id ORDER BY date DESC");
$stmt->execute(['user_id' => $user['id']]);
$expenses = $stmt->fetchAll();

// Generate HTML for PDF
$html = '<h1 style="text-align:center;">Transaction History</h1><table border="1" cellpadding="10" cellspacing="0" width="100%">';
$html .= '<thead><tr><th>Date</th><th>Category</th><th>Description</th><th>Amount (₹)</th></tr></thead><tbody>';

foreach ($expenses as $expense) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($expense['date']) . '</td>';
    $html .= '<td>' . htmlspecialchars($expense['category']) . '</td>';
    $html .= '<td>' . htmlspecialchars($expense['description']) . '</td>';
    $html .= '<td>' . htmlspecialchars($expense['amount']) . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table>';

// Create Dompdf instance
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output to browser as download
$dompdf->stream('transaction-history.pdf', ['Attachment' => 1]); // 1 = download
exit();
?>
