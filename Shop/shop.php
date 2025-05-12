<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        h1, h2 {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }
        .shop-container {
            max-width: 1200px;
            margin: 30px auto;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #212529;
        }
        .card-body {
            text-align: center;
        }
        .btn-primary {
            background-color: #007bff;
        }
    </style>
</head>
<body>
    <?php include '../header.php'; ?>
    <div class="container shop-container">
        <h1>Shop</h1>
        <h2>Available Artefacts</h2>
        <div class="row">
            <?php
            include '../connection.php';
            $sql = "SELECT s.id, s.artifact_id, a.type, s.price FROM stock_list s JOIN artefacts a ON s.artifact_id = a.id WHERE s.availability = 'Yes'";
            $result = sqlsrv_query($conn, $sql);
  
            // Check if the query returned any rows
            if ($result === false) {
                die('<div class="alert alert-danger" role="alert">' . print_r(sqlsrv_errors(), true) . '</div>');
            }
  
            if (sqlsrv_has_rows($result)) {
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    $id = htmlspecialchars($row['id'] ?? 'N/A');
                    $artifact_id = htmlspecialchars($row['artifact_id'] ?? 'N/A');
                    $type = htmlspecialchars($row['type'] ?? 'Unknown Type');
                    $price = number_format($row['price'] ?? 0, 2); // Format price to 2 decimals (e.g., 49.99)
                    ?>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Artefact #<?php echo $artifact_id; ?></h5>
                                <p class="card-text"><strong>Type:</strong> <?php echo $type; ?></p>
                                <p class="card-text"><strong>Price:</strong> €<?php echo $price; ?></p>
                                <a href="../Basket/add_to_basket.php?id=<?= $row['id']?>" class="btn btn-primary">Add to Basket</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div class='alert alert-warning text-center' role='alert'>No artifacts available at the moment.</div>";
            }
  
            sqlsrv_close($conn);
            ?>
        </div>
    </div>
  
    <div class="text-center mt-4">
        <button class="btn btn-secondary" onclick="window.location.href='../index.php'">Back to Main Page</button>
    </div>
  
    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>