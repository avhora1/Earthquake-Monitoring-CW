<?php
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $country = trim($_POST['country'] ?? '');
    $magnitude = $_POST['magnitude'] ?? '';
    $type = $_POST['type'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $latitude = $_POST['latitude'] ?? '';
    $longitude = $_POST['longitude'] ?? '';
    $observatory_id = $_POST['observatory_id'] ?? '';

    // 1. Country: non-empty, max 56 chars
    if ($country === "" || strlen($country) > 56 || is_numeric($country)) {
        die("Country is required, must be at most 56 characters and must not be numeric.");
    }

    // 2. Magnitude: 1.0 <= x <= 9.9, max one decimal place
    if (
        !is_numeric($magnitude) ||
        $magnitude < 1.0 || $magnitude > 9.9 ||
        !preg_match('/^\d(\.\d)?$/', strval($magnitude)) // Only one decimal allowed
    ) {
        die("Magnitude must be a number between 1.0 and 9.9 (one decimal place max).");
    }

    // 3. Type: must be allowed value
    $valid_types = ['tectonic','volcanic','collapse','explosion'];
    if (!in_array($type, $valid_types, true)) {
        die("Type must be one of: tectonic, volcanic, collapse, explosion.");
    }

    // 4. Date: must be valid YYYY-MM-DD
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) {
        die("Invalid date format.");
    }

    // 5. Time: must be valid HH:MM or HH:MM:SS, 24h
    if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $time)) {
        die("Invalid time format.");
    }

    // 6. Latitude: numeric between -90 and 90, max 6 decimals
    if (
        !is_numeric($latitude)
        || $latitude < -90 || $latitude > 90
        || !preg_match('/^-?\d{1,2}(\.\d{1,6})?$/', strval($latitude))
    ) {
        die("Latitude must be between -90 and 90, maximum 6 decimals.");
    }

    // 7. Longitude: numeric between -180 and 180, max 6 decimals
    if (
        !is_numeric($longitude)
        || $longitude < -180 || $longitude > 180
        || !preg_match('/^-?\d{1,3}(\.\d{1,6})?$/', strval($longitude))
    ) {
        die("Longitude must be between -180 and 180, maximum 6 decimals.");
    }
    
    // 8. Observatory ID must not be empty or invalid
    if ($observatory_id == "" || !is_numeric($observatory_id) || intval($observatory_id) < 1) {
        die("Invalid observatory selection");
    }

    // Check DB for observatory ID exists
    // Go through DB and count how many entires match the observatory ID submitted
    $check_sql = "SELECT COUNT(*) as cnt FROM observatories WHERE id = ?";
    // Query this. Intval is an array of 1 value and safely casts the observatory id to an int. 
    $check_stmt = sqlsrv_query($conn, $check_sql, [intval($observatory_id)]);
    // If query fails then die
    if ($check_stmt == false) {
        die ("Database error while checking observatory");
    }
    /* Fetch the row of results from the DB
    if ID is correct, then row = true and cnt = 1.
    If not correct, then row = false and cnt = 0
    */ 
    $row = sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC);
    if (!$row || $row['cnt'] == 0) {
        die("The selected observatory does not exist.");
    }
    sqlsrv_free_stmt($check_stmt);

    // SQL query with placeholders because we want to do SQL prepared statements (they're safer from SQL injection attacks)
    $sql = "INSERT INTO earthquakes (country, magnitude, type, date, time, latitude, longitude, observatory_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $params = array($country, $magnitude, $type, $date, $time, $latitude, $longitude, $observatory_id);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        // Print error to see why query failed (to fix it)
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "New earthquake record created successfully";
    }

    // Free the statement resources so it doesn't take up anymore memory server side 
    sqlsrv_free_stmt($stmt);

    sqlsrv_close($conn);

    header("Location: add_earthquake.php");
    exit();
}
?>