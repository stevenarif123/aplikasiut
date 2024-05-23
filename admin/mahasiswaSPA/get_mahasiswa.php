<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once "../koneksi.php";
if (!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
}

$no = $_GET['No'];
$query = "SELECT * FROM mahasiswa WHERE No = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $no);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$mahasiswa = mysqli_fetch_assoc($result);

$query_jurusan = "SELECT nama_jurusan FROM jurusan";
$result_jurusan = mysqli_query($koneksi, $query_jurusan);
$jurusan = [];
while ($row = mysqli_fetch_assoc($result_jurusan)) {
    $jurusan[] = $row['nama_jurusan'];
}

echo json_encode(array_merge($mahasiswa, ['jurusanOptions' => $jurusan]));
?>
