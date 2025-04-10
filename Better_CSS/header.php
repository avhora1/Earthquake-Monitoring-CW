<header class="p-3 text-bg-dark">
  <style>
    /* Dropdown menu appears automatically on hover */
    .nav-item.dropdown:hover .dropdown-menu {
      display: block; /* Show dropdown on hover */
      margin-top: 0; /* Align dropdown menu directly below the link */
    }

    /* Add hover effect for all nav links */
    .nav-link:hover {
      color: #ffc107; /* Yellow color for hover effect */
    }
  </style>
  
  <div class="container">
    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
      <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
        <!-- Home Link -->
        <li><a href="../../Better_CSS/index.php" class="nav-link px-2 text-secondary">Home</a></li>

        <!-- Dropdown for Observatory -->
        <li class="nav-item dropdown">
          <a class="nav-link px-2 text-white dropdown-toggle" id="observatoryDropdown" role="button">Observatory</a>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="../../Better_CSS/Observatories/add_observatory.php">Add Observatory</a></li>
            <li><a class="dropdown-item" href="../../Better_CSS/Observatories/view_observatories.php">View Observatories</a></li>
            <li><a class="dropdown-item" href="../../Better_CSS/Observatories/manage_observatories.php">Manage Observatories</a></li>
          </ul>
        </li>
        <!-- Earthquake drop down -->
        <li class="nav-item dropdown">
          <a class="nav-link px-2 text-white dropdown-toggle" id="earthquakeDropdown" role="button">Earthquake</a>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="../../Better_CSS/Earthquake/add_earthquake.php">Add Earthquake</a></li>
            <li><a class="dropdown-item" href="../../Better_CSS/Earthquake/view_earthquakes.php">View Earthquakes</a></li>
            <li><a class="dropdown-item" href="../../Better_CSS/Earthquake/manage_earthquakes.php">Manage Earthquakes</a></li>
          </ul>
        </li>

        <!-- Artefact dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link px-2 text-white dropdown-toggle" id="artefactDropdown" role="button">Artefact</a>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="../../Better_CSS/Artefact/add_artefact.php">Add Artefact</a></li>
            <li><a class="dropdown-item" href="../../Better_CSS/Artefact/view_artefacts.php">View Artefacts</a></li>
            <li><a class="dropdown-item" href="../../Better_CSS/Artefact/manage_artefacts.php">Manage Artefacts</a></li>
          </ul>
        </li>
        <!-- Pallet dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link px-2 text-white dropdown-toggle" id="palletDropdown" role="button">Pallet</a>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="../../Better_CSS/Pallet/add_pallet.php">Add Pallet</a></li>
            <li><a class="dropdown-item" href="../../Better_CSS/Pallet/view_pallets.php">View Pallets</a></li>
            <li><a class="dropdown-item" href="../../Better_CSS/Artefact/manage_pallets.php">Manage Pallets</a></li>
          </ul>
        </li>

        <!-- Shop dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link px-2 text-white dropdown-toggle" id="ShopDropdown" role="button">Shop</a>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="../../Better_CSS/Shop/shop.php">View shop</a></li>
            <li><a class="dropdown-item" href="../../Better_CSS/Shop/add_to_shop.php">Add to shop</a></li>
            <li><a class="dropdown-item" href="../../Better_CSS/Shop/remove_from_shop.php">Remove from shop</a></li>
          </ul>
        </li>
      </ul>
      
      <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
        <input type="search" class="form-control form-control-dark text-bg-light" placeholder="Search..." aria-label="Search">
      </form>
      
      <div class="text-end">
        <button onclick="window.location.href='../../Better_CSS/Sign-in/sign-in.html'" type="button" class="btn btn-outline-light me-2">Login</button>
        <button type="button" class="btn btn-warning">Sign-up</button>
      </div>
    </div>
  </div>
</header>