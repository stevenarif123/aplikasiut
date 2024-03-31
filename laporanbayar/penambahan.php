<?php
// Include database connection file
require_once "koneksi.php";

// Initialize variables
$jenisBayar = $ut = $pokjar = $catatanKhusus = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input data (add your validation logic here)

    // Get data from form
    $kodeLaporan = generateKodeLaporan();
    $nim = $_POST['nim'];
    $namaMahasiswa = $_POST['nama_mahasiswa'];
    $jurusan = $_POST['jurusan'];
    $jenisBayar = $_POST['jenis_bayar'];
    $ut = $_POST['ut'];
    $pokjar = $_POST['pokjar'];
    $total = $ut + $pokjar;
    $catatanKhusus = $_POST['catatan_khusus'];

    // Insert data into database
    $sql = "INSERT INTO laporanuangmasuk (KodeLaporan, JenisBayar, NamaMahasiswa, Nim, Jurusan, Ut, Pokjar, Total, CatatanKhusus) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("sssssssss", $kodeLaporan, $jenisBayar, $namaMahasiswa, $nim, $jurusan, $ut, $pokjar, $total, $catatanKhusus);

    if ($stmt->execute()) {
        echo "Laporan berhasil ditambahkan.";
    } else {
        echo "Error: " . $koneksi->error;
    }

    // Close statement and database connection
    $stmt->close();
    $koneksi->close();
}

// Get data from query string
$nim = $_GET['nim'] ?? '';
$namaMahasiswa = urldecode($_GET['nama'] ?? '');
$jurusan = urldecode($_GET['jurusan'] ?? '');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Laporan Bayar</title>
</head>
<body>

<h1>Tambah Laporan Bayar</h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
    <label for="nim" ><?php echo $nim; ?></label>
    <label for="nama_mahasiswa"><?php echo $namaMahasiswa; ?></label>
    <label for="jurusan"><?php echo $jurusan; ?></label>
    <div>
        <label for="jenis_bayar">Jenis Bayar:</label>
        <input type="text" id="jenis_bayar" name="jenis_bayar" required>
    </div>
    <div>
        <label for="ut">UT:</label>
        <input type="number" id="ut" name="ut" required>
    </div>
    <div>
        <label for="pokjar">Pokjar:</label>
        <input type="number" id="pokjar" name="pokjar" required>
    </div>
    <div>
        <label for="catatan_khusus">Catatan Khusus:</label>
        <textarea id="catatan_khusus" name="catatan_khusus"></textarea>
    </div>
    <button type="submit">Tambah Laporan Bayar</button>
</form>

</body>
</html>