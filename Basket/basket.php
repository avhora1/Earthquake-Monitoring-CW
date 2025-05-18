<?php
include '../connection.php';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Load Bootstrap CSS -->
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="checkout.css" rel="stylesheet">
    <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body class="bg-body-tertiary">
  <?php include '../header.php';
  $basket = isset($_SESSION['basket']) ? $_SESSION['basket'] : [];?>

  <div class="container">
    <main>
      <div class="py-5 text-center">
        <h1 class="h2">Checkout</h1>
      </div>
      <?php if (isset($_GET['removed']) && $_GET['removed'] == 1): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
  <div id="updateToast" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Artefact removed from basket successfully!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<script>
  // Automatically dismiss after 3 seconds, or user can close manually
  setTimeout(function() {
    var toastEl = document.getElementById('updateToast');
    if (toastEl) {
      var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
      toast.hide();
    }
  }, 3000);
</script>
<?php endif; ?>
      <?php if (empty($basket)): ?>
        <div class="alert alert-dark text-center w-50 mx-auto">Your basket is empty.</div>
        <div class="text-center"><a href="../Shop/shop.php" class="btn btn-primary">Return to Shop</a></div>
    </main>
  </div>
  <script src="../script.js"></script>
</body>

</html>
<?php exit;
      endif; ?>

<div class="row g-5">
  <div class="col-md-5 col-lg-4 order-md-last">
    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span class="text-primary">Your basket</span>
    </h4>
    <ul class="list-group mb-3">
      <?php
      // Fetch artefact data dynamically
      $ids = implode(",", array_map('intval', array_keys($basket)));
      $sql = "SELECT s.id, s.artifact_id, a.type, s.price FROM stock_list s JOIN artefacts a ON s.artifact_id = a.id WHERE s.id IN ($ids)";
      $result = sqlsrv_query($conn, $sql);
      $total = 0;
      $idsForForm = [];
      while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $total += $row['price'];
        $idsForForm[] = $row['id'];
        echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
        echo '<div>';
        echo '  <h6 class="my-0">' . htmlspecialchars($row['type']) . '</h6>';
        echo '  <small class="text-body-secondary">ID #' . htmlspecialchars($row['artifact_id']) . '</small>';
        echo '</div>';
        echo '<div class="d-flex align-items-center">';
        echo '  <span class="text-body-secondary me-2">€' . number_format($row['price'], 2) . '</span>';
        echo '  <a href="remove_from_basket.php?id=' . urlencode($row['id']) . '" class="btn btn-danger btn-sm">Remove</a>';
        echo '</div>';
        echo '</li>';
      }
      ?>
      <li class="list-group-item d-flex justify-content-between">
        <span>Total</span>
        <strong>€<?= number_format($total, 2) ?></strong>
      </li>
    </ul>

  </div>
  <div class="col-md-7 col-lg-8">
    <h4 class="mb-3">Customer information</h4>
    <form class="needs-validation" novalidate>
      <div class="row g-3">
        <div class="col-sm-6">
          <label for="firstName" class="form-label">First name</label>
          <input type="text" class="form-control" id="firstName" placeholder="" value="" required>
          <div class="invalid-feedback">
            Valid first name is required.
          </div>
        </div>

        <div class="col-sm-6">
          <label for="lastName" class="form-label">Last name</label>
          <input type="text" class="form-control" id="lastName" placeholder="" value="" required>
          <div class="invalid-feedback">
            Valid last name is required.
          </div>
        </div>

        <div class="col-12">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
          <div class="invalid-feedback">
            Please enter a valid email address.
          </div>
        </div>

        <div class="col-12">
          <label for="address" class="form-label">Phone number</label>
          <input type="text" class="form-control" id="phoneNumber" placeholder="01234 567890" required>
          <div class="invalid-feedback">
            Please enter your phone number.
          </div>
        </div>

        <hr class="my-4">

        <h4 class="mb-3">Payment</h4>


        <div class="col-md-6">
          <label for="cc-name" class="form-label">Name on card</label>
          <input type="text" class="form-control" id="cc-name" placeholder="" required>
          <small class="text-body-secondary">Full name as displayed on card</small>
          <div class="invalid-feedback">
            Name on card is required
          </div>
        </div>

        <div class="col-md-6">
          <label for="cc-number" class="form-label">Credit card number</label>
          <input type="text" class="form-control" id="cc-number" placeholder="" required>
          <div class="invalid-feedback">
            Card number is required
          </div>
        </div>

        <div class="col-md-3">
          <label for="cc-expiration" class="form-label">Expiration</label>
          <input type="text" class="form-control" id="cc-expiration" placeholder="" required>
          <div class="invalid-feedback">
            Expiration date required
          </div>
        </div>

        <div class="col-md-3">
          <label for="cc-cvv" class="form-label">CVV</label>
          <input type="text" class="form-control" id="cc-cvv" placeholder="" required>
          <div class="invalid-feedback">
            Security code required
          </div>
        </div>
      </div>

      <hr class="my-4">

      <button class="w-100 btn btn-primary btn-lg" type="submit">Pay now</button>
    </form>
  </div>
</div>
</main>

<footer class="my-5 pt-5 text-body-secondary text-center text-small">
  <p class="mb-1">&copy; 2017–2025 The Earthquake Monitoring System</p>
  <ul class="list-inline">
    <li class="list-inline-item"><a href="#">Privacy</a></li>
    <li class="list-inline-item"><a href="#">Terms</a></li>
    <li class="list-inline-item"><a href="#">Support</a></li>
  </ul>
</footer>
</div>
<script defer src="../assets/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"></script>

<script defer src="checkout.js"></script>
</body>

</html>