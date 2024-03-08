<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
  }
// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db = "datamahasiswa";

$koneksi = mysqli_connect($host, $user, $pass, $db);

// Ambil id dari URL
$id = $_GET['No']; // Pastikan parameter 'No' sesuai dengan yang digunakan di URL

// Query untuk mendapatkan data mahasiswa berdasarkan id
$query = "SELECT * FROM mahasiswa WHERE No=$id";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($koneksi));
}

$mahasiswa = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lihat Data Mahasiswa</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      line-height: 1.6;
      padding: 20px;
    }
    h1 {
      text-align: center;
    }
    .data-container {
      max-width: 800px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      grid-gap: 20px;
    }
    .data-item {
      margin-bottom: 20px;
    }
    .data-label {
      font-weight: bold;
      color: #666;
      margin-bottom: 5px;
    }
    .data-value {
      padding: 5px 10px;
      margin-top: 5px;
      color: #333;
      background-color: #d6d6d6; /* Ganti warna latar belakang di sini */
      border-radius: 4px;
    }
    a {
      display: block;
      text-align: center;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <h1>Lihat Data Mahasiswa</h1>

  <?php if ($mahasiswa): ?>
    <div class="data-container">
    <span class="data-label">NIM:</span>
    <span class="data-value"><?php echo $mahasiswa['Nim']; ?></span>

    <span class="data-label">Jalur Program:</span>
    <span class="data-value"><?php echo $mahasiswa['JalurProgram']; ?></span>

    <span class="data-label">Nama Lengkap:</span>
    <span class="data-value"><?php echo $mahasiswa['NamaLengkap']; ?></span>

    <span class="data-label">Tempat Lahir:</span>
    <span class="data-value"><?php echo $mahasiswa['TempatLahir']; ?></span>

    <span class="data-label">Tanggal Lahir:</span>
    <span class="data-value"><?php echo $mahasiswa['TanggalLahir']; ?></span>

    <span class="data-label">Nama Ibu Kandung:</span>
    <span class="data-value"><?php echo $mahasiswa['NamaIbuKandung']; ?></span>

    <span class="data-label">NIK:</span>
    <span class="data-value"><?php echo $mahasiswa['NIK']; ?></span>

    <span class="data-label">Jurusan:</span>
    <span class="data-value"><?php echo $mahasiswa['Jurusan']; ?></span>

    <span class="data-label">Nomor HP:</span>
    <span class="data-value"><?php echo $mahasiswa['NomorHP']; ?></span>

    <span class="data-label">Email:</span>
    <span class="data-value"><?php echo $mahasiswa['Email']; ?></span>

    <span class="data-label">Password:</span>
    <span class="data-value"><?php echo $mahasiswa['Password']; ?></span> <!-- Pastikan untuk menangani password dengan aman -->

    <span class="data-label">Agama:</span>
    <span class="data-value"><?php echo $mahasiswa['Agama']; ?></span>

    <span class="data-label">Jenis Kelamin:</span>
    <span class="data-value"><?php echo $mahasiswa['JenisKelamin']; ?></span>

    <span class="data-label">Status Perkawinan:</span>
    <span class="data-value"><?php echo $mahasiswa['StatusPerkawinan']; ?></span>

    <span class="data-label">Nomor HP Alternatif:</span>
    <span class="data-value"><?php echo $mahasiswa['NomorHPAlternatif']; ?></span>

    <span class="data-label">Nomor Ijazah:</span>
    <span class="data-value"><?php echo $mahasiswa['NomorIjazah']; ?></span>

    <span class="data-label">Tahun Ijazah:</span>
    <span class="data-value"><?php echo $mahasiswa['TahunIjazah']; ?></span>

    <span class="data-label">NISN:</span>
    <span class="data-value"><?php echo $mahasiswa['NISN']; ?></span>

    <span class="data-label">Layanan Paket Semester:</span>
    <span class="data-value"><?php echo $mahasiswa['LayananPaketSemester']; ?></span>

    <span class="data-label">Di Input Oleh:</span>
    <span class="data-value"><?php echo $mahasiswa['DiInputOleh']; ?></span>

    <span class="data-label">Di Input Pada:</span>
    <span class="data-value"><?php echo $mahasiswa['DiInputPada']; ?></span>

    <span class="data-label">Status Input SIA:</span>
    <span class="data-value"><?php echo $mahasiswa['STATUS_INPUT_SIA']; ?></span>
    </div>
    <!-- Anda dapat menambahkan lebih banyak div.data-container di sini untuk data lainnya -->
  <?php else: ?>
    <p>Data mahasiswa tidak ditemukan.</p>
  <?php endif; ?>

  <a href="dashboard.php">Kembali ke Dashboard</a>
</body>
</html>
