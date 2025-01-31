<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../koneksi.php';  // Pastikan path ini benar

$identifier = $_POST['identifier'] ?? '';

if (empty($identifier)) {
    echo json_encode(['error' => 'Identifier is required']);
    exit;
}

// Query untuk mengambil data saldo berdasarkan Nim atau NamaMahasiswa
$sql = "SELECT Nim, NamaMahasiswa, TotalTagihan, TotalPembayaran, (TotalPembayaran - TotalTagihan) AS Saldo, isLunas 
        FROM saldo20242 
        WHERE Nim = ? OR NamaMahasiswa = ?";

if ($stmt = $koneksi->prepare($sql)) {
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'No data found']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Prepare failed: ' . $koneksi->error]);
}
?>
