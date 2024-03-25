<?php

// Session status check

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}



require_once "koneksi.php";

// Ambil id dari URL
$id = $_GET['No'];

// Query untuk menghapus data mahasiswa berdasarkan id
$query = "DELETE FROM mahasiswa WHERE No=$id";
mysqli_query($koneksi, $query);

// Redirect ke halaman dashboard
header("Location: dashboard.php");

?>