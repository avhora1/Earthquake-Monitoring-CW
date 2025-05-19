<?php
require_once '../includes/fpdf/fpdf.php';
include '../connection.php';
// Start session safely if none active
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
// Display errors on the page after form submit
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form fields
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phoneNumber'] ?? '');
    $ccName = trim($_POST['cc-name'] ?? '');
    $ccNumber = preg_replace('/\D/','', $_POST['cc-number'] ?? '');
    $ccExpiration = trim($_POST['cc-expiration'] ?? '');
    $ccCVV = trim($_POST['cc-cvv'] ?? '');

    // Server-side validations

    // First/Last name validation (1-30 characters)
    if (strlen($firstName) < 1 || strlen($firstName) > 30)
        $errors[] = "First name needs to be 1-30 characters.";
    if (strlen($lastName) < 1 || strlen($lastName) > 30)
        $errors[] = "Last name needs to be 1-30 characters.";

    // Basic email validation
    if (!preg_match('/^[^@]+@[^@]+\.[a-z]{2,}$/i', $email))
        $errors[] = "Please enter a valid email address.";

    // UK phone number check
    if (!preg_match('/^(\+44\s?7\d{3}|\(?07\d{3}\)?)\s?\d{3}\s?\d{3}$/', preg_replace('/\s+/', '', $phone)))
        $errors[] = "Please enter a valid UK phone number.";

    // Card type: Visa, MC, or AMEX
    $isVisa = preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $ccNumber);
    $isMC = preg_match('/^5[1-5][0-9]{14}$/', $ccNumber);
    $isAmex = preg_match('/^3[47][0-9]{13}$/', $ccNumber);

    // Luhn algorithm for card validity
    function luhn($number) {
        $sum = 0; $alt = false;
        for ($i = strlen($number)-1; $i >= 0; $i--) {
            $n = intval($number[$i]);
            if ($alt) {
                $n *= 2;
                if ($n > 9) $n -= 9;
            }
            $sum += $n;
            $alt = !$alt;
        }
        return $sum % 10 == 0;
    }
    if (!luhn($ccNumber) || (!$isVisa && !$isMC && !$isAmex))
        $errors[] = "Please enter a valid credit card number.";

    // Expiry date MM/YY or MM/YYYY not expired
    if(!preg_match('/^(0[1-9]|1[0-2])\/?([0-9]{2}|[0-9]{4})$/', $ccExpiration)) {
        $errors[] = "Expiration date must be in MM/YY or MM/YYYY format.";
    } else {
        $parts = explode('/', $ccExpiration);
        $mm = $parts[0];
        $yy = (strlen($parts[1]) == 2) ? ('20'.$parts[1]) : $parts[1];
        $expTime = strtotime("$yy-$mm-01 +1 month"); // end of expiry month
        if ($expTime < time()) {
            $errors[] = "Card expiration date cannot be in the past.";
        }
    }

    // CVV
    if ($isAmex) {
        if (!preg_match('/^\d{4}$/', $ccCVV)) $errors[] = "AMEX CVV must be 4 digits.";
    } else {
        if (!preg_match('/^\d{3}$/', $ccCVV)) $errors[] = "CVV must be 3 digits.";
    }

    // Check basket
    $basket = isset($_SESSION['basket']) ? $_SESSION['basket'] : [];
    if (empty($basket)) {
        $errors[] = "Your basket is empty.";
    }

    // Only proceed if no errors
    if (empty($errors)) {
        $success = true;
        $itemIds = array_map('intval', array_keys($basket));
        $placeholders = implode(',', array_fill(0, count($itemIds), '?'));

        // Update stock_list - set availability = "No"
        $updateSql = "UPDATE stock_list SET availability = 'No' WHERE id IN ($placeholders)";
        $updateStmt = sqlsrv_query($conn, $updateSql, $itemIds);
        if ($updateStmt === false) {
            $errors[] = "Error updating stock: " . print_r(sqlsrv_errors(), true);
        } else {
            // Fetch info for pdf
            $items = [];
            $sql = "SELECT s.id, s.artifact_id, a.type, s.price FROM stock_list s JOIN artefacts a ON s.artifact_id = a.id WHERE s.id IN ($placeholders)";
            $itemResult = sqlsrv_query($conn, $sql, $itemIds);
            while ($row = sqlsrv_fetch_array($itemResult, SQLSRV_FETCH_ASSOC)) {
                $items[] = $row;
            }

            // Prepare order info as before...
            $items = []; $total = 0;
            $rst = sqlsrv_query($conn, $sql, $itemIds);
            while ($row = sqlsrv_fetch_array($rst, SQLSRV_FETCH_ASSOC)) {
                $items[] = $row;
                $total += $row['price'];
            }
            // Optionally free SQL resources
            sqlsrv_free_stmt($rst);
            sqlsrv_close($conn);
            $_SESSION['basket'] = []; // clear the basket

            // --- PDF Generation Section ---
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Times','B',16);
            $pdf->Cell(0,10,'Order Receipt',0,1,'C');
            $pdf->SetFont('Times','',12);
            $pdf->Cell(0,10,"Customer: $firstName $lastName",0,1);
            $pdf->Cell(0,10,"Email: $email",0,1);
            $pdf->Cell(0,10,"Phone: $phone",0,1);
            $pdf->Ln(5);

            $pdf->SetFont('Times','B',12);
            $pdf->Cell(70,10,'Item',1);
            $pdf->Cell(40,10,'ID',1);
            $pdf->Cell(40,10,'Price',1);
            $pdf->Ln();

            $pdf->SetFont('Times','',12);
            foreach ($items as $item) {
                $pdf->Cell(70,10,$item['type'],1);
                $pdf->Cell(40,10,$item['artifact_id'],1);
                $pdf->Cell(40,10,'EUR '.number_format($item['price'],2),1);
                $pdf->Ln();
            }
            $pdf->SetFont('Times','B',12);
            $pdf->Cell(110,10,'Total',1);
            $pdf->Cell(40,10,'EUR '.number_format($total,2),1);
            $pdf->Ln(20);

            $pdf->SetFont('Times','I',10);
            $pdf->Cell(0,10,"Thank you for your order! - The Earthquake Monitoring System",0,1,'C');

            // Output PDF to browser for download:
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="receipt.pdf"');
            $pdf->Output('D', 'receipt.pdf');
            exit;
        }
    }
}