<?php

// Include database connection file
require_once "koneksi.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
  if (!isset($_SESSION['username'])) {
    header("Location: login.php");
  }

// Function to generate report code
function generateKodeLaporan($jenisBayar) {
    // Mendefinisikan kode untuk setiap jenis pembayaran
    $kodeJenisBayar = [
        "SPP" => "SP",
        "Almamater" => "AL",
        "Pokjar" => "PK"
    ];

    // Mendapatkan kode jenis pembayaran untuk jenis pembayaran yang diberikan
    $kodeJenisPembayaran = $kodeJenisBayar[$jenisBayar];

    // Get the last report code for the given payment type
    global $koneksi;
    $query = "SELECT KodeLaporan FROM laporanuangmasuk WHERE JenisBayar = ? ORDER BY id DESC LIMIT 1";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("s", $jenisBayar);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $row = $result->fetch_assoc();
        if ($row) {
            $lastKode = $row['KodeLaporan'];

            // Mengambil bagian angka dari kode laporan terakhir
            $numericPart = substr($lastKode, 2);
            $newNumericPart = str_pad(intval($numericPart) + 1, 4, '0', STR_PAD_LEFT); // Increment angka dengan padding 4 digit
        } else {
            // Jika tidak ada data sebelumnya, mengembalikan angka awal
            $newNumericPart = "0001";
        }
    } else {
        // Jika terjadi kesalahan saat mengambil data, tampilkan pesan error
        echo "Error fetching last report code: " . $koneksi->error;
        return false;
    }

    // Menggabungkan kode jenis pembayaran dengan angka yang telah di-generate
    $newKode = $kodeJenisPembayaran . $newNumericPart;

    return $newKode;
}

$admin = $_SESSION['username'];

// Initialize variables
$hasilPencarian = null;

// Array jenis pembayaran
$jenis_pembayaran = array("SPP", "Almamater", "Pokjar");

// If form is submitted (POST request)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate input data
    // Add your validation logic here

    // Generate report code
    $kodeLaporan = generateKodeLaporan();

    // Get data from form
    $nim = $_POST['nim'] ?? '';
    $namaMahasiswa = $_POST['nama_mahasiswa'] ?? '';
    $jurusan = $_POST['jurusan'] ?? '';
    $jenisBayar = $_POST['jenis_bayar'] ?? '';
    $ut = $_POST['ut'] ?? '';
    $pokjar = $_POST['pokjar'] ?? '';
    $total = $ut + $pokjar;
    $adminTulis = $_POST['admin'];
    $catatanKhusus = $_POST['catatan_khusus'] ?? '';
    $isMaba = isset($_POST['is_maba']) ? 1 : 0;
    $metodeBayar = $_POST['metode_bayar'] ?? '';

    // Handle file upload if payment method is Transfer
    // Pastikan folder BuktiTF ada dan memiliki izin penulisan yang tepat
    $uploadDir = "BuktiTF/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Buat folder jika belum ada
    }

    // Handle file upload if payment method is Transfer
    $alamatFile = "";
    if ($metodeBayar == "Transfer" && isset($_FILES['bukti_file'])) {
        $uploadDir = "BuktiTF/"; // Folder untuk menyimpan berkas
        $namaFile = generateKodeLaporan() . "_" . basename($_FILES['bukti_file']['name']); // Nama file diubah sesuai dengan kode laporan
        $uploadFile = $uploadDir . $namaFile; // Path lengkap untuk menyimpan berkas

        // Pindahkan berkas yang diunggah ke folder yang ditentukan
        if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $uploadFile)) {
            $alamatFile = $uploadFile; // Simpan alamat file ke dalam variabel
        } else {
            echo "Error uploading file."; // Tampilkan pesan jika terjadi kesalahan saat pengunggahan
        }
    }


    // Prepare SQL query to insert data
    $sql = "INSERT INTO laporanuangmasuk (KodeLaporan, JenisBayar, NamaMahasiswa, Nim, Jurusan, Ut, Pokjar, Total, Admin, isMaba, CatatanKhusus, MetodeBayar, AlamatFile)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $koneksi->prepare($sql);
    if (!$stmt) {
        echo "Error preparing SQL statement: " . $koneksi->error; // Debugging
    } else {
        // Bind parameters to the statement
        $bindResult = $stmt->bind_param("sssssssssssss", $kodeLaporan, $jenisBayar, $namaMahasiswa, $nim, $jurusan, $ut, $pokjar, $total, $adminTulis, $isMaba, $catatanKhusus, $metodeBayar, $alamatFile);
        if (!$bindResult) {
            echo "Error binding parameters: " . $stmt->error; // Debugging
        } else {
            // Execute the query
            $executeResult = $stmt->execute();
            if ($executeResult) {
                header("Location: index.php");
            } else {
                echo "Error executing query: " . $stmt->error; // Debugging
            }
        }
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
    <h2>Halo Admin  <?php echo $admin; ?></h2>
    <label for="nim">NIM : </label>
    <input type="text" id="nim" name="nim" value="<?php echo $nim; ?>" readonly>

    <label for="nama_mahasiswa">NAMA MAHASISWA : </label>
    <input type="text" id="nama_mahasiswa" name="nama_mahasiswa" value="<?php echo $namaMahasiswa; ?>" readonly>

    <label for="jurusan">JURUSAN : </label>
    <input type="text" id="jurusan" name="jurusan" value="<?php echo $jurusan; ?>" readonly>

    <label for="admin">ADMIN PENGINPUT : </label>
    <input type="text" id="admin" name="admin" value="<?php echo $admin; ?>" readonly>

    <div>
        <label for="jenis_bayar">Jenis Bayar:</label>
        <select id="jenis_bayar" name="jenis_bayar" required>
            <option value="">Pilih Jenis Bayar</option>
            <?php
            // Menampilkan opsi jenis pembayaran dari array
            foreach ($jenis_pembayaran as $jenis) {
                echo "<option value='$jenis'>$jenis</option>";
            }
            ?>
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
        <label for="catatan_khusus">Catatan Khusus:</label>
        <textarea id="catatan_khusus" name="catatan_khusus"></textarea>
    </div>
    <div>
        <label for="is_maba">Mahasiswa Baru (Maba):</label>
        <input type="checkbox" id="is_maba" name="is_maba" value="1">
    </div>
    <div>
    <label>Metode Bayar:</label>
    <div>
        <input type="radio" id="metode_transfer" name="metode_bayar" value="Transfer" onchange="toggleUpload(this)"> <label for="metode_transfer">Transfer</label>
        <input type="radio" id="metode_cash" name="metode_bayar" value="Cash" onchange="toggleUpload(this)"> <label for="metode_cash">Cash</label>
    </div>
    </div>

    <div id="upload_section" style="display:none;">
        <div>
            <label for="bukti_file">Upload File Bukti Transfer:</label>
            <input type="file" id="bukti_file" name="bukti_file">
        </div>
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

    <button type="submit">Tambah Laporan Bayar</button>
</form>

</body>
</html>