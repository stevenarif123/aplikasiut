<?php
// Include database connection file
require_once "koneksi.php";
require_once "kode_generator.php";

// Check if session is not active, start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
}


// Get data from query string
$id = $_GET['id'] ?? '';

// Fetch data from the database based on the report code
$query = "SELECT * FROM laporanuangmasuk WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Check if data is found
// if (!$row) {
//     echo "Data not found!";
//     exit;
// }

// Populate variables with retrieved data
$nim = $row['Nim'] ?? '';
$namaMahasiswa = $row['NamaMahasiswa'] ?? '';
$jurusan = $row['Jurusan'] ?? '';
$jenisBayar = $row['JenisBayar'] ?? '';
$ut = $row['Ut'] ?? '';
$pokjar = $row['Pokjar'] ?? '';
$catatanKhusus = $row['CatatanKhusus'] ?? '';
$isMaba = $row['isMaba'] ?? '';
$metodeBayar = $row['MetodeBayar'] ?? '';
$alamatFile = $row['AlamatFile'] ?? '';
$kodeLaporan = $row['KodeLaporan'] ?? '';

// Process form submission
// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $id = $_POST['id'] ?? '';
    $ut = $_POST['ut'] ?? '';
    $pokjar = $_POST['pokjar'] ?? '';
    $total = floatval($ut) + floatval($pokjar);
    $catatanKhusus = $_POST['catatan_khusus'] ?? '';
    $isMaba = isset($_POST['is_maba']) ? 1 : 0;
    $metodeBayar = $_POST['metode_bayar'] ?? '';
    $jenisBayar = $_POST['jenis_bayar'] ?? '';
    $kodeLaporan = $_POST['kode_laporan'] ?? '';

    // Handle file upload if Transfer method is selected
   // Handle file deletion if Transfer method is selected
    $alamatFile = "";
    if ($metodeBayar == "Transfer" && isset($_FILES['bukti_file'])) {
        // Check if there's an existing file
        $existingFile = $row['AlamatFile'];
        if (!empty($existingFile) && file_exists($existingFile)) {
            // Attempt to delete the existing file
            if (unlink($existingFile)) {
                echo "File lama berhasil dihapus.";
            } else {
                echo "Gagal menghapus file lama: " . error_get_last()['message'];
            }
        } else {
            echo "File lama tidak ditemukan atau tidak ada yang dihapus.";
        }

        // Upload the new file
        $uploadDir = "BuktiTF/"; // Folder untuk menyimpan berkas
        $namaFile = $kodeLaporan; // Nama file diubah sesuai dengan kode laporan
        $uploadFile = $uploadDir . $namaFile; // Path lengkap untuk menyimpan berkas

        // Pindahkan berkas yang diunggah ke folder yang ditentukan
        if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $uploadFile)) {
            $alamatFile = './' . $uploadFile; // Tambahkan './' pada awal alamat file
        } else {
            echo "Error uploading file."; // Tampilkan pesan jika terjadi kesalahan saat pengunggahan
        }
    }


    // Prepare SQL query to update data
    $sql = "UPDATE laporanuangmasuk SET Ut = ?, Pokjar = ?, CatatanKhusus = ?, isMaba = ?, MetodeBayar = ?, JenisBayar = ?, AlamatFile = ?, Total = ? WHERE id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("iisssssii", $ut, $pokjar, $catatanKhusus, $isMaba, $metodeBayar, $jenisBayar, $alamatFile, $total, $id);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to index.php if update is successful
        header("Location: index.php");
        exit;
    } else {
        // Redirect to error page if update fails
        header("Location: error.php");
        exit;
    }
}

$jenis_pembayaran = array("SPP", "Almamater", "Pokjar");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan Bayar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
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
            <li><a class="dropdown-item" href="../mahasiswa/mahasiswa.php">Daftar Mahasiswa</a></li>
            <li><a class="dropdown-item" href="../mahasiswa/tambah_data.php">Tambah Mahasiswa</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="./" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Laporan Pembayaran
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item active" href="./">Laporan Bayar</a></li>
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
          <a class="nav-link" aria-current="page" href="../cekstatus/pencarian.php">Cek Status Mahasiswa</a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn btn-warning text-dark fw-bold" href="../logout.php">Keluar</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-5">
    <h1 class="mb-4">Edit Laporan Bayar</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="mb-3">
            <label for="kode_laporan" class="form-label">KODE LAPORAN : </label>
            <input type="text" id="kode_laporan" name="kode_laporan" value="<?php echo $kodeLaporan; ?>" class="form-control" readonly>
        </div>
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
            <label for="jenis_bayar" class="form-label">Jenis Bayar:</label>
            <select id="jenis_bayar" name="jenis_bayar" class="form-select" required>
                <option value="">Pilih Jenis Bayar</option>
                <?php
                // Menampilkan opsi jenis pembayaran dari array
                foreach ($jenis_pembayaran as $jenis) {
                    $selected = ($jenis == $jenisBayar) ? 'selected' : ''; // Tandai opsi yang sesuai dengan nilai jenisBayar dari database
                    echo "<option value='$jenis' $selected>$jenis</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="ut" class="form-label">UT:</label>
            <input type="number" id="ut" name="ut" value="<?php echo $ut; ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="pokjar" class="form-label">Pokjar:</label>
            <input type="number" id="pokjar" name="pokjar" value="<?php echo $pokjar; ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="catatan_khusus" class="form-label">Catatan Khusus:</label>
            <textarea id="catatan_khusus" name="catatan_khusus" class="form-control"><?php echo $catatanKhusus; ?></textarea>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" id="is_maba" name="is_maba" value="1" class="form-check-input" <?php echo $isMaba == 1 ? 'checked' : ''; ?>>
            <label class="form-check-label" for="is_maba">Mahasiswa Baru (Maba)</label>
        </div>
        <div class="mb-3">
            <label class="form-label">Metode Bayar:</label><br>
            <div class="form-check form-check-inline">
                <input type="radio" id="metode_transfer" name="metode_bayar" value="Transfer" onchange="toggleUpload(this)" class="form-check-input" <?php echo $metodeBayar == 'Transfer' ? 'checked' : ''; ?>>
                <label class="form-check-label" for="metode_transfer">Transfer</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" id="metode_cash" name="metode_bayar" value="Cash" onchange="toggleUpload(this)" class="form-check-input" <?php echo $metodeBayar == 'Cash' ? 'checked' : ''; ?>>
                <label class="form-check-label" for="metode_cash">Cash</label>
            </div>
        </div>
        <div id="upload_section" style="display:<?php echo $metodeBayar == 'Transfer' ? 'block' : 'none'; ?>;">
            <?php $alamatFile = $row['AlamatFile']; ?>
            <?php if (!empty($alamatFile)) : ?>
                <img src="<?php echo $alamatFile; ?>" alt="Bukti Transfer" class="img-fluid" style="max-width: 300px;"><br><br>
            <?php else : ?>
                <span>No file uploaded.</span><br><br>
            <?php endif; ?>
            <div class="mb-3">
                <label for="bukti_file" class="form-label">Upload File Bukti Transfer:</label>
                <input type="file" id="bukti_file" name="bukti_file" class="form-control">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
    <div class="mt-3">
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function toggleUpload(selectedMethod) {
            var uploadSection = document.getElementById("upload_section");
            if (selectedMethod.value === "Transfer") {
                uploadSection.style.display = "block";
            } else {
                uploadSection.style.display = "none";
            }
        }
    </script>
</div>
</body>
</html>
