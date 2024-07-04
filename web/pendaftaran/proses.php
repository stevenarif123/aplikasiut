<?php
// Enable output buffering
ob_start();

// Set JSON header
header('Content-Type: application/json');

// Error reporting and logging
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/your/php-error.log'); // Ensure the path is writable by the server
ini_set('display_errors', 0); // Disable display errors to avoid JSON output issues
error_reporting(E_ALL);

// Start error logging
error_log("Script started");

// Koneksi ke database
require_once "koneksi.php";

// Check connection
if ($koneksi->connect_error) {
    $response = array(
        'success' => false,
        'message' => "Koneksi gagal: " . $koneksi->connect_error
    );
    error_log("Connection failed: " . $koneksi->connect_error);
    echo json_encode($response);
    exit;
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure all required fields are present
    $required_fields = ["nama_lengkap", "tempat_lahir", "tanggal_lahir", "nama_ibu_kandung", "nik", "jurusan", "nomor_hp", "agama", "jenis_kelamin", "pesan"];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $response = array(
                'success' => false,
                'message' => "Field $field is missing or empty"
            );
            echo json_encode($response);
            exit;
        }
    }

    $nama_lengkap = $koneksi->real_escape_string($_POST["nama_lengkap"]);
    $tempat_lahir = $koneksi->real_escape_string($_POST["tempat_lahir"]);
    $tanggal_lahir = $koneksi->real_escape_string($_POST["tanggal_lahir"]);
    $nama_ibu_kandung = $koneksi->real_escape_string($_POST["nama_ibu_kandung"]);
    $nik = $koneksi->real_escape_string($_POST["nik"]);
    $jurusan = $koneksi->real_escape_string($_POST["jurusan"]);
    $nomor_hp = $koneksi->real_escape_string($_POST["nomor_hp"]);
    $agama = $koneksi->real_escape_string($_POST["agama"]);
    $jenis_kelamin = $koneksi->real_escape_string($_POST["jenis_kelamin"]);
    $pesan = $koneksi->real_escape_string($_POST["pesan"]);

    $sql = "INSERT INTO mabawebsite (nama_lengkap, tempat_lahir, tanggal_lahir, nama_ibu_kandung, nik, jurusan, nomor_hp, agama, jenis_kelamin, pesan)
            VALUES ('$nama_lengkap', '$tempat_lahir', '$tanggal_lahir', '$nama_ibu_kandung', '$nik', '$jurusan', '$nomor_hp', '$agama', '$jenis_kelamin', '$pesan')";

    if ($koneksi->query($sql) === TRUE) {
        $response = array(
            'success' => true,
            'message' => "Data berhasil disimpan"
        );
        error_log("Data successfully inserted");
    } else {
        $response = array(
            'success' => false,
            'message' => "Error: " . $sql . "<br>" . $koneksi->error
        );
        error_log("Error executing query: " . $koneksi->error);
    }
    echo json_encode($response);
    exit;
} else {
    $response = array(
        'success' => false,
        'message' => "Invalid request method"
    );
    echo json_encode($response);
    exit;
}

// Close connection
$koneksi->close();

// Flush the output buffer
ob_end_flush();
?>
