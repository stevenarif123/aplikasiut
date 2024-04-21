<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

// Koneksi ke database
require_once "koneksi.php";
// Di awal file atau di tempat Anda ingin konten dashboard.html muncul
//include 'dashboard.html';

if (!$koneksi) {
  die("Koneksi gagal: " . mysqli_connect_error());
}

// Inisialisasi variabel
$keyword = "";
$mahasiswa = [];

// Query untuk mendapatkan data user
$username = $_SESSION['username'];
$query = "SELECT * FROM admin WHERE username='$username'";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);
if (!$result) {
  die("Query gagal: " . mysqli_error($koneksi));
}

// Tentukan jumlah data per halaman
$jumlah_data_per_halaman = isset($_GET['jumlah_data_per_halaman']) ? $_GET['jumlah_data_per_halaman'] : 10;

// Cek jika jumlah data per halaman adalah 'all', maka query tanpa LIMIT
if ($jumlah_data_per_halaman == 'all') {
    $limit_sql = "";
} else {
    // Hitung halaman saat ini
    $halaman_saat_ini = isset($_GET['halaman']) ? $_GET['halaman'] : 1;

    // Hitung offset data
    $offset = ($halaman_saat_ini - 1) * (int)$jumlah_data_per_halaman;
    $limit_sql = "LIMIT $offset, $jumlah_data_per_halaman";
}

// Periksa apakah formulir pencarian telah disubmit
if (isset($_POST['search'])) {
  // Ambil kata kunci dari formulir
  $keyword = $_POST['keyword'];
}
// Query untuk mencari data mahasiswa berdasarkan kata kunci
$query = "SELECT * FROM mahasiswa WHERE NamaLengkap LIKE '%$keyword%' OR Nim LIKE '%$keyword%' ORDER BY No DESC $limit_sql";
$result = mysqli_query($koneksi, $query);
if (!$result) {
  die("Query gagal: " . mysqli_error($koneksi));
}
// Simpan hasil pencarian ke dalam array
while ($row = mysqli_fetch_assoc($result)) {
  $mahasiswa[] = $row;
}

// Hitung jumlah total data
$query_total = "SELECT COUNT(*) AS total FROM mahasiswa WHERE NamaLengkap LIKE '%$keyword%' OR Nim LIKE '%$keyword%'";
$result_total = mysqli_query($koneksi, $query_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_data = $row_total['total'];

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"s crossorigin="anonymous">
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
          <a class="nav-link" aria-current="page" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle active" href="mahasiswa.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Mahasiswa
          </a>
          <ul class="dropdown-menu">
          <li><a class="dropdown-item active" href="mahasiswa.php">Daftar Mahasiswa</a></li>
            <li><a class="dropdown-item" href="tambah_data.php">Tambah Mahasiswa</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="./laporanbayar" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Laporan Pembayaran
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="./laporanbayar">Laporan Bayar</a></li>
            <li><a class="dropdown-item" href="./laporanbayar/tambah_laporan.php">Tambah Laporan</a></li>
            <li><a class="dropdown-item" href="./laporanbayar/verifikasi_laporan.php">Verifikasi Laporan</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Mahasiswa Baru
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="./maba/dashboard.php">Daftar Mahasiswa</a></li>
            <li><a class="dropdown-item" href="./maba/tambah_data.php">Tambah Mahasiswa</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="./cekstatus/pencarian.php">Cek Status Mahasiswa</a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn btn-warning text-dark fw-bold" href="logout.php">Keluar</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<?php if (count($mahasiswa) > 0) { ?>
    <div class="container-sm mt-3">
      <form action="" method="POST" class="mb-3">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Cari berdasarkan nama atau nim" name="keyword">
          <button type="submit" class="btn btn-primary" name="search">Cari</button>
        </div>
      </form>
    </div>
    <div class="container-sm">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">No</th>
            <th scope="col">NIM</th>
            <th scope="col">Nama Lengkap</th>
            <th scope="col">Email</th>
            <th scope="col">Password</th>
            <th scope="col">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          foreach ($mahasiswa as $mhs) {
            ?>
            <tr>
              <th scope="row"><?php echo $no++; ?></th>
              <td><?php echo $mhs['Nim']; ?></td>
              <td><?php echo $mhs['NamaLengkap']; ?></td>
              <td><?php echo $mhs['Email']; ?></td>
              <td><?php echo $mhs['Password']; ?></td>
              <td>
                <a href="lihat_data_mahasiswa.php?No=<?php echo $mhs['No']; ?>" class="btn btn-primary">Detail</a>
                <a href="edit_data.php?No=<?php echo $mhs['No']; ?>" class="btn btn-warning">Edit</a>
                <a href="hapus_data.php?No=<?php echo $mhs['No']; ?>" class="btn btn-danger">Hapus</a>
              </td>
            </tr>
            <?php
          }
          ?>
        </tbody>
      </table>
    </div>
    <div class="container-sm">
      <nav aria-label="Page navigation example">
        <ul class="pagination">
          <?php
          $jumlah_halaman = ceil($total_data / $jumlah_data_per_halaman);
          for ($i = 1; $i <= $jumlah_halaman; $i++) {
            if ($i == $halaman_saat_ini) {
              $active = "active";
            } else {
              $active = "";
            }
            ?>
            <li class="page-item <?php echo $active; ?>"><a class="page-link" href="mahasiswa.php?halaman=<?php echo $i; ?>&jumlah_data_per_halaman=<?php echo $jumlah_data_per_halaman; ?>"><?php echo $i; ?></a></li>
            <?php
          }
          ?>
        </ul>
      </nav>
    </div>
    <?php
  } else {
    ?>
    <div class="container-sm">
      <div class="alert alert-warning" role="alert">
        Data tidak ditemukan
      </div>
    </div>
    <?php
  }
  ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>
