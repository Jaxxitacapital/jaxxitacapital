<?php
// Include database connection
include 'db.php';

// Determine export format (default = Excel)
$format = $_GET['format'] ?? 'excel';

// Fetch proposals from database
$res = $conn->query("SELECT * FROM proposals ORDER BY submitted_at DESC");

if ($format === "excel") {
  header("Content-Type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=proposals.xls");
  
  // Print column headers
  echo "ID\tProject Name\tFunding (KES)\tExpected ROI (%)\tTimeline\tSubmitted At\n";

  // Loop through and print each row
  while ($r = $res->fetch_assoc()) {
    echo "{$r['id']}\t" .
         "{$r['projectName']}\t" .
         "{$r['funding']}\t" .
         "{$r['roi']}\t" .
         "{$r['timeline']}\t" .
         "{$r['submitted_at']}\n";
  }
}
?>
