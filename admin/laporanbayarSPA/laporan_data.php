<?php

// Menghubungkan ke database
require_once '../koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

$admin = $_SESSION['username'];

// Mengatur jumlah data per halaman
$dataPerPage = 10;

// Mengambil halaman saat ini
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Menghitung offset data
$offset = ($currentPage - 1) * $dataPerPage;

// Menyiapkan query dasar
$query = "SELECT * FROM laporanuangmasuk WHERE Admin='$admin'";

// Menambahkan filter berdasarkan rentang tanggal
if (isset($_GET['tanggal_awal']) && isset($_GET['tanggal_akhir'])) {
    $tanggal_awal = $_GET['tanggal_awal'];
    $tanggal_akhir = $_GET['tanggal_akhir'];
    $query .= " AND TanggalInput BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
}

// Menambahkan filter berdasarkan nama dan NIM (jika diperlukan)
if (isset($_GET['nama']) && !empty($_GET['nama'])) {
    $nama = $_GET['nama'];
    $query .= " AND NamaMahasiswa LIKE '%$nama%'";
}
if (isset($_GET['nim']) && !empty($_GET['nim'])) {
    $nim = $_GET['nim'];
    $query .= " AND Nim LIKE '%$nim%'";
}

// Menambahkan filter untuk laporan yang belum diverifikasi
$query .= " AND isVerifikasi = 0";

// Mengambil total data
$result = mysqli_query($koneksi, $query);
$totalData = mysqli_num_rows($result);

// Menambahkan limit pada query
$query .= " LIMIT $offset, $dataPerPage";
$result = mysqli_query($koneksi, $query);

$laporanList = [];
while ($row = mysqli_fetch_assoc($result)) {
    $laporanList[] = $row;
}

echo json_encode([
    'totalData' => $totalData,
    'dataPerPage' => $dataPerPage,
    'currentPage' => $currentPage,
    'laporanList' => $laporanList
]);

mysqli_close($koneksi);
?>
