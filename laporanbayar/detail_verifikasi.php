<?php
// Include database connection file
require_once "koneksi.php";

// Check if session is not active, start the session
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if user is not logged in or not a verificator
if (!isset($_SESSION['username']) || $_SESSION['peran'] != 'verifikator') {
    header("Location: ../login.php?error=1");
    exit; // Stop further execution
}

// Check if ID parameter exists in the URL
if (!isset($_GET['id'])) {
    echo "ID parameter is missing.";
    exit;
}

$verifikator = $_SESSION['username'];
// Fetch data based on ID from the database
$id = $_GET['id'];
$query = "SELECT * FROM laporanuangmasuk WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Check if data is found
if ($result->num_rows <= 0) {
    echo "Data not found.";
    exit;
}

// Fetch the row
$row = $result->fetch_assoc();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reject'])) {
    $catatanKhusus = $_POST['catatan_khusus'] ?? '';

    // Update the 'isVerifikasi' column to 1 (rejected) and set special notes
    $query_update = "UPDATE laporanuangmasuk SET isVerifikasi = ?, CatatanKhusus = ?, Verifikator = ? WHERE id = ?";
    $stmt_update = $koneksi->prepare($query_update);
    $isVerifikasi = 0; // Set to 0 (rejected)
    $stmt_update->bind_param("issi", $isVerifikasi, $catatanKhusus, $verifikator, $id);
    if ($stmt_update->execute()) {
        header("Location: verifikasi_laporan.php");
        exit;
    } else {
        echo "Error updating record.";
    }
}

// Directly update 'isVerifikasi' column to 1 (verified)
if (isset($_GET['verifikasi']) && $_GET['verifikasi'] == "true") {
    $query_verifikasi = "UPDATE laporanuangmasuk SET isVerifikasi = ? WHERE id = ?";
    $stmt_verifikasi = $koneksi->prepare($query_verifikasi);
    $isVerifikasi = 1; // Set to 1 (verified)
    $stmt_verifikasi->bind_param("ii", $isVerifikasi, $id);
    if ($stmt_verifikasi->execute()) {
        header("Location: verifikasi_laporan.php");
        exit;
    } else {
        echo "Error updating record.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Verifikasi Laporan</title>
</head>
<body>

<h1>Detail Verifikasi Laporan</h1>

<!-- Display details of the payment -->
<div>
    <p>Kode Laporan: <?php echo $row['KodeLaporan']; ?></p>
    <p>Jenis Bayar: <?php echo $row['JenisBayar']; ?></p>
    <p>Tanggal Input: <?php echo $row['TanggalInput']; ?></p>
    <p>Nama Mahasiswa: <?php echo $row['NamaMahasiswa']; ?></p>
    <p>NIM: <?php echo $row['Nim']; ?></p>
    <p>Jurusan: <?php echo $row['Jurusan']; ?></p>
    <p>UT: <?php echo $row['Ut']; ?></p>
    <p>Pokjar: <?php echo $row['Pokjar']; ?></p>
    <p>Admin: <?php echo $row['Admin']; ?></p>
    <p>Catatan Khusus: <?php echo $row['CatatanKhusus']; ?></p>
    <p>Metode Bayar: <?php echo $row['MetodeBayar']; ?></p>
    <p>Alamat File: <?php echo $row['AlamatFile']; ?></p>
    <img src="<?php echo $row['AlamatFile']; ?>" alt="Bukti Transfer" style="max-width: 300px;"><br><br>
</div>

<?php if ($row['isVerifikasi'] == 0) : ?>
    <!-- Form for rejecting payment with special notes -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>">
        <label for="catatan_khusus">Catatan Khusus untuk Penolakan:</label><br>
        <textarea id="catatan_khusus" name="catatan_khusus" required></textarea><br><br>
        <button type="submit" name="reject">Tolak</button>
    </form>
<?php endif; ?>

<!-- Button for verification -->
<a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id . "&verifikasi=true"); ?>">Verifikasi</a>

<!-- Button to return to verification page -->
<a href="verifikasi_laporan.php">Kembali</a>

</body>
</html>
