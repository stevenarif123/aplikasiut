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
$query = "SELECT * FROM mahasiswabaru20242 WHERE No = ?";
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
    $nama_lengkap = $_POST['NamaLengkap'];
    $tempat_lahir = $_POST['TempatLahir'];
    $tanggal_lahir = $_POST['TanggalLahir'];
    $nama_ibu_kandung = $_POST['NamaIbuKandung'];
    $nik = $_POST['NIK'];
    $jurusan = $_POST['Jurusan'];
    $nomor_hp = $_POST['NomorHP'];
    $email = filter_input(INPUT_POST, 'Email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['Password'];
    $agama = $_POST['Agama'];
    $jenis_kelamin = $_POST['JenisKelamin'];
    $status_perkawinan = $_POST['StatusPerkawinan'];
    $nomor_hp_alternatif = $_POST['NomorHPAlternatif'];
    $nomor_ijazah = $_POST['NomorIjazah'];
    $tahun_ijazah = filter_input(INPUT_POST, 'TahunIjazah', FILTER_SANITIZE_NUMBER_INT);
    $nisn = $_POST['NISN'];
    $layanan_paket_semester = $_POST['LayananPaketSemester'];
    $di_input_oleh = $user['nama_lengkap'];
    $di_edit_pada = date("Y-m-d H:i:s");
    $status_input_sia = $_POST['STATUS_INPUT_SIA'];
    $ukuranbaju = $_POST['UkuranBaju'];

    // Prepare UPDATE query with placeholders
    $updateQuery = "UPDATE mahasiswabaru20242 SET 
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
        STATUS_INPUT_SIA = ?,
		UkuranBaju = ?
        WHERE No = ?";

    // Prepare statement
    $stmt = mysqli_prepare($koneksi, $updateQuery);

    // Bind parameters
    mysqli_stmt_bind_param(
        $stmt,
        "ssssssssssssssssssssssi",
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
        $ukuranbaju,
        $no // No digunakan sebagai parameter terakhir
    );

    // Execute update
    if (mysqli_stmt_execute($stmt)) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error updating data: " . mysqli_error($koneksi);
    }
}

// Retrieve list of majors (Jurusan) from the database
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
    "Tidak Kawin" => "Tidak Kawin",
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
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .nav-item:hover .dropdown-menu {
            display: block;
        }

        .box-form {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
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
    <div class="container mt-5 jrkctn">
        <div class="box-form">
            <h1 class="text-center mb-4 text-2xl jrk">Edit Data Mahasiswa</h1>
            <p>Admin, <?php echo htmlspecialchars($user['nama_lengkap'], ENT_QUOTES, 'UTF-8'); ?>!</p>
            <form action="edit_data.php?No=<?php echo $no; ?>" method="post">
                <div class="center grid gap-4 jrk">
                    <div class="col w-1/2 mb-1">
                        <label for="jalur_program">Jalur Program:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="jalur_program_rpl" name="JalurProgram" value="RPL" <?php if ($mahasiswa['JalurProgram'] == "RPL") echo "checked"; ?> required>
                            <label class="form-check-label" for="jalur_program_rpl">RPL</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="jalur_program_reguler" name="JalurProgram" value="Reguler" <?php if ($mahasiswa['JalurProgram'] == "Reguler") echo "checked"; ?>required>
                            <label class="form-check-label" for="jalur_program_reguler">Reguler</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap:</label>
                        <input type="text" name="NamaLengkap" id="nama_lengkap" value="<?php echo $mahasiswa['NamaLengkap']; ?>" class="form-control">
                    </div>
                    <div class="mb-1 flex flex-wrap -mx-3">
                        <div class="w-1/2 px-3 mb-1">
                            <label for="tempat_lahir" class="form-label">Tempat Lahir:</label>
                            <input type="text" name="TempatLahir" id="tempat_lahir" class="form-control" value="<?php echo $mahasiswa['TempatLahir']; ?>" required>
                        </div>
                        <div class="w-1/2 px-3 mb-1">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir:</label>
                            <input type="date" name="TanggalLahir" id="tanggal_lahir" class="form-control" value="<?php echo $mahasiswa['TanggalLahir']; ?>" required>
                        </div>
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
                            <?php foreach ($daftarJurusan as $nama_jurusan) : ?>
                                <option value="<?php echo $nama_jurusan; ?>" <?php if ($selectedJurusan == $nama_jurusan) echo "selected"; ?>>
                                    <?php echo $nama_jurusan; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="NomorHP" class="form-label">Nomor HP:</label>
                        <input type="text" name="NomorHP" id="nomor_hp" value="<?php echo $mahasiswa['NomorHP']; ?>" class="form-control">
                    </div>
                    <div class="mb-1 flex flex-wrap -mx-3">
                        <div class="w-1/2 px-3 mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="Email" id="email" class="form-control" value="<?php echo $mahasiswa['Email']; ?>" required>
                        </div>
                        <div class="w-1/2 px-3 mb-3">
                            <label for="password" class="form-label">Password Mahasiswa:</label>
                            <input type="text" name="Password" id="password" class="form-control" value="<?php echo $mahasiswa['Password']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="agama" class="form-label">Agama:</label>
                        <select name="Agama" id="agama" class="form-control">
                            <?php foreach ($agama as $value => $label) : ?>
                                <option value="<?php echo $value; ?>" <?php if ($mahasiswa['Agama'] == $value) echo "selected"; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w-1/2 mb-1">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="jenis_kelamin_laki" name="JenisKelamin" value="Laki-laki" <?php if ($mahasiswa['JenisKelamin'] == "Laki-laki") echo "checked"; ?> required>
                            <label class="form-check-label" for="jenis_kelamin_laki">Laki-laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="jenis_kelamin_perempuan" name="JenisKelamin" value="Perempuan" <?php if ($mahasiswa['JenisKelamin'] == "Perempuan") echo "checked"; ?> required>
                            <label class="form-check-label" for="jenis_kelamin_perempuan">Perempuan</label>
                        </div>
                    </div>
                    <div class="w-1/2 mb-1">
                        <label for="status_perkawinan" class="form-label">Status Perkawinan:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="status_perkawinan_tidak_kawin" name="StatusPerkawinan" value="Tidak Kawin" <?php if ($mahasiswa['StatusPerkawinan'] == "Tidak Kawin") echo "checked"; ?> required>
                            <label class="form-check-label" for="status_perkawinan_tidak_kawin">Tidak Kawin</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="status_perkawinan_kawin" name="StatusPerkawinan" value="Kawin" <?php if ($mahasiswa['StatusPerkawinan'] == "Kawin") echo "checked"; ?> required>
                            <label class="form-check-label" for="status_perkawinan_kawin">Kawin</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="NomorHPAlternatif" class="form-label">Nomor HP Alternatif:</label>
                        <input type="text" name="NomorHPAlternatif" id="nomor_hp_alternatif" value="<?php echo $mahasiswa['NomorHPAlternatif']; ?>" class="form-control">
                    </div>
                    <div class="mb-1 flex flex-wrap -mx-3">
                        <div class="w-1/3 px-3 mb-1">
                            <label for="nomor_ijazah" class="form-label">Nomor Ijazah:</label>
                            <input type="text" name="NomorIjazah" id="nomor_ijazah" class="form-control" value="<?php echo $mahasiswa['NomorIjazah']; ?>">
                        </div>
                        <div class="w-1/3 px-3 mb-1">
                            <label for="tahun_ijazah" class="form-label">Tahun Ijazah:</label>
                            <input type="text" name="TahunIjazah" id="tahun_ijazah" class="form-control" value="<?php echo $mahasiswa['TahunIjazah']; ?>">
                        </div>
                        <div class="w-1/3 px-3 mb-1">
                            <label for="nisn" class="form-label">NISN:</label>
                            <input type="text" name="NISN" id="nisn" class="form-control" value="<?php echo $mahasiswa['NISN']; ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="UkuranBaju" class="form-label">Ukuran Baju:</label>
                        <select name="UkuranBaju" id="UkuranBaju" class="form-select" required>
                            <option value="" disabled <?php if (empty($mahasiswa['UkuranBaju'])) echo 'selected'; ?>>Pilih Ukuran Baju</option>
                            <option value="S" <?php if ($mahasiswa['UkuranBaju'] == "S") echo "selected"; ?>>S</option>
                            <option value="M" <?php if ($mahasiswa['UkuranBaju'] == "M") echo "selected"; ?>>M</option>
                            <option value="L" <?php if ($mahasiswa['UkuranBaju'] == "L") echo "selected"; ?>>L</option>
                            <option value="XL" <?php if ($mahasiswa['UkuranBaju'] == "XL") echo "selected"; ?>>XL</option>
                            <option value="XXL" <?php if ($mahasiswa['UkuranBaju'] == "XXL") echo "selected"; ?>>XXL</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label for="layanan_paket_semester" class="form-label">Layanan Paket Semester:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="layanan_paket_semester_sipas" name="LayananPaketSemester" value="SIPAS" <?php if ($mahasiswa['LayananPaketSemester'] == "SIPAS") echo "checked"; ?> required>
                            <label class="form-check-label" for="layanan_paket_semester_sipas">SIPAS</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="layanan_paket_semester_non_sipas" name="LayananPaketSemester" value="NON SIPAS" <?php if ($mahasiswa['LayananPaketSemester'] == "NON SIPAS") echo "checked"; ?> required>
                            <label Name="form-check-label" for="layanan_paket_semester_non_sipas">NON SIPAS</label>
                        </div>
                    </div>
                    <div class="card mb-3" style="max-width: 540px;">
    <div class="card-body">
        <h5 class="card-title">Informasi Input</h5>
        <p class="card-text"><strong>Di Input Oleh:</strong> <?php echo $mahasiswa['DiInputOleh']; ?></p>
        <p class="card-text"><strong>Di Input Pada:</strong> <?php echo $mahasiswa['DiInputPada']; ?></p>
        <p class="card-text"><strong>Terakhir di Edit Pada:</strong> <?php echo $mahasiswa['DiEditPada']; ?></p>
    </div>
</div>

                    <div class="mb-3">
                        <label for="STATUS_INPUT_SIA" class="form-label">Status Input SIA:</label>
                        <select name="STATUS_INPUT_SIA" id="STATUS_INPUT_SIA" class="form-select">
                            <?php foreach ($status_input_sia as $value => $label) : ?>
                                <option value="<?php echo $value; ?>" <?php if ($mahasiswa['STATUS_INPUT_SIA'] == $value) echo "selected"; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mt-4">
                        <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>

</html>