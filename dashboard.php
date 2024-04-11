<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

// Koneksi ke database
require_once "koneksi.php";

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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Halaman Dashboard</title>
</head>
<body>
  <h1>Halaman Dashboard</h1>

  <p>Selamat datang, <?php echo $user['nama_lengkap']; ?>!</p>
  <p>Peran sebagai <?php echo $user['peran']; ?></p>

  <form action="dashboard.php" method="post">
    <label for="keyword">Cari data mahasiswa:</label>
    <input type="text" name="keyword" id="keyword" value="<?php echo $keyword; ?>">
    <input type="submit" name="search" value="Cari">
  </form>

  <a href="backup_database.php">Backup Database</a>
  <a href="./laporanbayar/verifikasi_laporan.php">Verifikasi Laporan</a>
  <a href="tambah_data.php">Tambah Data</a>
  <a href="./laporanbayar/">Laporan Uang Masuk</a>
  <a href="logout.php">LogOut</a>
  <br>

  <?php if (count($mahasiswa) > 0) { ?>
    <table border="1" cellpadding="10">
      <tr>
        <th>No</th>
        <th>Nama Lengkap</th>
        <th>Tempat Lahir</th>
        <th>Tanggal lahir</th>
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

      <?php $no = ($halaman_saat_ini - 1) * $jumlah_data_per_halaman + 1; foreach ($mahasiswa as $mhs) { ?>
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
            <a href="lihat_data_mahasiswa.php?No=<?php echo $mhs['No']; ?>">Lihat Data</a> |
            <a href="edit_data.php?No=<?php echo $mhs['No']; ?>">Edit Data</a> |
            <a href="hapus_data_mahasiswa.php?No=<?php echo $mhs['No']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus Data</a>|
          </td>
        </tr>
        <?php } ?>
      </table>

      <!-- Tampilkan opsi pemilihan jumlah data per halaman -->
      <form action="" method="GET">
            <label for="jumlah_data_per_halaman">Tampilkan per halaman:</label>
            <select name="jumlah_data_per_halaman" id="jumlah_data_per_halaman" onchange="this.form.submit()">
                <option value="10" <?php echo ($jumlah_data_per_halaman == 10) ? 'selected' : ''; ?>>10</option>
                <option value="25" <?php echo ($jumlah_data_per_halaman == 25) ? 'selected' : ''; ?>>25</option>
                <option value="100" <?php echo ($jumlah_data_per_halaman == 100) ? 'selected' : ''; ?>>100</option>
                <option value="all" <?php echo ($jumlah_data_per_halaman == 'all') ? 'selected' : ''; ?>>Semua</option>
            </select>
        </form>

      <!-- Tampilkan navigasi pagination -->
        <div>
            <?php if ($halaman_saat_ini > 1) { ?>
                <a href="?halaman=<?php echo $halaman_saat_ini - 1; ?>&jumlah_data_per_halaman=<?php echo $jumlah_data_per_halaman; ?>">Sebelumnya</a>
            <?php } ?>
            <?php for ($i = 1; $i <= $total_halaman; $i++) { ?>
                <a href="?halaman=<?php echo $i; ?>&jumlah_data_per_halaman=<?php echo $jumlah_data_per_halaman; ?>"><?php echo $i; ?></a>
            <?php } ?>
            <?php if ($halaman_saat_ini < $total_halaman) { ?>
                <a href="?halaman=<?php echo $halaman_saat_ini + 1; ?>&jumlah_data_per_halaman=<?php echo $jumlah_data_per_halaman; ?>">Selanjutnya</a>
            <?php } ?>
        </div>
    <?php } else { ?>
      <p>Data mahasiswa tidak ditemukan.</p>
    <?php } ?>

  </body>
</html>
