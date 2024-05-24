<?php
require_once "../koneksi.php";

$query = "SELECT nama_jurusan FROM jurusan";
$result = mysqli_query($koneksi, $query);
$majors = array();
while ($row = mysqli_fetch_assoc($result)) {
    $majors[] = $row['nama_jurusan'];
}

echo json_encode($majors);
?>
