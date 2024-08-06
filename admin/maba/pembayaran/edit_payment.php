<?php
// Koneksi ke database
require_once("../../koneksi.php");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$id = $_POST['id'];
$type = $_POST['type'];

if ($type == 'admisi') {
    $admisi = $_POST['admisi'];
    $status_admisi = $_POST['status_admisi'];
    $sql = "UPDATE catatan_bayarmaba20242 SET admisi='$admisi', status_admisi='$status_admisi' WHERE id='$id'";
} else {
    $almamater = $_POST['almamater'];
    $spp = $_POST['spp'];
    $salut = $_POST['salut'];
    $sql = "UPDATE catatan_bayarmaba20242 SET almamater='$almamater', spp='$spp', salut='$salut' WHERE id='$id'";
}

if ($koneksi->query($sql) === TRUE) {
    echo "Data berhasil diperbarui";
} else {
    echo "Error: " . $sql . "<br>" . $koneksi->error;
}

$koneksi->close();
?>
