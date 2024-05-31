<?php
// Connect to database
require_once "../admin/koneksi.php";

$pesanstatus = '';
$nama_lengkap = '';
$tempat_lahir = '';
$tanggal_lahir = '';
$nama_ibu_kandung = '';
$nik = '';
$jurusan = '';
$nomor_hp = '';
$agama = '';
$jenis_kelamin = '';
$pesan = '';

// Get list of jurusan from database
$query_jurusan = "SELECT * FROM jurusan";
$result_jurusan = mysqli_query($koneksi, $query_jurusan);
$daftar_jurusan = array();
while ($row = mysqli_fetch_assoc($result_jurusan)) {
    $daftar_jurusan[] = $row["nama_jurusan"];
}

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
    if (isset($nama_lengkap, $tempat_lahir, $tanggal_lahir, $nama_ibu_kandung, $nik, $jurusan, $nomor_hp, $agama, $jenis_kelamin, $pesan)) {
        // Insert data into database using prepared statement
        $query = "INSERT INTO mabawebsite (nama_lengkap, tempat_lahir, tanggal_lahir, nama_ibu_kandung, nik, jurusan, nomor_hp, agama, jenis_kelamin, pesan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ssssssssss", $nama_lengkap, $tempat_lahir, $tanggal_lahir, $nama_ibu_kandung, $nik, $jurusan, $nomor_hp, $agama, $jenis_kelamin, $pesan);

        if (mysqli_stmt_execute($stmt)) {
            $pesanstatus = "Data berhasil disimpan";
            echo "<meta http-equiv=\"refresh\" content=\"3;url=sukses.php\">";
        } else {
            $pesanstatus = "Gagal menyimpan data: " . mysqli_error($koneksi);
        }
    } else {
        $pesanstatus = "Gagal menyimpan data: Variabel yang diperlukan tidak diatur";
    }
    
    header('Content-Type: application/json');
    $response = array(
        'status' => $pesanstatus
    );
    echo json_encode($response);
    exit;
}
?>