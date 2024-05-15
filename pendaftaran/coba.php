<?php
// Connect to database
require_once "../admin/koneksi.php";

$pesanstatus = '';

// Process form data
if (isset($_POST['submit'])) {
    // Set variabel
    $nama_lengkap = isset($_POST['nama_lengkap']) ? $_POST['nama_lengkap'] : '';
    $tempat_lahir = isset($_POST['tempat_lahir']) ? $_POST['tempat_lahir'] : '';
    $tanggal_lahir = isset($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : '';
    $nama_ibu_kandung = isset($_POST['nama_ibu_kandung']) ? $_POST['nama_ibu_kandung'] : '';
    $nik = isset($_POST['nik']) ? $_POST['nik'] : '';
    $jurusan = isset($_POST['jurusan']) ? $_POST['jurusan'] : '';
    $nomor_hp = isset($_POST['nomor_hp']) ? $_POST['nomor_hp'] : '';
    $agama = isset($_POST['agama']) ? $_POST['agama'] : '';
    $jenis_kelamin = isset($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : '';
    $pesan = isset($_POST['pesan']) ? $_POST['pesan'] : '';

    // Cek apakah semua variabel yang dibutuhkan telah diatur
    if (!empty($nama_lengkap) && !empty($tempat_lahir) && !empty($tanggal_lahir) && !empty($nama_ibu_kandung) && !empty($nik) && !empty($jurusan) && !empty($nomor_hp) && !empty($agama) && !empty($jenis_kelamin)) {
        // Insert data into database using prepared statement
        $query = "INSERT INTO mabawebsite (nama_lengkap, tempat_lahir, tanggal_lahir, nama_ibu_kandung, nik, jurusan, nomor_hp, agama, jenis_kelamin, pesan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ssssssssss", $nama_lengkap, $tempat_lahir, $tanggal_lahir, $nama_ibu_kandung, $nik, $jurusan, $nomor_hp, $agama, $jenis_kelamin, $pesan);

        if (mysqli_stmt_execute($stmt)) {
            $pesanstatus = "Data berhasil disimpan";
        } else {
            $pesanstatus = "Gagal menyimpan data: " . mysqli_error($koneksi);
        }
    } else {
        $pesanstatus = "Gagal menyimpan data: Mohon lengkapi semua field yang diperlukan";
    }

    header('Content-Type: application/json');
    $response = array(
        'status' => $pesanstatus
    );
    echo json_encode($response);
    exit;
}
?>
