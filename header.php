<?php
include 'session.php';
include 'connection.php';
$acct = isset($_SESSION['account_type']) ? $_SESSION['account_type'] : 'guest';
// Always at the top, after session_start(), on every page that uses the basket!
if (isset($_SESSION['basket']) && count($_SESSION['basket']) > 0) {
    $ids = array_keys($_SESSION['basket']);
    $ids_int = array_map('intval', $ids);

    // Fetch only IDs that still exist in stock_list table
    if (!empty($ids_int)) {
        $in = implode(',', $ids_int);
        $sql = "SELECT id FROM stock_list WHERE id IN ($in)";
        $result = sqlsrv_query($conn, $sql);
        $live_ids = [];
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $live_ids[] = $row['id'];
        }
        // Remove any IDs not in $live_ids
        $_SESSION['basket'] = array_intersect_key($_SESSION['basket'], array_flip($live_ids));
    }
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<header class="p-3 text-bg-dark" style="z-index:1030;">
  <style>
    .nav-item.dropdown:hover .dropdown-menu { display: block; margin-top: 0; }
    .nav-link:hover { color: #ffc107; }
  </style>
  <div class="container-fluid">
    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
      <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
        <li><a href="/index.php" class="nav-link px-2 text-secondary">Home</a></li>

        <!-- Observatory: scientist/admin, all options -->
        <?php if (in_array($acct, ['junior_scientist', 'senior_scientist', 'admin'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link px-2 text-white dropdown-toggle"
               id="observatoryDropdown"
               role="button"
               data-bs-toggle="dropdown"
               aria-expanded="false">Observatory</a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li><a class="dropdown-item" href="/Observatories/add_observatory.php">Add Observatory</a></li>
              <li><a class="dropdown-item" href="/Observatories/view_observatories.php">View Observatories</a></li>
              <li><a class="dropdown-item" href="/Observatories/manage_observatories.php">Manage Observatories</a></li>
            </ul>
          </li>
        <?php endif; ?>

        <!-- Earthquake: scientist/admin, all options -->
        <?php if (in_array($acct, ['junior_scientist', 'senior_scientist', 'admin'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link px-2 text-white dropdown-toggle"
               id="earthquakeDropdown"
               role="button"
               data-bs-toggle="dropdown"
               aria-expanded="false">Earthquake</a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li><a class="dropdown-item" href="/Earthquake/add_earthquake.php">Add Earthquake</a></li>
              <li><a class="dropdown-item" href="/Earthquake/view_earthquakes.php">View Earthquakes</a></li>
              <li><a class="dropdown-item" href="/Earthquake/manage_earthquakes.php">Manage Earthquakes</a></li>
            </ul>
          </li>
        <?php endif; ?>

        <!-- Artefact: junior=view only, senior/admin=all options -->
        <?php if (in_array($acct, ['junior_scientist', 'senior_scientist', 'admin'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link px-2 text-white dropdown-toggle"
               id="artefactDropdown"
               role="button"
               data-bs-toggle="dropdown"
               aria-expanded="false">Artefact</a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <?php if (in_array($acct, ['senior_scientist', 'admin'])): ?>
                <li><a class="dropdown-item" href="/Artefact/add_artefact.php">Add Artefact</a></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="/Artefact/view_artefacts.php">View Artefacts</a></li>
              <?php if (in_array($acct, ['senior_scientist', 'admin'])): ?>
                <li><a class="dropdown-item" href="/Artefact/manage_artefacts.php">Manage Artefacts</a></li>
              <?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

        <!-- Pallet: junior=view only, senior/admin=all options -->
        <?php if (in_array($acct, ['junior_scientist', 'senior_scientist', 'admin'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link px-2 text-white dropdown-toggle"
               id="palletDropdown"
               role="button"
               data-bs-toggle="dropdown"
               aria-expanded="false">Pallet</a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <?php if (in_array($acct, ['senior_scientist', 'admin'])): ?>
                <li><a class="dropdown-item" href="/Pallet/add_pallet.php">Add Pallet</a></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="/Pallet/view_pallets.php">View Pallets</a></li>
              <?php if (in_array($acct, ['senior_scientist', 'admin'])): ?>
                <li><a class="dropdown-item" href="/Pallet/manage_pallets.php">Manage Pallets</a></li>
              <?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

        <!-- Shop: All see dropdown, but only senior/admin see Add/Remove -->
        <?php if (in_array($acct, ['senior_scientist', 'admin'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link px-2 text-white dropdown-toggle"
               id="ShopDropdown"
               role="button"
               data-bs-toggle="dropdown"
               aria-expanded="false">Shop</a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li><a class="dropdown-item" href="/Shop/shop.php">View shop</a></li>
              <li><a class="dropdown-item" href="/Shop/add_to_shop.php">Add to shop</a></li>
              <li><a class="dropdown-item" href="/Shop/remove_from_shop.php">Remove from shop</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item dropdown">
            <a class="nav-link px-2 text-white dropdown-toggle"
               id="ShopDropdown"
               role="button"
               data-bs-toggle="dropdown"
               aria-expanded="false">Shop</a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li><a class="dropdown-item" href="/Shop/shop.php">View shop</a></li>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
      <div class="text-end">
        <?php if (isset($_SESSION['account_loggedin']) && $_SESSION['account_loggedin']): ?>
          <a href="/Account_Managment/profile.php"
             class="btn btn-link text-warning fw-bold me-2 p-0 align-baseline"
             style="text-decoration:none;">
            <i class="bi bi-person-circle"></i>
            <?= htmlspecialchars($_SESSION['account_name']) ?>
          </a>
          <a href="/Sign-in/logout.php" class="btn btn-warning">Logout</a>
        <?php else: ?>
          <a href="/Sign-in/signin.php" class="btn btn-outline-light me-2">Sign-in</a>
          <a href="/Sign-in/register.php" class="btn btn-warning">Sign-up</a>
        <?php endif; ?>
      </div>
      <?php $basket_count = isset($_SESSION['basket']) ? count($_SESSION['basket']) : 0; ?>
      <a href="/Basket/basket.php"
         class="text-decoration-none text-light ms-3 position-relative"
         style="font-size:1.7rem;">
         <i class="bi bi-bag-check"></i>
         <?php if ($basket_count > 0) : ?>
           <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary fs-6"
                 style="z-index:1;">
             <?= $basket_count ?>
             <span class="visually-hidden">basket items</span>
           </span>
         <?php endif; ?>
      </a>
    </div>
  </div>
</header>