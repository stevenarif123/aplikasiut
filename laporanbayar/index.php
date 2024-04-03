<?php

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

// Mengambil data admin
$admin = $_SESSION['username'];

// Menghubungkan ke database
require_once 'koneksi.php';

// Mengatur jumlah data per halaman
$dataPerPage = 10;

// Mengambil halaman saat ini
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Menghitung offset data
$offset = ($currentPage - 1) * $dataPerPage;

// Menyiapkan query dasar
$query = "SELECT * FROM laporanuangmasuk WHERE 1=1"; // Memulai dengan kondisi benar (true)

// Menambahkan filter berdasarkan tanggal, nama, dan NIM
if (isset($_GET['tanggal']) && !empty($_GET['tanggal'])) {
    $tanggal = $_GET['tanggal'];
    $query .= " AND TanggalInput LIKE '%$tanggal%'";
}
if (isset($_GET['nama']) && !empty($_GET['nama'])) {
    $nama = $_GET['nama'];
    $query .= " AND NamaMahasiswa LIKE '%$nama%'";
}
if (isset($_GET['nim']) && !empty($_GET['nim'])) {
    $nim = $_GET['nim'];
    $query .= " AND Nim LIKE '%$nim%'";
}

// Menambahkan filter untuk laporan yang belum diverifikasi
$query .= " AND isVerifikasi = 0 AND Admin = '$admin'";

// Mengambil total data
$result = mysqli_query($koneksi, $query);
$totalData = mysqli_num_rows($result);

// Menambahkan limit pada query
$query .= " LIMIT $offset, $dataPerPage";
$result = mysqli_query($koneksi, $query);

?>

<h1>Laporan Uang Masuk</h1>
<a href="tambah_laporan.php">Tambah Laporan</a>
<!-- Pemberitahuan laporan yang belum diverifikasi -->
<?php if ($totalData > 0): ?>
<p>Terdapat <b><?php echo $totalData; ?></b> laporan yang belum diverifikasi.</p>
<?php endif; ?>

<!-- Form filter -->
<form method="get">
    <label for="tanggal">Tanggal:</label>
    <input type="date" name="tanggal" id="tanggal">
    <label for="nama">Nama Mahasiswa:</label>
    <input type="text" name="nama" id="nama">
    <label for="nim">NIM Mahasiswa:</label>
    <input type="text" name="nim" id="nim">
    <button type="submit">Filter</button>
</form>

<!-- Tabel laporan -->
<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Kode Laporan</th>
            <th>Jenis Bayar</th>
            <th>Tanggal Input</th>
            <th>Nama Mahasiswa</th>
            <th>NIM</th>
            <th>Total</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = $offset + 1; ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $row['KodeLaporan']; ?></td>
            <td><?php echo $row['JenisBayar']; ?></td>
            <td><?php echo $row['TanggalInput']; ?></td>
            <td><?php echo $row['NamaMahasiswa']; ?></td>
            <td><?php echo $row['Nim']; ?></td>
            <td><?php echo $row['Total']; ?></td>
            <td>
                <a href="edit_laporan.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a href="lihat_laporan.php?id=<?php echo $row['id']; ?>">Lihat</a>
                <a href="hapus_laporan.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Navigasi halaman -->
<?php if ($totalData > $dataPerPage): ?>
<ul class="pagination">
    <?php for ($i = 1; $i <= ceil($totalData / $dataPerPage); $i++): ?>
    <li class="<?php echo ($currentPage == $i) ? 'active' : ''; ?>">
        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
    </li>
    <?php endfor; ?>
</ul>
<?php endif; ?>

<?php
// Menutup koneksi database
mysqli_close($koneksi);
?>
