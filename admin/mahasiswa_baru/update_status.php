<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once "../koneksi.php";

    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'];
    $status = $data['status'];

    $stmt = $koneksi->prepare("UPDATE mahasiswabaru SET STATUS_INPUT_SIA = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
    $koneksi->close();
}
?>
