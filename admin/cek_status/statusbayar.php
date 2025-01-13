<?php
require_once "../koneksi.php";
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
        echo "Gagal mendapatkan token akses.";
        return null;
    }

    $response = json_decode($result, true);
    if ($response && isset($response['data']['signInUser']['access_token'])) {
        return $response['data']['signInUser']['access_token'];
    } else {
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
        echo "Gagal mendapatkan data tagihan.";
        return null;
    }

    $response = json_decode($result, true);
    return $response['data']['getAllBillMhs'];
}

// Fungsi untuk menampilkan data mahasiswa
function displayMahasiswaData($nim, $nama, $jurusan, $billData) {
    if ($billData) {
        // echo "<div class='container mt-5'>";
        // echo "<h2 class='mb-4'>Data Mahasiswa</h2>";
        // echo "<div class='card mb-4'>";
        // echo "<div class='card-body'>";
        // echo "<p class='card-text'>NIM: $nim</p>";
        // echo "<p class='card-text'>Nama: $nama</p>";
        // echo "<p class='card-text'>Jurusan: $jurusan</p>";
        // echo "<h2 class='mb-4'>Data Tagihan Mahasiswa</h2>";
        // echo "<p class='card-text'>Total Tagihan Lunas: " . $billData['totalTagihanLunas'] . "</p>";
        // echo "<p class='card-text'>Tagihan Saat Ini: " . $billData['tagihanSaatIni'] . "</p>";
        // echo "<h3 class='mb-3'>Data Tagihan:</h3>";
        // echo "<div class='table-responsive'>";
        // echo "<table class='table table-bordered'>";
        // echo "<thead>
        //         <tr>
        //             <th>ID Masa</th>
        //             <th>Masa</th>
        //             <th>Keterangan Masa</th>
        //             <th>Nomor Billing</th>
        //             <th>ID Jenis Bayar</th>
        //             <th>Keterangan Jenis Bayar</th>
        //             <th>Total Bayar</th>
        //             <th>ID Status Billing</th>
        //             <th>Keterangan Status Billing</th>
        //             <th>ID Status Bayar</th>
        //             <th>Keterangan Status Bayar</th>
        //             <th>Tanggal Lunas</th>
        //         </tr>
        //     </thead>";
        // echo "<tbody>";
        foreach ($billData['data'] as $item) {
        //     echo "<tr>";
        //     echo "<td>" . $item['idmasa'] . "</td>";
        //     echo "<td>" . $item['masa'] . "</td>";
        //     echo "<td>" . $item['ketmasa'] . "</td>";
        //     echo "<td>" . $item['nobilling'] . "</td>";
        //     echo "<td>" . $item['idjenisbayar'] . "</td>";
        //     echo "<td>" . $item['keteranganjenisbayar'] . "</td>";
        //     echo "<td>" . $item['totalbayar'] . "</td>";
        //     echo "<td>" . $item['idstatusbilling'] . "</td>";
        //     echo "<td>" . $item['keteranganstatusbilling'] . "</td>";
        //     echo "<td>" . $item['idstatusbayar'] . "</td>";
        //     echo "<td>" . $item['keteranganstatusbayar'] . "</td>";
        //     echo "<td>" . $item['tanggallunas'] . "</td>";
        //     echo "</tr>";
        // }
        // echo "</tbody>";
        // echo "</table>";
        // echo "</div>";
        // echo "</div>";
        // echo "</div>";
        // echo "</div>";
    } //else {
    //     // echo "Data tagihan tidak ditemukan.";
    }
}

// Main program

$id_to_query = ""; // Nilai default

// Membuat koneksi

// Memeriksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Memeriksa apakah data yang akan dicari telah disediakan
// Memeriksa apakah ada parameter GET No dan tidak kosong
if (isset($_GET['No']) && !empty($_GET['No'])) {
    // Melindungi input untuk mencegah SQL injection
    $id_to_query = $koneksi->real_escape_string($_GET['No']);
}

// Memeriksa apakah ada parameter GET jurusan dan tidak kosong
// Memeriksa apakah ada parameter GET id dan tidak kosong
if (!empty($id_to_query)) {
    // Melakukan kueri ke database
    $sql = "SELECT Nim, NamaLengkap, Jurusan, Email, Password FROM mahasiswa WHERE No = '$id_to_query'";
    $result = $koneksi->query($sql);

    if ($result->num_rows > 0) {
        // output data of the first row (assuming email is unique)
        $row = $result->fetch_assoc();
        $email = $row["Email"];
        $password = $row["Password"];
        $nim = $row["Nim"];
        $nama = $row["NamaLengkap"];
        $jurusan = $row["Jurusan"];

        // Mendapatkan token akses
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
} else {
    echo "Tidak ada ID yang ditentukan.";
}

$koneksi->close();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Contoh</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Data Mahasiswa</h2>
        <div class="card mb-4">
            <div class="card-body">
                <p class="card-text">NIM: <?php echo $nim; ?></p>
                <p class="card-text">Nama: <?php echo $nama; ?></p>
                <p class="card-text">Jurusan: <?php echo $jurusan; ?></p>
                <h2 class="mb-4">Data Tagihan Mahasiswa</h2>
                <p class="card-text">Total Tagihan Lunas: Rp <?php echo $billData['totalTagihanLunas']; ?></p>
                <p class="card-text">Tagihan Saat Ini: Rp <?php echo $billData['tagihanSaatIni']; ?></p>
                <h3 class="mb-3">Data Tagihan:</h3>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($billData['data'] as $item): ?>
                                <tr>
                                    <td><?php echo $item['idmasa']; ?></td>
                                    <td><?php echo $item['masa']; ?></td>
                                    <td><?php echo $item['ketmasa']; ?></td>
                                    <td><?php echo $item['nobilling']; ?></td>
                                    <td><?php echo $item['idjenisbayar']; ?></td>
                                    <td><?php echo $item['keteranganjenisbayar']; ?></td>
                                    <td>Rp <?php echo $item['totalbayar']; ?></td>
                                    <td><?php echo $item['idstatusbilling']; ?></td>
                                    <td><?php echo $item['keteranganstatusbilling']; ?></td>
                                    <td><?php echo $item['idstatusbayar']; ?></td>
                                    <td><?php echo $item['keteranganstatusbayar']; ?></td>
                                    <td><?php echo $item['tanggallunas']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <a href="pencarian.php" class="btn btn-primary">Kembali</a>
    </div>
</body>
</html>