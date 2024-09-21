<?php
require_once "../koneksi.php"; // Koneksi ke database

// Cek apakah data POST ada
if (isset($_POST['nama_lengkap']) && isset($_POST['ukuran_baru'])) {
    $namaLengkap = $_POST['nama_lengkap'];
    $ukuranBaru = $_POST['ukuran_baru'];

    // Query untuk memperbarui ukuran baju di database
    $query = "UPDATE mahasiswabaru20242 SET UkuranBaju = ? WHERE NamaLengkap = ?";

    // Siapkan statement
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param('ss', $ukuranBaru, $namaLengkap); // ss artinya kedua parameter adalah string

    // Eksekusi query
    if ($stmt->execute()) {
        echo "Ukuran baju berhasil diperbarui.";
    } else {
        echo "Gagal memperbarui ukuran baju.";
    }

    $stmt->close();
} else {
    echo "Data tidak lengkap.";
}

$koneksi->close();
?>
