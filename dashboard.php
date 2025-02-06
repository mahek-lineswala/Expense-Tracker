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
    <title>Expense Tracker - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <nav class="bg-blue-600 text-white p-4 flex justify-between items-center">
        <span class="text-xl font-bold">Expense Tracker</span>
        <div class="flex items-center space-x-4">
            <span><?php echo htmlspecialchars($user['name']); ?></span>
            <img src="<?php echo !empty($user['picture']) ? htmlspecialchars($user['picture']) : 'default-profile.png'; ?>" alt="Profile" class="w-10 h-10 rounded-full">
            <a href="logout.php" class="bg-red-500 px-3 py-1 rounded text-white hover:bg-red-600">Logout</a>
        </div>
    </nav>

    <div class="flex flex-1">
        <main class="flex-1 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-gray-700 text-lg font-bold">Total Balance</h2>
                    <p class="text-2xl font-semibold">$<?php echo number_format($total_expenses, 2); ?></p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <h2 class="text-xl font-bold mb-4">Add Expense</h2>
                <form method="POST" action="add-expense.php">
                    <input type="number" name="amount" placeholder="Amount" required class="w-full p-2 border rounded mb-2">
                    <input type="text" name="category" placeholder="Category" required class="w-full p-2 border rounded mb-2">
                    <textarea name="description" placeholder="Description" class="w-full p-2 border rounded mb-2"></textarea>
                    <input type="date" name="date" required class="w-full p-2 border rounded mb-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add Expense</button>
                </form>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">Recent Transactions</h2>
                <ul>
                    <?php if (empty($expenses)): ?>
                        <li class="p-2 border-b">No transactions yet</li>
                    <?php else: ?>
                        <?php foreach ($expenses as $expense): ?>
                            <li class="p-2 border-b">
                                <?php echo htmlspecialchars($expense['category']); ?> - $<?php echo htmlspecialchars($expense['amount']); ?> 
                                (<?php echo htmlspecialchars($expense['date']); ?>)
                                <p><?php echo htmlspecialchars($expense['description']); ?></p>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>
