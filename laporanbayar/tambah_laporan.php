<?php
// Include file koneksi database
require_once "koneksi.php";

// Inisialisasi variabel $hasilPencarian
$hasilPencarian = null;

// Jika formulir disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validasi input disini

    // Generate kode laporan
    $kodeLaporan = generateKodeLaporan();

    // Ambil data dari formulir
    $jenisBayar = $_POST['jenis_bayar'];
    $namaMahasiswa = $_POST['nama_mahasiswa'];
    $nim = $_POST['nim'];
    $jurusan = $_POST['jurusan'];
    $ut = $_POST['ut'];
    $pokjar = $_POST['pokjar'];
    $total = $ut + $pokjar;
    $admin = $_SESSION['username'];
    $isMaba = isset($_POST['is_maba']) ? 1 : 0;
    $catatanKhusus = $_POST['catatan_khusus'];
    $metodeBayar = $_POST['metode_bayar'];

    // Tentukan alamat file jika metode bayar Transfer dan file di-upload
    $alamatFile = ""; // Tentukan alamat file

    // Query untuk memasukkan data ke database
    $sql = "INSERT INTO laporanuangmasuk (KodeLaporan, JenisBayar, NamaMahasiswa, Nim, Jurusan, Ut, Pokjar, Total, Admin, isMaba, CatatanKhusus, MetodeBayar, AlamatFile)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ssssssssdsiis", $kodeLaporan, $jenisBayar, $namaMahasiswa, $nim, $jurusan, $ut, $pokjar, $total, $admin, $isMaba, $catatanKhusus, $metodeBayar, $alamatFile);

    // Eksekusi statement
    if ($stmt->execute()) {
        echo "Laporan berhasil ditambahkan.";
    } else {
        echo "Error: " . $koneksi->error;
    }

    // Tutup statement dan koneksi database
    $stmt->close();
    $koneksi->close();
}

// Fungsi untuk generate kode laporan
function generateKodeLaporan() {
    // Ambil data terakhir dari database
    $query = "SELECT KodeLaporan FROM laporanuangmasuk ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($koneksi, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            $lastKode = $row['KodeLaporan'];
            // Proses generate kode laporan disini
            // ...
            return $newKode;
        } else {
            // Jika tidak ada data sebelumnya
            return "BA0001"; // Contoh kode laporan awal
        }
    } else {
        return "BA0001"; // Jika terjadi kesalahan saat mengambil data
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Laporan Uang Masuk</title>
</head>
<body>

<h1>Tambah Laporan Uang Masuk</h1>

<form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">

    <div>
        <label for="cari_mahasiswa">Cari Mahasiswa:</label>
        <input type="text" id="cari_mahasiswa" name="cari_mahasiswa">
        <button type="submit">Cari</button>
    </div>

    <?php
    // Jika formulir pencarian disubmit
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['cari_mahasiswa'])) {
        $nama_mahasiswa = $_GET['cari_mahasiswa'];

        // Query untuk mencari mahasiswa berdasarkan nama atau nim
        $query = "SELECT * FROM mahasiswa WHERE NamaLengkap LIKE '%$nama_mahasiswa%' OR Nim = '$nama_mahasiswa'";
        $hasilPencarian = mysqli_query($koneksi, $query);

        // Jika hasil pencarian ditemukan
        if ($hasilPencarian && mysqli_num_rows($hasilPencarian) > 0) {
            // Sembunyikan tabel hasil pencarian
            echo '<style>#tabel_pencarian { display: none; }</style>';

            // Tampilkan formulir pembayaran
            echo '<form id="form_laporan" method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" enctype="multipart/form-data">
                    <!-- Isi form input dan pilihan -->
                    <div>
                        <label for="jenis_bayar">Jenis Bayar:</label>
                        <select id="jenis_bayar" name="jenis_bayar">
                            <!-- Opsi jenis bayar -->
                            <option value="Pembayaran">Pembayaran</option>
                            <option value="Baju">Baju</option>
                            <option value="Almamater">Almamater</option>
                        </select>
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
                        <label for="is_maba">Mahasiswa Baru:</label>
                        <input type="checkbox" id="is_maba" name="is_maba" value="1">
                    </div>

                    <div>
                        <label for="catatan_khusus">Catatan Khusus:</label>
                        <textarea id="catatan_khusus" name="catatan_khusus"></textarea>
                    </div>

                    <div>
                        <label for="metode_bayar">Metode Bayar:</label>
                        <select id="metode_bayar" name="metode_bayar">
                            <option value="Cash">Cash</option>
                            <option value="Transfer">Transfer</option>
                        </select>
                    </div>

                    <div id="bukti_transfer" style="display: none;">
                        <label for="bukti_file">Bukti Transfer:</label>
                        <input type="file" id="bukti_file" name="bukti_file">
                    </div>

                    <button type="submit">Simpan</button>
                </form>';
        } elseif ($hasilPencarian && mysqli_num_rows($hasilPencarian) == 0) {
            // Tampilkan pesan jika tidak ada hasil pencarian
            echo '<p>Data mahasiswa tidak ditemukan.</p>';
        }
    }
    ?>

    <!-- Tampilkan tabel hasil pencarian -->
    <table id="tabel_pencarian">
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
            // Ambil data hasil pencarian
            while ($row = mysqli_fetch_assoc($hasilPencarian)) {
                echo '<tr>
                        <td>' . $row['Nim'] . '</td>
                        <td>' . $row['NamaLengkap'] . '</td>
                        <td>' . $row['Jurusan'] . '</td>
                        <td>
                            <button type="button" onclick="pilihMahasiswa(\'' . $row['Nim'] . '\', \'' . $row['NamaLengkap'] . '\', \'' . $row['Jurusan'] . '\')">Pilih</button>
                        </td>
                    </tr>';
            }
            ?>
        </tbody>
    </table>

<script>
    function pilihMahasiswa(nim, nama, jurusan) {
        // Sembunyikan tabel hasil pencarian
        document.getElementById("tabel_pencarian").style.display = "none";

        // Set nilai input field dengan data mahasiswa yang dipilih
        document.getElementById("cari_mahasiswa").value = nama;
        document.getElementById("cari_mahasiswa").readOnly = true; // Nonaktifkan input pencarian
        document.getElementById("Nim").value = nim;
        document.getElementById("NamaLengkap").value = nama;
        document.getElementById("Jurusan").value = jurusan;

        // Tampilkan formulir pembayaran
        document.getElementById("form_laporan").style.display = "block";
    }
</script>

</body>
</html>
