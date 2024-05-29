<?php
require_once "koneksi.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated']);
    exit;
}

function generateKodeLaporan() {
    global $koneksi;
    $query = "SELECT KodeLaporan FROM laporanuangmasuk ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($koneksi, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            $lastKode = $row['KodeLaporan'];
            $numericPart = substr($lastKode, 2);
            $newNumericPart = str_pad(intval($numericPart) + 1, 4, '0', STR_PAD_LEFT);
            return "BA" . $newNumericPart;
        }
    }
    return "BA0001";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_laporan = generateKodeLaporan();
    $nim = $_POST['nim'] ?? '';
    $namaMahasiswa = $_POST['nama_mahasiswa'] ?? '';
    $jurusan = $_POST['jurusan'] ?? '';
    $jenisBayar = $_POST['jenis_bayar'] ?? '';
    $ut = $_POST['ut'] ?? '';
    $pokjar = $_POST['pokjar'] ?? '';
    $total = $ut + $pokjar;
    $admin = $_SESSION['username'];
    $catatanKhusus = $_POST['catatan_khusus'] ?? '';
    $isMaba = isset($_POST['is_maba']) ? 1 : 0;
    $metodeBayar = $_POST['metode_bayar'] ?? '';

    $alamatFile = "";
    if ($metodeBayar == "Transfer" && isset($_FILES['bukti_file'])) {
        $uploadDir = "BuktiTF/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $namaFile = $kode_laporan . "_" . basename($_FILES['bukti_file']['name']);
        $uploadFile = $uploadDir . $namaFile;
        if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $uploadFile)) {
            $alamatFile = './' . $uploadFile;
        } else {
            echo json_encode(['success' => false, 'error' => 'Error uploading file']);
            exit;
        }
    }

    $sql = "INSERT INTO laporanuangmasuk (KodeLaporan, JenisBayar, NamaMahasiswa, Nim, Jurusan, Ut, Pokjar, Total, Admin, isMaba, CatatanKhusus, MetodeBayar, AlamatFile)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssssssssssss", $kode_laporan, $jenisBayar, $namaMahasiswa, $nim, $jurusan, $ut, $pokjar, $total, $admin, $isMaba, $catatanKhusus, $metodeBayar, $alamatFile);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error executing query']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Error preparing SQL statement']);
    }
    $koneksi->close();
}
?>
