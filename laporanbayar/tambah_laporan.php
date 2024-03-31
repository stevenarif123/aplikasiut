<?php
// Include database connection file
require_once "koneksi.php";

// Function to generate report code
function generateKodeLaporan() {
    global $koneksi; // Make the database connection available inside the function

    // Get the last report code from the database
    $query = "SELECT KodeLaporan FROM laporanuangmasuk ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            $lastKode = $row['KodeLaporan'];

            // Process to generate new report code here
            // Example: Increment the numeric part of the code
            $numericPart = substr($lastKode, 2);
            $newNumericPart = str_pad(intval($numericPart) + 1, 4, '0', STR_PAD_LEFT);
            $newKode = "BA" . $newNumericPart;

            return $newKode;
        } else {
            // If no previous data, return the initial code
            return "BA0001";
        }
    } else {
        // If error fetching data, return the initial code
        return "BA0001";
    }
}

// Initialize variables
$hasilPencarian = null;

// If form is submitted (POST request)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['cari_mahasiswa'])) {
    $nama_mahasiswa = trim($_GET['cari_mahasiswa']); // Trim whitespace

    // Assuming your database is case-insensitive:
    $nama_mahasiswa = strtolower($nama_mahasiswa); // Convert to lowercase

    // Query to search for mahasiswa (modified)
    $query = "SELECT * FROM mahasiswa WHERE NamaLengkap LIKE '%$nama_mahasiswa%' OR Nim = '$nama_mahasiswa' ORDER BY No DESC";
    $hasilPencarian = mysqli_query($koneksi, $query);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pencarian Mahasiswa</title>
</head>
<body>

<h1>Pencarian Mahasiswa</h1>

<form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
    <div>
        <label for="cari_mahasiswa">Cari Mahasiswa:</label>
        <input type="text" id="cari_mahasiswa" name="cari_mahasiswa">
        <button type="submit">Cari</button>
    </div>
</form>

<!-- Search results table -->
<table>
    <thead>
    <tr>
        <th>NIM</th>
        <th>Nama Mahasiswa</th>
        <th>Jurusan</th>
        <th>Aksi</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($hasilPencarian && mysqli_num_rows($hasilPencarian) > 0) {
        // Display the table with search results
        while ($row = mysqli_fetch_assoc($hasilPencarian)) {
            echo '<tr>
                      <td>' . $row['Nim'] . '</td>
                      <td>' . $row['NamaLengkap'] . '</td>
                      <td>' . $row['Jurusan'] . '</td>
                      <td>
                          <a href="penambahan.php?nim=' . $row['Nim'] . '&nama=' . urlencode($row['NamaLengkap']) . '&jurusan=' . urlencode($row['Jurusan']) . '">Tambah Laporan Bayar</a>
                      </td>
                  </tr>';
        }
    } else {
        // Display a message if no results are found
        echo "<p>Data mahasiswa tidak ditemukan.</p>";
    }
    ?>
    </tbody>
</table>

</body>
</html>