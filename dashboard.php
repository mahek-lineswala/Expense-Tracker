<?php
session_start();
require 'db.php'; // Include your database connection

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

$user = $_SESSION['user']; // Store user data

// Fetch expenses for the logged-in user
$stmt = $pdo->prepare("SELECT * FROM expenses WHERE user_id = :user_id ORDER BY date DESC");
$stmt->execute(['user_id' => $user['id']]);
$expenses = $stmt->fetchAll();

// Calculate total expenses
$stmt = $pdo->prepare("SELECT SUM(amount) as total_expenses FROM expenses WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user['id']]);
$total_expenses = $stmt->fetch()['total_expenses'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> etrackr - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
<style>
  .jakarta-font {
    font-family: 'Plus Jakarta sans';
  }
</style>
</head>
<body class="bg-[#1c1c1c] min-h-screen flex jakarta-font">
    <div class="w-1/4 border-r">
        <h2 class="text-[#12D861] text-3xl text-center mt-4 font-bold"><span class="text-[#FF8A22]">e</span>Trackr</h2>
        <nav class="text-white text-2xl ml-28 mt-48">
            <ul class="space-y-8">
                <li><a href="" class="font-bold text-[#12D861]">Dashboard</a></li>
                <li><a href="addexpense.php">Add expense</a></li>
                <li><a href="transactions.php">Transactions</a></li>
                <li><a href="logout.php" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Logout</a></li>
            </ul>
        </nav>
    </div>
    <div class="w-3/4">
        
        <div class="flex flex-1">
            <main class="flex-1 p-6 bg-[#1c1c1c]">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="text-2xl text-white">Hi, <span><?php echo htmlspecialchars($user['name']);?>👋</span></div>
                    <div class="bg-gray-300 p-6 rounded-lg shadow w-2/3 ml-20">
                        <h2 class="text-gray-700 text-lg font-bold">Total Balance</h2>
                        <p class="text-2xl font-semibold">$<?php echo number_format($total_expenses, 2); ?></p>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
