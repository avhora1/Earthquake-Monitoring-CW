<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Light grey background */
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
            color: #212529;
        }
        .table-container {
            margin-top: 50px;
            max-width: 90%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
    <?php include '../header.php'; ?>
    <div id="observatoriesTable" class="container mt-4">
        <h1>Observatories List</h1>
        <!-- Include the PHP file responsible for fetching observatory data -->
        <?php include 'fetch_observatories.php'; ?>
    </div>
    
    <!-- Include Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script.js"></script>
</body>
</html>