<?php
require_once "../koneksi.php";
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }

// Check if the user is authenticated
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

// Establish a database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$query = "SELECT * FROM admin WHERE username='$username'";

$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
if (!$result) {
  die("Query gagal: " . mysqli_error($conn));
}

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Sanitize and validate input data
    $jalur_program = $conn->real_escape_string(trim($_POST['JalurProgram']));
    $nama_lengkap = $conn->real_escape_string(trim($_POST['NamaLengkap']));
    $tempat_lahir = $conn->real_escape_string(trim($_POST['TempatLahir']));
    $tanggal_lahir = date('Y-m-d', strtotime($_POST['TanggalLahir']));
    $nama_ibu_kandung = $conn->real_escape_string(trim($_POST['NamaIbuKandung']));
    $nik = $conn->real_escape_string(trim($_POST['NIK']));
    $jurusan = $conn->real_escape_string(trim($_POST['Jurusan']));
    $nomor_hp = $conn->real_escape_string(trim($_POST['NomorHP']));
    $email = $conn->real_escape_string(trim($_POST['Email']));
    $password = $conn->real_escape_string(trim($_POST['Password']));
    $agama = $conn->real_escape_string(trim($_POST['Agama']));
    $jenis_kelamin = $conn->real_escape_string(trim($_POST['JenisKelamin']));
    $status_perkawinan = $conn->real_escape_string(trim($_POST['StatusPerkawinan']));
    $nomor_hp_alternatif = $conn->real_escape_string(trim($_POST['NomorHPAlternatif']));
    $nomor_ijazah = $conn->real_escape_string(trim($_POST['NomorIjazah']));
    $tahun_ijazah = $conn->real_escape_string(trim($_POST['TahunIjazah']));
    $nisn = $conn->real_escape_string(trim($_POST['NISN']));
    $layanan_paket_semester = $conn->real_escape_string(trim($_POST['LayananPaketSemester']));
    $di_input_oleh = $conn->real_escape_string(trim($user['nama_lengkap']));
    $status_input_sia = $conn->real_escape_string(trim($_POST['STATUS_INPUT_SIA']));

    // Prepare the SQL statement
// Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO mahasiswabaru ( 
    JalurProgram, 
    NamaLengkap, 
    TempatLahir, 
    TanggalLahir, 
    NamaIbuKandung, 
    NIK, 
    Jurusan, 
    NomorHP, 
    Email, 
    Password, 
    Agama, 
    JenisKelamin, 
    StatusPerkawinan, 
    NomorHPAlternatif, 
    NomorIjazah, 
    TahunIjazah, 
    NISN, 
    LayananPaketSemester, 
    DiInputOleh, 
    STATUS_INPUT_SIA) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    // Check for errors in preparing the statement
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

    // Bind parameters to the prepared statement
    $stmt->bind_param("ssssssssssssssssssss", 
    $jalur_program, 
    $nama_lengkap, 
    $tempat_lahir, 
    $tanggal_lahir, 
    $nama_ibu_kandung, 
    $nik, 
    $jurusan, 
    $nomor_hp, 
    $email, 
    $password, 
    $agama, 
    $jenis_kelamin, 
    $status_perkawinan, 
    $nomor_hp_alternatif, 
    $nomor_ijazah, 
    $tahun_ijazah, 
    $nisn, 
    $layanan_paket_semester, 
    $di_input_oleh, 
    $status_input_sia);

        // Execute the prepared statement
        if ($stmt->execute()) {
            // Redirect to the dashboard page after successful insertion
            header("Location: dashboard.php");
            exit;
        } else {
            // Display an error message
            $error_message = "Error: " . $stmt->error;
        }


    // Close the prepared statement
    $stmt->close();
}

$queryJurusan = "SELECT * FROM jurusan"; // Ganti 'daftar_jurusan' dengan nama tabel yang sesuai
$resultJurusan = mysqli_query($koneksi, $queryJurusan);

// Inisialisasi array untuk menyimpan daftar jurusan
$daftarJurusan = array();

// Periksa apakah query berhasil dieksekusi
if ($resultJurusan) {
    // Loop untuk mengambil setiap baris hasil query dan menyimpannya dalam array
    while ($row = mysqli_fetch_assoc($resultJurusan)) {
        $daftarJurusan[] = $row['nama_jurusan']; // Sesuaikan 'nama_jurusan' dengan nama kolom yang sesuai
    }
} else {
    // Jika query gagal dieksekusi, tampilkan pesan error
    echo "Error retrieving list of majors: " . mysqli_error($koneksi);
}

// Close the database connection
$conn->close();
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
            <li><a class="dropdown-item active" href="../maba/tambah_data.php">Tambah Mahasiswa</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="../cekstatus/pencarian.php">Cek Status Mahasiswa</a>
        </li>
        <!-- Tambahkan tombol log out di sini -->
        <li class="nav-item">
          <a class="nav-link btn btn-warning text-dark fw-bold" href="../logout.php">Keluar</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
    <div class="container mt-3">
        <h1 class="mb-4">Tambah Data Mahasiswa</h1>
        <form action="tambah_data.php" method="post">
            <div class="mb-3">
                <label for="jalur_program" class="form-label">Jalur Program:</label>
                <select name="JalurProgram" id="jalur_program" class="form-select" required>
                    <option value="RPL">RPL</option>
                    <option value="Reguler">Reguler</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap:</label>
                <input type="text" name="NamaLengkap" id="nama_lengkap" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="tempat_lahir" class="form-label">Tempat Lahir:</label>
                <input type="text" name="TempatLahir" id="tempat_lahir" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir:</label>
                <input type="date" name="TanggalLahir" id="tanggal_lahir" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="nama_ibu_kandung" class="form-label">Nama Ibu Kandung:</label>
                <input type="text" name="NamaIbuKandung" id="nama_ibu_kandung" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="nik" class="form-label">NIK:</label>
                <input type="text" name="NIK" id="nik" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="jurusan" class="form-label">Jurusan:</label>
                <select name="Jurusan" id="jurusan" class="form-select" required>
                    <?php foreach ($daftarJurusan as $major) : ?>
                        <option value="<?php echo $major; ?>"><?php echo $major; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="nomor_hp" class="form-label">Nomor HP:</label>
                <input type="text" name="NomorHP" id="nomor_hp" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="Email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password Mahasiswa:</label>
                <input type="text" name="Password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="agama" class="form-label">Agama:</label>
                <select name="Agama" id="agama" class="form-select" required>
                    <!-- Add options for religions here -->
                    <option value="Islam">Islam</option>
                    <option value="Kristen">Kristen</option>
                    <option value="Katolik">Katolik</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Buddha">Buddha</option>
                    <option value="Konghucu">Konghucu</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin:</label>
                <select name="JenisKelamin" id="jenis_kelamin" class="form-select" required>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="status_perkawinan" class="form-label">Status Perkawinan:</label>
                <select name="StatusPerkawinan" id="status_perkawinan" class="form-select" required>
                    <option value="Belum Menikah">Belum Menikah</option>
                    <option value="Menikah">Menikah</option>
                    <option value="Cerai Hidup">Cerai Hidup</option>
                    <option value="Cerai Mati">Cerai Mati</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="nomor_hp_alternatif" class="form-label">Nomor HP Alternatif:</label>
                <input type="text" name="NomorHPAlternatif" id="nomor_hp_alternatif" class="form-control">
            </div>
            <div class="mb-3">
                <label for="nomor_ijazah" class="form-label">Nomor Ijazah:</label>
                <input type="text" name="NomorIjazah" id="nomor_ijazah" class="form-control">
            </div>
            <div class="mb-3">
                <label for="tahun_ijazah" class="form-label">Tahun Ijazah:</label>
                <input type="text" name="TahunIjazah" id="tahun_ijazah" class="form-control">
            </div>
            <div class="mb-3">
                <label for="nisn" class="form-label">NISN:</label>
                <input type="text" name="NISN" id="nisn" class="form-control">
            </div>
            <div class="mb-3">
                <label for="layanan_paket_semester" class="form-label">Layanan Paket Semester:</label>
                <select name="LayananPaketSemester" id="layanan_paket_semester" class="form-select" required>
                    <option value="SIPAS">SIPAS</option>
                    <option value="NON SIPAS">NON SIPAS</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="status_input_sia" class="form-label">Status Input Sia:</label>
                <select name="STATUS_INPUT_SIA" id="status_input_sia" class="form-select" required>
                    <!-- Add options for semester package services here -->
                    <option value="Belum Terdaftar">Belum Terdaftar</option>
                    <option value="Input admisi">Input admisi</option>
                    <option value="Pengajuan Admisi">Pengajuan Admisi</option>
                    <option value="Berkas Kurang">Berkas Kurang</option>
                    <option value="Admisi Diterima">Admisi Diterima</option>
                </select>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
