<?php
// Include database connection file
require_once "../koneksi.php";

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
    $query = "SELECT Nim, NamaMahasiswa, Jurusan, TotalTagihan, TotalPembayaran, Saldo, isLunas 
              FROM saldo20242 
              WHERE LOWER(NamaMahasiswa) LIKE ? OR LOWER(Nim) LIKE ? 
              ORDER BY NamaMahasiswa DESC";
    $stmt = $koneksi->prepare($query);
    if (!$stmt) {
        die('Prepare failed: ' . $koneksi->error);
    }
    $searchParam = "%$nama_mahasiswa%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
    $stmt->execute();
    $hasilPencarian = $stmt->get_result();
    if (!$hasilPencarian) {
        die('Execute failed: ' . $stmt->error);
    }
}
?>
<h1 class="mb-4">Pencarian Mahasiswa</h1>
<form id="search-form" method="get" enctype="multipart/form-data">
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
            <th>Jurusan</th>
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
                          <td>' . $row['Jurusan'] . '</td>
                          <td>' . $row['TotalTagihan'] . '</td>
                          <td>' . $row['TotalPembayaran'] . '</td>
                          <td>' . $row['Saldo'] . '</td>
                          <td class="' . $statusColor . '">' . $status . '</td>
                          <td>
                              <a href="penambahan.php?nim=' . $row['Nim'] . '&nama=' . urlencode($row['NamaMahasiswa']) . '" class="btn btn-success btn-penambahan">Tambah Laporan Bayar</a>
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
<a href="#daftar_laporan" class="btn btn-secondary mb-3">Kembali</a>