<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pribadi Mahasiswa</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            color: #000;
            margin: 5px;
            font-size: 16px;
        }

        .kop-surat {
            text-align: center;
            margin-bottom: 5px;
        }

        .kop-surat h1 {
            font-size: 20px;
            text-transform: uppercase;
            margin: 5px 0;
        }

        .kop-surat p {
            margin: 2px 0;
            font-size: 14px;
        }

        hr {
            border: 1px solid #000;
            margin-bottom: 10px;
        }

        .data-diri {
            margin-bottom: 5px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .col {
            flex: 1;
            min-width: 50%;
            box-sizing: border-box;
            padding: 5px;
        }

        .col p {
            margin: 5px 0;
            font-size: 16px;
        }

        /* Tabel Mata Kuliah */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 12px;
            text-align: left;
            font-size: 16px;
        }

        table th {
            background-color: #f0f0f0;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }

        .debug-log {
            font-size: 12px;
            background-color: #f8f8f8;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="kop-surat">
        <h1>SENTRA LAYANAN UNIVERSITAS TERBUKA TANA TORAJA</h1>
        <p>Tana Toraja, Sulawesi Selatan</p>
        <p>Jln. Buntu Pantan No. 22, Makale</p>
        <p>http://www.uttoraja.com</p>
        <hr>
    </div>

    <?php
    if (isset($_GET['Email']) && isset($_GET['Password'])) {  
        $email = $_GET['Email'];
        $password = $_GET['Password'];

        // Fungsi untuk mendapatkan access token
        function getAccessToken($email, $password) {
            $url = 'https://api-sia.ut.ac.id/backend-sia/api/graphql';
            $headers = [
                'Content-Type: application/json',
                'User-Agent: Mozilla/5.0',
            ];

            $data = [
                'query' => 'mutation {
                    signInUser(email: "' . $email . '", password: "' . $password . '"){
                        access_token
                    }
                }',
                'variables' => new stdClass()
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);
            curl_close($ch);

            $responseArray = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($responseArray['data']['signInUser']['access_token'])) {
                echo '<p class="error-message">Error: Gagal mendapatkan access token. Silakan cek email/password. Response: ' . htmlspecialchars(print_r($responseArray, true)) . '</p>';
                return null;
            }

            return $responseArray['data']['signInUser']['access_token'];
        }

        $accessToken = getAccessToken($email, $password);

        if ($accessToken) {
            // Query untuk data pribadi mahasiswa
            function performQuery($accessToken, $query) {
                $url = 'https://api-sia.ut.ac.id/backend-sia/api/graphql';
                $headers = [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $accessToken,
                    'User-Agent: Mozilla/5.0',
                ];

                $data = [
                    'query' => $query,
                    'variables' => new stdClass()
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                $response = curl_exec($ch);
                curl_close($ch);

                return json_decode($response, true);
            }

            // Query Data Pribadi
            $query1 = '
                query getDp {
                    getPeragaanDp {
                        nim
                        namaMahasiswa
                        namaUpbjj
                        namaFakultas
                        namaProgramStudi
                        namaSipas
                        nik
                        tempatLahirMhs
                        tanggalLahirMhs
                        namaAgama
                        jenisKelamin
                        namaIbuKandung
                        namaProvinsi
                        namaKabko
                        nomorHpMahasiswa
                        perguruanTinggiAsal
                        namaTingkatMengajar
                        namaLamaAjar
                        tempatMengajar
                        statusGuru
                        namaPokjar
                    }
                }
            ';
            $result1 = performQuery($accessToken, $query1);

            // Cek dan tampilkan data pribadi jika ditemukan
            if (!empty($result1['data']['getPeragaanDp'])) {
                echo '<div class="data-diri">';
                echo '<div class="row">';
                foreach ($result1['data']['getPeragaanDp'] as $key => $value) {
                    echo '<div class="col">';
                    echo '<p>' . htmlspecialchars($key) . ': <strong>' . htmlspecialchars($value) . '</strong> </p>';
                    echo '</div>';
                }
                echo '</div>'; // Penutup row
                echo '</div>'; // Penutup data-diri
            } else {
                echo '<p class="error-message">Error: Data pribadi tidak ditemukan.</p>';
            }

            // Query Mata Kuliah Berjalan
            $query4 = '
                query {
                    getMataKuliahBerjalan {
                        id
                        namaMataKuliah
                        kodeMataKuliah
                        redaksiHeader
                    }
                }
            ';
            $result4 = performQuery($accessToken, $query4);

            // Cek dan tampilkan data mata kuliah jika ditemukan
            if (!empty($result4['data']['getMataKuliahBerjalan'])) {
                echo '<h2>Daftar Mata Kuliah Berjalan</h2>';
                echo '<table>';
                echo '<thead>';
                echo '<tr>';
                echo '<th>No</th>';
                echo '<th>Kode Mata Kuliah</th>';
                echo '<th>Nama Mata Kuliah</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                $no = 1;
                foreach ($result4['data']['getMataKuliahBerjalan'] as $mataKuliah) {
                    echo '<tr>';
                    echo '<td>' . $no++ . '</td>';
                    echo '<td>' . htmlspecialchars($mataKuliah['kodeMataKuliah']) . '</td>';
                    echo '<td>' . htmlspecialchars($mataKuliah['namaMataKuliah']) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p class="error-message">Error: Data mata kuliah tidak ditemukan.</p>';
            }

            // Langsung tampilkan dialog print
            echo '<script>window.print();</script>';
        } else {
            echo '<p class="error-message">Error: Gagal mendapatkan access token. Periksa email dan password Anda.</p>';
        }
    } else {
        echo '<p class="error-message">Error: Email dan Password tidak ditemukan di URL.</p>';
    }
    ?>
</div>

</body>
</html>
