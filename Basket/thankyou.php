<?php
include '../session.php';
include '../header.php';
$order_id = $_GET['order'] ?? '';
if (!$order_id) {
    echo "Order ID is missing.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Thank You For Your Order</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5 text-center">
    <div class="alert alert-success">
      <h2>Thank You for Your Order!</h2>
      <p>Your order number is <b><?php echo htmlspecialchars($order_id); ?></b>.</p>
      <p>
        <a href="download_receipt.php?order=<?php echo urlencode($order_id); ?>" class="btn btn-primary">
          Download Your PDF Receipt
        </a>
      </p>
    </div>
  </div>
</body>
</html>