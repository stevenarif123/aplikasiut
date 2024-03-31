<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db = "datamahasiswa";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if ($koneksi->connect_error) {
    die("Connection failed: " . $koneksi->connect_error);
}
?>