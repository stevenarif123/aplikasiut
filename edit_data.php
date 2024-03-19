<?php

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Database connection details
$host = "localhost";
$user = "root";
$pass = "";
$db = "datamahasiswa";

// Connect to database
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Check for connection error
if (!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
}

//Check admin
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

    // Hash password securely if it's not empty
    if (!empty($_POST['Password'])) {
        $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);
    } else {
        $password = $mahasiswa['Password']; // Keep existing password
    }

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

    // Prepare statement
    $stmt = mysqli_prepare($koneksi, $updateQuery);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "sssssssssssssssssssssis",
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

// Close database connection
mysqli_close($koneksi);

// Define dropdown options
$jurusan = [
    "Pembangunan" => "Pembangunan",
    "Ekonomi Syariah" => "Ekonomi Syariah",
    // ... (add other options)
];

$agama = [
    "Islam" => "Islam",
    "Kristen" => "Kristen",
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Mahasiswa</title>
</head>
<body>
    <h1>Edit Data Mahasiswa</h1>
    <p>Admin, <?php echo $user['nama_lengkap']; ?>!</p>
    <form action="edit_data.php?No=<?php echo $no; ?>" method="post">
        <label for="jalur_program">Jalur Program:</label>
        <select name="JalurProgram" id="jalur_program">
            <option value="RPL" <?php if ($mahasiswa['JalurProgram'] == "RPL") echo "selected"; ?>>RPL</option>
            <option value="Reguler" <?php if ($mahasiswa['JalurProgram'] == "Reguler") echo "selected"; ?>>Reguler</option>
        </select>
        <br>

        <label for="nama_lengkap">Nama Lengkap:</label>
        <input type="text" name="NamaLengkap" id="nama_lengkap" value="<?php echo $mahasiswa['NamaLengkap']; ?>">
        <br>

        <label for="tempat_lahir">Tempat Lahir:</label>
        <input type="text" name="TempatLahir" id="tempat_lahir" value="<?php echo $mahasiswa['TempatLahir']; ?>">
        <br>

        <label for="tanggal_lahir">Tanggal Lahir:</label>
        <input type="date" name="TanggalLahir" id="tanggal_lahir" value="<?php echo $mahasiswa['TanggalLahir']; ?>">
        <br>

        <label for="nama_ibu_kandung">Nama Ibu Kandung:</label>
        <input type="text" name="NamaIbuKandung" id="nama_ibu_kandung" value="<?php echo $mahasiswa['NamaIbuKandung']; ?>">
        <br>

        <label for="nik">NIK:</label>
        <input type="text" name="NIK" id="nik" value="<?php echo $mahasiswa['NIK']; ?>">
        <br>

        <label for="jurusan">Jurusan:</label>
        <select name="Jurusan" id="jurusan">
            <?php foreach ($jurusan as $value => $label): ?>
                <option value="<?php echo $value; ?>" <?php if ($selectedJurusan == $value) echo "selected"; ?>>
                    <?php echo $label; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="NomorHP">Nomor HP:</label>
        <input type="text" name="NomorHP" id="nomor_hp" value="<?php echo $mahasiswa['NomorHP']; ?>">
        <br>

        <label for="Email">Email:</label>
        <input type="email" name="Email" id="email" value="<?php echo $mahasiswa['Email']; ?>">
        <br>

        <label for="Password">Password:</label>
        <input type="text" name="Password" id="password">
        <br>

        <label for="agama">Agama:</label>
        <select name="Agama" id="agama">
            <?php foreach ($agama as $value => $label): ?>
                <option value="<?php echo $value; ?>" <?php if ($mahasiswa['Agama'] == $value) echo "selected"; ?>>
                    <?php echo $label; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="JenisKelamin">Jenis Kelamin:</label>
        <select name="JenisKelamin" id="jenis_kelamin">
            <?php foreach ($jenis_kelamin as $value => $label): ?>
                <option value="<?php echo $value; ?>" <?php if ($mahasiswa['JenisKelamin'] == $value) echo "selected"; ?>>
                    <?php echo $label; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="StatusPerkawinan">Status Perkawinan:</label>
        <select name="StatusPerkawinan" id="status_perkawinan">
            <?php foreach ($status_perkawinan as $value => $label): ?>
                <option value="<?php echo $value; ?>" <?php if ($mahasiswa['StatusPerkawinan'] == $value) echo "selected"; ?>>
                    <?php echo $label; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="NomorHPAlternatif">Nomor HP Alternatif:</label>
        <input type="text" name="NomorHPAlternatif" id="nomor_hp_alternatif" value="<?php echo $mahasiswa['NomorHPAlternatif']; ?>">
        <br>

        <label for="NomorIjazah">Nomor Ijazah:</label>
        <input type="text" name="NomorIjazah" id="nomor_ijazah" value="<?php echo $mahasiswa['NomorIjazah']; ?>">
        <br>

        <label for="TahunIjazah">Tahun Ijazah:</label>
        <input type="text" name="TahunIjazah" id="tahun_ijazah" value="<?php echo $mahasiswa['TahunIjazah']; ?>">
        <br>

        <label for="NISN">NISN:</label>
        <input type="text" name="NISN" id="nisn" value="<?php echo $mahasiswa['NISN']; ?>">
        <br>

        <label for="LayananPaketSemester">Layanan Paket Semester:</label>
        <select name="LayananPaketSemester" id="layanan_paket_semester">
            <?php foreach ($layanan_paket_semester as $value => $label): ?>
                <option value="<?php echo $value; ?>" <?php if ($mahasiswa['LayananPaketSemester'] == $value) echo "selected"; ?>>
                    <?php echo $label; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="DiInputOleh">Di Input Oleh: <?php echo $mahasiswa['DiInputOleh']; ?></label>
        <br>

        <label for="DiInputPada">Di Input Pada: <?php echo $mahasiswa['DiInputPada']; ?></label>
        <br>

        <label for="DiInputPada">Terakhir di Edit Pada: <?php echo $mahasiswa['DiEditPada']; ?></label>
        <br>

        <label for="STATUS_INPUT_SIA">Status Input SIA:</label>
        <select name="STATUS_INPUT_SIA" id="STATUS_INPUT_SIA">
            <?php foreach ($status_input_sia as $value => $label): ?>
                <option value="<?php echo $value; ?>" <?php if ($mahasiswa['STATUS_INPUT_SIA'] == $value) echo "selected"; ?>>
                    <?php echo $label; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <input type="submit" name="submit" value="Simpan">
    </form>
</body>
</html>