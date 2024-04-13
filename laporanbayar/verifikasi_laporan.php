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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">SALUT TANA TORAJA</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="../dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="../mahasiswa.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Mahasiswa
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../mahasiswa.php">Daftar Mahasiswa</a></li>
            <li><a class="dropdown-item" href="../tambah_data.php">Tambah Mahasiswa</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="./" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Laporan Pembayaran
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="./">Laporan Bayar</a></li>
            <li><a class="dropdown-item" href="./tambah_laporan.php">Tambah Laporan</a></li>
            <li><a class="dropdown-item active" href="">Verifikasi Laporan</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Mahasiswa Baru
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../maba/dashboard.php">Daftar Mahasiswa</a></li>
            <li><a class="dropdown-item" href="../maba/tambah_data.php">Tambah Mahasiswa</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="../cekstatus/pencarian.php">Cek Status Mahasiswa</a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn btn-warning text-dark fw-bold" href="../logout.php">Keluar</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-5">
    <h1 class="mb-4">Laporan Belum Diverifikasi</h1>

    <?php if (empty($laporanBelumDiverifikasi)) : ?>
        <p>Tidak ada laporan yang belum diverifikasi.</p>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
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
                                <a href="detail_verifikasi.php?id=<?php echo $laporan['id']; ?>" class="btn btn-primary">Verifikasi</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
