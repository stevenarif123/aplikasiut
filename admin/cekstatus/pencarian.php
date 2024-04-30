<?php
// Buat koneksi ke database
require_once "../koneksi.php";

// Inisialisasi variabel untuk menyimpan hasil pencarian
$dataMahasiswa = array();

// Proses pencarian data mahasiswa jika parameter pencarian diberikan
$pencarian = isset($_GET['pencarian']) ? $_GET['pencarian'] : "";
$kriteria = isset($_GET['kriteria']) ? $_GET['kriteria'] : "nama";

// Lakukan pembersihan nilai pencarian untuk mencegah serangan SQL Injection
$pencarian = $koneksi->real_escape_string($pencarian);

// Bangun kueri SQL berdasarkan kriteria pencarian
$sql = "SELECT No, Nim, NamaLengkap AS Nama, Jurusan FROM mahasiswa WHERE ";

if ($kriteria == 'nim') {
    $sql .= "Nim LIKE '%$pencarian%'";
} elseif ($kriteria == 'nama') {
    $sql .= "NamaLengkap LIKE '%$pencarian%'";
} elseif ($kriteria == 'jurusan') {
    $sql .= "Jurusan LIKE '%$pencarian%'";
}

// Lakukan kueri ke database
$result = $koneksi->query($sql);

// Periksa apakah kueri berhasil dieksekusi
if (!$result) {
    die("Kesalahan dalam eksekusi kueri: " . $koneksi->error);
}

// Pagination
$jumlahDataPerHalaman = isset($_GET['limit']) ? $_GET['limit'] : 5;
$jumlahData = $result->num_rows;
$jumlahHalaman = ceil($jumlahData / $jumlahDataPerHalaman);
$halamanAktif = isset($_GET['halaman']) ? $_GET['halaman'] : 1;
$awalData = ($halamanAktif - 1) * $jumlahDataPerHalaman;

$sql .= " LIMIT $awalData, $jumlahDataPerHalaman";
$result = $koneksi->query($sql);

// Periksa apakah ada hasil yang ditemukan
if ($result->num_rows > 0) {
    // Loop melalui hasil kueri dan simpan ke dalam array
    while($row = $result->fetch_assoc()) {
        $dataMahasiswa[] = $row;
    }
} else {
    echo "Tidak ada hasil yang ditemukan.";
}

// Tutup koneksi database
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Data Mahasiswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
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
            <li><a class="dropdown-item" href="../mahasiswa.php">Daftar Mahasiswa</a></li>
            <li><a class="dropdown-item" href="../tambah_data.php">Tambah Mahasiswa</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="./" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Laporan Pembayaran
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="./">Laporan Bayar</a></li>
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
          <a class="nav-link active" aria-current="page" href="../cekstatus/pencarian.php">Cek Status Mahasiswa</a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn btn-warning text-dark fw-bold" href="../logout.php">Keluar</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-5">
    <h1 class="mb-4">Pencarian Data Mahasiswa</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
        <div class="mb-3">
            <label for="pencarian" class="form-label">Masukkan Kata Kunci:</label>
            <input type="text" class="form-control" id="pencarian" name="pencarian" value="<?php echo isset($_GET['pencarian']) ? $_GET['pencarian'] : ''; ?>">
        </div>
        
        <div class="mb-3">
            <label for="kriteria" class="form-label">Pilih Kriteria Pencarian:</label>
            <select class="form-select" id="kriteria" name="kriteria">
                <option value="nim" <?php if ($kriteria == 'nim') echo 'selected'; ?>>NIM</option>
                <option value="nama" <?php if ($kriteria == 'nama') echo 'selected'; ?>>Nama</option>
                <option value="jurusan" <?php if ($kriteria == 'jurusan') echo 'selected'; ?>>Jurusan</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="limit" class="form-label">Jumlah Data Per Halaman:</label>
            <select class="form-select" id="limit" name="limit">
                <option value="5" <?php if ($jumlahDataPerHalaman == 5) echo 'selected'; ?>>5</option>
                <option value="10" <?php if ($jumlahDataPerHalaman == 10) echo 'selected'; ?>>10</option>
                <option value="25" <?php if ($jumlahDataPerHalaman == 25) echo 'selected'; ?>>25</option>
                <option value="<?php echo $jumlahData; ?>" <?php if ($jumlahDataPerHalaman == $jumlahData) echo 'selected'; ?>>Semua Data</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    <?php if (!empty($dataMahasiswa)): ?>
    <h2>Hasil Pencarian Data Mahasiswa</h2>
    <table class="table">
        <thead>
            <tr>
                <th>NIM</th>
                <th>Nama</th>
                <th>Jurusan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dataMahasiswa as $mahasiswa): ?>
            <tr>
                <td><?php echo $mahasiswa['Nim']; ?></td>
                <td><?php echo $mahasiswa['Nama']; ?></td>
                <td><?php echo $mahasiswa['Jurusan']; ?></td>
                <td><a href="statusbayar.php?No=<?php echo $mahasiswa['No']; ?>" class="btn btn-info">Status Bayar</a></td>
                <td><a href="pilihmasa.php?No=<?php echo $mahasiswa['No']; ?>" class="btn btn-info">Cek Nilai</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Pagination -->
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <?php if ($halamanAktif > 1): ?>
                <li class="page-item"><a class="page-link" href="?halaman=<?php echo $halamanAktif - 1; ?>&limit=<?php echo $jumlahDataPerHalaman; ?>">Sebelumnya</a></li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $jumlahHalaman; $i++): ?>
                <li class="page-item <?php echo ($i == $halamanAktif) ? 'active' : ''; ?>"><a class="page-link" href="?halaman=<?php echo $i; ?>&limit=<?php echo $jumlahDataPerHalaman; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
            <?php if ($halamanAktif < $jumlahHalaman): ?>
                <li class="page-item"><a class="page-link" href="?halaman=<?php echo $halamanAktif + 1; ?>&limit=<?php echo $jumlahDataPerHalaman; ?>">Selanjutnya</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    </nav>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
