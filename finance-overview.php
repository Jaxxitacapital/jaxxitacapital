<?php
session_start();
require 'db.php';

// ✅ Only allow executive and admin roles
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['executive', 'admin'])) {
  header("Location: login.php");
  exit();
}

// ✅ Fetch contributions
$contribRes = $conn->query("SELECT name, amount, penalty, date FROM contributions ORDER BY date DESC");

// ✅ Fetch investment records
$investRes = $conn->query("SELECT type, name, amount, growth, date FROM investments ORDER BY date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Finance Overview | SmartSurplus Z-825</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #0a0a0a;
      color: #fff;
      padding: 40px 20px;
    }
    h1 {
      text-align: center;
      color: #00ffff;
      margin-bottom: 30px;
    }
    h2 {
      color: #00ffcc;
      margin-top: 50px;
      border-bottom: 2px solid #00ffff66;
      padding-bottom: 8px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: #111;
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #00ffff33;
    }
    th {
      background: #00ffff;
      color: #000;
      font-weight: bold;
    }
    tr:hover {
      background: rgba(255, 255, 255, 0.05);
    }
    a.back {
      display: inline-block;
      margin: 40px auto 0;
      padding: 12px 24px;
      background: #00ffff;
      color: #000;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      text-align: center;
    }
    .footer {
      text-align: center;
      margin-top: 60px;
      font-size: 0.85em;
      color: #aaa;
    }
  </style>
</head>
<body>

  <h1>💼 SmartSurplus Z-825 Finance Overview</h1>

  <h2>💰 Member Contributions</h2>
  <table>
    <tr><th>Name</th><th>Amount (KSh)</th><th>Penalty</th><th>Date</th></tr>
    <?php while($row = $contribRes->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= number_format($row['amount']) ?></td>
        <td><?= number_format($row['penalty']) ?></td>
        <td><?= date('d M Y', strtotime($row['date'])) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>

  <h2>📊 Investment Tracker</h2>
  <table>
    <tr><th>Type</th><th>Institution</th><th>Amount (KSh)</th><th>Growth (%)</th><th>Date</th></tr>
    <?php while($row = $investRes->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['type']) ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= number_format($row['amount']) ?></td>
        <td><?= htmlspecialchars($row['growth']) ?></td>
        <td><?= date('d M Y', strtotime($row['date'])) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>

  <div style="text-align:center;">
    <a href="executive-dashboard.php" class="back">← Back to Dashboard</a>
  </div>

  <div class="footer">
    © 2025 Jaxxita Capital • SmartSurplus Z-825 • Skylance Z1
  </div>
</body>
</html>
