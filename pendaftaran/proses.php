<?php
// Koneksi ke database
$host = "localhost";
$username = "root";
$password = "";
$database = "datamahasiswa";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    $response = array(
        'success' => false,
        'message' => "Koneksi gagal: " . $conn->connect_error
    );
    echo json_encode($response);
    exit;
}

// Daftar jurusan
$daftar_jurusan = array("Teknik Informatika", "Sistem Informasi", "Manajemen Informatika", "Ilmu Komputer");

// Proses data dari form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = $_POST["nama_lengkap"];
    $tempat_lahir = $_POST["tempat_lahir"];
    $tanggal_lahir = $_POST["tanggal_lahir"];
    $nama_ibu_kandung = $_POST["nama_ibu_kandung"];
    $nik = $_POST["nik"];
    $jurusan = $_POST["jurusan"];
    $nomor_hp = $_POST["nomor_hp"];
    $agama = $_POST["agama"];
    $jenis_kelamin = $_POST["jenis_kelamin"];
    $pesan = $_POST["pesan"];

    $sql = "INSERT INTO mabawebsite (nama_lengkap, tempat_lahir, tanggal_lahir, nama_ibu_kandung, nik, jurusan, nomor_hp, agama, jenis_kelamin, pesan)
            VALUES ('$nama_lengkap', '$tempat_lahir', '$tanggal_lahir', '$nama_ibu_kandung', '$nik', '$jurusan', '$nomor_hp', '$agama', '$jenis_kelamin', '$pesan')";

    if ($conn->query($sql) === TRUE) {
        $response = array(
            'success' => true,
            'message' => "Data berhasil disimpan"
        );
    } else {
        $response = array(
            'success' => false,
            'message' => "Error: " . $sql . "<br>" . $conn->error
        );
    }
    echo json_encode($response);
    exit;
}

$conn->close();
?>