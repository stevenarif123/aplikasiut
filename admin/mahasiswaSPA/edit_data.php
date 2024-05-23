<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once "../koneksi.php";
if (!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no = $_GET['No'];
    $nim = filter_input(INPUT_POST, 'Nim', FILTER_SANITIZE_STRING);
    $jalur_program = filter_input(INPUT_POST, 'JalurProgram', FILTER_SANITIZE_STRING);
    $nama_lengkap = filter_input(INPUT_POST, 'NamaLengkap', FILTER_SANITIZE_STRING);
    $tempat_lahir = filter_input(INPUT_POST, 'TempatLahir', FILTER_SANITIZE_STRING);
    $tanggal_lahir = filter_input(INPUT_POST, 'TanggalLahir', FILTER_SANITIZE_STRING);
    $nama_ibu_kandung = filter_input(INPUT_POST, 'NamaIbuKandung', FILTER_SANITIZE_STRING);
    $nik = filter_input(INPUT_POST, 'NIK', FILTER_SANITIZE_STRING);
    $jurusan = filter_input(INPUT_POST, 'Jurusan', FILTER_SANITIZE_STRING);
    $nomor_hp = filter_input(INPUT_POST, 'NomorHP', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'Email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'Password', FILTER_SANITIZE_EMAIL);
    $agama = filter_input(INPUT_POST, 'Agama', FILTER_SANITIZE_STRING);
    $jenis_kelamin = filter_input(INPUT_POST, 'JenisKelamin', FILTER_SANITIZE_STRING);
    $status_perkawinan = filter_input(INPUT_POST, 'StatusPerkawinan', FILTER_SANITIZE_STRING);
    $nomor_hp_alternatif = filter_input(INPUT_POST, 'NomorHPAlternatif', FILTER_SANITIZE_STRING);
    $nomor_ijazah = filter_input(INPUT_POST, 'NomorIjazah', FILTER_SANITIZE_STRING);
    $tahun_ijazah = filter_input(INPUT_POST, 'TahunIjazah', FILTER_SANITIZE_NUMBER_INT);
    $nisn = filter_input(INPUT_POST, 'NISN', FILTER_SANITIZE_STRING);
    $layanan_paket_semester = filter_input(INPUT_POST, 'LayananPaketSemester', FILTER_SANITIZE_STRING);
    $di_input_oleh = $_SESSION['username'];
    $di_edit_pada = date("Y-m-d H:i:s");
    $status_input_sia = $koneksi->real_escape_string(trim($_POST['STATUS_INPUT_SIA']));

    $updateQuery = "UPDATE mahasiswa SET 
        Nim = ?, 
        JalurProgram = ?, 
        NamaLengkap = ?, 
        TempatLahir = ?, 
        TanggalLahir = ?, 
        NamaIbuKandung = ?, 
        NIK = ?, 
        Jurusan = ?, 
        NomorHP = ?, 
        Email = ?, 
        Password = ?, 
        Agama = ?, 
        JenisKelamin = ?, 
        StatusPerkawinan = ?, 
        NomorHPAlternatif = ?, 
        NomorIjazah = ?, 
        TahunIjazah = ?, 
        NISN = ?, 
        LayananPaketSemester = ?, 
        DiInputOleh = ?, 
        DiEditPada = ?,
        STATUS_INPUT_SIA = ?
        WHERE No = ?";

    $stmt = mysqli_prepare($koneksi, $updateQuery);
    mysqli_stmt_bind_param($stmt, "sssssssssssssssssssssss", $nim, $jalur_program, $nama_lengkap, $tempat_lahir, $tanggal_lahir, $nama_ibu_kandung, $nik, $jurusan, $nomor_hp, $email, $password, $agama, $jenis_kelamin, $status_perkawinan, $nomor_hp_alternatif, $nomor_ijazah, $tahun_ijazah, $nisn, $layanan_paket_semester, $di_input_oleh, $di_edit_pada, $status_input_sia, $no);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($koneksi)]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($koneksi);
}
?>
