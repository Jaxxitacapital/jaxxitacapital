<?php
require 'db.php'; // this uses your provided db.php

// Fetch users
$sql = "SELECT id, name, email, role, approved FROM users";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SmartSurplus Users</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f7f9fc;
      margin: 0;
      padding: 40px;
    }
    h1 {
      text-align: center;
      color: #00aaff;
    }
    table {
      width: 90%;
      margin: auto;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px 20px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    th {
      background-color: #00aaff;
      color: white;
    }
    tr:hover {
      background-color: #f1f1f1;
    }
    footer {
      margin-top: 50px;
      text-align: center;
      color: #888;
    }
  </style>
</head>
<body>

  <h1>🧑‍💻 SmartSurplus – Registered Users</h1>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Approved</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
            <td><?= $row['approved'] == 1 ? '✅ Yes' : ($row['approved'] == -1 ? '❌ Rejected' : '⏳ Pending') ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5">No users found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <footer>
    SmartSurplus Z-825 © <?= date('Y') ?> | Powered by Jaxxita Capital
  </footer>
</body>
</html>
