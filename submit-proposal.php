<?php
session_start();
include 'db.php';

// Only 'procurement' and 'admin' roles allowed
if (!isset($_SESSION['user']) || !in_array($_SESSION['role'], ['procurement', 'admin'])) {
  header("Location: login.php");
  exit();
}

$notify_email = "admin@jaxxita.com"; // Set your receiving email
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>📥 Submit Proposal | SmartSurplus Z-825</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron&family=Poppins&display=swap" rel="stylesheet"/>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('https://images.unsplash.com/photo-1606923829579-01c7071c4fbb?auto=format&fit=crop&w=1400&q=80') no-repeat center center fixed;
      background-size: cover;
      margin: 0;
      padding: 30px;
      color: #fff;
    }

    .container {
      max-width: 750px;
      margin: auto;
      background: rgba(0, 0, 0, 0.85);
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 0 20px #00ffff88;
    }

    h1 {
      font-family: 'Orbitron', sans-serif;
      color: #00ffff;
      text-align: center;
      margin-bottom: 25px;
    }

    label {
      font-weight: bold;
      color: #00ffff;
      display: block;
      margin-top: 15px;
    }

    input, textarea {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      margin-top: 6px;
      background: rgba(255,255,255,0.1);
      color: #fff;
      font-size: 1rem;
    }

    textarea {
      resize: vertical;
      min-height: 100px;
    }

    button {
      width: 100%;
      padding: 14px;
      background: #00ffff;
      color: #000;
      font-weight: bold;
      border: none;
      border-radius: 10px;
      margin-top: 20px;
      cursor: pointer;
    }

    button:hover {
      background: #00cccc;
    }

    .back-link {
      display: block;
      margin-top: 20px;
      text-align: center;
      color: #00ffff;
      text-decoration: none;
    }

    .back-link:hover {
      text-decoration: underline;
    }

    .alert {
      margin-top: 20px;
      padding: 12px;
      border-radius: 10px;
      text-align: center;
      font-weight: bold;
    }

    .success { background: #00cc99; color: #fff; }
    .error { background: #ff4444; color: #fff; }

    footer {
      text-align: center;
      margin-top: 40px;
      font-size: 0.85rem;
      color: #ccc;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>📥 Submit New Proposal</h1>
    <form method="POST" enctype="multipart/form-data">
      <label>Project Name</label>
      <input type="text" name="projectName" required />

      <label>Summary</label>
      <textarea name="summary" required></textarea>

      <label>Funding Needed (KES)</label>
      <input type="number" name="funding" required />

      <label>Expected ROI (%)</label>
      <input type="number" name="roi" required />

      <label>Timeline</label>
      <input type="text" name="timeline" required />

      <label>Business Plan (PDF)</label>
      <input type="file" name="plan" accept="application/pdf" />

      <button type="submit">📨 Submit Proposal</button>
    </form>

    <a class="back-link" href="proposal-list.php">📋 View All Proposals</a>

    <footer>© 2025 SmartSurplus Z-825 • Jaxxita Capital • Skylance Z1</footer>
  </div>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $pn = $conn->real_escape_string($_POST['projectName']);
  $sm = $conn->real_escape_string($_POST['summary']);
  $fu = (int) $_POST['funding'];
  $roi= (float) $_POST['roi'];
  $tl = $conn->real_escape_string($_POST['timeline']);
  $pl = "";

  if (!file_exists('uploads')) mkdir('uploads');

  if (!empty($_FILES['plan']['name'])) {
    $pl = "uploads/" . time() . "_" . basename($_FILES['plan']['name']);
    move_uploaded_file($_FILES['plan']['tmp_name'], $pl);
  }

  $sql = "INSERT INTO proposals (projectName, summary, funding, roi, timeline, plan, submitted_at) 
          VALUES ('$pn', '$sm', $fu, $roi, '$tl', '$pl', NOW())";

  if ($conn->query($sql)) {
    // Email
    $to = $notify_email;
    $subject = "📥 New Proposal: $pn";
    $message = "A new proposal was submitted:\n\n"
      . "👤 User: {$_SESSION['user']} ({$_SESSION['role']})\n"
      . "📌 Project: $pn\n"
      . "💰 Funding: KSh $fu\n"
      . "📈 ROI: $roi%\n"
      . "⏳ Timeline: $tl\n"
      . "📝 Summary:\n$sm\n\n"
      . "🌐 View: https://yourdomain.com/proposal-list.php";

    $headers = "From: SmartSurplus Z-825 <no-reply@jaxxita.com>";
    @mail($to, $subject, $message, $headers); // Suppress mail() errors

    echo "<div class='alert success'>✅ Proposal submitted! Notification sent.</div>";
  } else {
    echo "<div class='alert error'>❌ Error: " . $conn->error . "</div>";
  }
}
?>
</body>
</html>
