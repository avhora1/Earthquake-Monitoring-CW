<?php
// Start session safely if none active
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
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
        <li class="nav-item dropdown">
          <a class="nav-link px-2 text-white dropdown-toggle" id="observatoryDropdown" role="button">Observatory</a>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="/Observatories/add_observatory.php">Add Observatory</a></li>
            <li><a class="dropdown-item" href="/Observatories/view_observatories.php">View Observatories</a></li>
            <li><a class="dropdown-item" href="/Observatories/manage_observatories.php">Manage Observatories</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link px-2 text-white dropdown-toggle" id="earthquakeDropdown" role="button">Earthquake</a>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="/Earthquake/add_earthquake.php">Add Earthquake</a></li>
            <li><a class="dropdown-item" href="/Earthquake/view_earthquakes.php">View Earthquakes</a></li>
            <li><a class="dropdown-item" href="/Earthquake/manage_earthquakes.php">Manage Earthquakes</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link px-2 text-white dropdown-toggle" id="artefactDropdown" role="button">Artefact</a>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="/Artefact/add_artefact.php">Add Artefact</a></li>
            <li><a class="dropdown-item" href="/Artefact/view_artefacts.php">View Artefacts</a></li>
            <li><a class="dropdown-item" href="/Artefact/manage_artefacts.php">Manage Artefacts</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link px-2 text-white dropdown-toggle" id="palletDropdown" role="button">Pallet</a>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="/Pallet/add_pallet.php">Add Pallet</a></li>
            <li><a class="dropdown-item" href="/Pallet/view_pallets.php">View Pallets</a></li>
            <li><a class="dropdown-item" href="/Pallet/manage_pallets.php">Manage Pallets</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link px-2 text-white dropdown-toggle" id="ShopDropdown" role="button">Shop</a>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="/Shop/shop.php">View shop</a></li>
            <li><a class="dropdown-item" href="/Shop/add_to_shop.php">Add to shop</a></li>
            <li><a class="dropdown-item" href="/Shop/remove_from_shop.php">Remove from shop</a></li>
          </ul>
        </li>
      </ul>

      <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
        <input type="search" class="form-control form-control-dark text-bg-light" placeholder="Search..." aria-label="Search">
      </form>

      <!-- Login/Logout/Register Buttons -->
      <div class="text-end">
        <?php if (isset($_SESSION['account_loggedin']) && $_SESSION['account_loggedin']): ?>
          <span class="me-2 text-warning">
            <i class="bi bi-person-circle"></i>
            <?= htmlspecialchars($_SESSION['account_name']) ?>
          </span>
          <a href="/Sign-in/logout.php" class="btn btn-warning">Logout</a>
        <?php else: ?>
          <a href="/Sign-in/signin.php" class="btn btn-outline-light me-2">Sign-in</a>
          <a href="/Sign-in/register.php" class="btn btn-warning">Sign-up</a>
        <?php endif; ?>
      </div>

      <!-- Basket icon functionality -->
      </a>
    </div>
  </div>
</header>