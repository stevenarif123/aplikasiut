<?php
// Include database connection file
require_once "koneksi.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

// Initialize variables
$hasilPencarian = null;

// If form is submitted (GET request)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['cari_mahasiswa'])) {
    $nama_mahasiswa = trim($_GET['cari_mahasiswa']); // Trim whitespace

    // Assuming your database is case-insensitive:
    $nama_mahasiswa = strtolower($nama_mahasiswa); // Convert to lowercase

    // Query to search for mahasiswa in saldo20242 table
    $query = "SELECT Nim, NamaMahasiswa, TotalTagihan, TotalPembayaran, Saldo, isLunas 
              FROM saldo20242 
              WHERE LOWER(NamaMahasiswa) LIKE ? OR LOWER(Nim) LIKE ? 
              ORDER BY NamaMahasiswa DESC";
    $stmt = $koneksi->prepare($query);
    $searchParam = "%$nama_mahasiswa%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
    $stmt->execute();
    $hasilPencarian = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Mahasiswa</title>
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
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="../dashboard.php">Dashboard</a>
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
          <a class="nav-link dropdown-toggle" href="./laporanbayar" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Laporan Pembayaran
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="./">Laporan Bayar</a></li>
            <li><a class="dropdown-item active" href="">Tambah Laporan</a></li>
            <li><a class="dropdown-item" href="./verifikasi_laporan.php">Verifikasi Laporan</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Mahasiswa Baru
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../mahasiswa/mahasiswa.php">Daftar Mahasiswa</a></li>
            <li><a class="dropdown-item" href="../mahasiswa/tambah_data.php">Tambah Mahasiswa</a></li>
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
        <h1 class="mb-4">Pencarian Mahasiswa</h1>
        <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Cari Mahasiswa" name="cari_mahasiswa">
                <button class="btn btn-primary" type="submit">Cari</button>
            </div>
        </form>

        <!-- Search results table -->
        <table class="table">
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Total Tagihan</th>
                    <th>Total Pembayaran</th>
                    <th>Saldo</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($hasilPencarian && mysqli_num_rows($hasilPencarian) > 0) {
                    // Display the table with search results
                    while ($row = mysqli_fetch_assoc($hasilPencarian)) {
                        $status = $row['isLunas'] == 1 ? 'Lunas' : 'Belum Lunas';
                        $statusColor = $row['isLunas'] == 1 ? 'text-success' : 'text-danger';
                        echo '<tr>
                                  <td>' . $row['Nim'] . '</td>
                                  <td>' . $row['NamaMahasiswa'] . '</td>
                                  <td>' . $row['TotalTagihan'] . '</td>
                                  <td>' . $row['TotalPembayaran'] . '</td>
                                  <td>' . $row['Saldo'] . '</td>
                                  <td class="' . $statusColor . '">' . $status . '</td>
                                  <td>
                                      <a href="penambahan.php?nim=' . $row['Nim'] . '&nama=' . urlencode($row['NamaMahasiswa']) . '" class="btn btn-success">Tambah Laporan Bayar</a>
                                  </td>
                              </tr>';
                    }
                } else {
                    // Display a message if no results are found
                    echo "<tr><td colspan='7'>Data mahasiswa tidak ditemukan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
