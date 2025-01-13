<?php
// Koneksi ke database
require_once("../../koneksi.php");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil data dari tabel mahasiswabaru20242
$sql_select = "SELECT NamaLengkap, JalurProgram, Jurusan FROM mahasiswabaru20242";
$result = $koneksi->query($sql_select);

if ($result->num_rows > 0) {
    // Loop melalui setiap baris data dan masukkan ke tabel catatan_bayarmaba20242
    while($row = $result->fetch_assoc()) {
        // Escape nilai-nilai yang diambil dari database
        $nama_lengkap = $koneksi->real_escape_string($row['NamaLengkap']);
        $jalur_program = $koneksi->real_escape_string($row['JalurProgram']);
        $jurusan = $koneksi->real_escape_string($row['Jurusan']);

        // Tentukan nilai default untuk admisi berdasarkan jalur_program
        if ($jalur_program == 'Reguler') {
            $admisi = 200000;
        } elseif ($jalur_program == 'RPL') {
            $admisi = 600000;
        } else {
            $admisi = 0; // Nilai default jika jalur_program tidak dikenal
        }

        $almamater = 200000;
        $salut = 350000;
        $spp = 0; // nilai default SPP, nanti akan di update sesuai input
        $total_bayar = $almamater + $salut + $spp;

        // Cek apakah data sudah ada di tabel catatan_bayarmaba20242
        $sql_check = "SELECT COUNT(*) as count FROM catatan_bayarmaba20242 
                      WHERE nama_lengkap='$nama_lengkap' AND jalur_program='$jalur_program' AND jurusan='$jurusan'";
        $check_result = $koneksi->query($sql_check);
        $check_row = $check_result->fetch_assoc();

        if ($check_row['count'] == 0) {
            // Masukkan data ke tabel catatan_bayarmaba20242
            $sql_insert = "INSERT INTO catatan_bayarmaba20242 (nama_lengkap, jalur_program, jurusan, admisi, almamater, salut, spp, total_bayar, jumlah_pembayaran) 
                           VALUES ('$nama_lengkap', '$jalur_program', '$jurusan', '$admisi', '$almamater', '$salut', '$spp', '$total_bayar', 0)";

            if ($koneksi->query($sql_insert) === TRUE) {
                echo "Data berhasil dipindahkan untuk $nama_lengkap<br>";
            } else {
                echo "Error: " . $sql_insert . "<br>" . $koneksi->error . "<br>";
            }
        } else {
            echo "Data untuk $nama_lengkap sudah ada di tabel tujuan<br>";
        }
    }
} else {
    echo "Tidak ada data di tabel mahasiswabaru20242";
}

$koneksi->close();
?>
