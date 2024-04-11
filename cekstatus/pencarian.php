<?php
// Buat koneksi ke database
require_once "../koneksi.php";

// Inisialisasi variabel untuk menyimpan hasil pencarian
$dataMahasiswa = array();

// Proses pencarian data mahasiswa jika parameter pencarian diberikan
$pencarian = isset($_GET['pencarian']) ? $_GET['pencarian'] : "";
$kriteria = isset($_GET['kriteria']) ? $_GET['kriteria'] : "nama";

// Lakukan pembersihan nilai pencarian untuk mencegah serangan SQL Injection
$pencarian = $koneksi->real_escape_string($pencarian);

// Bangun kueri SQL berdasarkan kriteria pencarian
$sql = "SELECT No, Nim, NamaLengkap AS Nama, Jurusan FROM mahasiswa WHERE ";

if ($kriteria == 'nim') {
    $sql .= "Nim LIKE '%$pencarian%'";
} elseif ($kriteria == 'nama') {
    $sql .= "NamaLengkap LIKE '%$pencarian%'";
} elseif ($kriteria == 'jurusan') {
    $sql .= "Jurusan LIKE '%$pencarian%'";
}

// Lakukan kueri ke database
$result = $koneksi->query($sql);

// Periksa apakah kueri berhasil dieksekusi
if (!$result) {
    die("Kesalahan dalam eksekusi kueri: " . $koneksi->error);
}

// Periksa apakah ada hasil yang ditemukan
if ($result->num_rows > 0) {
    // Loop melalui hasil kueri dan simpan ke dalam array
    while($row = $result->fetch_assoc()) {
        $dataMahasiswa[] = $row;
    }
} else {
    echo "Tidak ada hasil yang ditemukan.";
}

// Tutup koneksi database
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Data Mahasiswa</title>
</head>
<body>
    <h1>Pencarian Data Mahasiswa</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
        <label for="pencarian">Masukkan Kata Kunci:</label><br>
        <input type="text" id="pencarian" name="pencarian" value="<?php echo isset($_GET['pencarian']) ? $_GET['pencarian'] : ''; ?>"><br><br>
        
        <label for="kriteria">Pilih Kriteria Pencarian:</label><br>
        <select id="kriteria" name="kriteria">
            <option value="nim" <?php if ($kriteria == 'nim') echo 'selected'; ?>>NIM</option>
            <option value="nama" <?php if ($kriteria == 'nama') echo 'selected'; ?>>Nama</option>
            <option value="jurusan" <?php if ($kriteria == 'jurusan') echo 'selected'; ?>>Jurusan</option>
        </select><br><br>
        
        <input type="submit" value="Cari">
    </form>

    <?php if (!empty($dataMahasiswa)): ?>
    <h2>Hasil Pencarian Data Mahasiswa</h2>
    <table border="1">
        <tr>
            <th>NIM</th>
            <th>Nama</th>
            <th>Jurusan</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($dataMahasiswa as $mahasiswa): ?>
        <tr>
            <td><?php echo $mahasiswa['Nim']; ?></td>
            <td><?php echo $mahasiswa['Nama']; ?></td>
            <td><?php echo $mahasiswa['Jurusan']; ?></td>
            <td><a href="statusbayar.php?No=<?php echo $mahasiswa['No']; ?>">Status Bayar</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</body>
</html>
