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
    <title> etrackr - Add expense</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
<link rel="icon" href="./money-bill-trend-up-solid (1).svg">
<style>
  .jakarta-font {
    font-family: 'Plus Jakarta sans';
  }
</style>
</head>
<body class="bg-[#1c1c1c] min-h-screen flex jakarta-font">
    <div class="w-1/4 border-r">
    <img src="./eTrackr logo1.png" alt="" class="w-28 ml-28 mt-4 fixed">
    <nav class="text-white text-2xl ml-28 fixed h-screen flex flex-col justify-around">
        <!-- Top Links -->
        <ul class="space-y-8 mt-40">
            <li><a href="dashboard.php" >Dashboard</a></li>
            <li><a href="addexpense.php" class="font-bold text-[#12D861]">Add expense</a></li>
            <li><a href="transactions.php" >Transactions</a></li>
        </ul>
        <!-- Logout at Bottom -->
        <ul class="mb-12">
            <li>
            <a href="logout.php" class="ml-4 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-xl">
                Logout
            </a>
            </li>
        </ul>
    </nav>
    </div>
    <div class="bg-[#0f172a] p-6 w-3/4">
                    <h2 class="text-2xl font-bold mb-4 text-white">Add Expense</h2>
                    <form id="expenseForm" method="POST" action="add-expense.php" >
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
                               
                        <textarea name="description" placeholder="Description" class="w-full p-2 border rounded mb-2"></textarea>
                        <input type="date" name="date" required class="w-fit p-2 border rounded mb-2"><br>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded mt-1">Add Expense</button>
                    </form>
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
                            function showToast(message, bgColor = 'bg-green-600') {
                                const toast = document.getElementById('toast');
                                toast.className = `fixed top-5 right-5 text-white px-4 py-2 rounded shadow-lg z-50 ${bgColor}`;
                                toast.textContent = message;
                                toast.classList.remove('hidden');

                                // Play sound
                                if (bgColor === 'bg-green-600') {
                                    document.getElementById('success-sound').play();
                                } else if (bgColor === 'bg-red-600') {
                                    document.getElementById('error-sound').play();
                                }

                                setTimeout(() => {
                                    toast.classList.add('hidden');
                                }, 3000);
                            }

                            document.getElementById('expenseForm').addEventListener('submit', async function (e) {
                            e.preventDefault();
                            const form = e.target;
                            const formData = new FormData(form);

                            const category = formData.get('category');
                            const customCategory = formData.get('custom_category');
                            if (category === 'Other' && customCategory.trim() !== '') {
                                formData.set('category', customCategory.trim());
                            }

                            const response = await fetch('add-expense.php', {
                                method: 'POST',
                                body: formData
                            });

                            const result = await response.text();

                            if (result.trim() === 'success') {
                                showToast('✅ Expense added successfully!');
                                form.reset();
                                toggleCustomCategory({ value: '' }); // Hide custom input if shown
                            } else {
                                showToast('❌ Failed to add expense.', 'bg-red-600');
                            }
                            });
                    </script>
        </div>
        <!-- Toast Notification -->
        <div id="toast" class="hidden fixed top-5 right-5 text-white px-4 py-2 rounded shadow-lg z-50"></div>
        <!-- Sound Effects -->
        <audio id="success-sound" src="./sounds/ding.wav" preload="auto"></audio>
        <audio id="error-sound" src="./sounds/error.wav" preload="auto"></audio>

    </body>
</html>