<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['username'])) {
  header("Location: ../login.php");
}
// Mengambil data admin
$admin = $_SESSION['username'];
// Menghubungkan ke database
require_once '../koneksi.php';

// Mengatur jumlah data per halaman
$dataPerPage = 10;

// Mengambil halaman saat ini
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Menghitung offset data
$offset = ($currentPage - 1) * $dataPerPage;

// Menyiapkan query dasar
$query = "SELECT * FROM laporanuangmasuk20242 WHERE isVerifikasi = 0 AND Admin = '$admin'";

// Menambahkan filter berdasarkan rentang tanggal
if (isset($_GET['tanggal_awal']) && isset($_GET['tanggal_akhir'])) {
    $tanggal_awal = $_GET['tanggal_awal'];
    $tanggal_akhir = $_GET['tanggal_akhir'];
    if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
        $query .= " AND TanggalInput BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
    }
}

// Menambahkan filter berdasarkan nama atau NIM (jika diperlukan)
if (isset($_GET['cari']) && !empty($_GET['cari'])) {
    $cari = $_GET['cari'];
    $query .= " AND (NamaMahasiswa LIKE '%$cari%' OR Nim LIKE '%$cari%')";
}

// Mengambil total data
$totalResult = mysqli_query($koneksi, $query);
$totalData = mysqli_num_rows($totalResult);

// Menambahkan limit pada query
$query .= " LIMIT $offset, $dataPerPage";
$result = mysqli_query($koneksi, $query);

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>

<h1 class="mb-4">Laporan Pembayaran</h1>
<?php if ($totalData > 0): ?>
<div class="alert alert-warning" role="alert">
    Terdapat <b><?php echo $totalData; ?></b> laporan yang belum diverifikasi.
</div>
<?php endif; ?>

<!-- Form filter -->
<form method="get" class="mb-4">
    <div class="row g-3 align-items-center">
        <div class="col-auto">
            <label for="tanggalAwal" class="col-form-label">Tanggal Awal:</label>
        </div>
        <div class="col-auto">
            <input type="date" name="tanggal_awal" id="tanggalAwal" class="form-control" value="<?php echo isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : ''; ?>">
        </div>
        <div class="col-auto">
            <label for="tanggalAkhir" class="col-form-label">Tanggal Akhir:</label>
        </div>
        <div class="col-auto">
            <input type="date" name="tanggal_akhir" id="tanggalAkhir" class="form-control" value="<?php echo isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : ''; ?>">
        </div>
        <div class="col-auto">
            <label for="cari" class="col-form-label">Cari Nama/NIM:</label>
        </div>
        <div class="col-auto">
            <input type="text" name="cari" id="cari" class="form-control" value="<?php echo isset($_GET['cari']) ? $_GET['cari'] : ''; ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary">Filter</button>
        </div>
    </div>
</form>

<!-- Tabel laporan -->
<table class="table">
    <thead>
        <tr>
            <th>No.</th>
            <th>Kode Laporan</th>
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
            <td><?php echo $row['TanggalInput']; ?></td>
            <td><?php echo $row['NamaMahasiswa']; ?></td>
            <td><?php echo $row['Nim']; ?></td>
            <td><?php echo formatRupiah($row['JumlahBayar']); ?></td>
            <td>
                <a href="edit_laporan.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success btn-edit_laporan">Edit</a>
                <a href="lihat_laporan.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Lihat</a>
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?php echo $row['id']; ?>">Hapus</button>
                <!-- Modal -->
                <div class="modal fade" id="modalHapus<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
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
                        <a href="hapus_laporan.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Hapus</a>
                      </div>
                    </div>
                  </div>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Navigasi halaman -->
<?php if ($totalData > $dataPerPage): ?>
<nav>
    <ul class="pagination">
        <?php for ($i = 1; $i <= ceil($totalData / $dataPerPage); $i++): ?>
        <li class="page-item <?php echo ($currentPage == $i) ? 'active' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $i; ?>&tanggal_awal=<?php echo isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : ''; ?>&tanggal_akhir=<?php echo isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : ''; ?>&cari=<?php echo isset($_GET['cari']) ? $_GET['cari'] : ''; ?>"><?php echo $i; ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php
// Menutup koneksi database
mysqli_close($koneksi);
?>
