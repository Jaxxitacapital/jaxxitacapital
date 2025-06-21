<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'systems' || $_SESSION['user'] !== 'ian') {
  header("Location: login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Tools – Ian | Z-825</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 30px;
      background: #0b0c1c;
      color: #fff;
    }

    h1 {
      color: #00ffff;
      text-align: center;
      margin-bottom: 40px;
    }

    .tool {
      background: #111;
      padding: 20px;
      border-left: 5px solid #00ffff;
      margin: 20px auto;
      max-width: 700px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 255, 255, 0.3);
    }

    h3 {
      margin-top: 0;
    }

    a.btn {
      display: inline-block;
      background: #00ffff;
      color: #000;
      padding: 10px 18px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      margin-top: 10px;
    }

    a.btn:hover {
      background: #00cccc;
    }

    footer {
      text-align: center;
      margin-top: 50px;
      color: #888;
      font-size: 0.85em;
    }
  </style>
</head>
<body>
  <h1>🛠️ Edit Tools & Templates</h1>

  <div class="tool">
    <h3>📁 Budget Template (XLSX)</h3>
    <p>Edit this file and re-upload to systems storage.</p>
    <a class="btn" href="templates/budget-template.xlsx" download>⬇️ Download Budget Template</a>
  </div>

  <div class="tool">
    <h3>📄 Financial Tracker</h3>
    <p>Maintain daily income & expense tracking.</p>
    <a class="btn" href="templates/financial-tracker.xlsx" download>⬇️ Download Financial Tracker</a>
  </div>

  <footer>
    SmartSurplus Z-825 | Jaxxita Capital © <?= date('Y') ?>
  </footer>
</body>
</html>
