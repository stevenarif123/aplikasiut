<?php
require_once '../koneksi.php';  // Pastikan path ini benar
require_once 'kode_generatorlaporan.php';   // Include the code generator

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil data dari form
$kode_laporan = $_POST['kode_laporan'] ?? '';
$nim = $_POST['nim'] ?? '';
$nama = $_POST['nama_mahasiswa'] ?? '';
$jumlah_bayar = $_POST['jumlah_bayar'] ?? 0;
$admin = $_POST['admin'] ?? '';
$catatan_khusus = $_POST['catatan_khusus'] ?? '';
$metode_bayar = $_POST['metode_bayar'] ?? '';

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

// Insert the new payment into the database with the report code
$sql = "INSERT INTO laporanuangmasuk20242 (KodeLaporan, NamaMahasiswa, Nim, JumlahBayar, Admin, CatatanKhusus, MetodeBayar, AlamatFile)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $koneksi->prepare($sql);

if (!$stmt) {
    echo "<script>alert('Prepare failed: " . $koneksi->error . "'); window.location.href='tambah_laporan.php';</script>";
    exit;
}

$stmt->bind_param("ssssssss", $kode_laporan, $nama, $nim, $jumlah_bayar, $admin, $catatan_khusus, $metode_bayar, $alamat_file);

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

        if (!empty($nim)) {
            $sql = "UPDATE saldo20242 SET TotalPembayaran = ?, Saldo = ?, isLunas = ? WHERE Nim = ? AND NamaMahasiswa = ?";
            $stmt = $koneksi->prepare($sql);
            if (!$stmt) {
                echo "<script>alert('Prepare failed: " . $koneksi->error . "'); window.location.href='tambah_laporan.php';</script>";
                exit;
            }
            $stmt->bind_param("disss", $newTotalPayment, $newBalance, $isLunas, $nim, $nama);
        } else {
            $sql = "UPDATE saldo20242 SET TotalPembayaran = ?, Saldo = ?, isLunas = ? WHERE NamaMahasiswa = ?";
            $stmt = $koneksi->prepare($sql);
            if (!$stmt) {
                echo "<script>alert('Prepare failed: " . $koneksi->error . "'); window.location.href='tambah_laporan.php';</script>";
                exit;
            }
            $stmt->bind_param("diss", $newTotalPayment, $newBalance, $isLunas, $nama);
        }

        if ($stmt->execute()) {
            echo "<script>alert('Pembayaran berhasil ditambahkan dan saldo berhasil diperbarui'); window.location.href='index.php';</script>";
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
