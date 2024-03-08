<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
  }
// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db = "datamahasiswa";

$koneksi = mysqli_connect($host, $user, $pass, $db);

// Ambil id dari URL
$id = $_GET['No'];

// Query untuk menghapus data mahasiswa berdasarkan id
$query = "DELETE FROM mahasiswa WHERE No=$id";
mysqli_query($koneksi, $query);

// Redirect ke halaman dashboard
header("Location: dashboard.php");

?>