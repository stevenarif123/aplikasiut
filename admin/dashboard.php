<?php
// Start session if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"s crossorigin="anonymous">
  </head>
  <body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">SALUT TANA TORAJA</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="mahasiswa.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Mahasiswa
          </a>
          <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="mahasiswa.php">Daftar Mahasiswa</a></li>
            <li><a class="dropdown-item" href="tambah_data.php">Tambah Mahasiswa</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="./laporanbayar" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Laporan Pembayaran
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="./laporanbayar">Laporan Bayar</a></li>
            <li><a class="dropdown-item" href="./laporanbayar/tambah_laporan.php">Tambah Laporan</a></li>
            <li><a class="dropdown-item" href="./laporanbayar/verifikasi_laporan.php">Verifikasi Laporan</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Mahasiswa Baru
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="./maba/dashboard.php">Daftar Mahasiswa</a></li>
            <li><a class="dropdown-item" href="./maba/tambah_data.php">Tambah Mahasiswa</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="./cekstatus/pencarian.php">Cek Status Mahasiswa</a>
        </li>
        <!-- Tambahkan tombol log out di sini -->
        <li class="nav-item">
          <a class="nav-link btn btn-warning text-dark fw-bold" href="logout.php">Keluar</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>