<?php
include '../../koneksi.php';  // Ensure this path is correct
include '../kode_generator.php';   // Include the code generator

$nim = $_POST['nim'];
$nama = $_POST['nama'];
$jurusan = $_POST['jurusan'];
$jenis_bayar = $_POST['jenis_bayar'];
$jumlah_tagihan = $_POST['jumlah_tagihan'];
$admin = $_POST['admin'];

// Generate a unique report code based on the payment type
$kode_laporan = generateKodeLaporan($jenis_bayar);

// Insert the new bill into the database with the report code
$sql = "INSERT INTO tagihan20242 (Nim, NamaMahasiswa, KodeLaporan, Jurusan, JenisBayar, TotalBayar, TanggalInput, Admin, isMaba, CatatanKhusus, isLunas)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, 0, '', 0)";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("sssssds", $nim, $nama, $kode_laporan, $jurusan, $jenis_bayar, $jumlah_tagihan, $admin);

if ($stmt->execute()) {
    echo "Tagihan berhasil ditambahkan";
    // Update the balance using both NIM and name
    updateBalance($koneksi, $nim, $nama, $jumlah_tagihan);
} else {
    echo "Terjadi kesalahan: " . $stmt->error;
}

function updateBalance($koneksi, $nim, $nama, $jumlah_tagihan) {
    // Determine whether to use NIM or name based on availability
    $condition = !empty($nim) ? "Nim = ?" : "NamaMahasiswa = ?";
    $identifier = !empty($nim) ? $nim : $nama;

    // Prepare to fetch current total bill and payment
    $sql = "SELECT TotalTagihan, TotalPembayaran FROM saldo20242 WHERE $condition";
    $stmt = $koneksi->prepare($sql);
    if (!$stmt) {
        die('Prepare failed: ' . $koneksi->error);
    }
    $stmt->bind_param("s", $identifier);
    if (!$stmt->execute()) {
        die('Execute failed: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    // Calculate new total bill and new balance
    $newTotalBill = $data['TotalTagihan'] + $jumlah_tagihan;
    $newBalance = $data['TotalPembayaran'] - $newTotalBill;

    // Prepare to update the saldo table
    $sql = "UPDATE saldo SET TotalTagihan = ?, Saldo = ? WHERE $condition";
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
}
?>
