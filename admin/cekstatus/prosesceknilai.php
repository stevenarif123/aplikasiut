<?php

// Ambil data masa dan No dari parameter POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data No dari formulir sebelumnya
    $selected_nim = isset($_POST['No']) ? trim($_POST['No']) : '';
    
    // Validasi No
    if (empty($selected_nim)) {
        echo "NIM tidak valid.";
        exit;
    }

    $masa = trim($_POST["masa"]);

    // Validasi masa
    if (empty($masa)) {
        echo "Masa tidak valid.";
        exit;
    }

    // Mendapatkan email dan password mahasiswa dari database
    require_once "../koneksi.php";

    // Melakukan kueri ke database untuk mendapatkan email dan password
    $sql = "SELECT Email, Password FROM mahasiswa WHERE No = ?";
    
    if ($stmt = $koneksi->prepare($sql)) {
        $stmt->bind_param("s", $selected_nim);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
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
                    } else {
                        // Menampilkan pesan peringatan jika data tidak ditemukan
                        echo '<div class="alert alert-warning" role="alert">
                                Data DNU mahasiswa tidak ditemukan.
                              </div>';
                    }
                }
            } else {
                echo "Data mahasiswa tidak ditemukan.";
            }
        } else {
            echo "Terjadi kesalahan. Silakan coba lagi nanti.";
        }
        $stmt->close();
    }
    $koneksi->close();
}

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

// Fungsi untuk menampilkan data mahasiswa dan DNU
function displayMahasiswaData($dnuMahasiswa) {
    echo "<div class='modal-header'>";
    echo "<h5 class='modal-title'>Hasil Nilai Mahasiswa</h5>";
    echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
    echo "</div>";
    echo "<div class='modal-body'>";
    
    // Tampilkan data header
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
        echo "<p>Data mahasiswa tidak ditemukan.</p>";
    }

    // Tampilkan data body
    echo "<h2>Daftar Nilai Mahasiswa</h2>";
    echo "<p>Keterangan Masa: " . $dnuMahasiswa['ketMasa'] . "</p>";
    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>Nama Matakuliah</th><th>Kode Matakuliah</th><th>Grade</th><th>SKS</th><th>Mutu</th><th>Keterangan</th></tr></thead>";
    echo "<tbody>";
    foreach ($dnuMahasiswa['body'] as $item) {
        echo "<tr>";
        echo "<td>{$item['namamtk']}</td>";
        echo "<td>{$item['kodemtk']}</td>";
        echo "<td>{$item['grade']}</td>";
        echo "<td>{$item['sks']}</td>";
        echo "<td>{$item['mutu']}</td>";
        echo "<td>{$item['ket']}</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";

    // Tampilkan data footer
    echo "<p>Jumlah SKS Diambil: " . $dnuMahasiswa['footer']['jmlSksDiambil'] . "</p>";
    echo "<p>Jumlah SKS Lulus: " . $dnuMahasiswa['footer']['jmlSksLulus'] . "</p>";
    echo "<p>Jumlah Mutu Lulus: " . $dnuMahasiswa['footer']['jmlMutuLulus'] . "</p>";
    echo "<p>Indeks Prestasi Semester: " . $dnuMahasiswa['footer']['indeksPrestasiSemester'] . "</p>";

    echo "</div>";
    echo "<div class='modal-footer'>";
    echo "<button type='button' class='btn btn-secondary' id='logoutButton'>Tutup</button>";
    echo "<button type='button' class='btn btn-primary' id='copyButton'>Copy</button>";
    echo "</div>";
}
?>
