<?php
include 'db.php';
session_start();

if (!isset($_GET['id'])) die("Missing ID");
$id = (int) $_GET['id'];

// Fetch the record
$data = $conn->query("SELECT * FROM proposals WHERE id=$id")->fetch_assoc();
if (!$data) die("❌ Proposal Not Found");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $pn  = $conn->real_escape_string(trim($_POST['projectName']));
  $sm  = $conn->real_escape_string(trim($_POST['summary']));
  $fu  = floatval($_POST['funding']);
  $roi = floatval($_POST['roi']);
  $tl  = $conn->real_escape_string(trim($_POST['timeline']));
  $updPlan = "";

  if (isset($_FILES['plan']) && $_FILES['plan']['error'] === 0) {
    $mime = mime_content_type($_FILES['plan']['tmp_name']);
    if ($mime !== 'application/pdf') {
      echo "<script>alert('❌ Only PDF files allowed');</script>";
    } else {
      $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9.]+/", "_", basename($_FILES['plan']['name']));
      $uploadPath = "uploads/" . $filename;
      move_uploaded_file($_FILES['plan']['tmp_name'], $uploadPath);
      $updPlan = ", business_plan='$uploadPath'";
    }
  }

  $conn->query("UPDATE proposals SET project_name='$pn', summary='$sm', funding=$fu, roi=$roi, timeline='$tl' $updPlan WHERE id=$id");
  echo "<script>alert('✅ Proposal Updated'); window.location='proposal-list.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Proposal – Z-825</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Orbitron&family=Poppins&display=swap" rel="stylesheet" />
  <style>
    body {
      background: #0e0e0e url('https://images.unsplash.com/photo-1523961131990-5ea7c61b2107?auto=format&fit=crop&w=1350&q=80') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Poppins', sans-serif;
      color: #fff;
      padding: 40px;
    }
    .box {
      background: rgba(0,0,0,0.8);
      padding: 30px;
      border-radius: 20px;
      max-width: 650px;
      margin: auto;
      box-shadow: 0 0 20px #00ffff44;
    }
    h1 {
      font-family: 'Orbitron', sans-serif;
      color: #00ffff;
      text-align: center;
      margin-bottom: 25px;
    }
    label {
      font-weight: bold;
      display: block;
      margin-top: 20px;
      color: #ccc;
    }
    input, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 8px;
      background: rgba(255,255,255,0.1);
      border: none;
      border-radius: 10px;
      color: #fff;
    }
    textarea {
      resize: vertical;
      height: 100px;
    }
    button {
      margin-top: 25px;
      background: #00ffff;
      color: #000;
      font-weight: bold;
      padding: 12px 20px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: 0.3s ease;
    }
    button:hover {
      background: #00cccc;
    }
    a.back {
      display: inline-block;
      margin-top: 20px;
      color: #aaa;
      text-decoration: none;
      font-size: 0.9rem;
    }
    a.back:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="box">
    <h1>📝 Edit Proposal</h1>
    <form method="POST" enctype="multipart/form-data">
      <label for="projectName">Project Name:</label>
      <input name="projectName" required value="<?= htmlspecialchars($data['project_name']) ?>" />

      <label for="summary">Summary:</label>
      <textarea name="summary" required><?= htmlspecialchars($data['summary']) ?></textarea>

      <label for="funding">Funding (KES):</label>
      <input type="number" step="0.01" name="funding" required value="<?= $data['funding'] ?>" />

      <label for="roi">ROI (%):</label>
      <input type="number" step="0.01" name="roi" required value="<?= $data['roi'] ?>" />

      <label for="timeline">Timeline:</label>
      <input name="timeline" required value="<?= htmlspecialchars($data['timeline']) ?>" />

      <label for="plan">Replace Business Plan (PDF only):</label>
      <input type="file" name="plan" accept="application/pdf" />

      <button type="submit">💾 Update Proposal</button>
    </form>
    <a href="proposal-list.php" class="back">← Back to Proposals</a>
  </div>
</body>
</html>
