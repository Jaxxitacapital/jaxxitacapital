<?php
session_start();

// ✅ Only 'audit' role can proceed
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'audit') {
  header("Location: login.php");
  exit();
}

// ✅ Connect to DB
$conn = new mysqli("localhost", "root", "", "smartsurplus_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// ✅ Sanitize and prepare data
$pid = (int)$_POST['pid'];
$comment = $conn->real_escape_string($_POST['comment']);
$receiptPath = '';

// ✅ Handle file upload
if (isset($_FILES['receipt']) && $_FILES['receipt']['tmp_name']) {
  $uploadDir = 'uploads/';
  if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

  $ext = pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION);
  $filename = 'receipt_' . $pid . '_' . time() . '.' . $ext;
  $receiptPath = $uploadDir . $filename;

  move_uploaded_file($_FILES['receipt']['tmp_name'], $receiptPath);
}

// ✅ Insert audit flag if comment or receipt is present
if ($comment || $receiptPath) {
  $stmt = $conn->prepare("INSERT INTO audit_flags (procurement_id, comment, receipt, flagged_by, flagged_at) VALUES (?, ?, ?, ?, NOW())");
  $stmt->bind_param("isss", $pid, $comment, $receiptPath, $_SESSION['user']);
  $stmt->execute();
  $stmt->close();

  // ✅ Send email alert
  $to = 'jaxxitacapital@gmail.com';
  $subject = "🛡️ Audit Flag Added by Dennis";
  $msg = "Procurement Record #$pid has been flagged.\n\nComment: $comment\nReceipt: " . ($receiptPath ?: 'No file uploaded') . "\nTime: " . date('Y-m-d H:i:s');

  @mail($to, $subject, $msg);
}

// ✅ Redirect back
header("Location: audit-dashboard.php");
exit();
?>
