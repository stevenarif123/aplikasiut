<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

// Koneksi ke database
require_once "../koneksi.php";

// Query untuk mendapatkan data user
$username = $_SESSION['username'];
$query = "SELECT * FROM admin WHERE username='$username'";

$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);
if (!$result) {
  die("Query gagal: " . mysqli_error($koneksi));
}

// Inisialisasi variabel
$keyword = "";
$mahasiswa = [];

// Cek apakah form pencarian disubmit
if (isset($_POST['search'])) {
  // Ambil keyword dari form
  $keyword = $_POST['keyword'];
}
  // Query untuk mencari data mahasiswa berdasarkan keyword
  $query = "SELECT * FROM mahasiswabaru WHERE NamaLengkap LIKE '%$keyword%' OR Jurusan LIKE '%$keyword%' ORDER BY No DESC";
  $result = mysqli_query($koneksi, $query);
  if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
  }
  // Simpan hasil pencarian ke dalam array
  while ($row = mysqli_fetch_assoc($result)) {
    $mahasiswa[] = $row;
  }

// Pagination
$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * (intval($limit) == 'all' ? count($mahasiswa) : intval($limit));
$total_pages = ceil(count($mahasiswa) / (intval($limit) == 'all' ? 1 : intval($limit)));
$mahasiswa = array_slice($mahasiswa, $start, intval($limit) == 'all' ? count($mahasiswa) : intval($limit));

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mahasiswa Baru</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
    }
    .container {
      margin-top: 50px;
    }
    h1 {
      font-size: 2.5rem;
      color: #007bff;
    }
    .btn-primary {
      margin-right: 10px;
    }
    .table {
      margin-top: 20px;
    }
    /* Tambahkan CSS untuk membuat tabel lebih fluid dan responsif */
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #f2f2f2;
    }
    tr:nth-child(even) {
      background-color: #f2f2f2;
    }
    tr:hover {
      background-color: #f1f1f1;
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
  <div class="container-sm">
    <h1 class="h1">Pengelolaan Calon Mahasiswa Baru</h1>
    <h2 class="h2">Mahasiswa Baru dari Website</h2>
    <a class="btn btn-primary" href="./mabawebsite/" role="button">Proses</a>
    <form action="dashboard.php" method="post" class="mb-3">
      <label for="keyword" class="form-label">Cari data mahasiswa:</label>
      <div class="input-group">
        <input type="text" name="keyword" id="keyword" class="form-control" value="<?php echo $keyword; ?>">
        <button type="submit" name="search" class="btn btn-primary">Cari</button>
      </div>
      <label for="limit" class="form-label">Jumlah data per halaman:</label>
      <select name="limit" id="limit" class="form-select">
        <option value="10" <?php if ($limit == 10) echo "selected"; ?>>10</option>
        <option value="25" <?php if ($limit == 25) echo "selected"; ?>>25</option>
        <option value="100" <?php if ($limit == 100) echo "selected"; ?>>100</option>
        <option value="all" <?php if ($limit == 'all') echo "selected"; ?>>Semua Data</option>
      </select>
      <br>
      <button type="submit" class="btn btn-primary">Tampilkan</button>
    </form>

    <?php if (count($mahasiswa) > 0) { ?>
      <div class="table-responsive"> <!-- Tambahkan class table-responsive untuk membuat tabel responsif -->
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Lengkap</th>
              <th>Tempat Lahir</th>
              <th>Tanggal Lahir</th>
              <th>Nama Ibu Kandung</th>
              <th>NIK</th>
              <th>Jalur Program</th>
              <th>Jurusan</th>
              <th>Nomor HP</th>
              <th>Email</th>
              <th>Password</th>
              <th>Status Input SIA</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; foreach ($mahasiswa as $mhs) { ?>
              <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $mhs['NamaLengkap']; ?></td>
                <td><?php echo $mhs['TempatLahir']; ?></td>
                <td><?php echo $mhs['TanggalLahir']; ?></td>
                <td><?php echo $mhs['NamaIbuKandung']; ?></td>
                <td><?php echo $mhs['NIK']; ?></td>
                <td><?php echo $mhs['JalurProgram']; ?></td>
                <td><?php echo $mhs['Jurusan']; ?></td>
                <td><?php echo $mhs['NomorHP']; ?></td>
                <td><?php echo $mhs['Email']; ?></td>
                <td><?php echo $mhs['Password']; ?></td>
                <td><?php echo $mhs['STATUS_INPUT_SIA']; ?></td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="lihat_data_mahasiswa.php?No=<?php echo $mhs['No']; ?>" class="btn btn-info">Detail</a>
                    <a href="edit_data.php?No=<?php echo $mhs['No']; ?>" class="btn btn-warning">Edit</a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?php echo $mhs['No']; ?>">
                      Hapus
                    </button>
                  </div>
                  <!-- Modal Hapus -->
                  <div class="modal fade" id="modalHapus<?php echo $mhs['No']; ?>" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="modalHapusLabel">Konfirmasi Hapus Data</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          Apakah Anda yakin ingin menghapus data ini?
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                          <a href="hapus_data_mahasiswa.php?No=<?php echo $mhs['No']; ?>" class="btn btn-danger">Hapus</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <!-- Tambahkan Pagination di sini -->
      <div class="pagination">
        <ul class="pagination">
          <?php if ($page > 1) : ?>
            <li class="page-item">
              <a class="page-link" href="?page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?>" class="btn btn-primary">Sebelumnya</a>
            </li>
          <?php endif; ?>
          <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <li class="page-item">
              <a class="page-link" href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>" <?php if ($page == $i) echo "class='active'"; ?>><?php echo $i; ?></a>
            </li>
          <?php } ?>
          <?php if ($page < $total_pages) : ?>
            <li class="page-item">
              <a class="page-link" href="?page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?>" class="btn btn-primary">Selanjutnya</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    <?php } else { ?>
      <p>Data mahasiswa tidak ditemukan.</p>
    <?php } ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    </body>
</html>


