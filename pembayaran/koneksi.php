<?php

$servername = "localhost";
$username = "root";
$password = "";
$nama_database = "datamahasiswa";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $nama_database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>