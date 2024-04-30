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

// Fungsi untuk mendapatkan data DNU mahasiswa
function getDnuMhsData($accessToken, $masa) {
    $url = "https://api-sia.ut.ac.id/backend-sia/api/graphql";
    $data = array(
        "query" => "query {
            getDnuMhs(masa: \"$masa\") {
                header2 {
                    nama
                    nim
                    nama_upbjj
                }
                body {
                    namamtk
                    kodemtk
                    grade
                    sks
                    mutu
                    ket
                }
                footer {
                    jmlSksDiambil
                    jmlSksLulus
                    jmlMutuLulus
                    indeksPrestasiSemester
                }
                ketMasa
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
        echo "Gagal mendapatkan data DNU mahasiswa.";
        return null;
    }

    $response = json_decode($result, true);
    return $response['data']['getDnuMhs'];
}

// Main program

$id_to_query = ""; // Nilai default
$masa = "20231"; // Masa default

// Memeriksa apakah data yang akan dicari telah disediakan
// Memeriksa apakah ada parameter GET No dan tidak kosong
if (isset($_GET['No']) && !empty($_GET['No'])) {
    // Melindungi input untuk mencegah SQL injection
    $id_to_query = $koneksi->real_escape_string($_GET['No']);
}

// Memeriksa apakah ada parameter GET masa dan tidak kosong
if (isset($_GET['masa']) && !empty($_GET['masa'])) {
    $masa = $_GET['masa'];
}

// Memeriksa apakah ada parameter GET id dan tidak kosong
if (!empty($id_to_query)) {
    // Melakukan kueri ke database
    $sql = "SELECT Email, Password FROM mahasiswa WHERE No = '$id_to_query'";
    $result = $koneksi->query($sql);

    if ($result->num_rows > 0) {
        // output data of the first row (assuming email is unique)
        $row = $result->fetch_assoc();
        $email = $row["Email"];
        $password = $row["Password"];

        // Mendapatkan token akses
        $accessToken = getAccessToken($email, $password);
        if ($accessToken) {
            // Mendapatkan data DNU mahasiswa
            $dnuMahasiswa = getDnuMhsData($accessToken, $masa);
            if ($dnuMahasiswa) {
                // Menampilkan data mahasiswa dan DNU dalam bentuk tabel
                displayMahasiswaData($dnuMahasiswa);
            }
        }
    }
} 
// Memeriksa apakah data DNU mahasiswa ditemukan
if ($dnuMahasiswa) {
    // Menampilkan data mahasiswa dan DNU dalam bentuk tabel
    displayMahasiswaData($dnuMahasiswa);
} else {
    echo "Data DNU mahasiswa tidak ditemukan.";
}

$koneksi->close();

// Fungsi untuk menampilkan data mahasiswa dan DNU
function displayMahasiswaData($dnuMahasiswa) {
    // Your display code here...
}
//var_dump($dnuMahasiswa['header2']);
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
        <h2 class="mb-4">Pilih Masa</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="mb-3">
                <label for="masa" class="form-label">Masa:</label>
                <select class="form-select" name="masa" id="masa">
                    <?php
                    $selectedMasa = isset($_POST['masa']) ? $_POST['masa'] : '20201';
                    $masaList = ['20201', '20202', '20211', '20212', '20221', '20222', '20231', '20232', '20241', '20242'];

                    foreach ($masaList as $masa) {
                        $selected = ($masa == $selectedMasa) ? 'selected' : '';
                        echo "<option value=\"$masa\" $selected>$masa</option>";
                    }
                    ?>
                </select>
            </div>
            <input type="hidden" name="No" value="<?php echo $nim; ?>">
            <button type="submit" class="btn btn-primary" name="submit">Submit</button>
        </form>
    </div>

        <?php if ($dnuMahasiswa) : ?>
            <div class="container mt-5">
            <?php if ($dnuMahasiswa) : ?>
        <div class="container mt-5">
            <h2 class="mb-4">Data Mahasiswa</h2>
            <div class="card mb-4">
                <div class="card-body">
                <?php
                    if (isset($dnuMahasiswa['header2'])) {
                        echo "<table class='table'>";
                        foreach ($dnuMahasiswa['header2'][0] as $key => $value) {
                            echo "<tr>";
                            echo "<td>$key</td>";
                            echo "<td>$value</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p class='card-text'>Data mahasiswa tidak ditemukan.</p>";
                    }
                    ?>

                    <h2 class="mb-4">Data DNU Mahasiswa</h2>
                    <p class="card-text">Keterangan Masa: <?php echo $dnuMahasiswa['ketMasa']; ?></p>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Matakuliah</th>
                                <th>Kode Matakuliah</th>
                                <th>Grade</th>
                                <th>SKS</th>
                                <th>Mutu</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dnuMahasiswa['body'] as $item) : ?>
                                <tr>
                                    <td><?php echo $item['namamtk']; ?></td>
                                    <td><?php echo $item['kodemtk']; ?></td>
                                    <td><?php echo $item['grade']; ?></td>
                                    <td><?php echo $item['sks']; ?></td>
                                    <td><?php echo $item['mutu']; ?></td>
                                    <td><?php echo $item['ket']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p class="card-text">Jumlah SKS Diambil: <?php echo $dnuMahasiswa['footer']['jmlSksDiambil']; ?></p>
                    <p class="card-text">Jumlah SKS Lulus: <?php echo $dnuMahasiswa['footer']['jmlSksLulus']; ?></p>
                    <p class="card-text">Jumlah Mutu Lulus: <?php echo $dnuMahasiswa['footer']['jmlMutuLulus']; ?></p>
                    <p class="card-text">Indeks Prestasi Semester: <?php echo $dnuMahasiswa['footer']['indeksPrestasiSemester']; ?></p>
                </div>
            </div>
            <a href="pencarian.php" class="btn btn-primary">Kembali</a>
        </div>
    <?php endif; ?>

        </div>
    <?php endif; ?>
</body>
</html>
