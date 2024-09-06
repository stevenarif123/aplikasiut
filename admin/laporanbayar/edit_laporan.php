<?php
// Include database connection file
require_once "../koneksi.php";
require_once "kode_generatorlaporan.php";

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
$query = "SELECT * FROM laporanuangmasuk20242 WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Check if data is found
if (!$row) {
    echo "Data not found!";
    exit;
}

// Populate variables with retrieved data
$nim = $row['Nim'] ?? '';
$namaMahasiswa = $row['NamaMahasiswa'] ?? '';
$jurusan = $row['Jurusan'] ?? '';
$jumlahBayar = $row['JumlahBayar'] ?? '';
$catatanKhusus = $row['CatatanKhusus'] ?? '';
$metodeBayar = $row['MetodeBayar'] ?? '';
$alamatFile = $row['AlamatFile'] ?? '';
$kodeLaporan = $row['KodeLaporan'] ?? '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $id = $_POST['id'] ?? '';
    $jumlahBayar = $_POST['jumlah_bayar'] ?? 0;
    $catatanKhusus = $_POST['catatan_khusus'] ?? '';
    $metodeBayar = $_POST['metode_bayar'] ?? '';
    $kodeLaporan = $_POST['kode_laporan'] ?? '';

    // Handle file upload if Transfer method is selected
    $alamatFile = $row['AlamatFile'];
    if ($metodeBayar == "Transfer" && isset($_FILES['bukti_file'])) {
        // Check if there's an existing file
        $existingFile = $row['AlamatFile'];
        if (!empty($existingFile) && file_exists($existingFile)) {
            // Attempt to delete the existing file
            unlink($existingFile);
        }

        // Upload the new file
        $uploadDir = "BuktiTF/"; // Folder untuk menyimpan berkas
        $ext = pathinfo($_FILES['bukti_file']['name'], PATHINFO_EXTENSION);
        $namaFile = $kodeLaporan . '.' . $ext; // Nama file diubah sesuai dengan kode laporan
        $uploadFile = $uploadDir . $namaFile; // Path lengkap untuk menyimpan berkas

        // Pindahkan berkas yang diunggah ke folder yang ditentukan
        if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $uploadFile)) {
            $alamatFile = './' . $uploadFile; // Tambahkan './' pada awal alamat file
        } else {
            echo "Error uploading file."; // Tampilkan pesan jika terjadi kesalahan saat pengunggahan
        }
    }

    // Prepare SQL query to update data
    $sql = "UPDATE laporanuangmasuk20242 SET JumlahBayar = ?, CatatanKhusus = ?, MetodeBayar = ?, AlamatFile = ? WHERE id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("dsssi", $jumlahBayar, $catatanKhusus, $metodeBayar, $alamatFile, $id);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to index.php if update is successful
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='index.php';</script>";
    } else {
        // Redirect to error page if update fails
        echo "<script>alert('Gagal memperbarui data: " . $stmt->error . "'); window.location.href='index.php';</script>";
    }
}

$jenis_pembayaran = "Pembayaran";

?>

    <h1 class="mb-4">Edit Laporan Bayar</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="mb-3">
            <label for="kode_laporan" class="form-label">KODE LAPORAN:</label>
            <input type="text" id="kode_laporan" name="kode_laporan" value="<?php echo $kodeLaporan; ?>" class="form-control" readonly>
        </div>
        <div class="mb-3">
            <label for="nim" class="form-label">NIM:</label>
            <input type="text" id="nim" name="nim" value="<?php echo $nim; ?>" class="form-control" readonly>
        </div>
        <div class="mb-3">
            <label for="nama_mahasiswa" class="form-label">NAMA MAHASISWA:</label>
            <input type="text" id="nama_mahasiswa" name="nama_mahasiswa" value="<?php echo $namaMahasiswa; ?>" class="form-control" readonly>
        </div>
        <div class="mb-3">
            <label for="jurusan" class="form-label">JURUSAN:</label>
            <input type="text" id="jurusan" name="jurusan" value="<?php echo $jurusan; ?>" class="form-control" readonly>
        </div>
        <div class="mb-3">
            <label for="jumlah_bayar" class="form-label">Jumlah Bayar:</label>
            <input type="number" id="jumlah_bayar" name="jumlah_bayar" value="<?php echo $jumlahBayar; ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="catatan_khusus" class="form-label">Catatan Khusus:</label>
            <textarea id="catatan_khusus" name="catatan_khusus" class="form-control"><?php echo $catatanKhusus; ?></textarea>
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
        <a href="#daftar_laporan" class="btn btn-secondary mb-3">Kembali</a>
    </form>
    <div class="mt-3">
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </div>
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