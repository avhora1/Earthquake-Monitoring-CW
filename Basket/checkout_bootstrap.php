<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';

$basket = isset($_SESSION['basket']) ? $_SESSION['basket'] : [];
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="checkout.css">
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }
      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
      .checkout-label {font-weight: 500;}
    </style>
</head>
<body class="bg-body-tertiary">
<?php include '../header.php';?>

<div class="container">
  <main>
    <div class="py-5 text-center">
      <h1 class="h2">Checkout</h1>
    </div>

    <?php if (empty($basket)): ?>
      <div class="alert alert-info text-center w-50 mx-auto">Your basket is empty.</div>
      <div class="text-center"><a href="shop.php" class="btn btn-primary">Return to Shop</a></div>
      </main></div>
      <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
      <script src="../script.js"></script>
      </body></html>
      <?php exit; endif; ?>

    <div class="row g-5">
      <div class="col-md-5 col-lg-4 order-md-last">
        <h4 class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-primary">Your basket</span>
          <?php $count = count($basket); ?>
          <span class="badge bg-primary rounded-pill"><?= $count ?></span>
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
              echo '<li class="list-group-item d-flex justify-content-between lh-sm">';
              echo '<div><h6 class="my-0">'.htmlspecialchars($row['type']).'</h6>';
              echo '<small class="text-body-secondary">ID #'.htmlspecialchars($row['artifact_id']).'</small></div>';
              echo '<span class="text-body-secondary">€'.number_format($row['price'],2)."</span>";
              echo '</li>';
          }
          ?>
          <li class="list-group-item d-flex justify-content-between">
            <span>Total</span>
            <strong>€<?= number_format($total,2) ?></strong>
          </li>
        </ul>
        <div class="mb-2"><a href="basket.php" class="btn btn-outline-secondary btn-sm">Edit Basket</a></div>
      </div>

      <div class="col-md-7 col-lg-8">
        <h4 class="mb-3">Customer information</h4>
        <form class="needs-validation" method="post" action="process_checkout.php" novalidate>
          <?php foreach ($idsForForm as $bid): ?>
            <input type="hidden" name="basket_ids[]" value="<?= htmlspecialchars($bid) ?>">
          <?php endforeach; ?>

          <div class="row g-3">
            <div class="col-12">
              <label for="cust_name" class="form-label checkout-label">Full Name</label>
              <input type="text" class="form-control" id="cust_name" name="cust_name" required>
              <div class="invalid-feedback">
                Please enter your full name.
              </div>
            </div>

            <div class="col-12">
              <label for="email" class="form-label checkout-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
              <div class="invalid-feedback">
                Please enter a valid email address.
              </div>
            </div>

            <div class="col-12">
              <label for="address" class="form-label checkout-label">Postage Address</label>
              <textarea class="form-control" id="address" name="address" required></textarea>
              <div class="invalid-feedback">
                Please enter your postage address.
              </div>
            </div>

            <div class="col-12">
              <label for="phoneNumber" class="form-label checkout-label">Phone number <span class="text-muted">(optional)</span></label>
              <input type="text" class="form-control" id="phoneNumber" name="phone">
            </div>
          </div>

          <hr class="my-4">

          <button class="w-100 btn btn-success btn-lg" type="submit">Complete Purchase</button>
        </form>
      </div>
    </div>
  </main>

  <footer class="my-5 pt-5 text-body-secondary text-center text-small">
    <p class="mb-1">&copy; 2024 Your Project Name</p>
    <ul class="list-inline">
      <li class="list-inline-item"><a href="#">Privacy</a></li>
      <li class="list-inline-item"><a href="#">Terms</a></li>
      <li class="list-inline-item"><a href="#">Support</a></li>
    </ul>
  </footer>
</div>
<script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Bootstrap form validation
  (() => {
    'use strict'
    let forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false)
    })
  })();
</script>
<script src="../script.js"></script>
</body>
</html>