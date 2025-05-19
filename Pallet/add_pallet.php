<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Pallet</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Light grey background */
        }
        .form-container {
            margin: auto;
            margin-top: 50px;
            max-width: 600px;
            padding: 30px;
            background-color: #ffffff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: #212529;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-warning {
            background-color: #ffc107;
        }
        .btn-secondary {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <?php include '../header.php'; ?>
    <div class="form-container">
        <h1>Add Pallet</h1>
        <form id="palletForm" action="process_pallet.php" method="POST" onsubmit="return validatePalletForm()">
            <div class="mb-3">
                <label for="pallet_size" class="form-label">Pallet Size:</label>
                <select id="pallet_size" name="pallet_size" class="form-select" required>
                    <option value="half">Half</option>
                    <option value="full">Full</option>
                </select>
            </div>
            <button type="submit" class="btn btn-warning">Continue</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='../index.php'">Back to Main Page</button>
        </form>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script.js"></script>
</body>
</html>