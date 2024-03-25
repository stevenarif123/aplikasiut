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
  $query = "SELECT * FROM mahasiswa WHERE NamaLengkap LIKE '%$keyword%' OR Nim LIKE '%$keyword%' ORDER BY No DESC";
  $result = mysqli_query($koneksi, $query);
  if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
  }
  // Simpan hasil pencarian ke dalam array
  while ($row = mysqli_fetch_assoc($result)) {
    $mahasiswa[] = $row;
  }


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

  <form action="dashboard.php" method="post">
    <label for="keyword">Cari data mahasiswa:</label>
    <input type="text" name="keyword" id="keyword" value="<?php echo $keyword; ?>">
    <input type="submit" name="search" value="Cari">
  </form>

  <a href="backup_database.php">Backup Database</a>
  <a href="tambah_data.php">Tambah Data</a>
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
            <a href="lihat_data_mahasiswa.php?No=<?php echo $mhs['No']; ?>">Lihat Data</a> |
            <a href="edit_data.php?No=<?php echo $mhs['No']; ?>">Edit Data</a> |
            <a href="hapus_data_mahasiswa.php?No=<?php echo $mhs['No']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus Data</a>
          </td>
        </tr>
        <?php } ?>
      </table>
    <?php } else { ?>
      <p>Data mahasiswa tidak ditemukan.</p>
    <?php } ?>

  </body>
</html>