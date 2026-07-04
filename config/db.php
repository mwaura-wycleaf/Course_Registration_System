<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "course_registration_system";
$port = 3307;

// Create connection
$conn = mysqli_connect($host, $username, $password, $database, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Uncomment this line if you want to test the connection
// echo "Database connected successfully!";

?>