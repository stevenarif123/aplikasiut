<?php
// Buat koneksi ke database (menggunakan contoh koneksi)
require_once("koneksi.php");

// Check if session is not active, start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username']) || $_SESSION['peran'] != 'verifikator') {
    header("Location: ../login.php?error=1");
    exit; // Stop further execution
}
// Periksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Query untuk mengambil laporan yang belum diverifikasi (isVerifikasi = 1)
$sql = "SELECT * FROM laporanuangmasuk WHERE isVerifikasi = 0";
$result = $koneksi->query($sql);

// Inisialisasi array untuk menyimpan laporan yang belum diverifikasi
$laporanBelumDiverifikasi = [];

// Periksa apakah hasil query tidak kosong
if ($result->num_rows > 0) {
    // Ambil setiap baris hasil query dan masukkan ke dalam array
    while ($row = $result->fetch_assoc()) {
        $laporanBelumDiverifikasi[] = $row;
    }
}

// Tutup koneksi database
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Laporan</title>
</head>
<body>

<h1>Laporan Belum Diverifikasi</h1>

<?php if (empty($laporanBelumDiverifikasi)) : ?>
    <p>Tidak ada laporan yang belum diverifikasi.</p>
<?php else : ?>
    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Laporan</th>
                <th>Jenis Bayar</th>
                <th>Tanggal Input</th>
                <th>Nama Mahasiswa</th>
                <th>NIM</th>
                <th>Jurusan</th>
                <th>UT</th>
                <th>Pokjar</th>
                <th>Admin</th>
                <th>Catatan Khusus</th>
                <th>Metode Bayar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($laporanBelumDiverifikasi as $index => $laporan) : ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $laporan['KodeLaporan']; ?></td>
                    <td><?php echo $laporan['JenisBayar']; ?></td>
                    <td><?php echo $laporan['TanggalInput']; ?></td>
                    <td><?php echo $laporan['NamaMahasiswa']; ?></td>
                    <td><?php echo $laporan['Nim']; ?></td>
                    <td><?php echo $laporan['Jurusan']; ?></td>
                    <td><?php echo $laporan['Ut']; ?></td>
                    <td><?php echo $laporan['Pokjar']; ?></td>
                    <td><?php echo $laporan['Admin']; ?></td>
                    <td><?php echo $laporan['CatatanKhusus']; ?></td>
                    <td><?php echo $laporan['MetodeBayar']; ?></td>
                    <td>
                        <a href="detail_verifikasi.php?id=<?php echo $laporan['id']; ?>">Verifikasi</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
