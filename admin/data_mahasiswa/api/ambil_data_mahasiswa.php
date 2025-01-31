<?php
require_once '../../koneksi.php'; // Sesuaikan dengan path ke file koneksi Anda

$no = $_POST['No'];
$query = "SELECT * FROM mahasiswa WHERE No = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $no);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

echo json_encode($user);
?>
