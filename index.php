<!-- Save as index.php -->
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home · Observatory Dashboard</title>
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <script type="importmap">
      {
        "imports": {
          "three": "https://cdn.jsdelivr.net/npm/three@v0.174.0/build/three.module.js",
          "three/examples/jsm/controls/OrbitControls.js": "https://cdn.jsdelivr.net/npm/three@v0.174.0/examples/jsm/controls/OrbitControls.js"
        }
      }
    </script>
    <script type="module" src="earth.js"></script>

    <style>
      /* Fixed Header Styling */
      header {
        position: fixed; /* Fix the header at the top */
        top: 0;
        left: 0;
        right: 0;
        z-index: 1030; /* Ensure the header stays above other elements */
      }

      /* Padding for body to prevent overlapping the fixed header */
      body {
        padding-top: 70px; /* Adjust the padding based on header height */
      }

      /* Dropdown menu appears on hover */
      .nav-item.dropdown:hover .dropdown-menu {
        display: block;
      }

      /* Additional styling for dropdown hover experience */
      .nav-item.dropdown:hover {
        cursor: pointer;
      }
    </style>
  </head>
  <body>
    <!-- Include the header -->
    <?php include 'header.php'; ?>

<<<<<<< HEAD
<!-- Main Hero Container -->
    <div>Main Hero</div>
    <div class="container my-5">
        <div class="row p-4 pb-0 pe-lg-0 pt-lg-5 align-items-center rounded-3 border shadow-lg overflow-hidden">
            <div class="col-lg-7 p-3 p-lg-5 pt-lg-3">
                <h1 class="display-4 fw-bold lh-1 text-body-emphasis">bootstrap</h1>
                <p class="lead">Quickly design and customize responsive mobile-first sites with Bootstrap, the
                    world’s most popular front-end open source toolkit, featuring Sass variables and mixins,
                    responsive grid system, extensive prebuilt components, and powerful JavaScript plugins.</p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start mb-4 mb-lg-3"> <button type="button"
                        class="btn btn-primary btn-lg px-4 me-md-2 fw-bold">Dive in!</button></div>
            </div>
            <div class="col m-0 p-0" style="width:500px; height:400px;">
              <img class="" src="assets/earth/earth placeholder.png" style="width:700px; height:600px; display:block; object-fit:cover;"></img>
            </div>
        </div>
    </div>

<!-- Shop Overview -->
    <div>Shop Overview</div>

    <div class="container my-5">
      <div class="row">

        <div class="card col m-2 shadow">
          <img src="assets\home images\artifact placeholder.png" class="card-img-top" alt="...">

          <div class="card-body">
            <h5 class="card-title">Card title</h5>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
          </div>
        </div>

        <div class="card col m-2 shadow">
          <img src="assets\home images\artifact placeholder.png" class="card-img-top" alt="...">

          <div class="card-body">
            <h5 class="card-title">Card title</h5>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
          </div>
        </div>

        <div class="card col m-2 shadow">
          <img src="assets\home images\artifact placeholder.png" class="card-img-top" alt="...">

          <div class="card-body">
            <h5 class="card-title">Card title</h5>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
          </div>
        </div>

      </div>
    </div>
=======
    <!-- Main content -->
    <main class="container mt-5">
    <h1>Welcome<?= isset($_SESSION['account_name']) ? ', ' . htmlspecialchars($_SESSION['account_name']) : '' ?>!</h1>
  <p>This is your main content area.</p>
</main>
>>>>>>> main

    <!-- Include Bootstrap JS -->
    <script src="assets/dist/js/bootstrap.bundle.min.js" defer></script>
  </body>
</html>