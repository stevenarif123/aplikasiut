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
        header("Location: ../");
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
    "AKTIF" => "AKTIF",
    "CUTI" => "CUTI",
    "Lainnya" => "Lainnya"
];

$selectedJurusan = $mahasiswa['Jurusan'];

?>

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
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Mahasiswa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Edit Data Mahasiswa</h2>
        <form action="edit_data.php?No=<?php echo $no; ?>" method="post" class="space-y-4">
            <div>
                <label for="nim" class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIM</label>
                <input type="text" name="Nim" id="nim" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="041100000" value="<?php echo $mahasiswa['Nim']; ?>" required>
            </div>
            <div>
                <label for="jalur_program" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jalur Program</label>
                <select name="JalurProgram" id="jalur_program" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    <option value="RPL" <?php if ($mahasiswa['JalurProgram'] == "RPL") echo "selected"; ?>>RPL</option>
                    <option value="Reguler" <?php if ($mahasiswa['JalurProgram'] == "Reguler") echo "selected"; ?>>Reguler</option>
                </select>
            </div>
            <div>
                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                <input type="text" name="NamaLengkap" id="nama_lengkap" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="<?php echo $mahasiswa['NamaLengkap']; ?>" required>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tempat Lahir</label>
                    <input type="text" name="TempatLahir" id="tempat_lahir" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="<?php echo $mahasiswa['TempatLahir']; ?>" required>
                </div>
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Lahir</label>
                    <input type="date" name="TanggalLahir" id="tanggal_lahir" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="<?php echo $mahasiswa['TanggalLahir']; ?>" required>
                </div>
            </div>
            <div>
                <label for="nama_ibu_kandung" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Ibu Kandung</label>
                <input type="text" name="NamaIbuKandung" id="nama_ibu_kandung" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="<?php echo $mahasiswa['NamaIbuKandung']; ?>" required>
            </div>
            <div>
                <label for="nik" class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIK</label>
                <input type="text" name="NIK" id="nik" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="<?php echo $mahasiswa['NIK']; ?>" required>
            </div>
            <div>
                <label for="jurusan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jurusan</label>
                <select name="Jurusan" id="jurusan" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    <?php foreach ($jurusan as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php if ($selectedJurusan == $value) echo "selected"; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="nomor_hp" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor HP</label>
                <input type="text" name="NomorHP" id="nomor_hp" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="<?php echo $mahasiswa['NomorHP']; ?>" required>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" name="Email" id="email" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="<?php echo $mahasiswa['Email']; ?>" required>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    <input type="password" name="Password" id="password" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="<?php echo $mahasiswa['Password']; ?>" required>
                </div>
            </div>
            <div>
                <label for="agama" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Agama</label>
                <select name="Agama" id="agama" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    <?php foreach ($agama as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php if ($mahasiswa['Agama'] == $value) echo "selected"; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Kelamin</label>
                    <select name="JenisKelamin" id="jenis_kelamin" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <?php foreach ($jenis_kelamin as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php if ($mahasiswa['JenisKelamin'] == $value) echo "selected"; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="status_perkawinan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Perkawinan</label>
                    <select name="StatusPerkawinan" id="status_perkawinan" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <?php foreach ($status_perkawinan as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php if ($mahasiswa['StatusPerkawinan'] == $value) echo "selected"; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label for="nomor_hp_alternatif" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor HP Alternatif</label>
                <input type="text" name="NomorHPAlternatif" id="nomor_hp_alternatif" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="<?php echo $mahasiswa['NomorHPAlternatif']; ?>">
            </div>
            <div>
                <label for="nomor_ijazah" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Ijazah</label>
                <input type="text" name="NomorIjazah" id="nomor_ijazah" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="<?php echo $mahasiswa['NomorIjazah']; ?>">
            </div>
            <div>
                <label for="tahun_ijazah" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Ijazah</label>
                <input type="text" name="TahunIjazah" id="tahun_ijazah" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="<?php echo $mahasiswa['TahunIjazah']; ?>">
            </div>
            <div>
                <label for="nisn" class="block text-sm font-medium text-gray-700 dark:text-gray-300">NISN</label>
                <input type="text" name="NISN" id="nisn" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="<?php echo $mahasiswa['NISN']; ?>">
            </div>
            <div>
                <label for="layanan_paket_semester" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Layanan Paket Semester</label>
                <select name="LayananPaketSemester" id="layanan_paket_semester" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    <?php foreach ($layanan_paket_semester as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php if ($mahasiswa['LayananPaketSemester'] == $value) echo "selected"; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="status_input_sia" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Input Sia</label>
                <select name="STATUS_INPUT_SIA" id="status_input_sia" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    <?php foreach ($status_input_sia as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php if ($mahasiswa['STATUS_INPUT_SIA'] == $value) echo "selected"; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="submit" name="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Simpan</button>
            </div>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>