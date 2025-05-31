<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Artefact</title>
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
        <h1>Add Artefact</h1>
        <form id="artefactForm" action="process_artefact.php" method="POST" onsubmit="return validateArtefactForm()">
            <div class="mb-3">
                <label for="earthquake_id" class="form-label">Earthquake:</label>
                <select id="earthquake_id" name="earthquake_id" class="form-select" required>
                    <?php
                    
                    $sql = "SELECT id, country, date FROM earthquakes";
                    $result = sqlsrv_query($conn, $sql);

                    if ($result === false) {
                        die(print_r(sqlsrv_errors(), true)); // Debug errors if query fails
                    }

                    if (sqlsrv_has_rows($result)) {
                        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                            $formattedDate = $row["date"] instanceof DateTime
                                ? $row["date"]->format('Y-m-d')
                                : $row["date"];
                            echo "<option value='" . $row['id'] . "'>" . $row['country'] . " - " . $formattedDate . "</option>";
                        }
                    } else {
                        echo "<option value=''>No earthquakes found</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Type:</label>
                <select id="type" name="type" class="form-select" required>
                    <option value="solidified lava">Solidified Lava</option>
                    <option value="foreign debris">Foreign Debris (Tephra)</option>
                    <option value="ash sample">Ash Sample</option>
                    <option value="ground soil">Ground Soil</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="shelving_loc" class="form-label">Shelving Location:</label>
                <select id="shelving_loc" name="shelving_loc" class="form-select" required>
                    <?php
                    $sqlOrder = "SELECT shelf FROM shelves WHERE capacity > 0 ORDER BY shelf ASC";
                    $stmtOrder = sqlsrv_query($conn, $sqlOrder);

                    if(!$stmtOrder){
                        echo "<option disabled>Error fetching shelves</option>";
                    } else{
                        while($row = sqlsrv_fetch_array($stmtOrder, SQLSRV_FETCH_ASSOC)){
                            $shelf = htmlspecialchars($row["shelf"]);
                            echo "<option value='$shelf'>$shelf</option>";
                        }
                    }
                    sqlsrv_close($conn);
                    ?>
                </select>
            </div>
            <div class="mb-3">
                 <label for="description" class="form-label">Description:</label>
                    <textarea 
                        id="description" 
                        name="description"
                        class="form-control"
                        rows="4"
                        required
                        placeholder="Enter a description"></textarea>
            </div>
            <button type="submit" class="btn btn-warning">Submit</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='../index.php'">Back to Main Page</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script.js"></script>
</body>
</html>