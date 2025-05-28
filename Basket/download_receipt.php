<?php
require_once '../includes/fpdf/fpdf.php';
include '../connection.php';

$order_id = $_GET['order'] ?? '';

if (!$order_id || !is_numeric($order_id)) {
    die('Invalid order ID.');
}

// Fetch order main info
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = sqlsrv_query($conn, $sql, [intval($order_id)]);
$order = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$order) {
    die('Order not found');
}

// Fetch order items
$item_sql = "SELECT oi.price, s.artifact_id, a.type
               FROM order_items oi
               JOIN stock_list s ON oi.stock_id = s.id
               JOIN artefacts a ON s.artifact_id = a.id
              WHERE oi.order_id = ?";
$items = [];
$item_stmt = sqlsrv_query($conn, $item_sql, [intval($order_id)]);
while ($row = sqlsrv_fetch_array($item_stmt, SQLSRV_FETCH_ASSOC)) {
    $items[] = $row;
}

// --- Generate PDF as before ---
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Times','B',16);
$pdf->Cell(0,10,'Order Receipt',0,1,'C');
$pdf->SetFont('Times','',12);
$pdf->Cell(0,10,"Customer: {$order['first_name']} {$order['last_name']}",0,1);
$pdf->Cell(0,10,"Email: {$order['email']}",0,1);
$pdf->Cell(0,10,"Phone: {$order['phone']}",0,1);
$pdf->Ln(5);

$pdf->SetFont('Times','B',12);
$pdf->Cell(70,10,'Item',1);
$pdf->Cell(40,10,'ID',1);
$pdf->Cell(40,10,'Price',1);
$pdf->Ln();

$pdf->SetFont('Times','',12);
$total = 0;
foreach ($items as $item) {
    $pdf->Cell(70,10,$item['type'],1);
    $pdf->Cell(40,10,$item['artifact_id'],1);
    $pdf->Cell(40,10,'EUR '.number_format($item['price'],2),1);
    $pdf->Ln();
    $total += $item['price'];
}
$pdf->SetFont('Times','B',12);
$pdf->Cell(110,10,'Total',1);
$pdf->Cell(40,10,'EUR '.number_format($total,2),1);
$pdf->Ln(20);

$pdf->SetFont('Times','I',10);
$pdf->Cell(0,10,"Thank you for your order! - The Earthquake Monitoring System",0,1,'C');

// Output PDF
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="receipt_'.$order_id.'.pdf"');
$pdf->Output('D', 'receipt_'.$order_id.'.pdf');
exit;