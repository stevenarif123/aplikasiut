<?php
require_once "../koneksi.php";

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

// Fetch the authenticated user information
$username = $_SESSION['username'];
$query = "SELECT * FROM admin WHERE username='$username'";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);

if (!$result) {
    die("Query failed: " . mysqli_error($koneksi));
}

$success_message = '';
$error_message = '';

// Function to generate unique ID
function generateUniqueId($kodeJurusan, $nim, $tglLahir, $nomorUrut) {
    // Combine elements into a single string
    $combinedString = $kodeJurusan . $nim . $tglLahir . $nomorUrut;

    // Generate hash from combined string
    $hash = hash('sha256', $combinedString);

    // Take the first 8 characters of the hash
    $uniqueId = substr($hash, 0, 8);

    return $uniqueId;
}

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Check if all required fields are set
    $nim = isset($_POST['NIM']) ? $koneksi->real_escape_string(trim($_POST['NIM'])) : '';
    $nama_lengkap = isset($_POST['NamaLengkap']) ? $koneksi->real_escape_string(trim($_POST['NamaLengkap'])) : '';
    $tempat_lahir = isset($_POST['TempatLahir']) ? $koneksi->real_escape_string(trim($_POST['TempatLahir'])) : '';
    $tanggal_lahir = isset($_POST['TanggalLahir']) ? date('Y-m-d', strtotime($_POST['TanggalLahir'])) : '';
    $nik = isset($_POST['NIK']) ? $koneksi->real_escape_string(trim($_POST['NIK'])) : '';
    $jurusan = isset($_POST['Jurusan']) ? $koneksi->real_escape_string(trim($_POST['Jurusan'])) : '';
    $nomor_hp = isset($_POST['NomorHP']) ? $koneksi->real_escape_string(trim($_POST['NomorHP'])) : '';
    $jenis_kelamin = isset($_POST['JenisKelamin']) ? $koneksi->real_escape_string(trim($_POST['JenisKelamin'])) : '';
    $alamat_lengkap = isset($_POST['AlamatLengkap']) ? $koneksi->real_escape_string(trim($_POST['AlamatLengkap'])) : '';
    $di_input_oleh = isset($user['nama_lengkap']) ? $koneksi->real_escape_string(trim($user['nama_lengkap'])) : '';

    // Validate the required fields are not empty
    if (!empty($nim) && !empty($nama_lengkap) && !empty($tempat_lahir) && !empty($tanggal_lahir) && !empty($nik) && !empty($jurusan) && !empty($nomor_hp) && !empty($jenis_kelamin) && !empty($alamat_lengkap)) {
        // Generate unique ID
        $uniqueId = generateUniqueId($jurusan, $nim, date('Ymd', strtotime($tanggal_lahir)), '001'); // You can modify the '001' part based on your requirement

        // Prepare the SQL statement
        $stmt = $koneksi->prepare("INSERT INTO mahasiswa (No, NIM, NamaLengkap, TempatLahir, TanggalLahir, NIK, Jurusan, NomorHP, JenisKelamin, Alamat, DiInputOleh, ID) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $koneksi->error);
        }

        // Bind parameters to the prepared statement
        $stmt->bind_param("sssssssssss", $nim, $nama_lengkap, $tempat_lahir, $tanggal_lahir, $nik, $jurusan, $nomor_hp, $jenis_kelamin, $alamat_lengkap, $di_input_oleh, $uniqueId);

        // Execute the prepared statement
        if ($stmt->execute()) {
            $success_message = "Data mahasiswa berhasil ditambahkan.";
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        $error_message = "Harap mengisi semua bidang yang diperlukan.";
    }
}

// Close the database connection
$koneksi->close();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Mahasiswa</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="#">SALUT TANA TORAJA</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./index.php">Daftar Mahasiswa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Keluar</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title text-center">Tambah Data Mahasiswa</h4>
                    <?php if ($success_message): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label for="nim">NIM</label>
                            <input type="text" class="form-control" name="NIM" id="nim" required>
                        </div>
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" class="form-control" name="NamaLengkap" id="nama_lengkap" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="tempat_lahir">Tempat Lahir</label>
                                <input type="text" class="form-control" name="TempatLahir" id="tempat_lahir" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" class="form-control" name="TanggalLahir" id="tanggal_lahir" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nik">NIK</label>
                            <input type="text" class="form-control" name="NIK" id="nik" required>
                        </div>
                        <div class="form-group">
                            <label for="jurusan">Jurusan</label>
                            <input type="text" class="form-control" name="Jurusan" id="jurusan" required>
                        </div>
                        <div class="form-group">
                            <label for="nomor_hp">No. HP/WA</label>
                            <input type="text" class="form-control" name="NomorHP" id="nomor_hp" required>
                        </div>
                        <div class="form-group">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select class="form-control" name="JenisKelamin" id="jenis_kelamin" required>
                                <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="alamat_lengkap">Alamat Lengkap</label>
                            <textarea class="form-control" name="AlamatLengkap" id="alamat_lengkap" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary btn-block">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
