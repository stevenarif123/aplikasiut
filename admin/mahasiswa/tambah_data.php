<?php
require_once "koneksi.php";

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }

// Check if the user is authenticated
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check connection
if ($koneksi->connect_error) {
    die("Connection failed: " . $koneksi->connect_error);
}

$query = "SELECT nama_jurusan FROM jurusan";
$result = mysqli_query($koneksi, $query);
$majors = array();
while($row = mysqli_fetch_assoc($result)) {
    $majors[] = $row['nama_jurusan'];
}

$username = $_SESSION['username'];

$query = "SELECT * FROM admin WHERE username='$username'";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);

if (!$result) {
  die("Query gagal: " . mysqli_error($koneksi));
}

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Sanitize and validate input data
    $nim = $koneksi->real_escape_string(trim($_POST['Nim']));
    $jalur_program = $koneksi->real_escape_string(trim($_POST['JalurProgram']));
    $nama_lengkap = $koneksi->real_escape_string(trim($_POST['NamaLengkap']));
    $tempat_lahir = $koneksi->real_escape_string(trim($_POST['TempatLahir']));
    $tanggal_lahir = date('Y-m-d', strtotime($_POST['TanggalLahir']));
    $nama_ibu_kandung = $koneksi->real_escape_string(trim($_POST['NamaIbuKandung']));
    $nik = $koneksi->real_escape_string(trim($_POST['NIK']));
    $jurusan = $koneksi->real_escape_string(trim($_POST['Jurusan']));
    $nomor_hp = $koneksi->real_escape_string(trim($_POST['NomorHP']));
    $email = $koneksi->real_escape_string(trim($_POST['Email']));
    // Generate password menggunakan tanggal lahir dan menambahkan karakter khusus
    $password = '@'.date('dmY', strtotime($tanggal_lahir)).'Ut';
    $agama = $koneksi->real_escape_string(trim($_POST['Agama']));
    $jenis_kelamin = $koneksi->real_escape_string(trim($_POST['JenisKelamin']));
    $status_perkawinan = $koneksi->real_escape_string(trim($_POST['StatusPerkawinan']));
    $nomor_hp_alternatif = $koneksi->real_escape_string(trim($_POST['NomorHPAlternatif']));
    $nomor_ijazah = $koneksi->real_escape_string(trim($_POST['NomorIjazah']));
    $tahun_ijazah = $koneksi->real_escape_string(trim($_POST['TahunIjazah']));
    $nisn = $koneksi->real_escape_string(trim($_POST['NISN']));
    $layanan_paket_semester = $koneksi->real_escape_string(trim($_POST['LayananPaketSemester']));
    $di_input_oleh = $koneksi->real_escape_string(trim($user['nama_lengkap']));
    $status_input_sia = $koneksi->real_escape_string(trim($_POST['STATUS_INPUT_SIA']));

    // Prepare the SQL statement
    $stmt = $koneksi->prepare("INSERT INTO mahasiswa (No, 
    Nim, 
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
    STATUS_INPUT_SIA) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    // Check for errors in preparing the statement
        if (!$stmt) {
            die("Prepare failed: " . $koneksi->error);
        }

    // Bind parameters to the prepared statement
    $stmt->bind_param("sssssssssssssssssssss", $nim, $jalur_program, $nama_lengkap, $tempat_lahir, $tanggal_lahir, $nama_ibu_kandung, $nik, $jurusan, $nomor_hp, $email, $password, $agama, $jenis_kelamin, $status_perkawinan, $nomor_hp_alternatif, $nomor_ijazah, $tahun_ijazah, $nisn, $layanan_paket_semester, $di_input_oleh, $status_input_sia);

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

// Close the database koneksiection
$koneksi->close();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Mahasiswa</title>
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
          <a class="nav-link" aria-current="page" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle active" href="mahasiswa.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Mahasiswa
          </a>
          <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="mahasiswa.php">Daftar Mahasiswa</a></li>
            <li><a class="dropdown-item active" href="tambah_data.php">Tambah Mahasiswa</a></li>
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
        <li class="nav-item">
            <a class="nav-link btn btn-warning text-dark fw-bold" href="logout.php">Keluar</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container-md mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title text-center">Tambah Data Mahasiswa</h4>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-3">
                            <label for="nim" class="form-label">NIM</label>
                            <input type="text" class="form-control" name="Nim" id="nim" placeholder="041100000" required>
                        </div>
                        <div class="mb-3">
                            <label for="jalur_program" class="form-label">Jalur Program:</label>
                            <select class="form-select" aria-label="JalurProgram" name="JalurProgram" id="jalur_program" required>
                                <option value="" disabled selected>Silahkan Pilih Jalur Programs</option>
                                <option value="RPL">RPL</option>
                                <option value="Reguler">Reguler</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nama_lengkap">Nama Lengkap:</label>
                            <input type="text" class="form-control" name="NamaLengkap" id="nama_lengkap" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tempat_lahir" class="form-label">Tempat Lahir:</label>
                                <input type="text" class="form-control" name="TempatLahir" id="tempat_lahir" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir:</label>
                                <input type="date" class="form-control" name="TanggalLahir" id="tanggal_lahir" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="nama_ibu_kandung" class="form-label">Nama Ibu Kandung:</label>
                            <input type="text" class="form-control" name="NamaIbuKandung" id="nama_ibu_kandung" required>
                        </div>
                        <div class="mb-3">
                            <label for="nik" class="form-label">NIK:</label>
                            <input type="text" class="form-control" name="NIK" id="nik" required>
                        </div>
                        <div class="mb-3">
                            <label for="jurusan" class="form-label">Jurusan:</label>
                            <select class="form-select" name="Jurusan" id="jurusan" required>
                                <option value="" disabled selected>Silahkan Pilih Jurusan</option>
                                <?php foreach ($majors as $major) { ?>
                                    <option value="<?php echo $major; ?>"><?php echo $major; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nomor_hp" class="form-label">Nomor HP:</label>
                            <input type="text" class="form-control" name="NomorHP" id="nomor_hp" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="Email" id="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="text" class="form-control" name="Password" id="password" disabled>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="agama" class="form-label">Agama:</label>
                            <select class="form-select" name="Agama" id="agama" required>
                                <option value="" disabled selected>Silahkan Pilih Agama</option>
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin:</label>
                                <select class="form-select" name="JenisKelamin" id="jenis_kelamin" required>
                                    <option value="" disabled selected>Silahkan Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="status_perkawinan">Status Perkawinan:</label>
                                <select class="form-select" name="StatusPerkawinan" id="status_perkawinan" required>
                                    <option value="" disabled selected>Silahkan Pilih Status Perkawinan</option>
                                    <option value="Belum Menikah">Belum Menikah</option>
                                    <option value="Menikah">Menikah</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="nomor_hp_alternatif">Nomor HP Alternatif:</label>
                            <input type="text" class="form-control" name="NomorHPAlternatif" id="nomor_hp_alternatif">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="nomor_ijazah">Nomor Ijazah:</label>
                            <input type="text" class="form-control" name="NomorIjazah" id="nomor_ijazah">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="tahun_ijazah">Tahun Ijazah:</label>
                            <input type="text" class="form-control" name="TahunIjazah" id="tahun_ijazah">
                        </div>
                        <div class="mb-3">
                            <label for="nisn">NISN:</label>
                            <input type="text" class="form-control" name="NISN" id="nisn">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="layanan_paket_semester">Layanan Paket Semester:</label>
                            <select name="LayananPaketSemester" id="layanan_paket_semester" class="form-select" required>
                                <option value="" disabled selected>Pilih Layanan Paket Semester</option>
                                <option value="SIPAS">SIPAS</option>
                                <option value="NON SIPAS">NON SIPAS</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="status_input_sia">Status Input Sia:</label>
                            <select name="STATUS_INPUT_SIA" id="status_input_sia" class="form-select" required>
                                <option value="" disabled selected>Pilih Status Input Sia</option>
                                <option value="Belum Terdaftar">CUTI</option>
                                <option value="Admisi Diterima">AKTIF</option>
                                <option value="Admisi Diterima">ALUMNI</option>
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>