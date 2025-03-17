<?php
//File to hold connection information for our database


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testing2";

//Create a connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

//Check connection
if(!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully";

?>