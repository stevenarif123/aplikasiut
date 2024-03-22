<?php

// Include file koneksi database
require_once "koneksi.php";

// Ambil data dari form
$jenis_pembayaran = $_POST['jenis_pembayaran'];
$nim = $_POST['nim'];
$nama = $_POST['nama'];

// Ambil detail pembayaran
$detail_pembayaran = array();
foreach ($_POST as $key => $value) {
    if ($key != 'jenis_pembayaran' && $key != 'nim' && $key != 'nama') {
        $detail_pembayaran[$key] = $value;
    }
}

// Query untuk menyimpan data pembayaran
$sql_pembayaran = "INSERT INTO pembayaran (id_jenis_pembayaran, nim, nama, tanggal_pembayaran) VALUES ('$jenis_pembayaran', '$nim', '$nama', NOW())";

if (mysqli_query($conn, $sql_pembayaran)) {

    // Ambil ID pembayaran terakhir
    $id_pembayaran = mysqli_insert_id($conn);

    // Query untuk menyimpan detail pembayaran
    foreach ($detail_pembayaran as $key => $value) {
        $sql_detail_pembayaran = "INSERT INTO detail_pembayaran (id_pembayaran, nama_item, jumlah) VALUES ('$id_pembayaran', '$key', '$value')";
        mysqli_query($conn, $sql_detail_pembayaran);
    }

    // Buat kwitansi pembayaran
    // ...

    echo "Pembayaran berhasil disimpan.";

} else {
    echo "Error: " . mysqli_error($conn);
}

?>