<?php
require_once '../koneksi.php';  // Pastikan path ini benar
require_once 'kode_generator.php';   // Include the code generator

$nim = $_POST['nim'] ?? '';
$nama = $_POST['nama'] ?? '';
$jurusan = $_POST['jurusan'] ?? '';
$jumlah_bayar = $_POST['jumlah_bayar'] ?? 0;
$admin = $_POST['admin'] ?? '';
$catatan_khusus = $_POST['catatan_khusus'] ?? '';
$metode_bayar = $_POST['metode_bayar'] ?? '';

// Generate a unique report code based on the payment type
$kode_laporan = generateKodeLaporan('BY');

// Insert the new payment into the database with the report code
$sql = "INSERT INTO laporanuangmasuk20242 (KodeLaporan, NamaMahasiswa, Nim, Jurusan, JumlahBayar, Admin, CatatanKhusus, MetodeBayar, AlamatFile)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $koneksi->prepare($sql);

if (!$stmt) {
    echo "<script>alert('Prepare failed: " . $koneksi->error . "'); window.location.href='tambah_laporan.php';</script>";
    exit;
}

// Handle file upload if payment method is Transfer
$alamat_file = '';
if ($metode_bayar === 'Transfer' && isset($_FILES['bukti_file'])) {
    $upload_dir = "./BuktiTF/"; // Ubah path sesuai dengan direktori Anda
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Buat folder jika belum ada
    }
    
    $ext = pathinfo($_FILES['bukti_file']['name'], PATHINFO_EXTENSION);
    $nama_file = $nama . "_20242_" . time() . "." . $ext; // Menggunakan timestamp untuk nama file unik
    $upload_file = $upload_dir . $nama_file;

    if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $upload_file)) {
        $alamat_file = './' . $upload_file;
    } else {
        echo "<script>alert('Error uploading file.'); window.location.href='tambah_laporan.php';</script>";
        exit;
    }
}

$stmt->bind_param("sssssssss", $kode_laporan, $nama, $nim, $jurusan, $jumlah_bayar, $admin, $catatan_khusus, $metode_bayar, $alamat_file);

if ($stmt->execute()) {
    // Update the balance in saldo20242 table
    $sql = "SELECT TotalTagihan, TotalPembayaran FROM saldo20242 WHERE Nim = ? OR NamaMahasiswa = ?";
    $stmt = $koneksi->prepare($sql);
    if (!$stmt) {
        echo "<script>alert('Prepare failed: " . $koneksi->error . "'); window.location.href='tambah_laporan.php';</script>";
        exit;
    }
    $stmt->bind_param("ss", $nim, $nama);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($data = $result->fetch_assoc()) {
        $newTotalPayment = $data['TotalPembayaran'] + $jumlah_bayar;
        $newBalance = $newTotalPayment - $data['TotalTagihan'];
        $isLunas = $newBalance >= 0 ? 1 : 0;

        $sql = "UPDATE saldo20242 SET TotalPembayaran = ?, Saldo = ?, isLunas = ? WHERE Nim = ? OR NamaMahasiswa = ?";
        $stmt = $koneksi->prepare($sql);
        if (!$stmt) {
            echo "<script>alert('Prepare failed: " . $koneksi->error . "'); window.location.href='tambah_laporan.php';</script>";
            exit;
        }
        $stmt->bind_param("disss", $newTotalPayment, $newBalance, $isLunas, $nim, $nama);
        if ($stmt->execute()) {
            echo "<script>alert('Pembayaran berhasil ditambahkan dan saldo berhasil diperbarui'); window.location.href='tambah_laporan.php';</script>";
        } else {
            echo "<script>alert('Error saat memperbarui saldo: " . $stmt->error . "'); window.location.href='tambah_laporan.php';</script>";
        }
    } else {
        echo "<script>alert('No matching record found in saldo20242'); window.location.href='tambah_laporan.php';</script>";
    }
} else {
    echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "'); window.location.href='tambah_laporan.php';</script>";
}

$stmt->close();
$koneksi->close();
?>
