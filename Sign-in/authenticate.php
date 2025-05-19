<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';

$serverName = "UK-DIET-SQL-T1";
$connectionOptions = [
    "Database" => "Group6_DB",
    "Uid" => "UserGroup6",
    "PWD" => "UpqrxGOkJdQ64MFC"
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

if (!isset($_POST['username'], $_POST['password'])) {
    exit('Please fill both the username and password fields!');
}

// Prepare our SQL, with ? parameter
$sql = "SELECT id, password FROM accounts WHERE username = ?";
$params = array($_POST['username']);

$stmt = sqlsrv_query($conn, $sql, $params);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $id = $row['id'];
    $password = $row['password'];
    if (password_verify($_POST['password'], $password)) {
        session_regenerate_id();
        $_SESSION['account_loggedin'] = TRUE;
        $_SESSION['account_name'] = $_POST['username'];
        $_SESSION['account_id'] = $id;
        $_SESSION['account_access'] = 1;
        header('Location: ../index.php');
        exit;
    } else {
        echo 'Incorrect username and/or password!';
    }
} else {
    echo 'Incorrect username and/or password!';
}
?>