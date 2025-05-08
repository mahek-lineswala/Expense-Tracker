<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

$user = $_SESSION['user'];

$stmt = $pdo->prepare("SELECT * FROM expenses WHERE user_id = :user_id ORDER BY date DESC ");
$stmt->execute(['user_id' => $user['id']]);
$expenses = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT SUM(amount) as total_expenses FROM expenses WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user['id']]);
$total_expenses = $stmt->fetch()['total_expenses'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>etrackr - Transactions</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
  <link rel="icon" href="./money-bill-trend-up-solid (1).svg">
  <style>
    .jakarta-font {
      font-family: 'Plus Jakarta Sans';
    }
  </style>
</head>
<body class="bg-[#1c1c1c] min-h-screen flex jakarta-font">
  <!-- Sidebar -->
  <div class="w-1/4 border-r ">
  <img src="./eTrackr logo1.png" alt="" class="w-28 ml-28 mt-4 fixed">
    <nav class="text-white text-2xl ml-28 fixed h-screen flex flex-col justify-around">
        <!-- Top Links -->
        <ul class="space-y-8 mt-40">
            <li><a href="dashboard.php" >Dashboard</a></li>
            <li><a href="addexpense.php">Add expense</a></li>
            <li><a href="transactions.php" class="font-bold text-[#12D861]">Transactions</a></li>
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

  <!-- Main Content -->
  <div class="bg-gray-300 p-6  w-3/4">
  <div class="flex justify-between">
    <h2 class="text-2xl font-bold mb-4">Transaction History</h2>
    <a href="export-pdf.php" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block mr-4">
      Download PDF
    </a>
  </div>
    <ul id="expenseList">
      <?php if (empty($expenses)): ?>
        <li class="p-2 border-b">No transactions yet</li>
      <?php else: ?>
        <?php foreach ($expenses as $expense): ?>
          <li class="p-4 border-b border-white text-xl flex justify-between items-center" id="expense-<?php echo $expense['id']; ?>">
            <div>
              <strong><?php echo htmlspecialchars($expense['category']); ?></strong> - ₹<?php echo htmlspecialchars($expense['amount']); ?>
              <span class="text-sm text-gray-700">(<?php echo htmlspecialchars($expense['date']); ?>)</span>
              <p class="text-base text-gray-800"><?php echo htmlspecialchars($expense['description']); ?></p>
            </div>
            <button onclick="deleteExpense(<?php echo $expense['id']; ?>)" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Delete</button>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </div>

  <!-- Toast Notification -->
  <div id="toast" class="hidden fixed top-5 right-5 text-white px-4 py-2 rounded shadow-lg z-50"></div>

  <!-- Script -->
  <script>
  function showToast(message, bgColor = 'bg-green-600', isSuccess = true) {
    const toast = document.getElementById('toast');
    const sound = isSuccess ? document.getElementById('success-sound') : document.getElementById('error-sound');

    toast.className = `fixed top-5 right-5 text-white px-4 py-2 rounded shadow-lg z-50 ${bgColor} transition-all transform scale-100 opacity-100`;
    toast.textContent = message;
    toast.classList.remove('hidden');
    sound.play();

    // Animate toast out
    setTimeout(() => {
      toast.classList.add('opacity-0', 'scale-90');
      setTimeout(() => {
        toast.classList.add('hidden');
        toast.classList.remove('opacity-0', 'scale-90');
      }, 500);
    }, 2500);
  }

  async function deleteExpense(id) {
    const confirmed = confirm("Delete this expense?");
    if (!confirmed) return;

    const formData = new FormData();
    formData.append('id', id);

    try {
      const res = await fetch('delete-expense.php', {
        method: 'POST',
        body: formData
      });

      const result = await res.text();

      if (result.trim() === 'success') {
        document.getElementById('expense-' + id).remove();
        showToast('✅ Expense deleted!', 'bg-green-600', true);
      } else {
        showToast('❌ Failed to delete.', 'bg-red-600', false);
      }
    } catch (err) {
      showToast('⚠️ Server error.', 'bg-yellow-600', false);
    }
  }
</script>
<!-- Toast Notification -->
<div id="toast" class="hidden fixed top-5 right-5 text-white px-4 py-2 rounded shadow-lg z-50 transition-all transform opacity-0 scale-90"></div>

<!-- Sound Effects -->
<audio id="success-sound" src="./sounds/success.wav" preload="auto"></audio>
<audio id="error-sound" src="./sounds/error.wav" preload="auto"></audio>


</body>
</html>
