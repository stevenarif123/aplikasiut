<?php
// Session status check
if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
  }
// Connect to the database
require_once "koneksi.php";
// Check for connection error
if (!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
}
// Check get admin data
$username = $_SESSION['username'];
$query = "SELECT * FROM admin WHERE username='$username'";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);
if (!$result) {
  die("Query gagal: " . mysqli_error($koneksi));
}

// Retrieve student ID from URL
$no = $_GET['No'];

// Prepare and execute query to fetch student data
$query = "SELECT * FROM mahasiswa WHERE No = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $no);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check for query error
if (!$result) {
    die("Error retrieving data: " . mysqli_error($koneksi));
}

$mahasiswa = mysqli_fetch_assoc($result);
date_default_timezone_set("Asia/Singapore");
// Check if form is submitted
if (isset($_POST['submit'])) {

    // Sanitize and validate input data
    $nim = filter_input(INPUT_POST, 'Nim', FILTER_SANITIZE_STRING);
    $jalur_program = filter_input(INPUT_POST, 'JalurProgram', FILTER_SANITIZE_STRING);
    $nama_lengkap = filter_input(INPUT_POST, 'NamaLengkap', FILTER_SANITIZE_STRING);
    $tempat_lahir = filter_input(INPUT_POST, 'TempatLahir', FILTER_SANITIZE_STRING);
    $tanggal_lahir = filter_input(INPUT_POST, 'TanggalLahir', FILTER_SANITIZE_STRING);
    $nama_ibu_kandung = filter_input(INPUT_POST, 'NamaIbuKandung', FILTER_SANITIZE_STRING);
    $nik = filter_input(INPUT_POST, 'NIK', FILTER_SANITIZE_STRING);
    $jurusan = filter_input(INPUT_POST, 'Jurusan', FILTER_SANITIZE_STRING);
    $nomor_hp = filter_input(INPUT_POST, 'NomorHP', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'Email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'Password', FILTER_SANITIZE_EMAIL);
    $agama = filter_input(INPUT_POST, 'Agama', FILTER_SANITIZE_STRING);
    $jenis_kelamin = filter_input(INPUT_POST, 'JenisKelamin', FILTER_SANITIZE_STRING);
    $status_perkawinan = filter_input(INPUT_POST, 'StatusPerkawinan', FILTER_SANITIZE_STRING);
    $nomor_hp_alternatif = filter_input(INPUT_POST, 'NomorHPAlternatif', FILTER_SANITIZE_STRING);
    $nomor_ijazah = filter_input(INPUT_POST, 'NomorIjazah', FILTER_SANITIZE_STRING);
    $tahun_ijazah = filter_input(INPUT_POST, 'TahunIjazah', FILTER_SANITIZE_NUMBER_INT);
    $nisn = filter_input(INPUT_POST, 'NISN', FILTER_SANITIZE_STRING);
    $layanan_paket_semester = filter_input(INPUT_POST, 'LayananPaketSemester', FILTER_SANITIZE_STRING);
    $di_input_oleh = $user['nama_lengkap'];
    $di_edit_pada = date("Y-m-d H:i:s");
    $status_input_sia = $koneksi->real_escape_string(trim($_POST['STATUS_INPUT_SIA']));


    // Prepare UPDATE query with placeholders
    $updateQuery = "UPDATE mahasiswa SET 
        Nim = ?, 
        JalurProgram = ?, 
        NamaLengkap = ?, 
        TempatLahir = ?, 
        TanggalLahir = ?, 
        NamaIbuKandung = ?, 
        NIK = ?, 
        Jurusan = ?, 
        NomorHP = ?, 
        Email = ?, 
        Password = ?, 
        Agama = ?, 
        JenisKelamin = ?, 
        StatusPerkawinan = ?, 
        NomorHPAlternatif = ?, 
        NomorIjazah = ?, 
        TahunIjazah = ?, 
        NISN = ?, 
        LayananPaketSemester = ?, 
        DiInputOleh = ?, 
        DiEditPada = ?,
        STATUS_INPUT_SIA = ?
        WHERE No = ?";

    //debug
   // echo "UPDATE Query: " . $updateQuery . "<br>";
    // Prepare statement
    $stmt = mysqli_prepare($koneksi, $updateQuery);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "sssssssssssssssssssssss",
    $nim,
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
    $di_edit_pada,
    $status_input_sia,
    $no
);
//echo $_POST['STATUS_INPUT_SIA'];
//echo "Bound Parameters: ";
//var_dump($nim, $jalur_program, $nama_lengkap, $tempat_lahir, $tanggal_lahir, $nama_ibu_kandung, $nik, $jurusan, $nomor_hp, $email, $password, $agama, $jenis_kelamin, $status_perkawinan, $nomor_hp_alternatif, $nomor_ijazah, $tahun_ijazah, $nisn, $layanan_paket_semester, $di_input_oleh, $di_edit_pada, $status_input_sia, $no);

    // Execute update
    if (mysqli_stmt_execute($stmt)) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error updating data: " . mysqli_error($koneksi);
    }

    // Close statement
    mysqli_stmt_close($stmt);
}
//debug


// Define dropdown options
$query_jurusan = "SELECT nama_jurusan FROM jurusan";
$result_jurusan = mysqli_query($koneksi, $query_jurusan);
$jurusan = [];
while($row = mysqli_fetch_assoc($result_jurusan)) {
    $jurusan[$row['nama_jurusan']] = $row['nama_jurusan'];
}
// Close database connection
mysqli_close($koneksi);

$agama = [
    "Islam" => "Islam",
    "Kristen" => "Kristen",
    "Katolik" => "Katolik",
    "Hindu" => "Hindu",
    "Buddha" => "Buddha",
    "Konghucu" => "Konghucu",
];

$jenis_kelamin = [
    "Laki-laki" => "Laki-laki",
    "Perempuan" => "Perempuan",
    // ... (add other options)
];

$status_perkawinan = [
    "Kawin" => "Kawin",
    "Belum Kawin" => "Belum Kawin",
    // ... (add other options)
];

$layanan_paket_semester = [
    "SIPAS" => "SIPAS",
    "NON SIPAS" => "NON SIPAS",
    // ... (add other options)
];
$status_input_sia = [
    "Belum Terdaftar" => "Belum Terdaftar",
    "Input admisi" => "Input admisi",
    "Pengajuan Admisi" => "Pengajuan Admisi",
    "Berkas Kurang" => "Berkas Kurang",
    "Admisi Diterima" => "Admisi Diterima"
];

$selectedJurusan = $mahasiswa['Jurusan'];

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
    <a class="navbar-brand" href="#">Navbar</a>
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
            <li><a class="dropdown-item" href="tambah_data.php">Tambah Mahasiswa</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="./laporanbayar" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Laporan Pembayaran
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="./laporanbayar/tambah_data.php">Tambah Laporan</a></li>
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
      </ul>
    </div>
  </div>
    </nav>
<div class="container-sm">
    <p class="isi">Admin : <?php echo $user['nama_lengkap']; ?>!</p>
    <form action="edit_data.php?No=<?php echo $no; ?>" method="post" class="row g-3">
        <div class="col-md-6">
            <label for="nim" class="form-label">NIM</label>
            <input type="text" class="form-control" name="Nim" id="nim" placeholder="041100000" value="<?php echo $mahasiswa['Nim']; ?>" required>
        </div>
        <div class="col-md-6">
            <label for="jalur_program" class="form-label">Jalur Program:</label>
            <select class="form-select" aria-label="JalurProgram" name="JalurProgram" id="jalur_program" required>
                <option value="RPL" <?php if ($mahasiswa['JalurProgram'] == "RPL") echo "selected"; ?>>RPL</option>
                <option value="Reguler" <?php if ($mahasiswa['JalurProgram'] == "Reguler") echo "selected"; ?>>Reguler</option>
            </select>
        </div>
        <div class="col-12">
            <label for="nama_lengkap">Nama Lengkap:</label>
            <input type="text" class="form-control" name="NamaLengkap" id="nama_lengkap" value="<?php echo $mahasiswa['NamaLengkap']; ?>" required>
        </div>
        <div class="col-6">
            <label for="tempat_lahir" class="form-label">Tempat Lahir:</label>
            <input type="text" class="form-control" name="TempatLahir" id="tempat_lahir" value="<?php echo $mahasiswa['TempatLahir']; ?>" required>
        </div>
        <div class="col-6">
            <label for="tanggal_lahir" class="form-label">Tanggal Lahir:</label>
            <input type="date" class="form-control" name="TanggalLahir" id="tanggal_lahir" value="<?php echo $mahasiswa['TanggalLahir']; ?>" required>
        </div>
        <div class="col-12">
            <label for="nama_ibu_kandung" class="form-label">Nama Ibu Kandung:</label>
            <input type="text" class="form-control" name="NamaIbuKandung" id="nama_ibu_kandung" value="<?php echo $mahasiswa['NamaIbuKandung']; ?>" required>
        </div>
        <div class="col-12">
            <label for="nik" class="form-label">NIK:</label>
            <input type="text" class="form-control" name="NIK" id="nik" value="<?php echo $mahasiswa['NIK']; ?>" required>
        </div>
        <div class="col-12">
            <label for="jurusan" class="form-label">Jurusan:</label>
            <select class="form-select" name="Jurusan" id="jurusan" required>
                <?php foreach ($jurusan as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php if ($selectedJurusan == $value) echo "selected"; ?>>
                        <?php echo $label; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <label for="nomor_hp" class="form-label">Nomor HP:</label>
            <input type="text" class="form-control" name="NomorHP" id="nomor_hp" value="<?php echo $mahasiswa['NomorHP']; ?>" required>
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="Email" id="email" value="<?php echo $mahasiswa['Email']; ?>" required>
        </div>
        <div class="col-md-6">
            <label for="password" class="form-label">Password</label>
            <input type="text" class="form-control" name="Password" id="password" value="<?php echo $mahasiswa['Password']; ?>" disabled>
        </div>
        <div class="col-12">
            <label for="agama" class="form-label">Agama:</label>
            <select class="form-select" name="Agama" id="agama" required>
                <?php foreach ($agama as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php if ($mahasiswa['Agama'] == $value) echo "selected"; ?>>
                        <?php echo $label; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin:</label>
            <select class="form-select" name="JenisKelamin" id="jenis_kelamin" required>
                <?php foreach ($jenis_kelamin as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php if ($mahasiswa['JenisKelamin'] == $value) echo "selected"; ?>>
                        <?php echo $label; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="status_perkawinan" ">Status Perkawinan:</label>
            <select class="form-select" name="StatusPerkawinan" id="status_perkawinan" required>
                <?php foreach ($status_perkawinan as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php if ($mahasiswa['StatusPerkawinan'] == $value) echo "selected"; ?>>
                        <?php echo $label; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label" for="nomor_hp_alternatif">Nomor HP Alternatif:</label>
            <input type="text" class="form-control" name="NomorHPAlternatif" id="nomor_hp_alternatif value="<?php echo $mahasiswa['NomorHPAlternatif']; ?>"">
        </div>
        <div class="col-12">
            <label class="form-label" for="nomor_ijazah">Nomor Ijazah:</label>
            <input type="text" class="form-control" name="NomorIjazah" id="nomor_ijazah" value="<?php echo $mahasiswa['NomorIjazah']; ?>">
        </div>
        <div class="col-12">
            <label class="form-label" for="tahun_ijazah">Tahun Ijazah:</label>
            <input type="text" class="form-control" name="TahunIjazah" id="tahun_ijazah" value="<?php echo $mahasiswa['TahunIjazah']; ?>">
        </div>
        <div class="col-12">
            <label for="nisn">NISN:</label>
            <input type="text" class="form-control" name="NISN" id="nisn" value="<?php echo $mahasiswa['NISN']; ?>">
        </div>
        <div class="col-12">
            <label class="form-label" for="layanan_paket_semester">Layanan Paket Semester:</label>
            <select name="LayananPaketSemester" id="layanan_paket_semester" class="form-select" required>
                <?php foreach ($layanan_paket_semester as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php if ($mahasiswa['LayananPaketSemester'] == $value) echo "selected"; ?>>
                        <?php echo $label; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="DiInputOleh">Di Input Oleh</label>
            <input type="text" class="form-control" name="DiInputOleh" id="di_input_oleh" value="<?php echo $mahasiswa['DiInputOleh']; ?>" disabled>
        </div>
        <div class="col-md-6">
            <label for="TanggalWaktuInput">Tanggal dan Waktu Input</label>
            <input type="text" class="form-control" name="TanggalWaktuInput" id="tanggal_waktu_input" value="<?php echo $mahasiswa['DiInputPada']; ?>" disabled>
        </div>
        <div class="col-md-6">
            <label for="TanggalInput">Tanggal Input</label>
            <input type="text" class="form-control" name="TanggalInput" id="tanggal_input" value="<?php echo $mahasiswa['DiInputPada']; ?>" disabled>
        </div>
        <div class="col-12">
            <label class="form-label" for="status_input_sia">Status Input Sia:</label>
            <select name="STATUS_INPUT_SIA" id="status_input_sia" class="form-select"s required>
                <option value="Belum Terdaftar" <?php if ($mahasiswa['STATUS_INPUT_SIA'] == 'Belum Terdaftar') echo "selected"; ?>>Belum Terdaftar</option>
                <option value="Admisi Diterima" <?php if ($mahasiswa['STATUS_INPUT_SIA'] == 'Admisi Diterima') echo "selected"; ?>>Admisi Diterima</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>