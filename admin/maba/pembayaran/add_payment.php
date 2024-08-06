<?php
// Koneksi ke database
require_once("../../koneksi.php");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$id = $_POST['id'];
$jumlah_bayar = $_POST['jumlah_bayar'];

// Ambil data saat ini dari database
$sql_select = "SELECT jumlah_pembayaran FROM catatan_bayarmaba20242 WHERE id='$id'";
$result_select = $koneksi->query($sql_select);

if ($result_select->num_rows > 0) {
    $row = $result_select->fetch_assoc();
    $jumlah_pembayaran_sebelumnya = $row['jumlah_pembayaran'];

    // Perbarui pembayaran di database
    $sql_update = "UPDATE catatan_bayarmaba20242 SET jumlah_pembayaran='$jumlah_bayar' WHERE id='$id'";

    if ($koneksi->query($sql_update) === TRUE) {
        echo "Pembayaran berhasil diperbarui";
    } else {
        echo "Error: " . $sql_update . "<br>" . $koneksi->error;
    }
} else {
    echo "Error: Data tidak ditemukan";
}

$koneksi->close();
?>
