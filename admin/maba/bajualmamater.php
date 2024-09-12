<?php
// Koneksi ke database
require_once "../koneksi.php";

// Query untuk mengambil data mahasiswa yang memenuhi syarat
$query = "
    SELECT catatan.nama_lengkap, catatan.almamater, catatan.status_admisi, catatan.sisa, mahasiswa.UkuranBaju
    FROM catatan_bayarmaba20242 AS catatan
    JOIN mahasiswabaru20242 AS mahasiswa
    ON catatan.nama_lengkap = mahasiswa.NamaLengkap
    WHERE catatan.almamater = 200000 
    AND catatan.status_admisi = 'lunas' 
    AND catatan.sisa >= 0
";

$result = $koneksi->query($query);

// Cek apakah query berhasil dieksekusi
if (!$result) {
    // Tampilkan pesan error jika query gagal
    die("Query error: " . $koneksi->error);
}

// Buat array untuk menghitung ukuran baju
$size_counts = ['S' => 0, 'M' => 0, 'L' => 0, 'XL' => 0, 'XXL' => 0];

// Buat tampilan tabel dengan Bootstrap 4.5
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Daftar Pemesanan Baju Almamater</title>
</head>
<body>
<div class="container">
    <h2 class="my-4">Daftar Mahasiswa Pemesan Baju Almamater</h2>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Nama Lengkap</th>
                <th>Ukuran Baju</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Output data setiap mahasiswa
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['nama_lengkap'] . "</td>";
                    echo "<td>" . $row['UkuranBaju'] . "</td>";
                    echo "</tr>";

                    // Hitung ukuran baju
                    $size = $row['UkuranBaju'];
                    if (array_key_exists($size, $size_counts)) {
                        $size_counts[$size]++;
                    }
                }
            } else {
                echo "<tr><td colspan='2'>Tidak ada data yang sesuai.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <h4>Summary Ukuran Baju</h4>
    <ul>
        <?php
        foreach ($size_counts as $size => $count) {
            echo "<li>Ukuran $size: $count</li>";
        }
        ?>
    </ul>

    <h4>Hitung Total Cost</h4>
    <form>
        <div class="form-group">
            <label for="hargaBaju">Harga per Baju (Rp)</label>
            <input type="number" class="form-control" id="hargaBaju" placeholder="Masukkan harga per baju">
        </div>
        <div class="form-group">
            <label for="ongkosKirim">Ongkos Kirim (Rp)</label>
            <input type="number" class="form-control" id="ongkosKirim" placeholder="Masukkan ongkos kirim">
        </div>
        <div class="form-group">
            <label for="jumlahBaju">Jumlah Baju</label>
            <input type="number" class="form-control" id="jumlahBaju" value="<?= array_sum($size_counts) ?>" readonly>
        </div>
        <div class="form-group">
            <label for="totalCost">Total Biaya (Rp)</label>
            <input type="text" class="form-control" id="totalCost" readonly>
        </div>
    </form>

    <button class="btn btn-primary" onclick="hitungTotal()">Hitung Total</button>
</div>

<script>
function hitungTotal() {
    var hargaBaju = parseInt(document.getElementById('hargaBaju').value);
    var ongkosKirim = parseInt(document.getElementById('ongkosKirim').value);
    var jumlahBaju = parseInt(document.getElementById('jumlahBaju').value);
    var totalCost = (hargaBaju * jumlahBaju) + ongkosKirim;
    document.getElementById('totalCost').value = totalCost;
}
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$koneksi->close();
?>
