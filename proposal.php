<?php
session_start();
if (!isset($_SESSION['name'])) {
  header("Location: login.php");
  exit();
}

$success = "";
$error = "";

// DB Connection
$conn = new mysqli("localhost", "root", "", "smartsurplus_db");
if ($conn->connect_error) {
  die("❌ DB Connection failed: " . $conn->connect_error);
}

// Form handling
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $projectName = htmlspecialchars(trim($_POST['projectName']));
  $summary     = htmlspecialchars(trim($_POST['summary']));
  $funding     = floatval($_POST['funding']);
  $roi         = floatval($_POST['roi']);
  $timeline    = htmlspecialchars(trim($_POST['timeline']));
  $submittedBy = $_SESSION['name'];
  $uploadPath  = "";

  // File upload validation
  if (isset($_FILES['plan']) && $_FILES['plan']['error'] === 0) {
    $allowedTypes = ['application/pdf'];
    $fileType = mime_content_type($_FILES['plan']['tmp_name']);
    $maxSize = 5 * 1024 * 1024; // 5MB max

    if (!in_array($fileType, $allowedTypes)) {
      $error = "❌ Only PDF files are allowed.";
    } elseif ($_FILES['plan']['size'] > $maxSize) {
      $error = "❌ File is too large. Max 5MB allowed.";
    } else {
      $safeName = time() . "_" . preg_replace("/[^a-zA-Z0-9.]+/", "_", basename($_FILES['plan']['name']));
      $uploadPath = "uploads/" . $safeName;
      move_uploaded_file($_FILES['plan']['tmp_name'], $uploadPath);
    }
  }

  if (!$error) {
    $stmt = $conn->prepare("INSERT INTO proposals (project_name, summary, funding, roi, timeline, business_plan, submitted_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddsss", $projectName, $summary, $funding, $roi, $timeline, $uploadPath, $submittedBy);
    
    if ($stmt->execute()) {
      $success = "✅ Proposal submitted successfully!";
    } else {
      $error = "❌ Submission failed. Please try again.";
    }
    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Proposal | SmartSurplus Z-825</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #141e30, #243b55);
      color: white;
      padding: 30px;
    }

    header {
      background: #00ffff;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-radius: 6px;
      margin-bottom: 30px;
    }

    header h2 {
      color: #000;
      margin: 0;
    }

    nav a {
      text-decoration: none;
      color: #000;
      font-weight: bold;
      margin: 0 10px;
      background: white;
      padding: 6px 12px;
      border-radius: 6px;
      transition: 0.3s;
    }

    nav a:hover {
      background: #ccc;
    }

    form {
      background: rgba(255, 255, 255, 0.1);
      padding: 25px;
      border-radius: 10px;
      max-width: 600px;
      margin: auto;
      box-shadow: 0 0 10px rgba(0, 255, 255, 0.4);
    }

    input, textarea {
      width: 100%;
      padding: 10px;
      margin: 12px 0;
      border: none;
      border-radius: 6px;
    }

    input[type="submit"] {
      background: #00ffff;
      color: #000;
      font-weight: bold;
      cursor: pointer;
    }

    .message {
      text-align: center;
      margin: 20px 0;
      font-weight: bold;
      color: #0f0;
    }

    .error {
      color: #f55;
    }
  </style>
</head>
<body>

<header>
  <h2>SmartSurplus Z-825</h2>
  <nav>
    <a href="index.html">🏠 Home</a>
    <a href="dashboard.html">📋 Dashboard</a>
    <a href="report.html">📊 Reports</a>
    <a href="proposal.php">📝 Proposal</a>
    <a href="logout.php" style="background: crimson; color: white;">🚪 Logout</a>
  </nav>
</header>

<h2 style="text-align:center;">📑 Submit Your Project Proposal</h2>

<?php if ($success): ?>
  <div class="message"><?= $success ?></div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="message error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
  <label>Project Name:</label>
  <input type="text" name="projectName" required>

  <label>Summary:</label>
  <textarea name="summary" rows="4" required></textarea>

  <label>Funding Required (KSh):</label>
  <input type="number" name="funding" step="0.01" required>

  <label>Estimated ROI (%):</label>
  <input type="number" name="roi" step="0.1" required>

  <label>Timeline (e.g., 6 months):</label>
  <input type="text" name="timeline" required>

  <label>Upload Business Plan (PDF only):</label>
  <input type="file" name="plan" accept="application/pdf">

  <input type="submit" value="Submit Proposal">
</form>

</body>
</html>
