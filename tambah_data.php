<?php

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }

// Check if the user is authenticated
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Database credentials (ideally, these should be stored in a separate configuration file or environment variables)
$host = "localhost";
$user = "root";
$pass = "";
$db = "datamahasiswa";

// Establish a database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$majors = array("Pembangunan",
    "Ekonomi Syariah",
    "Akuntansi",
    "Akuntansi Keuangan Publik",
    "Pariwisata",
    "Pendidikan Bahasa Dan Sastra Indonesia",
    "Pendidikan Bahasa Inggris",
    "Pendidikan Biologi",
    "Pendidikan Fisika",
    "Pendidikan Kimia",
    "Pendidikan Matematika",
    "Pendidikan Ekonomi",
    "Pendidikan Pancasila Dan Kewarganegaraan",
    "Teknologi Pendidikan",
    "PGSD",
    "PGPAUD",
    "PPG",
    "Statistika",
    "Matematika",
    "Biologi",
    "Teknologi Pangan",
    "Agribisnis",
    "Perencanaan Wilayah Dan Kota",
    "Sistem Informasi",
    "Kearsipan (D4)",
    "Perpajakan (D3)",
    "Perpustakaan",
    "Administrasi Publik",
    "Administrasi Bisnis",
    "Hukum",
    "Ilmu Pemerintahan",
    "Ilmu Komunikasi",
    "Ilmu Perpustakaan",
    "Sosiologi",
    "Sastra Inggris"
    );

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
    $nim = $conn->real_escape_string(trim($_POST['Nim']));
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
    $stmt = $conn->prepare("INSERT INTO mahasiswa (No, Nim, JalurProgram, NamaLengkap, TempatLahir, TanggalLahir, NamaIbuKandung, NIK, Jurusan, NomorHP, Email, Password, Agama, JenisKelamin, StatusPerkawinan, NomorHPAlternatif, NomorIjazah, TahunIjazah, NISN, LayananPaketSemester, DiInputOleh, STATUS_INPUT_SIA) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    // Check for errors in preparing the statement
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
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

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <p>Selamat datang, <?php echo $user['nama_lengkap']; ?>!</p>
    <title>Tambah Data Mahasiswa</title>
    <style>
        /* Add your CSS styles here */
    </style>
</head>
<body>
    <h1>Tambah Data Mahasiswa</h1>
    <?php if (isset($error_message)) : ?>
        <p><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="nim">NIM:</label>
        <input type="text" name="Nim" id="nim" required>
        <br>
        <label for="jalur_program">Jalur Program:</label>
        <select name="JalurProgram" id="jalur_program" required>
            <option value="RPL">RPL</option>
            <option value="Reguler">Reguler</option>
        </select>
        <br>
        <label for="nama_lengkap">Nama Lengkap:</label>
        <input type="text" name="NamaLengkap" id="nama_lengkap" required>
        <br>
        <label for="tempat_lahir">Tempat Lahir:</label>
        <input type="text" name="TempatLahir" id="tempat_lahir" required>
        <br>
        <label for="tanggal_lahir">Tanggal Lahir:</label>
        <input type="date" name="TanggalLahir" id="tanggal_lahir" required>
        <br>
        <label for="nama_ibu_kandung">Nama Ibu Kandung:</label>
        <input type="text" name="NamaIbuKandung" id="nama_ibu_kandung" required>
        <br>
        <label for="nik">NIK:</label>
        <input type="text" name="NIK" id="nik" required>
        <br>
        <label for="jurusan">Jurusan:</label>
        <select name="Jurusan" id="jurusan" required>
            <?php foreach ($majors as $major) : ?>
                <option value="<?php echo $major; ?>"><?php echo $major; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label for="nomor_hp">Nomor HP:</label>
        <input type="text" name="NomorHP" id="nomor_hp" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" name="Email" id="email" required>
        <br>
        <label for="password">Password Mahasiswa:</label>
        <input type="text" name="Password" id="password" required>
        <br>
        <label for="agama">Agama:</label>
        <select name="Agama" id="agama" required>
            <!-- Add options for religions here -->
            <option value="Islam">Islam</option>
            <option value="Kristen">Kristen</option>
            <option value="Katolik">Katolik</option>
            <option value="Hindu">Hindu</option>
            <option value="Buddha">Buddha</option>
            <option value="Konghucu">Konghucu</option>
        </select>
        <br>
        <label for="jenis_kelamin">Jenis Kelamin:</label>
        <select name="JenisKelamin" id="jenis_kelamin" required>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
        </select>
        <br>
        <label for="status_perkawinan">Status Perkawinan:</label>
        <select name="StatusPerkawinan" id="status_perkawinan" required>
            <option value="Belum Menikah">Belum Menikah</option>
            <option value="Menikah">Menikah</option>
            <option value="Cerai Hidup">Cerai Hidup</option>
            <option value="Cerai Mati">Cerai Mati</option>
        </select>
        <br>
        <label for="nomor_hp_alternatif">Nomor HP Alternatif:</label>
        <input type="text" name="NomorHPAlternatif" id="nomor_hp_alternatif">
        <br>
        <label for="nomor_ijazah">Nomor Ijazah:</label>
        <input type="text" name="NomorIjazah" id="nomor_ijazah">
        <br>
        <label for="tahun_ijazah">Tahun Ijazah:</label>
        <input type="text" name="TahunIjazah" id="tahun_ijazah">
        <br>
        <label for="nisn">NISN:</label>
        <input type="text" name="NISN" id="nisn">
        <br>
        <label for="layanan_paket_semester">Layanan Paket Semester:</label>
        <select name="LayananPaketSemester" id="layanan_paket_semester" required>
            <option value="SIPAS">SIPAS</option>
            <option value="NON SIPAS">NON SIPAS</option>
        </select>
        <br>
        <label for="status_input_sia">Status Input Sia:</label>
        <select name="STATUS_INPUT_SIA" id="status_input_sia" required>
            <!-- Add options for semester package services here -->
            <option value="Belum Terdaftar">Belum Terdaftar</option>
            <option value="Input admisi">Input admisi</option>
            <option value="Pengajuan Admisi">Pengajuan Admisi</option>
            <option value="Berkas Kurang">Berkas Kurang</option>
            <option value="Admisi Diterima">Admisi Diterima</option>
        </select>
        <br>
        <input type="submit" name="submit" value="Simpan">
    </form>
</body>
</html>