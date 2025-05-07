<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

$user = $_SESSION['user'];

$stmt = $pdo->prepare("SELECT SUM(amount) as total_expenses FROM expenses WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user['id']]);
$total_expenses = $stmt->fetch()['total_expenses'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>etrackr - Dashboard</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    .jakarta-font {
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
  </style>
  <link rel="icon" href="./money-bill-trend-up-solid (1).svg">
</head>
<body class="bg-[#0f172a] min-h-screen flex jakarta-font">
  <div class="w-1/4 border-r bg-[#1c1c1c]">
    <h2 class="text-[#12D861] text-3xl text-center mt-4 font-bold fixed left-28 top-16"><span class="text-[#FF8A22]">e</span>Trackr</h2>
    <nav class="text-white text-2xl ml-28 fixed h-screen flex flex-col justify-between">
        <!-- Top Links -->
        <ul class="space-y-8 mt-60">
            <li><a href="#" class="font-bold text-[#12D861]">Dashboard</a></li>
            <li><a href="addexpense.php">Add expense</a></li>
            <li><a href="transactions.php">Transactions</a></li>
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
  <div class="w-3/4">
    <div class="flex flex-1">
      <main class="flex-1 p-6 ">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          <div class="text-2xl text-white ml-14 font-bold"> Hello, <span><?php echo htmlspecialchars($user['name']); ?> 👋</span></div>
          <div class="bg-gray-300 p-6 rounded-lg shadow w-2/3 ml-20">
            <h2 class="text-gray-700 text-lg font-bold">Total Expenditure</h2>
            <p class="text-2xl font-semibold">$<?php echo number_format($total_expenses, 2); ?></p>
          </div>
        </div>

        <div class="flex flex-col md:flex-row justify-center items-start gap-10">
          <div class="max-w-xl mx-auto mt-10">
            <h2 class="text-xl font-bold mb-4 text-white">Spending Overview</h2>
            <canvas id="expenseChart" width="400" height="400"></canvas>
          </div>
          <div class="max-w-xl mx-auto mt-10">
            <h2 class="text-xl font-bold mb-4 text-white">Expense Categories</h2>
            <canvas id="expensePieChart" width="400" height="200"></canvas>
          </div>
        </div>

      </main>
    </div>
    <!-- Subscription Section -->
<div class=" ml-24 mt-10 mb-20">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-bold text-white">Upcoming Subscriptions</h2>
    <button onclick="openSubModal()" class="bg-[#12D861] text-black font-bold px-4 py-1 rounded hover:bg-[#0fb951] mr-36">+ Manage</button>
  </div>
  <ul id="subscriptionList" class="text-white space-y-2 text-sm">
    <!-- Subscription entries will be loaded here -->
  </ul>
</div>

<!-- Modal -->
<div id="subModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
    <h3 class="text-lg font-bold mb-4">Add Subscription</h3>
    <form id="subForm">
      <input type="text" name="title" placeholder="Subscription Name" class="w-full p-2 mb-2 border border-gray-300 rounded" required>
      <input type="date" name="date" class="w-full p-2 mb-2 border border-gray-300 rounded" required>
      <input type="number" name="amount" placeholder="Amount ($)" class="w-full p-2 mb-4 border border-gray-300 rounded" required>
      <div class="flex justify-end space-x-2">
        <button type="button" onclick="closeSubModal()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-[#12D861] text-black font-bold rounded">Add</button>
      </div>
    </form>
  </div>
</div>

  </div>

  <script>
      fetch('get-chart-data.php')
      .then(res => res.json())
      .then(data => {
        const ctx = document.getElementById('expenseChart').getContext('2d');
        const colors = [
          'rgba(255, 99, 132, 0.6)',   // Red
          'rgba(54, 162, 235, 0.6)',   // Blue
          'rgba(255, 206, 86, 0.6)',   // Yellow
          'rgba(75, 192, 192, 0.6)',   // Teal
          'rgba(153, 102, 255, 0.6)',  // Purple
          'rgba(255, 159, 64, 0.6)',   // Orange
          'rgba(199, 199, 199, 0.6)'   // Gray
        ];
        const borderColors = colors.map(c => c.replace('0.6', '1'));
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: data.labels,
            datasets: [{
              label: 'Monthly Expenses',
              data: data.amounts,
              backgroundColor: colors.slice(0, data.labels.length),
              borderColor: borderColors.slice(0, data.labels.length),
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  color: '#ffffff'
                }
              },
              x: {
                ticks: {
                  color: '#ffffff'
                }
              }
            },
            plugins: {
              legend: {
                labels: {
                  color: '#ffffff'
                }
              }
            }
          }
        });
      });
  </script>

  <script>
function openSubModal() {
  document.getElementById('subModal').classList.remove('hidden');
}
function closeSubModal() {
  document.getElementById('subModal').classList.add('hidden');
}

document.getElementById('subForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch('add-subscription.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(res => {
    if (res.success) {
      closeSubModal();
      loadSubscriptions();
    } else {
      alert('Error adding subscription');
    }
  });
});

function loadSubscriptions() {
  fetch('./get-subscription.php')
    .then(res => res.json())
    .then(data => {
      const list = document.getElementById('subscriptionList');
      list.innerHTML = '';
      data.forEach(sub => {
        list.innerHTML += `<li>🔔 <strong>${sub.title}</strong> on ${sub.date} — $${sub.amount}</li>`;
      });
    });
}

document.addEventListener('DOMContentLoaded', loadSubscriptions);
</script>
<script>
  fetch('get-pie-data.php')
    .then(res => res.json())
    .then(data => {
      const ctx = document.getElementById('expensePieChart').getContext('2d');
      new Chart(ctx, {
        type: 'pie',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'Expense Categories',
            data: data.totals,
            backgroundColor: [
              'rgba(255, 99, 132, 0.6)',
              'rgba(54, 162, 235, 0.6)',
              'rgba(255, 206, 86, 0.6)',
              'rgba(75, 192, 192, 0.6)',
              'rgba(153, 102, 255, 0.6)'
            ],
            borderColor: [
              'rgb(244, 80, 115)',
              'rgba(54, 162, 235, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(75, 192, 192, 1)',
              'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
              labels: {
                color: '#ffffff'
              }
            }
          }
        }
      });
    });
    </script>
</body>
</html>
