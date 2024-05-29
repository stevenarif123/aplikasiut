<?php
require_once "koneksi.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated']);
    exit;
}

$laporanId = $_POST['id'] ?? '';

if ($laporanId) {
    $laporanId = mysqli_real_escape_string($koneksi, $laporanId);

    $sql = "DELETE FROM laporanuangmasuk WHERE id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $laporanId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Gagal menghapus Laporan']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Gagal mendapatkan ID Laporan']);
}

$koneksi->close();
?>
