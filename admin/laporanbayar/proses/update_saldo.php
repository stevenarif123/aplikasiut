<?php
include '../../koneksi.php'; // Sesuaikan dengan path file konfigurasi database

$nim = $_POST['nim'];

// Dapatkan total tagihan dan total pembayaran
$sql = "SELECT SUM(TotalBayar) as total_tagihan FROM tagihan20242 WHERE Nim = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $nim);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_tagihan = $row['total_tagihan'];

$sql = "SELECT SUM(Total) as total_pembayaran FROM laporanuangmasuk20242 WHERE Nim = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $nim);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_pembayaran = $row['total_pembayaran'];

// Hitung saldo
$saldo = $total_pembayaran - $total_tagihan;

// Perbarui tabel saldo
$sql = "INSERT INTO saldo (student_id, Nim, NamaMahasiswa, Jurusan, TotalTagihan, TotalPembayaran, Saldo)
        SELECT id, Nim, NamaMahasiswa, Jurusan, ?, ?, ?
        FROM mahasiswa
        WHERE Nim = ?
        ON DUPLICATE KEY UPDATE TotalTagihan = ?, TotalPembayaran = ?, Saldo = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("dddsddd", $total_tagihan, $total_pembayaran, $saldo, $nim, $total_tagihan, $total_pembayaran, $saldo);

if ($stmt->execute()) {
    echo "Saldo berhasil diperbarui";
} else {
    echo "Terjadi kesalahan: " . $stmt->error;
}
?>
