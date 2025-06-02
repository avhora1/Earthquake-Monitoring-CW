<?php
session_start();
if(!isset($_SESSION['receipt'])) {
    echo "No receipt data found.";
    exit;
}
include '../connection.php';

$info = $_SESSION['receipt'];
$ordered_items = $info['ordered_items'] ?? [];

// Get stock_ids for the artefacts in the receipt
$ids = array_map('intval', array_keys($ordered_items));
$artefact_rows = [];
if ($ids) {
    $placeholders = implode(",", $ids);
    $sql = "SELECT s.id, s.artifact_id, s.price, a.type
            FROM stock_list s
            JOIN artefacts a ON s.artifact_id = a.id
            WHERE s.id IN ($placeholders)";
    $result = sqlsrv_query($conn, $sql);
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $artefact_rows[$row['id']] = $row;
    }
}

// Empty the basket and the receipt from session
unset($_SESSION['basket']);
unset($_SESSION['receipt']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Quake Receipt</title>
  <link rel="stylesheet" href="styles.css"> <!-- optional -->
  <style>
    body { font-family: Roboto, Arial, sans-serif; color: #222; background: #f5f6f8; margin: 0; padding: 40px;}
    .receipt-box {
      max-width: 480px; margin: 0 auto; background: #fff; border-radius: 13px; padding:32px; box-shadow:0 0 22px #0002;
    }
    h1 { font-size: 1.7rem; }
    .info {margin-bottom: 22px;}
    .order-table {width:100%; border-collapse: collapse; margin-bottom:28px;}
    .order-table th, .order-table td {border:1px solid #ccc; padding:6px 9px;}
  </style>
</head>
<body>
  <div class="receipt-box">
    <h1>Order Receipt</h1>
    <div class="info">
      <strong>Name:</strong> <?= htmlspecialchars($info['firstName'] . " " . $info['lastName']) ?><br>
      <strong>Email:</strong> <?= htmlspecialchars($info['email']) ?><br>
      <strong>Phone:</strong> <?= htmlspecialchars($info['phone']) ?><br>
      <strong>Date:</strong> <?= date("d/m/Y H:i") ?><br>
    </div>
    <table class="order-table">
      <thead>
        <tr>
        <th>#</th>
        <th>Type</th>
        <th>ID</th>
        <th>Price</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $i=1; $grand_total=0;
        foreach($ordered_items as $id => $qty) :
          if (!isset($artefact_rows[$id])) continue;
          $type = htmlspecialchars($artefact_rows[$id]['type']);
          $artid = htmlspecialchars($artefact_rows[$id]['artifact_id']);
          $price = number_format($artefact_rows[$id]['price'], 2);
          $grand_total += $artefact_rows[$id]['price'];
        ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= $type ?></td>
          <td><?= $artid ?></td>
          <td>€<?= $price ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
          <td colspan="3" style="text-align:right;"><b>TOTAL</b></td>
          <td><b>€<?= number_format($grand_total, 2) ?></b></td>
        </tr>
      </tbody>
    </table>
    <p>Thank you for shopping with Quake!</p>
  </div>
</body>
</html>