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
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href=""  class="font-bold text-[#12D861]">Add expense</a></li>
                <li><a href="transactions.php">Transactions</a></li>
                <li><a href="logout.php" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Logout</a></li>
            </ul>
        </nav>
    </div>
    <div class="bg-gray-300 p-6 rounded-lg shadow mb-6 w-3/4">
                    <h2 class="text-xl font-bold mb-4">Add Expense</h2>
                    <form method="POST" action="add-expense.php" >
                        <input type="number" name="amount" placeholder="Amount" required class="w-full p-4 border rounded mb-2">
                        <select name="category" id="categorySelect" required class="w-full p-4 border rounded mb-2" onchange="toggleCustomCategory(this)">
                                <option value="">Select Category</option>
                                <option value="Food">Food</option>
                                <option value="Transportation">Transportation</option>
                                <option value="Utilities">Utilities</option>
                                <option value="Rent">Rent</option>
                                <option value="Healthcare">Healthcare</option>
                                <option value="Entertainment">Entertainment</option>
                                <option value="Education">Education</option>
                                <option value="Groceries">Groceries</option>
                                <option value="Clothing">Clothing</option>
                                <option value="Other">Other</option>
                                </select>

                                <input type="text" name="custom_category" id="customCategoryInput" placeholder="Enter custom category"
                                    class="w-full p-4 border rounded mb-2 hidden">

                                <script>
                                function toggleCustomCategory(select) {
                                    const customInput = document.getElementById('customCategoryInput');
                                    if (select.value === 'Other') {
                                    customInput.classList.remove('hidden');
                                    customInput.required = true;
                                    } else {
                                    customInput.classList.add('hidden');
                                    customInput.required = false;
                                    }
                                }
                                </script>

                        <textarea name="description" placeholder="Description" class="w-full p-2 border rounded mb-2"></textarea>
                        <input type="date" name="date" required class="w-fit p-2 border rounded mb-2"><br>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded mt-1">Add Expense</button>
                    </form>
                </div>
    </body>
</html>