<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db = "datamahasiswa";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (mysqli_connect_errno()) {
    die("Gagal Koneksi ke Database". mysqli_connect_error());
}
?>