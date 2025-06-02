<?php
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
        // Store order info for receipt
        $_SESSION['receipt'] = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'ordered_items' => $basket,
        ];
        // Update stock_list - set availability = "No"
        $updateSql = "UPDATE stock_list SET availability = 'No' WHERE id IN ($placeholders)";
        $updateStmt = sqlsrv_query($conn, $updateSql, $itemIds);
        if ($updateStmt === false) {
            $errors[] = "Error updating stock: " . print_r(sqlsrv_errors(), true);
        } else {header("Location: ../Shop/thankYou.php");
            exit;
        }
    }
}