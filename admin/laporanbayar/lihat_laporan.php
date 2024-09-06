<?php
// Include database connection file
require_once "koneksi.php";

// Check if session is not active, start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit; // Stop further execution
}

// Get data from query string
$id = $_GET['id'] ?? '';

// Fetch data from the database based on the report id
$query = "SELECT * FROM laporanuangmasuk WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Check if data is found
if (!$row) {
    echo "Data not found!";
    exit;
}

// Populate variables with retrieved data
$kodeLaporan = $row['KodeLaporan'];
$nim = $row['Nim'];
$namaMahasiswa = $row['NamaMahasiswa'];
$jurusan = $row['Jurusan'];
$jenisBayar = $row['JenisBayar'];
$ut = $row['Ut'];
$pokjar = $row['Pokjar'];
$total = $row['Total'];
$admin = $row['Admin'];
$catatanKhusus = $row['CatatanKhusus'];
$isMaba = $row['isMaba'];
$metodeBayar = $row['MetodeBayar'];
$alamatFile = $row['AlamatFile'];
?>

<h1 class="mb-4">Detail Laporan Bayar</h1>
        <div class="row">
            <div class="col">
                <table class="table table-bordered">
                    <tr>
                        <th>ID</th>
                        <td><?php echo $kodeLaporan; ?></td>
                    </tr>
                    <tr>
                        <th>NIM</th>
                        <td><?php echo $nim; ?></td>
                    </tr>
                    <tr>
                        <th>Nama Mahasiswa</th>
                        <td><?php echo $namaMahasiswa; ?></td>
                    </tr>
                    <tr>
                        <th>Jurusan</th>
                        <td><?php echo $jurusan; ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Bayar</th>
                        <td><?php echo $jenisBayar; ?></td>
                    </tr>
                    <tr>
                        <th>UT</th>
                        <td><?php echo $ut; ?></td>
                    </tr>
                    <tr>
                        <th>Pokjar</th>
                        <td><?php echo $pokjar; ?></td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td><?php echo $total; ?></td>
                    </tr>
                    <tr>
                        <th>Admin</th>
                        <td><?php echo $admin; ?></td>
                    </tr>
                    <tr>
                        <th>Catatan Khusus</th>
                        <td><?php echo $catatanKhusus; ?></td>
                    </tr>
                    <tr>
                        <th>Mahasiswa Baru (Maba)</th>
                        <td><?php echo $isMaba == 1 ? 'Yes' : 'No'; ?></td>
                    </tr>
                    <tr>
                        <th>Metode Bayar</th>
                        <td><?php echo $metodeBayar; ?></td>
                    </tr>
                    <tr>
                        <th>Alamat File</th>
                        <td><?php echo $alamatFile; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    <?php if ($alamatFile && $metodeBayar == "Transfer") : ?>
        <img src="<?php echo $alamatFile; ?>" alt="Bukti Transfer" class="img-fluid" style="max-width: 300px;"><br><br>
    <?php endif; ?>
    <div class="text-center">
    <a href="#daftar_laporan" class="btn btn-secondary mb-3">Kembali</a>
    </div>