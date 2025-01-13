<?php
require_once "../../koneksi.php";

if(isset($_POST['no'])) {
    $no = $_POST['no'];

    // Mengambil data mahasiswa dari database
    $sql = "SELECT * FROM mahasiswa WHERE No = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "i", $no);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $mahasiswa = mysqli_fetch_assoc($result);

    if($mahasiswa) {
        $nim = $mahasiswa['NIM'];
        $nama = $mahasiswa['NamaLengkap'];
        $jurusan = $mahasiswa['Jurusan'];
        $email = $mahasiswa['Email'];
        $password = $mahasiswa['Password'];; // Atau isi sesuai kebutuhan

        // Cek apakah data sudah ada di tabel tuton
        $sql_check = "SELECT * FROM tuton WHERE NIM = ?";
        $stmt_check = mysqli_prepare($koneksi, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $nim);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $existing = mysqli_fetch_assoc($result_check);

        if(!$existing) {
            // Simpan data ke tabel tuton
            $sql_insert = "INSERT INTO tuton (NIM, Nama, Jurusan, Email, Password) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($koneksi, $sql_insert);
            mysqli_stmt_bind_param($stmt_insert, "sssss", $nim, $nama, $jurusan, $email, $password);
            if(mysqli_stmt_execute($stmt_insert)) {
                echo json_encode(['status' => 'success', 'message' => 'Migrasi data berhasil.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data ke tabel tuton.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data sudah ada di tabel tuton.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Mahasiswa tidak ditemukan.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
}
?>
