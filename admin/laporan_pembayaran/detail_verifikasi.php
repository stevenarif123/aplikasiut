<?php
// Include database connection file
require_once "koneksi.php";

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if user is not logged in or not a verificator
if (!isset($_SESSION['username']) || $_SESSION['peran'] != 'verifikator') {
    header("Location: ../login.php?error=1");
    exit; // Stop further execution
}

// Check if ID parameter exists in the URL
if (!isset($_GET['id'])) {
    echo "ID parameter is missing.";
    exit;
}

$verifikator = $_SESSION['username'];
// Fetch data based on ID from the database
$id = $_GET['id'];
$query = "SELECT * FROM laporanuangmasuk WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Check if data is found
if ($result->num_rows <= 0) {
    echo "Data not found.";
    exit;
}

// Fetch the row
$row = $result->fetch_assoc();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reject'])) {
    $catatanKhusus = $_POST['catatan_khusus'] ?? '';

    // Update the 'isVerifikasi' column to 1 (rejected) and set special notes
    $query_update = "UPDATE laporanuangmasuk SET isVerifikasi = ?, CatatanKhusus = ?, Verifikator = ? WHERE id = ?";
    $stmt_update = $koneksi->prepare($query_update);
    $isVerifikasi = 0; // Set to 0 (rejected)
    $stmt_update->bind_param("issi", $isVerifikasi, $catatanKhusus, $verifikator, $id);
    if ($stmt_update->execute()) {
        header("Location: verifikasi_laporan.php");
        exit;
    } else {
        echo "Error updating record.";
    }
}

// Directly update 'isVerifikasi' column to 1 (verified)
if (isset($_GET['verifikasi']) && $_GET['verifikasi'] == "true") {
    $query_verifikasi = "UPDATE laporanuangmasuk SET isVerifikasi = ? WHERE id = ?";
    $stmt_verifikasi = $koneksi->prepare($query_verifikasi);
    $isVerifikasi = 1; // Set to 1 (verified)
    $stmt_verifikasi->bind_param("ii", $isVerifikasi, $id);
    if ($stmt_verifikasi->execute()) {
        header("Location: verifikasi_laporan.php");
        exit;
    } else {
        echo "Error updating record.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Verifikasi Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">SALUT TANA TORAJA</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="../dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="../mahasiswa.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Mahasiswa
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../mahasiswa/mahasiswa.php">Daftar Mahasiswa</a></li>
            <li><a class="dropdown-item" href="../mahasiswa/tambah_data.php">Tambah Mahasiswa</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="./" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Laporan Pembayaran
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item active" href="./">Laporan Bayar</a></li>
            <li><a class="dropdown-item" href="./tambah_laporan.php">Tambah Laporan</a></li>
            <li><a class="dropdown-item" href="">Verifikasi Laporan</a></li>
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
            <a class="nav-link btn btn-warning text-dark fw-bold" href="../logout.php">Keluar</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-5">
<h1 class="mb-4">Detail Verifikasi Laporan</h1>
    <!-- Tampilkan detail pembayaran -->
    <div class="card mb-4">
        <div class="card-body">
            <p class="card-text">Kode Laporan: <?php echo $row['KodeLaporan']; ?></p>
            <p class="card-text">Jenis Bayar: <?php echo $row['JenisBayar']; ?></p>
            <p class="card-text">Tanggal Input: <?php echo $row['TanggalInput']; ?></p>
            <p class="card-text">Nama Mahasiswa: <?php echo $row['NamaMahasiswa']; ?></p>
            <p class="card-text">NIM: <?php echo $row['Nim']; ?></p>
            <p class="card-text">Jurusan: <?php echo $row['Jurusan']; ?></p>
            <p class="card-text">UT: <?php echo $row['Ut']; ?></p>
            <p class="card-text">Pokjar: <?php echo $row['Pokjar']; ?></p>
            <p class="card-text">Admin: <?php echo $row['Admin']; ?></p>
            <p class="card-text">Catatan Khusus: <?php echo $row['CatatanKhusus']; ?></p>
            <p class="card-text">Metode Bayar: <?php echo $row['MetodeBayar']; ?></p>
            <p class="card-text">Alamat File: <?php echo $row['AlamatFile']; ?></p>
            <img src="<?php echo $row['AlamatFile']; ?>" alt="Bukti Transfer" class="img-fluid" style="max-width: 300px;"><br><br>
        </div>
    </div>

    <?php if ($row['isVerifikasi'] == 0) : ?>
        <!-- Formulir untuk menolak pembayaran dengan catatan khusus -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>">
            <div class="mb-3">
                <label for="catatan_khusus" class="form-label">Catatan Khusus untuk Penolakan:</label>
                <textarea class="form-control" id="catatan_khusus" name="catatan_khusus" required></textarea>
            </div>
            <button type="submit" name="reject" class="btn btn-danger">Tolak</button>
        </form>
    <?php endif; ?>
        <!-- Tombol untuk verifikasi -->
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#verifikasiModal">
        Verifikasi
    </button>
    <!-- Modal untuk verifikasi -->
    <div class="modal fade" id="verifikasiModal" tabindex="-1" aria-labelledby="verifikasiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verifikasiModalLabel">Verifikasi Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin memverifikasi pembayaran ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="detail_verifikasi.php?id=<?php echo $row['id']; ?>&verifikasi=true" class="btn btn-primary">Verifikasi</a>
                </div>
            </div>
        </div>
    </div>
        <!-- Tombol untuk kembali ke halaman verifikasi -->
    <a href="verifikasi_laporan.php" class="btn btn-primary">Kembali</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
