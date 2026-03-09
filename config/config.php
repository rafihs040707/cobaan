<?php

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "alpha_2"; 
$port = 3306;

// Correct connection using variables
$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

// Check connection
// if (!$conn) {
//     die("Connection failed: " . mysqli_connect_error());
// }

?>