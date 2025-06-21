<?php
include 'db.php';
session_start();

// Secure deletion
if (isset($_GET['del'])) {
  $id = (int) $_GET['del'];
  $conn->query("DELETE FROM proposals WHERE id=$id");
  header("Location: proposal-list.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>📋 Proposals List | SmartSurplus Z-825</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron&family=Poppins&display=swap" rel="stylesheet"/>
  <style>
    body {
      background: #0e0e0e url('https://images.unsplash.com/photo-1603570419984-31e9bd2c9bb0?auto=format&fit=crop&w=1400&q=80') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Poppins', sans-serif;
      color: #fff;
      padding: 40px;
    }

    h1 {
      font-family: 'Orbitron', sans-serif;
      color: #00ffff;
      text-align: center;
      margin-bottom: 20px;
    }

    .top-bar {
      text-align: center;
      margin-bottom: 30px;
    }

    .btn {
      background: #00ffff;
      color: #000;
      padding: 10px 20px;
      margin: 10px 10px;
      border: none;
      font-weight: bold;
      border-radius: 10px;
      text-decoration: none;
      display: inline-block;
      transition: 0.3s;
    }

    .btn:hover {
      background: #00cccc;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(6px);
      margin-top: 10px;
      border-radius: 10px;
      overflow: hidden;
    }

    th, td {
      padding: 14px;
      text-align: left;
      border-bottom: 1px solid #444;
    }

    th {
      background: #00ffff;
      color: #000;
      font-weight: bold;
    }

    tr:hover {
      background: rgba(255, 255, 255, 0.05);
    }

    .actions a {
      margin-right: 10px;
      color: #00ffff;
      font-size: 1.2em;
      text-decoration: none;
    }

    .actions a:hover {
      color: #ff4444;
    }

    footer {
      margin-top: 40px;
      text-align: center;
      font-size: 0.85rem;
      color: #aaa;
    }
  </style>
</head>
<body>
  <h1>📋 Submitted Proposals</h1>

  <div class="top-bar">
    <a class="btn" href="proposal-submit.php">➕ New Proposal</a>
    <a class="btn" href="export.php?format=excel">📤 Export to Excel</a>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Project</th>
        <th>Funding</th>
        <th>ROI</th>
        <th>Timeline</th>
        <th>Submitted</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $res = $conn->query("SELECT * FROM proposals ORDER BY submitted_at DESC");
      if ($res->num_rows > 0) {
        while ($r = $res->fetch_assoc()) {
          echo "<tr>
            <td>{$r['id']}</td>
            <td>" . htmlspecialchars($r['project_name']) . "</td>
            <td>KSh " . number_format($r['funding'], 2) . "</td>
            <td>{$r['roi']}%</td>
            <td>" . htmlspecialchars($r['timeline']) . "</td>
            <td>{$r['submitted_at']}</td>
            <td class='actions'>
              <a href='proposal-edit.php?id={$r['id']}' title='Edit'>✏️</a>
              <a href='?del={$r['id']}' onclick=\"return confirm('Are you sure to delete this proposal?')\" title='Delete'>🗑️</a>
            </td>
          </tr>";
        }
      } else {
        echo "<tr><td colspan='7' style='text-align:center;'>No proposals found.</td></tr>";
      }
      ?>
    </tbody>
  </table>

  <footer>
    © 2025 SmartSurplus Z-825 • Jaxxita Capital • Skylance Z1 • SilentStream B625
  </footer>
</body>
</html>
