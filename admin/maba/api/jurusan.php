<?php
require_once "../../koneksi.php";

$sql = "SELECT * FROM prodi_admisi";
$result = $koneksi->query($sql);

// Simpan data jurusan dalam array
$daftarJurusan = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $daftarJurusan[] = $row["nama_program_studi"];
    }
}

echo json_encode($daftarJurusan);
$koneksi->close();
?>