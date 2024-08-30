<?php
require_once "../../koneksi.php";
require_once "../kode_generator.php"; // Include the code generator

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

$response = ["success" => false, "message" => ""];

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
    $ukuranbaju = $koneksi->real_escape_string(trim($_POST['UkuranBaju']));
    $di_input_pada = date('Y-m-d H:i:s'); // Add current timestamp for DiInputPada
    $di_edit_pada = date('Y-m-d H:i:s'); // Set current timestamp for DiEditPada

    // Fields specific to RPL and Reguler
    $asal_kampus = $koneksi->real_escape_string(trim($_POST['AsalKampus']));
    $tahun_lulus_kampus = $koneksi->real_escape_string(trim($_POST['TahunLulusKampus']));
    $ipk = $koneksi->real_escape_string(trim($_POST['IPK']));
    $jenis_sekolah = $koneksi->real_escape_string(trim($_POST['JenisSekolah']));
    $jurusan_smk = $koneksi->real_escape_string(trim($_POST['JurusanSMK']));
    $nama_sekolah = $koneksi->real_escape_string(trim($_POST['NamaSekolah']));
    
    $stmt = $koneksi->prepare("INSERT INTO mahasiswabaru20242 ( 
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
        DiInputPada,
        DiEditPada,
        STATUS_INPUT_SIA,
        UkuranBaju,
        AsalKampus,
        TahunLulusKampus,
        IPK,
        JenisSekolah,
        NamaSekolah,
        JurusanSMK) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        $response["message"] = "Prepare failed: " . $koneksi->error;
        echo json_encode($response);
        exit;
    }

    $stmt->bind_param("sssssssssssssssssssssssssssss", 
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
        $di_input_pada, 
        $di_edit_pada, 
        $status_input_sia,
        $ukuranbaju,
        $asal_kampus,
        $tahun_lulus_kampus,
        $ipk,
        $jenis_sekolah,
        $nama_sekolah,
        $jurusan_smk);

    if ($stmt->execute()) {
        $response["success"] = true;

        // Tambahkan logika untuk menambahkan data ke tabel catatan_bayarmaba20242
        if ($jalur_program === 'Reguler') {
            $admisi = 200000;
        } elseif ($jalur_program === 'RPL') {
            $admisi = 600000;
        } else {
            $admisi = 0;  // Jika jalur program tidak sesuai
        }

        $stmtCatatan = $koneksi->prepare("INSERT INTO catatan_bayarmaba20242 (nama_lengkap, jalur_program, jurusan, admisi, almamater, salut, spp, total_bayar, jumlah_pembayaran, sisa, status_admisi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmtCatatan) {
            $response["success"] = false;
            $response["message"] = "Prepare failed: " . $koneksi->error;
            echo json_encode($response);
            exit;
        }

        // Nilai default untuk almamater, salut, spp, total_bayar, jumlah_pembayaran, sisa, status_admisi, status_pembayaran
        $almamater = 200000;
        $salut = 350000;
        $spp = 0;
        $total_bayar = $almamater + $salut + $spp;
        $jumlah_pembayaran = 0;
        $sisa = $total_bayar;
        $status_admisi = 'belum lunas';
        $status_pembayaran = 'belum ada data';

        $stmtCatatan->bind_param("sssssssssss", $nama_lengkap, $jalur_program, $jurusan, $admisi, $almamater, $salut, $spp, $total_bayar, $jumlah_pembayaran, $sisa, $status_admisi);

        if ($stmtCatatan->execute()) {
            $response["success"] = true;
            $response["message"] = "Data mahasiswa dan catatan pembayaran berhasil ditambahkan";
        } else {
            $response["success"] = false;
            $response["message"] = "Execute failed: " . $stmtCatatan->error;
        }
    } else {
        $response["message"] = "Execute failed: " . $stmt->error;
    }
}

echo json_encode($response);

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
