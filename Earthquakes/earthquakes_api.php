<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'].'/queryLibrary.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/connection.php'; // defines $conn

$min_year = isset($_GET['min_year']) ? (int)$_GET['min_year'] : null;
$max_year = isset($_GET['max_year']) ? (int)$_GET['max_year'] : null;
$min_mag  = isset($_GET['min_mag']) ? (float)$_GET['min_mag'] : null;
$max_mag  = isset($_GET['max_mag']) ? (float)$_GET['max_mag'] : null;
$types        = (!empty($_GET['types'])) ? explode(',', $_GET['types']) : [];
$observatories = (!empty($_GET['observatories'])) ? array_map('intval', explode(',', $_GET['observatories'])) : [];
$countries     = (!empty($_GET['countries'])) ? explode(',', $_GET['countries']) : [];

$earthquakeRows = filter_earthquakes($conn, $min_year, $max_year, $min_mag, $max_mag, $types, $observatories, $countries);

// Map/massage results as needed for JS:
$earthquakes = array_map(function($row) {
    return [
        'lat' => $row['latitude'],
        'lon' => $row['longitude'],
        'mag' => $row['magnitude'],
        'city' => $row['city'] ?? '',
        'country' => $row['country'] ?? '',
        'date' => ($row['date'] instanceof DateTime)
            ? $row['date']->format('d.m.Y')
            : date('d.m.Y', strtotime($row['date'])),
        'desc' => $row['description'] ?? '',
    ];
}, $earthquakeRows);

echo json_encode($earthquakes);
exit;
?>