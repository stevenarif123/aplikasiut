<?php
require_once "../../koneksi.php";

$id = intval($_POST['id']);
$sql = "SELECT * FROM mabawebsite WHERE id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    $sql_insert = "INSERT INTO mahasiswabaru20242 (NamaLengkap, TempatLahir, TanggalLahir, NamaIbuKandung, NIK, Jurusan, NomorHP, Agama, JenisKelamin) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $koneksi->prepare($sql_insert);
    $stmt_insert->bind_param("sssssssss", $row['nama_lengkap'], $row['tempat_lahir'], $row['tanggal_lahir'], $row['nama_ibu_kandung'], $row['nik'], $row['jurusan'], $row['nomor_hp'], $row['agama'], $row['jenis_kelamin']);
    
    if ($stmt_insert->execute()) {
        echo "Data inserted successfully.";
    } else {
        error_log("Error inserting data: " . $stmt_insert->error);
        echo "Error inserting data.";
    }
} else {
    error_log("No data found with the given ID: " . $id);
    echo "No data found with the given ID.";
}

$koneksi->close();
?>
