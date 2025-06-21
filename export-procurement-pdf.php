<?php
require_once('tcpdf/tcpdf.php');

// ✅ Connect to database
$conn = new mysqli("localhost", "root", "", "smartsurplus_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM procurement ORDER BY date DESC");

// ✅ Start a new PDF document
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 11);

// ✅ Title
$pdf->SetFont('', 'B', 16);
$pdf->Cell(0, 10, 'SmartSurplus Z-825 – Procurement Report', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('', '', 11);

// ✅ Table Content
$html = '
<table border="1" cellpadding="5">
  <thead>
    <tr style="background-color:#e0f7fa;">
      <th><b>#</b></th>
      <th><b>Item</b></th>
      <th><b>Amount (KES)</b></th>
      <th><b>Date</b></th>
      <th><b>Description</b></th>
    </tr>
  </thead>
  <tbody>
';

$i = 1;
$total = 0;
while ($row = $result->fetch_assoc()) {
    $html .= '<tr>
      <td>' . $i++ . '</td>
      <td>' . htmlspecialchars($row['item']) . '</td>
      <td>' . number_format($row['amount']) . '</td>
      <td>' . htmlspecialchars($row['date']) . '</td>
      <td>' . htmlspecialchars($row['description']) . '</td>
    </tr>';
    $total += $row['amount'];
}

$html .= '
    <tr>
      <td colspan="2"><strong>Total Spent</strong></td>
      <td colspan="3"><strong>KSh ' . number_format($total) . '</strong></td>
    </tr>
  </tbody>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln(10);

// ✅ Footer
$pdf->SetFont('', 'I', 9);
$pdf->Cell(0, 10, 'Prepared by Procurement Lead – David | © 2025 Jaxxita Enterprises', 0, 1, 'C');

// ✅ Output the PDF to browser
$pdf->Output('procurement_report.pdf', 'I');
?>
