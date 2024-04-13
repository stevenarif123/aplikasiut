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

// Hitung halaman saat ini
$halaman_saat_ini = isset($_GET['halaman']) ? $_GET['halaman'] : 1;

// Hitung offset data
$offset = ($halaman_saat_ini - 1) * $jumlah_data_per_halaman;

// Cek apakah form pencarian disubmit
if (isset($_POST['search'])) {
  // Ambil keyword dari form
  $keyword = $_POST['keyword'];
}
// Query untuk mencari data mahasiswa berdasarkan keyword
$query = "SELECT * FROM mahasiswa WHERE NamaLengkap LIKE '%$keyword%' OR Nim LIKE '%$keyword%' ORDER BY No DESC LIMIT $offset, $jumlah_data_per_halaman";
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

// Hitung total halaman
$total_halaman = ceil($total_data / $jumlah_data_per_halaman);

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
    <div class="container-sm">
      <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col">No</th>
            <th scope="col">Nama Lengkap</th>
            <th scope="col">Jalur Program</th>
            <th scope="col">Jurusan</th>
            <th scope="col">Nomor HP</th>
            <th scope="col">Email</th>
            <th scope="col">Password</th>
            <th scope="col" colspan="3">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = ($halaman_saat_ini - 1) * $jumlah_data_per_halaman + 1; foreach ($mahasiswa as $mhs) { ?>
            <tr>
              <th scope="row"><?php echo $no++; ?></th>
              <td><?php echo $mhs['NamaLengkap']; ?></td>
              <td><?php echo $mhs['JalurProgram']; ?></td>
              <td><?php echo $mhs['Jurusan']; ?></td>
              <td><?php echo $mhs['NomorHP']; ?></td>
              <td><?php echo $mhs['Email']; ?></td>
              <td><?php echo $mhs['Password']; ?></td>
              <td>
                <a class="btn btn-info" role="button" href="lihat_data_mahasiswa.php?No=<?php echo $mhs['No']; ?>">Detail</a>
              </td>
              <td>
                <a class="btn btn-warning" role="button" href="edit_data.php?No=<?php echo $mhs['No']; ?>">Edit</a>
              </td>
              <td>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?php echo $mhs['No']; ?>">Hapus</button>

                <!-- Modal -->
                <div class="modal fade" id="modalHapus<?php echo $mhs['No']; ?>" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="modalHapusLabel">Konfirmasi Hapus</h5>
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
    <!-- Tampilkan opsi pemilihan jumlah data per halaman -->
    <div class="d-flex justify-content-center my-3">
        <form action="" method="GET" class="d-flex align-items-center">
            <label for="jumlah_data_per_halaman" class="me-2">Tampilkan per Halaman:</label>
            <select name="jumlah_data_per_halaman" id="jumlah_data_per_halaman" class="form-select" onchange="this.form.submit()" style="width: auto;">
                <option value="10" <?php echo ($jumlah_data_per_halaman == 10) ? 'selected' : ''; ?>>10</option>
                <option value="25" <?php echo ($jumlah_data_per_halaman == 25) ? 'selected' : ''; ?>>25</option>
                <option value="100" <?php echo ($jumlah_data_per_halaman == 100) ? 'selected' : ''; ?>>100</option>
                <option value="all" <?php echo ($jumlah_data_per_halaman == 'all') ? 'selected' : ''; ?>>Semua</option>
            </select>
        </form>
    </div>
    <!-- Tampilkan navigasi pagination -->
    <nav aria-label="Page navigation example">
      <ul class="pagination justify-content-center">
        <?php if ($halaman_saat_ini > 1) { ?>
          <li class="page-item">
            <a class="page-link" href="?halaman=<?php echo $halaman_saat_ini - 1; ?>&jumlah_data_per_halaman=<?php echo $jumlah_data_per_halaman; ?>">Sebelumnya</a>
          </li>
        <?php } else { ?>
          <li class="page-item disabled">
            <a class="page-link">Sebelumnya</a>
          </li>
        <?php } ?>
        
        <?php for ($i = 1; $i <= $total_halaman; $i++) { ?>
          <li class="page-item <?php echo ($i == $halaman_saat_ini) ? 'active' : ''; ?>"><a class="page-link" href="?halaman=<?php echo $i; ?>&jumlah_data_per_halaman=<?php echo $jumlah_data_per_halaman; ?>"><?php echo $i; ?></a></li>
        <?php } ?>
        
        <?php if ($halaman_saat_ini < $total_halaman) { ?>
          <li class="page-item">
            <a class="page-link" href="?halaman=<?php echo $halaman_saat_ini + 1; ?>&jumlah_data_per_halaman=<?php echo $jumlah_data_per_halaman; ?>">Selanjutnya</a>
          </li>
        <?php } else { ?>
          <li class="page-item disabled">
            <a class="page-link">Selanjutnya</a>
          </li>
        <?php } ?>
      </ul>
    </nav>
  <?php } else { ?>
    <p>Data mahasiswa tidak ditemukan.</p>
  <?php } ?>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>