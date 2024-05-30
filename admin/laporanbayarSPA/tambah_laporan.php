<?php
require_once "../koneksi.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
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
<div class="container mt-5">
    <h1 class="mb-4">Pencarian Mahasiswa</h1>
    <form method="get" action="tambah_laporan.php">
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Cari Mahasiswa" name="cari_mahasiswa" required>
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['cari_mahasiswa'])) {
        $nama_mahasiswa = trim($_GET['cari_mahasiswa']);
        $nama_mahasiswa = strtolower($nama_mahasiswa);

        $query = "SELECT * FROM mahasiswa WHERE NamaLengkap LIKE '%$nama_mahasiswa%' OR Nim = '$nama_mahasiswa' ORDER BY No DESC";
        $hasilPencarian = mysqli_query($koneksi, $query);

        if ($hasilPencarian && mysqli_num_rows($hasilPencarian) > 0) {
            echo '<table class="table"><thead><tr><th>NIM</th><th>Nama Mahasiswa</th><th>Jurusan</th><th>Aksi</th></tr></thead><tbody>';
            while ($row = mysqli_fetch_assoc($hasilPencarian)) {
                echo '<tr>
                        <td>' . $row['Nim'] . '</td>
                        <td>' . $row['NamaLengkap'] . '</td>
                        <td>' . $row['Jurusan'] . '</td>
                        <td>
                            <a href="penambahan.php?nim=' . $row['Nim'] . '&nama=' . urlencode($row['NamaLengkap']) . '&jurusan=' . urlencode($row['Jurusan']) . '" class="btn btn-success">Tambah Laporan Bayar</a>
                        </td>
                    </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo "<p>Data mahasiswa tidak ditemukan.</p>";
        }
    }
    ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
