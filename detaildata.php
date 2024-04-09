<?php

// Fungsi untuk mendapatkan access token
function getAccessToken($email, $password) {
    $url = "https://api-sia.ut.ac.id/backend-sia/api/graphql";
    $data = array(
        "query" => "mutation {
            signInUser(email: \"$email\", password: \"$password\"){
                access_token
            }
        }",
        "variables" => (object) array()
    );

    $options = array(
        "http" => array(
            "header"  => "Content-Type: application/json",
            "method"  => "POST",
            "content" => json_encode($data)
        )
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        echo "Failed to get access token.";
        return null;
    }

    $response = json_decode($result, true);
    // Pastikan $response bukan null sebelum mencoba mengakses indeks arraynya
    if ($response && isset($response['data']['signInUser']['access_token'])) {
        return $response['data']['signInUser']['access_token'];
    } else {
        // Handle kasus jika $response null atau indeks array tidak tersedia
        echo "Gagal mendapatkan token akses.";
        return null;
    }
}

// Fungsi untuk mendapatkan data tagihan mahasiswa
function getBillData($accessToken) {
    $url = "https://api-sia.ut.ac.id/backend-sia/api/graphql";
    $data = array(
        "query" => "query getAllBillMhs {
            getAllBillMhs {
                data {
                    idmasa
                    masa
                    ketmasa
                    nobilling
                    idjenisbayar
                    keteranganjenisbayar
                    totalbayar
                    idstatusbilling
                    keteranganstatusbilling
                    idstatusbayar
                    keteranganstatusbayar
                    tanggallunas
                }
                totalTagihanLunas
                tagihanSaatIni
            }
        }",
        "variables" => (object) array()
    );

    $options = array(
        "http" => array(
            "header"  => "Content-Type: application/json\r\n" .
                         "Authorization: Bearer " . $accessToken,
            "method"  => "POST",
            "content" => json_encode($data)
        )
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        echo "Failed to get bill data.";
        return null;
    }

    $response = json_decode($result, true);
    return $response['data']['getAllBillMhs'];
}

// Fungsi untuk menampilkan data mahasiswa
function displayMahasiswaData($nim, $nama, $jurusan, $billData) {
    if ($billData) {
        echo "<h2>Data Mahasiswa</h2>";
        echo "<p>NIM: $nim</p>";
        echo "<p>Nama: $nama</p>";
        echo "<p>Jurusan: $jurusan</p>";
        echo "<h2>Data Tagihan Mahasiswa</h2>";
        echo "<p>Total Tagihan Lunas: " . $billData['totalTagihanLunas'] . "</p>";
        echo "<p>Tagihan Saat Ini: " . $billData['tagihanSaatIni'] . "</p>";
        echo "<h3>Data Tagihan:</h3>";
        echo "<table border='1'>
                <tr>
                    <th>ID Masa</th>
                    <th>Masa</th>
                    <th>Keterangan Masa</th>
                    <th>Nomor Billing</th>
                    <th>ID Jenis Bayar</th>
                    <th>Keterangan Jenis Bayar</th>
                    <th>Total Bayar</th>
                    <th>ID Status Billing</th>
                    <th>Keterangan Status Billing</th>
                    <th>ID Status Bayar</th>
                    <th>Keterangan Status Bayar</th>
                    <th>Tanggal Lunas</th>
                </tr>";
        foreach ($billData['data'] as $item) {
            echo "<tr>";
            echo "<td>" . $item['idmasa'] . "</td>";
            echo "<td>" . $item['masa'] . "</td>";
            echo "<td>" . $item['ketmasa'] . "</td>";
            echo "<td>" . $item['nobilling'] . "</td>";
            echo "<td>" . $item['idjenisbayar'] . "</td>";
            echo "<td>" . $item['keteranganjenisbayar'] . "</td>";
            echo "<td>" . $item['totalbayar'] . "</td>";
            echo "<td>" . $item['idstatusbilling'] . "</td>";
            echo "<td>" . $item['keteranganstatusbilling'] . "</td>";
            echo "<td>" . $item['idstatusbayar'] . "</td>";
            echo "<td>" . $item['keteranganstatusbayar'] . "</td>";
            echo "<td>" . $item['tanggallunas'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No bill data found.";
    }
}

// Main program
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datamahasiswa";
$nim_to_query = ""; // Default value

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cek apakah data yang akan dicari telah disediakan
// Memeriksa apakah ada parameter GET nim dan tidak kosong
if (isset($_GET['nim']) && !empty($_GET['nim'])) {
    // Escape input untuk mencegah SQL injection
    $nim_to_query = $conn->real_escape_string($_GET['nim']);
}

// Memeriksa apakah ada parameter GET nama dan tidak kosong
if (isset($_GET['nama']) && !empty($_GET['nama'])) {
    // Escape input untuk mencegah SQL injection
    $nama_to_query = $conn->real_escape_string($_GET['nama']);
}

// Memeriksa apakah ada parameter GET jurusan dan tidak kosong
if (isset($_GET['jurusan']) && !empty($_GET['jurusan'])) {
    // Escape input untuk mencegah SQL injection
    $jurusan_to_query = $conn->real_escape_string($_GET['jurusan']);
}

// Membuat kueri SQL awal
$sql = "SELECT Nim, NamaLengkap, Jurusan, Email, Password FROM mahasiswa WHERE 1";

// Memeriksa dan menambahkan kondisi pencarian berdasarkan NIM
if (isset($nim_to_query)) {
    $sql .= " AND Nim = '$nim_to_query'";
}

// Memeriksa dan menambahkan kondisi pencarian berdasarkan Nama
if (isset($nama_to_query)) {
    $sql .= " AND NamaLengkap LIKE '%$nama_to_query%'";
}

// Memeriksa dan menambahkan kondisi pencarian berdasarkan Jurusan
if (isset($jurusan_to_query)) {
    $sql .= " AND Jurusan LIKE '%$jurusan_to_query%'";
}
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of the first row (assuming email is unique)
    $row = $result->fetch_assoc();
    $email = $row["Email"];
    $password = $row["Password"];
    $nim = $row["Nim"];
    $nama = $row["NamaLengkap"];
    $jurusan = $row["Jurusan"];

    // Mendapatkan access token
    $accessToken = getAccessToken($email, $password);
    if ($accessToken) {
        // Mendapatkan data tagihan
        $billData = getBillData($accessToken);
        if ($billData) {
            // Menampilkan data mahasiswa dan tagihan dalam bentuk tabel
            displayMahasiswaData($nim, $nama, $jurusan, $billData);
        }
    }
} else {
    echo "Data mahasiswa tidak ditemukan.";
}

$conn->close();

?>