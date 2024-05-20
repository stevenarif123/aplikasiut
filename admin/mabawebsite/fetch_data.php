<?php
require_once "../koneksi.php";

$sql = "SELECT * FROM mabawebsite";
$result = $koneksi->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    // Check if data exists in mahasiswabaru table
    $sql_check = "SELECT COUNT(*) AS count FROM mahasiswabaru WHERE nik = '" . $row['nik'] . "'";
    $check_result = $koneksi->query($sql_check);
    $check_row = $check_result->fetch_assoc();
    
    $row['processed'] = $check_row['count'] > 0;
    $data[] = $row;
}

echo json_encode($data);

$koneksi->close();
?>
