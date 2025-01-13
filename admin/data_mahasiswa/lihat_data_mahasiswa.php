<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit; // Jangan lupa untuk keluar setelah mengarahkan pengguna
}

// Koneksi ke database
require_once "../koneksi.php";

// Ambil id dari URL
$id = $_GET['No']; // Pastikan parameter 'No' sesuai dengan yang digunakan di URL

// Escape string untuk mencegah SQL Injection
$id = mysqli_real_escape_string($koneksi, $id);

// Query untuk mendapatkan data mahasiswa berdasarkan id
$query = "SELECT * FROM mahasiswa WHERE No='$id'"; // Tambahkan tanda kutip pada $id karena tipe datanya adalah string
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($koneksi));
}

// Periksa apakah ada baris data yang ditemukan
if (mysqli_num_rows($result) == 0) {
    die("Data Mahasiswa tidak ditemukan"); // Tambahkan penanganan jika data tidak ditemukan
}

// Ambil data mahasiswa dari hasil query
$mahasiswa = mysqli_fetch_assoc($result);

// Selanjutnya, Anda dapat menggunakan data mahasiswa ini untuk menampilkan informasi yang diinginkan di halaman web
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lihat Data</title>
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
          <a class="nav-link" aria-current="page" href="../dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle active" href="./index.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Mahasiswa
          </a>
          <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="./index.php">Daftar Mahasiswa</a></li>
            <li><a class="dropdown-item" href="./tambah_data.php">Tambah Mahasiswa</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="../laporanbayar" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Laporan Pembayaran
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../laporanbayar">Laporan Bayar</a></li>
            <li><a class="dropdown-item" href="../laporanbayar/tambah_laporan.php">Tambah Laporan</a></li>
            <li><a class="dropdown-item" href="../laporanbayar/verifikasi_laporan.php">Verifikasi Laporan</a></li>
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
            <a class="nav-link btn btn-warning text-dark fw-bold" href="logout.php">Keluar</a>
        </li>
      </ul>
    </div>
  </div>
    </nav>
<div class="container-sm mt-3">
    <h2>Detail Mahasiswa</h2>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>NIM</th>
                <td><?php echo $mahasiswa['Nim']; ?></td>
            </tr>
            <tr>
                <th>Jalur Program</th>
                <td><?php echo $mahasiswa['JalurProgram']; ?></td>
            </tr>
            <tr>
                <th>Nama Lengkap</th>
                <td><?php echo $mahasiswa['NamaLengkap']; ?></td>
            </tr>
            <tr>
                <th>Tempat, Tanggal Lahir</th>
                <td><?php echo $mahasiswa['TempatLahir'] . ', ' . $mahasiswa['TanggalLahir']; ?></td>
            </tr>
            <tr>
                <th>Nama Ibu Kandung</th>
                <td><?php echo $mahasiswa['NamaIbuKandung']; ?></td>
            </tr>
            <tr>
                <th>NIK</th>
                <td><?php echo $mahasiswa['NIK']; ?></td>
            </tr>
            <tr>
                <th>Jurusan</th>
                <td><?php echo $mahasiswa['Jurusan']; ?></td>
            </tr>
            <tr>
                <th>Nomor HP</th>
                <td><?php echo $mahasiswa['NomorHP']; ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo $mahasiswa['Email']; ?></td>
            </tr>
            <tr>
                <th>Password</th>
                <td><?php echo $mahasiswa['Password']; ?></td>
            </tr>
            <tr>
                <th>Agama</th>
                <td><?php echo $mahasiswa['Agama']; ?></td>
            </tr>
            <tr>
                <th>Jenis Kelamin</th>
                <td><?php echo $mahasiswa['JenisKelamin']; ?></td>
            </tr>
            <tr>
                <th>Status Perkawinan</th>
                <td><?php echo $mahasiswa['StatusPerkawinan']; ?></td>
            </tr>
            <tr>
                <th>Nomor HP Alternatif</th>
                <td><?php echo $mahasiswa['NomorHPAlternatif']; ?></td>
            </tr>
            <tr>
                <th>Nomor Ijazah</th>
                <td><?php echo $mahasiswa['NomorIjazah']; ?></td>
            </tr>
            <tr>
                <th>Tahun Ijazah</th>
                <td><?php echo $mahasiswa['TahunIjazah']; ?></td>
            </tr>
            <tr>
                <th>NISN</th>
                <td><?php echo $mahasiswa['NISN']; ?></td>
            </tr>
            <tr>
                <th>Layanan Paket Semester</th>
                <td><?php echo $mahasiswa['LayananPaketSemester']; ?></td>
            </tr>
            <tr>
                <th>Di Input Oleh</th>
                <td><?php echo $mahasiswa['DiInputOleh']; ?></td>
            </tr>
            <tr>
                <th>Tanggal dan Waktu Input</th>
                <td><?php echo $mahasiswa['DiInputPada']; ?></td>
            </tr>
            <tr>
                <th>Status Input Sia</th>
                <td><?php echo $mahasiswa['STATUS_INPUT_SIA']; ?></td>
            </tr>
        </tbody>
    </table>
    <a href="mahasiswa.php" class="btn btn-primary">Kembali</a>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>