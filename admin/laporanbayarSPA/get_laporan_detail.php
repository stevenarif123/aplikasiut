<?php

require_once "../koneksi.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

$id = $_GET['id'] ?? '';

$query = "SELECT * FROM laporanuangmasuk WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(['error' => 'Data not found']);
    exit;
}

echo json_encode($row);
?>
