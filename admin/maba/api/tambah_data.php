<?php
require_once "../../koneksi.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$username = $_SESSION['username'];
$query = "SELECT * FROM admin WHERE username='$username'";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);
if (!$result) {
    echo json_encode(["success" => false, "message" => "Query gagal: " . mysqli_error($koneksi)]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jalur_program = $koneksi->real_escape_string(trim($_POST['JalurProgram']));
    $nama_lengkap = $koneksi->real_escape_string(trim($_POST['NamaLengkap']));
    $tempat_lahir = $koneksi->real_escape_string(trim($_POST['TempatLahir']));
    $tanggal_lahir = date('Y-m-d', strtotime($_POST['TanggalLahir']));
    $nama_ibu_kandung = $koneksi->real_escape_string(trim($_POST['NamaIbuKandung']));
    $nik = $koneksi->real_escape_string(trim($_POST['NIK']));
    $jurusan = $koneksi->real_escape_string(trim($_POST['Jurusan']));
    $nomor_hp = $koneksi->real_escape_string(trim($_POST['NomorHP']));
    $email = $koneksi->real_escape_string(trim($_POST['Email']));
    $password = '@'.date('dmY', strtotime($tanggal_lahir)).'Ut';
    $agama = $koneksi->real_escape_string(trim($_POST['Agama']));
    $jenis_kelamin = $koneksi->real_escape_string(trim($_POST['JenisKelamin']));
    $status_perkawinan = $koneksi->real_escape_string(trim($_POST['StatusPerkawinan']));
    $nomor_hp_alternatif = $koneksi->real_escape_string(trim($_POST['NomorHPAlternatif']));
    $nomor_ijazah = $koneksi->real_escape_string(trim($_POST['NomorIjazah']));
    $tahun_ijazah = $koneksi->real_escape_string(trim($_POST['TahunIjazah']));
    $nisn = $koneksi->real_escape_string(trim($_POST['NISN']));
    $layanan_paket_semester = $koneksi->real_escape_string(trim($_POST['LayananPaketSemester']));
    $di_input_oleh = $koneksi->real_escape_string(trim($user['nama_lengkap']));
    $status_input_sia = $koneksi->real_escape_string(trim($_POST['STATUS_INPUT_SIA']));

    $stmt = $koneksi->prepare("INSERT INTO mahasiswabaru ( 
        JalurProgram, 
        NamaLengkap, 
        TempatLahir, 
        TanggalLahir, 
        NamaIbuKandung, 
        NIK, 
        Jurusan, 
        NomorHP, 
        Email, 
        Password, 
        Agama, 
        JenisKelamin, 
        StatusPerkawinan, 
        NomorHPAlternatif, 
        NomorIjazah, 
        TahunIjazah, 
        NISN, 
        LayananPaketSemester, 
        DiInputOleh, 
        STATUS_INPUT_SIA) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $koneksi->error]);
        exit;
    }

    $stmt->bind_param("ssssssssssssssssssss", 
        $jalur_program, 
        $nama_lengkap, 
        $tempat_lahir, 
        $tanggal_lahir, 
        $nama_ibu_kandung, 
        $nik, 
        $jurusan, 
        $nomor_hp, 
        $email, 
        $password, 
        $agama, 
        $jenis_kelamin, 
        $status_perkawinan, 
        $nomor_hp_alternatif, 
        $nomor_ijazah, 
        $tahun_ijazah, 
        $nisn, 
        $layanan_paket_semester, 
        $di_input_oleh, 
        $status_input_sia);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Execute failed: " . $stmt->error]);
        }
}

$sql = "SELECT * FROM prodi_admisi";
$result = $koneksi->query($sql);

// Simpan data jurusan dalam array
$daftarJurusan = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $daftarJurusan[] = $row["nama_program_studi"];
    }
}

// Close the database connection
$koneksi->close();
?>
