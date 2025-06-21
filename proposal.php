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
