<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Observatory</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Remove margin and padding from body and html */
        html, body {
            margin: 0;
            padding: 0;
        }

        /* Prevent content from overlapping the fixed header */
        body {
            background-color: #f8f9fa; /* Light grey background for better contrast */
        }

        /* Center the form on the page */
        .form-container {
            margin: auto;
            margin-top: 50px; /* Space below the header */
            max-width: 600px; /* Limit width to make it clean */
            padding: 30px;
            background-color: #ffffff; /* White card background */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow effect */
            border-radius: 10px; /* Rounded corners */
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: #212529;
        }

        .form-label {
            font-weight: 500; /* More readable labels */
        }

        .btn-warning {
            background-color: #ffc107; /* Bootstrap warning button color */
        }

        /* Style for buttons */
        .btn-secondary {
            margin-left: 10px; /* Small spacing between buttons */
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include '../header.php'; ?>

    <!-- Main Content -->
    <div class="form-container">
        <h1>Add Observatory</h1>
        <form id="observatoryForm" action="process_form.php" method="POST" onsubmit="return validateForm()">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="Enter observatory name">
            </div>

            <div class="mb-3">
                <label for="est_date" class="form-label">Establishment Date:</label>
                <input type="date" id="est_date" name="est_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="latitude" class="form-label">Latitude:</label>
                <input type="number" step="0.000001" id="latitude" name="latitude" class="form-control" required placeholder="e.g., 51.5074">
            </div>

            <div class="mb-3">
                <label for="longitude" class="form-label">Longitude:</label>
                <input type="number" step="0.000001" id="longitude" name="longitude" class="form-control" required placeholder="e.g., -0.1278">
            </div>

            <button type="submit" class="btn btn-warning">Submit</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='../index.php'">Back to Main Page</button>
        </form>
    </div>
  
    <!-- Optional Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script.js"></script>
</body>
</html>