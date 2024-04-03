<?php
// Include database connection file
require_once "koneksi.php";
require_once "kode_generator.php";

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
$kodeLaporan = $row['KodeLaporan'];
// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $ut = $_POST['ut'] ?? '';
    $pokjar = $_POST['pokjar'] ?? '';
    $catatanKhusus = $_POST['catatan_khusus'] ?? '';
    $isMaba = isset($_POST['is_maba']) ? 1 : 0;
    $metodeBayar = $_POST['metode_bayar'] ?? '';
    $jenisBayar = $_POST['jenis_bayar'] ??'';


    $alamatFile = "";
    if ($metodeBayar == "Transfer" && isset($_FILES['bukti_file'])) {
        $uploadDir = "BuktiTF/"; // Folder untuk menyimpan berkas
        $namaFile = $kodeLaporan . "_" . basename($_FILES['bukti_file']['name']); // Nama file diubah sesuai dengan kode laporan
        $uploadFile = $uploadDir . $namaFile; // Path lengkap untuk menyimpan berkas
        // Pindahkan berkas yang diunggah ke folder yang ditentukan
        if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $uploadFile)) {
            $alamatFile = './' . $uploadFile; // Tambahkan './' pada awal alamat file
        } else {
            echo "Error uploading file."; // Tampilkan pesan jika terjadi kesalahan saat pengunggahan
        }
    }

    // Prepare SQL query to update data
    $sql = "UPDATE laporanuangmasuk SET Ut = ?, Pokjar = ?, CatatanKhusus = ?, isMaba = ?, MetodeBayar = ?, JenisBayar = ?, AlamatFile = ? WHERE id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ssssssss", $ut, $pokjar, $catatanKhusus, $isMaba, $metodeBayar, $jenisBayar, $alamatFile, $id);


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
<html>
<head>
    <title>Edit Laporan Bayar</title>
</head>
<body>

<h1>Edit Laporan Bayar</h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <label for="kode_laporan">KODE LAPORAN : </label>
    <input type="text" id="kode_laporan" name="kode_laporan" value="<?php echo $kodeLaporan; ?>" readonly><br><br>

    <label for="nim">NIM : </label>
    <input type="text" id="nim" name="nim" value="<?php echo $nim; ?>" readonly><br><br>

    <label for="nama_mahasiswa">NAMA MAHASISWA : </label>
    <input type="text" id="nama_mahasiswa" name="nama_mahasiswa" value="<?php echo $namaMahasiswa; ?>" readonly><br><br>

    <label for="jurusan">JURUSAN : </label>
    <input type="text" id="jurusan" name="jurusan" value="<?php echo $jurusan; ?>" readonly><br><br>

    <div>
        <label for="jenis_bayar">Jenis Bayar:</label>
        <select id="jenis_bayar" name="jenis_bayar" required>
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


    <label for="ut">UT:</label>
    <input type="number" id="ut" name="ut" value="<?php echo $ut; ?>" required><br><br>

    <label for="pokjar">Pokjar:</label>
    <input type="number" id="pokjar" name="pokjar" value="<?php echo $pokjar; ?>" required><br><br>

    <label for="catatan_khusus">Catatan Khusus:</label>
    <textarea id="catatan_khusus" name="catatan_khusus"><?php echo $catatanKhusus; ?></textarea><br><br>

    <label for="is_maba">Mahasiswa Baru (Maba):</label>
    <input type="checkbox" id="is_maba" name="is_maba" value="1" <?php echo $isMaba == 1 ? 'checked' : ''; ?>><br><br>

    <label>Metode Bayar:</label><br>
    <input type="radio" id="metode_transfer" name="metode_bayar" value="Transfer" onchange="toggleUpload(this)" <?php echo $metodeBayar == 'Transfer' ? 'checked' : ''; ?>> <label for="metode_transfer">Transfer</label><br>
    <input type="radio" id="metode_cash" name="metode_bayar" value="Cash" onchange="toggleUpload(this)" <?php echo $metodeBayar == 'Cash' ? 'checked' : ''; ?>> <label for="metode_cash">Cash</label><br><br>

    <div id="upload_section" style="display:<?php echo $metodeBayar == 'Transfer' ? 'block' : 'none'; ?>;">
        <?php $alamatFile = $row['AlamatFile']; ?>
        <?php if (!empty($alamatFile)) : ?>
            <img src="<?php echo $alamatFile; ?>" alt="Bukti Transfer" style="max-width: 300px;"><br><br>
        <?php else : ?>
            <span>No file uploaded.</span><br><br>
        <?php endif; ?>
        
        <label for="bukti_file">Upload File Bukti Transfer:</label>
        <input type="file" id="bukti_file" name="bukti_file">
    </div>

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
    <button type="submit">Simpan Perubahan</button>
</form>
<div>
    <button onclick="window.location.href='index.php';">Kembali</button>
</div>


</body>
</html>