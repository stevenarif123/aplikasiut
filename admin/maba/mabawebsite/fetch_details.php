<?php
require_once "../../koneksi.php";

$id = intval($_GET['id']);
$sql = "SELECT * FROM mabawebsite WHERE id = $id";
$result = $koneksi->query($sql);
$row = $result->fetch_assoc();

if ($row) {
    echo "<p>Nama Lengkap: " . $row['nama_lengkap'] . "</p>";
    echo "<p>Tempat Lahir: " . $row['tempat_lahir'] . "</p>";
    echo "<p>Tanggal Lahir: " . $row['tanggal_lahir'] . "</p>";
    echo "<p>Nama Ibu Kandung: " . $row['nama_ibu_kandung'] . "</p>";
    echo "<p>NIK: " . $row['nik'] . "</p>";
    echo "<p>Jurusan: " . $row['jurusan'] . "</p>";
    echo "<p>Nomor HP: " . $row['nomor_hp'] . "</p>";
    echo "<p>Agama: " . $row['agama'] . "</p>";
    echo "<p>Jenis Kelamin: " . $row['jenis_kelamin'] . "</p>";
    echo "<p>Pesan: " . $row['pesan'] . "</p>";
}

$koneksi->close();
?>
