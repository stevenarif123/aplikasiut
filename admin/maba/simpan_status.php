<?php
// Path ke file JSON
$json_file = 'status_pengambilan.json';

// Ambil data dari form
$status_pengambilan_baru = isset($_POST['status_pengambilan']) 
    ? $_POST['status_pengambilan'] 
    : [];

// Baca data status pengambilan lama
$status_pengambilan = json_decode(file_get_contents($json_file), true);

// Perbarui status pengambilan
foreach ($status_pengambilan_baru as $nama => $value) {
    $status_pengambilan[$nama] = true;
}

// Untuk mahasiswa yang tidak dicentang, set status menjadi false
// Dapatkan daftar semua nama mahasiswa
$semua_nama = array_keys($status_pengambilan) + array_keys($status_pengambilan_baru);

// Pastikan semua nama terupdate
foreach ($semua_nama as $nama) {
    if (!isset($status_pengambilan_baru[$nama])) {
        $status_pengambilan[$nama] = false;
    }
}

// Simpan data status pengambilan ke file JSON
file_put_contents($json_file, json_encode($status_pengambilan));

// Redirect kembali ke halaman utama
header('Location: bajualmamater.php');
?>
