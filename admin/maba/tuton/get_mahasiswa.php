<?php
require_once "../../koneksi.php";

if(isset($_POST['no'])) {
    $no = $_POST['no'];

    $sql = "SELECT NamaLengkap FROM mahasiswa WHERE No = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "i", $no);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $mahasiswa = mysqli_fetch_assoc($result);

    if($mahasiswa) {
        echo json_encode(['status' => 'success', 'nama' => $mahasiswa['NamaLengkap']]);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
?>
