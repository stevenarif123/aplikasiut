<?php
require_once "../koneksi.php";
require_once "kode_generator.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
}

$admin = $_SESSION['username'];
$jenis_pembayaran = ["SPP", "Almamater", "Pokjar"];
$kode_laporan = generateKodeLaporan();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nim = $_POST['nim'];
    $namaMahasiswa = $_POST['nama_mahasiswa'];
    $jurusan = $_POST['jurusan'];
    $jenisBayar = $_POST['jenis_bayar'];
    $ut = $_POST['ut'];
    $pokjar = $_POST['pokjar'];
    $total = $ut + $pokjar;
    $adminTulis = $_POST['admin'];
    $catatanKhusus = $_POST['catatan_khusus'];
    $isMaba = isset($_POST['is_maba']) ? 1 : 0;
    $metodeBayar = $_POST['metode_bayar'];
    $alamatFile = "";

    if ($metodeBayar == "Transfer" && isset($_FILES['bukti_file'])) {
        $uploadDir = "BuktiTF/";
        $namaFile = $kode_laporan . "_" . basename($_FILES['bukti_file']['name']);
        $uploadFile = $uploadDir . $namaFile;

        if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $uploadFile)) {
            $alamatFile = './' . $uploadFile;
        } else {
            echo "Error uploading file.";
        }
    }

    $sql = "INSERT INTO laporanuangmasuk (KodeLaporan, JenisBayar, NamaMahasiswa, Nim, Jurusan, Ut, Pokjar, Total, Admin, isMaba, CatatanKhusus, MetodeBayar, AlamatFile)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("sssssssssssss", $kode_laporan, $jenisBayar, $namaMahasiswa, $nim, $jurusan, $ut, $pokjar, $total, $adminTulis, $isMaba, $catatanKhusus, $metodeBayar, $alamatFile);
    $stmt->execute();
    $stmt->close();
    $koneksi->close();

    header("Location: index.php");
    exit;
}

// Get data from query string
$nim = $_GET['nim'] ?? '';
$namaMahasiswa = urldecode($_GET['nama'] ?? '');
$jurusan = urldecode($_GET['jurusan'] ?? '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Laporan Bayar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        @media (min-width: 576px) {
            .container {
                max-width: 540px;
            }
        }
        @media (min-width: 768px) {
            .container {
                max-width: 720px;
            }
        }
        @media (min-width: 992px) {
            .container {
                max-width: 960px;
            }
        }
        @media (min-width: 1200px) {
            .container {
                max-width: 1140px;
            }
        }
    </style>
    <script>
        function toggleUpload(element) {
            if (element.value == "Transfer") {
                document.getElementById("upload_section").style.display = "block";
            } else {
                document.getElementById("upload_section").style.display = "none";
            }
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Tambah Laporan Bayar</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nim" class="form-label">NIM : </label>
            <input type="text" id="nim" name="nim" value="<?php echo $nim; ?>" class="form-control" readonly>
        </div>
        <div class="mb-3">
            <label for="nama_mahasiswa" class="form-label">NAMA MAHASISWA : </label>
            <input type="text" id="nama_mahasiswa" name="nama_mahasiswa" value="<?php echo $namaMahasiswa; ?>" class="form-control" readonly>
        </div>
        <div class="mb-3">
            <label for="jurusan" class="form-label">JURUSAN : </label>
            <input type="text" id="jurusan" name="jurusan" value="<?php echo $jurusan; ?>" class="form-control" readonly>
        </div>
        <div class="mb-3">
            <label for="admin" class="form-label">ADMIN PENGINPUT : </label>
            <input type="text" id="admin" name="admin" value="<?php echo $admin; ?>" class="form-control" readonly>
        </div>
        <div class="mb-3">
            <label for="jenis_bayar" class="form-label">Jenis Bayar:</label>
            <select id="jenis_bayar" name="jenis_bayar" class="form-select" required>
                <option value="">Pilih Jenis Bayar</option>
                <?php
                foreach ($jenis_pembayaran as $jenis) {
                    echo "<option value='$jenis'>$jenis</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="ut" class="form-label">UT:</label>
            <input type="number" id="ut" name="ut" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="pokjar" class="form-label">Pokjar:</label>
            <input type="number" id="pokjar" name="pokjar" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="catatan_khusus" class="form-label">Catatan Khusus:</label>
            <textarea id="catatan_khusus" name="catatan_khusus" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label for="is_maba" class="form-label">Mahasiswa Baru (Maba):</label>
            <input type="checkbox" id="is_maba" name="is_maba" value="1">
        </div>
        <div class="mb-3">
            <label class="form-label">Metode Bayar:</label>
            <div class="form-check">
                <input type="radio" id="metode_transfer" name="metode_bayar" value="Transfer" onchange="toggleUpload(this)" class="form-check-input"> <label for="metode_transfer" class="form-check-label">Transfer</label>
            </div>
            <div class="form-check">
                <input type="radio" id="metode_cash" name="metode_bayar" value="Cash" onchange="toggleUpload(this)" class="form-check-input"> <label for="metode_cash" class="form-check-label">Cash</label>
            </div>
        </div>
        <div id="upload_section" style="display:none;">
            <div class="mb-3">
                <label for="bukti_file" class="form-label">Unggah Gambar:</label>
                <input type="file" id="bukti_file" name="bukti_file" class="form-control" style="display: none;">
                <button type="button" class="btn btn-primary" onclick="document.getElementById('bukti_file').click()">Pilih Gambar</button>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Tambah Laporan Bayar</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
