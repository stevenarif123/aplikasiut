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
    $jalur_program = $koneksi->real_escape_string(trim($_POST['JalurProgram']));
    $nama_lengkap = $koneksi->real_escape_string(trim($_POST['NamaLengkap']));
    $tempat_lahir = $koneksi->real_escape_string(trim($_POST['TempatLahir']));
    $tanggal_lahir = date('Y-m-d', strtotime($_POST['TanggalLahir']));
    $nama_ibu_kandung = $koneksi->real_escape_string(trim($_POST['NamaIbuKandung']));
    $nik = $koneksi->real_escape_string(trim($_POST['NIK']));
    $jurusan = $koneksi->real_escape_string(trim($_POST['Jurusan']));
    $nomor_hp = $koneksi->real_escape_string(trim($_POST['NomorHP']));
    $email = $koneksi->real_escape_string(trim($_POST['Email']));
    $password = $koneksi->real_escape_string(trim($_POST['Password']));
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
// Prepare the SQL statement
    $stmt = $koneksi->prepare("INSERT INTO mahasiswabaru ( 
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
            die("Prepare failed: " . $koneksi->error);
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

$daftarJurusan = array(
    "Manajemen",
    "Ekonomi Pembangunan",
    "Ekonomi Syariah",
    "Akuntansi",
    "Akuntansi Keuangan Publik",
    "Pariwisata",
    "Pendidikan Bahasa dan Sastra Indonesia",
    "Pendidikan Bahasa Inggris",
    "Pendidikan Biologi",
    "Pendidikan Fisika",
    "Pendidikan Kimia",
    "Pendidikan Matematika",
    "Pendidikan Ekonomi",
    "Pendidikan Pancasila dan Kewarganegaraan",
    "Teknologi Pendidikan",
    "Pendidikan Guru Sekolah Dasar (PGSD)",
    "Pendidikan Guru Pendidikan Anak Usia Dini (PGPAUD)",
    "Program Pendidikan Profesi Guru (PPG)",
    "Pendidikan Agama Islam (PAI)",
    "Statistika",
    "Matematika",
    "Biologi",
    "Teknologi Pangan",
    "Agribisnis",
    "Perencanaan Wilayah dan Kota",
    "Sistem Informasi",
    "Sains Data",
    "Kearsipan (D4)",
    "Perpajakan (D3)",
    "Administrasi Publik (S1)",
    "Administrasi Bisnis (S1)",
    "Hukum (S1)",
    "Ilmu Pemerintahan (S1)",
    "Ilmu Komunikasi (S1)",
    "Ilmu Perpustakaan (S1)",
    "Sosiologi (S1)",
    "Sastra Inggris (S1)",
    "Perpajakan (S1)"
);


// Close the database connection
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .nav-item:hover .dropdown-menu {
            display: block;
        }
        .box-form {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .box-form-image {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: -1;
            opacity: 0.2;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-gray-200 border-b border-gray-300 shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">SALUT TANA TORAJA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="../dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Mahasiswa
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="../mahasiswa.php">Daftar Mahasiswa</a></li>
                        <li><a class="dropdown-item" href="../tambah_data.php">Tambah Mahasiswa</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown1" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Laporan Pembayaran
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown1">
                        <li><a class="dropdown-item" href="../laporanbayar">Laporan Bayar</a></li>
                        <li><a class="dropdown-item" href="../laporanbayar/tambah_laporan.php">Tambah Laporan</a></li>
                        <li><a class="dropdown-item" href="../laporanbayar/verifikasi_laporan.php">Verifikasi Laporan</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Mahasiswa Baru
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown2">
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

<div class="container-sm mt-5 relative">
    <img src="path/to/your/background/image.jpg" alt="Background Image" class="absolute inset-0 box-form-image">
    <div class="box-form">
        <h1 class="mb-4 text-2xl">Tambah Data Mahasiswa</h1>
        <form action="tambah_data.php" method="post">
            <div class="grid gap-4">
                <div class="w-1/2 mb-1">
                    <label for="jalur_program" class="form-label">Jalur Program:</label>
                    <select name="JalurProgram" id="jalur_program" class="form-select" required>
                        <option value="" disabled selected>Silahkan Pilih Jalur Program</option>
                        <option value="RPL">RPL</option>
                        <option value="Reguler">Reguler</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap:</label>
                    <input type="text" name="NamaLengkap" id="nama_lengkap" class="form-control" required>
                </div>
                <div class="mb-1 flex flex-wrap -mx-3">
                    <div class="w-1/2 px-3 mb-1">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir:</label>
                        <input type="text" name="TempatLahir" id="tempat_lahir" class="form-control" required>
                    </div>
                    <div class="w-1/2 px-3 mb-1">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir:</label>
                        <input type="date" name="TanggalLahir" id="tanggal_lahir" class="form-control" required>
                    </div>
                </div>
                <div class="mb-1">
                    <label for="nama_ibu_kandung" class="form-label">Nama Ibu Kandung:</label>
                    <input type="text" name="NamaIbuKandung" id="nama_ibu_kandung" class="form-control" required>
                </div>
                <div class="mb-1">
                    <label for="nik" class="form-label">NIK:</label>
                    <input type="text" name="NIK" id="nik" class="form-control" required>
                </div>
                <div class="mb-1">
                    <label for="jurusan" class="form-label">Jurusan:</label>
                    <select name="Jurusan" id="jurusan" class="form-select" required>
                        <option value="" disabled selected>Pilih Jurusan</option>
                        <?php foreach ($daftarJurusan as $major) : ?>
                            <option value="<?php echo $major; ?>"><?php echo $major; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-1">
                    <label for="nomor_hp" class="form-label">Nomor HP:</label>
                    <input type="text" name="NomorHP" id="nomor_hp" class="form-control" required>
                </div>
                <div class="mb-1 flex flex-wrap -mx-3">
                    <div class="w-1/2 px-3 mb-6">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="Email" id="email" class="form-control" required>
                    </div>
                    <div class="w-1/2 px-3 mb-6">
                        <label for="password" class="form-label">Password Mahasiswa:</label>
                        <input type="password" name="Password" id="password" class="form-control" required>
                    </div>
                </div>
                    <div class="mb-1">
                        <label for="agama" class="form-label">Agama:</label>
                        <select name="Agama" id="agama" class="form-select" required>
                            <option value="" disabled selected>Silahkan Pilih Agama</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                    </div>
                <div class="mb-1 flex flex-wrap -mx-3">
                    <div class="w-1/2 px-3 mb-1">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin:</label>
                        <select name="JenisKelamin" id="jenis_kelamin" class="form-select" required>
                        <option value="" disabled selected>Silahkan Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>  
                    </div>
                    <div class="w-1/2 px-3 mb-1">
                    <label for="status_perkawinan" class="form-label">Status Perkawinan:</label>
                        <select name="StatusPerkawinan" id="status_perkawinan" class="form-select" required>
                            <option value="" disabled selected>Silahkan Pilih Status Kawin</option>
                            <option value="Belum Menikah">Belum Menikah</option>
                            <option value="Menikah">Menikah</option>
                            <option value="Cerai Hidup">Cerai Hidup</option>
                            <option value="Cerai Mati">Cerai Mati</option>
                        </select>
                    </div>
                </div>
                <div class="mb-1">
                    <label for="nomor_hp_alternatif" class="form-label">Nomor HP Alternatif:</label>
                    <input type="text" name="NomorHPAlternatif" id="nomor_hp_alternatif" class="form-control">
                </div>
                <div class="mb-1 flex flex-wrap -mx-3">
                    <div class="w-1/3 px-3 mb-1">
                        <label for="nomor_ijazah" class="form-label">Nomor Ijazah:</label>
                        <input type="text" name="NomorIjazah" id="nomor_ijazah" class="form-control">
                    </div>
                    <div class="w-1/3 px-3 mb-1">
                        <label for="tahun_ijazah" class="form-label">Tahun Ijazah:</label>
                        <input type="text" name="TahunIjazah" id="tahun_ijazah" class="form-control">
                    </div>
                    <div class="w-1/3 px-3mb-1">
                        <label for="nisn" class="form-label">NISN:</label>
                        <input type="text" name="NISN" id="nisn" class="form-control">
                    </div>
                </div>

                <div class="mb-1">
                    <label for="layanan_paket_semester" class="form-label">Layanan Paket Semester:</label>
                    <select name="LayananPaketSemester" id="layanan_paket_semester" class="form-select" required>
                    <option value="" disabled selected>Silahkan Pilih Layanan Paket Semester</option>
                        <option value="SIPAS">SIPAS</option>
                        <option value="NON SIPAS">NON SIPAS</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label for="status_input_sia" class="form-label">Status Input Sia:</label>
                    <select name="STATUS_INPUT_SIA" id="status_input_sia" class="form-select" required>
                        <!-- Add options for semester package services here -->
                        <option value="" disabled selected>Silahkan Pilih Status Input di SIA</option>
                        <option value="Belum Terdaftar">Belum Terdaftar</option>
                        <option value="Input admisi">Input admisi</option>
                        <option value="Pengajuan Admisi">Pengajuan Admisi</option>
                        <option value="Berkas Kurang">Berkas Kurang</option>
                        <option value="Admisi Diterima">Admisi Diterima</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script>
    // Simple animation for the submit button
    anime({
        targets: '.btn',
        duration: 1000,
        loop: true
    });

    // Check if the form submission was successful
    if (window.location.href.includes('success')) {
        anime({
            targets: '.box-form',
            scale: 1.1,
            duration: 1000,
            elasticity: 0.5,
            complete: function() {
                alert('Data mahasiswa berhasil ditambahkan!');
            }
        });
    }
</script>

</body>
</html>
