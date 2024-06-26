<?php
// Session status check
if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
  }
// Connect to the database
require_once "../koneksi.php";
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
$query = "SELECT * FROM mahasiswabaru WHERE No = ?";
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
    $status_input_sia = filter_input(INPUT_POST, 'STATUS_INPUT_SIA', FILTER_SANITIZE_STRING);

    // Bind parameters
// Prepare UPDATE query with placeholders
$updateQuery = "UPDATE mahasiswabaru SET 
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

// Prepare statement
$stmt = mysqli_prepare($koneksi, $updateQuery);

// Bind parameters
mysqli_stmt_bind_param($stmt, "sssssssssssssssssssssi",
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
    $no // No digunakan sebagai parameter terakhir
);

// Execute update
if (mysqli_stmt_execute($stmt)) {
    header("Location: dashboard.php");
    //var_dump (mysqli_stmt_execute($stmt)) ;
    exit;
} else {
    echo "Error updating data: " . mysqli_error($koneksi);
}
}
//debug
$agama = [
    "Islam" => "Islam",
    "Protestan" => "Protestan",
    "Katolik" => "Katolik",
    "Buddha" => "Buddha",
    "Hindu" => "Hindu",
    "Khonghucu" => "Khonghucu",
    // ... (add other options)
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
$sql = "SELECT * FROM prodi_admisi";
$result = $koneksi->query($sql);

// Simpan data jurusan dalam array
$daftarJurusan = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $daftarJurusan[] = $row["nama_program_studi"];
    }
}
mysqli_close($koneksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
        }
        .form-label {
            font-weight: bold;
        }
    </style>
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
          <a class="nav-link active" aria-current="page" href="../dashboard.php">Dashboard</a>
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
            <li><a class="dropdown-item active" href="../maba/dashboard.php">Daftar Mahasiswa</a></li>
            <li><a class="dropdown-item" href="../maba/tambah_data.php">Tambah Mahasiswa</a></li>
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
    <div class="container mt-5">
        <h1 class="mb-4">Edit Data Mahasiswa</h1>
        <p>Admin, <?php echo $user['nama_lengkap']; ?>!</p>
        <form action="edit_data.php?No=<?php echo $no; ?>" method="post">
            <div class="form-group">
                <label for="jalur_program" class="form-label">Jalur Program:</label>
                <select name="JalurProgram" id="jalur_program" class="form-control">
                    <option value="RPL" <?php if ($mahasiswa['JalurProgram'] == "RPL") echo "selected"; ?>>RPL</option>
                    <option value="Reguler" <?php if ($mahasiswa['JalurProgram'] == "Reguler") echo "selected"; ?>>Reguler</option>
                </select>
            </div>
            <div class="form-group">
                <label for="nama_lengkap" class="form-label">Nama Lengkap:</label>
                <input type="text" name="NamaLengkap" id="nama_lengkap" value="<?php echo $mahasiswa['NamaLengkap']; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="tempat_lahir" class="form-label">Tempat Lahir:</label>
                <input type="text" name="TempatLahir" id="tempat_lahir" value="<?php echo $mahasiswa['TempatLahir']; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir:</label>
                <input type="date" name="TanggalLahir" id="tanggal_lahir" value="<?php echo $mahasiswa['TanggalLahir']; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="nama_ibu_kandung" class="form-label">Nama Ibu Kandung:</label>
                <input type="text" name="NamaIbuKandung" id="nama_ibu_kandung" value="<?php echo $mahasiswa['NamaIbuKandung']; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="nik" class="form-label">NIK:</label>
                <input type="text" name="NIK" id="nik" value="<?php echo $mahasiswa['NIK']; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="jurusan" class="form-label">Jurusan:</label>
                <select name="Jurusan" id="jurusan" class="form-control">
                    <?php foreach ($daftarJurusan as $jurusan): ?>
                        <option value="<?php echo $jurusan; ?>" <?php if ($selectedJurusan == $jurusan) echo "selected"; ?>>
                            <?php echo $jurusan; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="NomorHP" class="form-label">Nomor HP:</label>
                <input type="text" name="NomorHP" id="nomor_hp" value="<?php echo $mahasiswa['NomorHP']; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="Email" class="form-label">Email:</label>
                <input type="email" name="Email" id="email" value="<?php echo $mahasiswa['Email']; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="Password" class="form-label">Password:</label>
                <input type="text" name="Password" id="password" class="form-control">
            </div>
            <div class="form-group">
                <label for="agama" class="form-label">Agama:</label>
                <select name="Agama" id="agama" class="form-control">
                    <?php foreach ($agama as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php if ($mahasiswa['Agama'] == $value) echo "selected"; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="JenisKelamin" class="form-label">Jenis Kelamin:</label>
                <select name="JenisKelamin" id="jenis_kelamin" class="form-control">
                    <?php foreach ($jenis_kelamin as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php if ($mahasiswa['JenisKelamin'] == $value) echo "selected"; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="StatusPerkawinan" class="form-label">Status Perkawinan:</label>
                <select name="StatusPerkawinan" id="status_perkawinan" class="form-select">
                    <?php foreach ($status_perkawinan as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php if ($mahasiswa['StatusPerkawinan'] == $value) echo "selected"; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="NomorHPAlternatif" class="form-label">Nomor HP Alternatif:</label>
                <input type="text" name="NomorHPAlternatif" id="nomor_hp_alternatif" value="<?php echo $mahasiswa['NomorHPAlternatif']; ?>" class="form-control">
            </div>
            <div class="mb-3">
                <label for="NomorIjazah" class="form-label">Nomor Ijazah:</label>
                <input type="text" name="NomorIjazah" id="nomor_ijazah" value="<?php echo $mahasiswa['NomorIjazah']; ?>" class="form-control">
            </div>
            <div class="mb-3">
                <label for="TahunIjazah" class="form-label">Tahun Ijazah:</label>
                <input type="text" name="TahunIjazah" id="tahun_ijazah" value="<?php echo $mahasiswa['TahunIjazah']; ?>" class="form-control">
            </div>
            <div class="mb-3">
                <label for="NISN" class="form-label">NISN:</label>
                <input type="text" name="NISN" id="nisn" value="<?php echo $mahasiswa['NISN']; ?>" class="form-control">
            </div>
            <div class="mb-3">
                <label for="LayananPaketSemester" class="form-label">Layanan Paket Semester:</label>
                <select name="LayananPaketSemester" id="layanan_paket_semester" class="form-select">
                    <?php foreach ($layanan_paket_semester as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php if ($mahasiswa['LayananPaketSemester'] == $value) echo "selected"; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="DiInputOleh" class="form-label">Di Input Oleh: <?php echo $mahasiswa['DiInputOleh']; ?></label>
            </div>
            <div class="mb-3">
                <label for="DiInputPada" class="form-label">Di Input Pada: <?php echo $mahasiswa['DiInputPada']; ?></label>
            </div>
            <div class="mb-3">
                <label for="DiInputPada" class="form-label">Terakhir di Edit Pada: <?php echo $mahasiswa['DiEditPada']; ?></label>
            </div>
            <div class="mb-3">
                <label for="STATUS_INPUT_SIA" class="form-label">Status Input SIA:</label>
                <select name="STATUS_INPUT_SIA" id="STATUS_INPUT_SIA" class="form-select">
                    <?php foreach ($status_input_sia as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php if ($mahasiswa['STATUS_INPUT_SIA'] == $value) echo "selected"; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
</html>