<?php
// Koneksi ke database
require_once "../koneksi.php";

// Dapatkan data dari request
$no = $_POST['No'];
$namaLengkap = $_POST['NamaLengkap'];
$nomorHP = $_POST['NomorHP'];
$email = $_POST['Email'];
$password = $_POST['Password'];
$statusSIA = $_POST['STATUS_INPUT_SIA'];

// Siapkan query menggunakan prepared statement untuk mencegah SQL Injection
$stmt = $koneksi->prepare("UPDATE mahasiswabaru20242 SET NamaLengkap = ?, NomorHP = ?, Email = ?, Password = ?, STATUS_INPUT_SIA = ? WHERE No = ?");
$stmt->bind_param("sssssi", $namaLengkap, $nomorHP, $email, $password, $statusSIA, $no);

// Jalankan query
if ($stmt->execute()) {
  echo "success";
} else {
  echo "error: " . $stmt->error;
}

// Tutup statement dan koneksi
$stmt->close();
$koneksi->close();
?>
