<!-- Save as index.php -->
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home · Observatory Dashboard</title>
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
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

    <!-- Main content -->
    <main class="container mt-5">
    <h1>Welcome<?= isset($_SESSION['account_name']) ? ', ' . htmlspecialchars($_SESSION['account_name']) : '' ?>!</h1>
  <p>This is your main content area.</p>
</main>

    <!-- Include Bootstrap JS -->
    <script src="assets/dist/js/bootstrap.bundle.min.js" defer></script>
  </body>
</html>