<?php
require_once '../../koneksi.php';  // Ensure this path is correct
require_once '../kode_generator.php';   // Include the code generator

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit;
}

$admin = $_SESSION['username'];

$nim = $_POST['nim'];
$nama = $_POST['nama'];
$jurusan = $_POST['jurusan'];
// $jenis_bayar = $_POST['jenis_bayar'];
$jenis_bayar = isset($_POST['jenis_bayar']) ? $_POST['jenis_bayar'] : '';
$jumlah_tagihan = $_POST['jumlah_tagihan'];

// Generate a unique report code based on the payment type
$kode_laporan = generateKodeLaporan($jenis_bayar);

// Insert the new bill into the database with the report code
$sql = "INSERT INTO tagihan20242 (Nim, NamaMahasiswa, KodeLaporan, Jurusan, JenisBayar, TotalBayar, TanggalInput, Admin, isMaba, CatatanKhusus, isLunas)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, 0, '', 0)";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("sssssss", $nim, $nama, $kode_laporan, $jurusan, $jenis_bayar, $jumlah_tagihan, $admin);
  
if ($stmt->execute()) {
    echo "Tagihan berhasil ditambahkan";
    // Update the balance using both NIM and name
    updateBalance($koneksi, $nim, $nama, $jumlah_tagihan);
} else {
    echo "Terjadi kesalahan: " . $stmt->error;
}
$jenis_bayar = array("SPP", "Almamater", "Pokjar", "Admisi");
function updateBalance($koneksi, $nim, $nama, $jumlah_tagihan) {
    $condition = !empty($nim) ? "Nim = ?" : "NamaMahasiswa = ?";
    $identifier = !empty($nim) ? $nim : $nama;

    $sql = "SELECT TotalTagihan, TotalPembayaran FROM saldo20242 WHERE $condition";
    $stmt = $koneksi->prepare($sql);
    if (!$stmt) {
        die('Prepare failed: ' . $koneksi->error);
    }
    $stmt->bind_param("s", $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($data = $result->fetch_assoc()) {
        // Existing data found, update it
        $newTotalBill = $data['TotalTagihan'] + $jumlah_tagihan;
        $newBalance = $data['TotalPembayaran'] - $newTotalBill;

        $sql = "UPDATE saldo20242 SET TotalTagihan = ?, Saldo = ? WHERE $condition";
        $stmt = $koneksi->prepare($sql);
        if (!$stmt) {
            die('Prepare failed: ' . $koneksi->error);
        }
        $stmt->bind_param("dds", $newTotalBill, $newBalance, $identifier);
        if ($stmt->execute()) {
            echo "Saldo berhasil diperbarui";
        } else {
            echo "Error saat memperbarui saldo: " . $stmt->error;
        }
    } else {
        $jurusan = $_POST['jurusan'];
        // No existing data, insert new record
        $sql = "INSERT INTO saldo20242 (Nim, NamaMahasiswa, Jurusan, TotalTagihan, TotalPembayaran, Saldo) VALUES (?, ?, ?, ?, 0, -?)";
        $stmt = $koneksi->prepare($sql);
        if (!$stmt) {
            die('Prepare failed: ' . $koneksi->error);
        }
        $stmt->bind_param("sssds", $nim, $nama, $jurusan, $jumlah_tagihan, $jumlah_tagihan);
        if ($stmt->execute()) {
            echo "New saldo record created successfully";
        } else {
            echo "Error saat menambahkan saldo baru: " . $stmt->error;
        }
    }
}
?>
