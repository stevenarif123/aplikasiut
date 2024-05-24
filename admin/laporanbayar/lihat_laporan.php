<?php
// Include database connection file
require_once "koneksi.php";

// Check if session is not active, start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit; // Stop further execution
}

// Get data from query string
$id = $_GET['id'] ?? '';

// Fetch data from the database based on the report id
$query = "SELECT * FROM laporanuangmasuk WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Check if data is found
if (!$row) {
    echo "Data not found!";
    exit;
}

// Populate variables with retrieved data
$kodeLaporan = $row['KodeLaporan'];
$nim = $row['Nim'];
$namaMahasiswa = $row['NamaMahasiswa'];
$jurusan = $row['Jurusan'];
$jenisBayar = $row['JenisBayar'];
$ut = $row['Ut'];
$pokjar = $row['Pokjar'];
$total = $row['Total'];
$admin = $row['Admin'];
$catatanKhusus = $row['CatatanKhusus'];
$isMaba = $row['isMaba'];
$metodeBayar = $row['MetodeBayar'];
$alamatFile = $row['AlamatFile'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan Bayar</title>
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
          <a class="nav-link dropdown-toggle" href="./laporanbayar" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
    <h1 class="mb-4">Detail Laporan Bayar</h1>
    <div class="container">
        <div class="row">
            <div class="col">
                <table class="table table-bordered">
                    <tr>
                        <th>ID</th>
                        <td><?php echo $kodeLaporan; ?></td>
                    </tr>
                    <tr>
                        <th>NIM</th>
                        <td><?php echo $nim; ?></td>
                    </tr>
                    <tr>
                        <th>Nama Mahasiswa</th>
                        <td><?php echo $namaMahasiswa; ?></td>
                    </tr>
                    <tr>
                        <th>Jurusan</th>
                        <td><?php echo $jurusan; ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Bayar</th>
                        <td><?php echo $jenisBayar; ?></td>
                    </tr>
                    <tr>
                        <th>UT</th>
                        <td><?php echo $ut; ?></td>
                    </tr>
                    <tr>
                        <th>Pokjar</th>
                        <td><?php echo $pokjar; ?></td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td><?php echo $total; ?></td>
                    </tr>
                    <tr>
                        <th>Admin</th>
                        <td><?php echo $admin; ?></td>
                    </tr>
                    <tr>
                        <th>Catatan Khusus</th>
                        <td><?php echo $catatanKhusus; ?></td>
                    </tr>
                    <tr>
                        <th>Mahasiswa Baru (Maba)</th>
                        <td><?php echo $isMaba == 1 ? 'Yes' : 'No'; ?></td>
                    </tr>
                    <tr>
                        <th>Metode Bayar</th>
                        <td><?php echo $metodeBayar; ?></td>
                    </tr>
                    <tr>
                        <th>Alamat File</th>
                        <td><?php echo $alamatFile; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <?php if ($alamatFile && $metodeBayar == "Transfer") : ?>
        <img src="<?php echo $alamatFile; ?>" alt="Bukti Transfer" class="img-fluid" style="max-width: 300px;"><br><br>
    <?php endif; ?>
    <div class="text-center">
        <a href="index.php" class="btn btn-primary">Kembali</a>
    </div>
</body>
</html>
