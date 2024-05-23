<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once "../koneksi.php";
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : "";
$jumlah_data_per_halaman = 10;  // Fixed number of items per page
$halaman_saat_ini = isset($_GET['halaman']) ? $_GET['halaman'] : 1;
$offset = ($halaman_saat_ini - 1) * $jumlah_data_per_halaman;

$query = "SELECT * FROM mahasiswa WHERE NamaLengkap LIKE '%$keyword%' OR Nim LIKE '%$keyword%' ORDER BY No DESC LIMIT $offset, $jumlah_data_per_halaman";
$result = mysqli_query($koneksi, $query);
$mahasiswa = [];
while ($row = mysqli_fetch_assoc($result)) {
    $mahasiswa[] = $row;
}

$query_total = "SELECT COUNT(*) AS total FROM mahasiswa WHERE NamaLengkap LIKE '%$keyword%' OR Nim LIKE '%$keyword%'";
$result_total = mysqli_query($koneksi, $query_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_data = $row_total['total'];

echo json_encode([
    'mahasiswa' => $mahasiswa,
    'total' => $total_data,
    'perPage' => $jumlah_data_per_halaman,
    'currentPage' => (int)$halaman_saat_ini
]);
?>
