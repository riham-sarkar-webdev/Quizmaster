<?php
// db_connection.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Debugging: Log connection success
file_put_contents('connection_log.txt', "Connected to database at " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
?>
