<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data Pribadi Mahasiswa</title>
  <style type="text/css" media="print">
    @page {
      size: A4;
      margin: 0;
      height: 99%;
      page-break-after: auto !important;
      page-break-before: auto !important;
      page-break-inside: avoid !important;
    }
  </style>
  <style>
    * {
      margin: 0;
      box-sizing: border-box;
    }

    body {
      font-size: 12px;
      width: 21cm;
    }

    table,
    tr,
    td {
      font-size: 12px;
    }

    html,
    body {
      font-family: "Interstate-Regular";
    }

    .flex {
      display: flex;
    }

    .text-center {
      text-align: center;
    }

    .mx-auto {
      margin: 0 auto;
    }

    .justify-between {
      justify-content: space-between;
    }

    .font-bold {
      font-weight: 700;
    }

    .text-base {
      font-size: 1rem;
      line-height: 1.5rem;
    }

    .text-lg {
      font-size: 1.25rem;
      line-height: 1.75rem;
    }

    .text-xl {
      font-size: 1.5rem;
      line-height: 2rem;
    }

    .px-8 {
      padding: 0 2rem;
    }

    .mx-8 {
      margin: 0 2rem;
    }

    .mt-1 {
      margin-top: 0.25rem;
    }

    .mt-4 {
      margin-top: 1rem;
    }

    .w-1\/2 {
      width: 50%;
    }

    .w-full {
      width: 100%;
    }

    .dl-table {
      display: flex;
      margin: 0 0;
    }

    .dl-table .title {
      min-width: 170px;
    }

    .text-center-table {
      margin-left: auto;
      margin-right: auto;
      width: 100%;
      text-align: center;
    }

    @media print {
      body {
        width: 21cm;
      }
    }
  </style>
</head>
<body>
<div class="container">
    <div class="kop-surat">
        <h1 class="text-center font-bold text-xl">SENTRA LAYANAN UNIVERSITAS TERBUKA TANA TORAJA</h1>
        <p class="text-center text-base">Tana Toraja, Sulawesi Selatan</p>
        <p class="text-center text-base">Jln. Buntu Pantan No. 22, Makale</p>
        <p class="text-center text-base">http://www.uttoraja.com</p>
        <hr>
        <h2 class="text-center font-bold">DATA PRIBADI MAHASISWA</h2>
    </div>

    <?php
    if (isset($_GET['email']) && isset($_GET['password'])) {
        $email = $_GET['email'];
        $password = $_GET['password'];

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
            // Fungsi untuk melakukan query
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
                echo '<div class="data-diri mx-8">';
                echo '<div class="row">';

                // Kolom 1 (kiri)
                echo '<div class="w-1/2" style="font-size: 1rem;">';
                echo '<p>NIM: <strong>' . htmlspecialchars($result1['data']['getPeragaanDp']['nim']) . '</strong></p>';
                echo '<p>Nama Mahasiswa: <strong>' . htmlspecialchars($result1['data']['getPeragaanDp']['namaMahasiswa']) . '</strong></p>';
                echo '<p>Nama UPBJJ: <strong>' . htmlspecialchars($result1['data']['getPeragaanDp']['namaUpbjj']) . '</strong></p>';
                echo '<p>Nama Fakultas: <strong>' . htmlspecialchars($result1['data']['getPeragaanDp']['namaFakultas']) . '</strong></p>';
                echo '<p>Nama Program Studi: <strong>' . htmlspecialchars($result1['data']['getPeragaanDp']['namaProgramStudi']) . '</strong></p>';
                echo '<p>Nama SIPAS: <strong>' . htmlspecialchars($result1['data']['getPeragaanDp']['namaSipas']) . '</strong></p>';
                echo '<p>NIK: <strong>' . htmlspecialchars($result1['data']['getPeragaanDp']['nik']) . '</strong></p>';
                echo '</div>';

                // Kolom 2 (kanan)
                echo '<div class="w-1/2" style="font-size: 1rem;">';
                echo '<p>Tempat Lahir: <strong>' . htmlspecialchars($result1['data']['getPeragaanDp']['tempatLahirMhs']) . '</strong></p>';
                echo '<p>Tanggal Lahir: <strong>' . htmlspecialchars($result1['data']['getPeragaanDp']['tanggalLahirMhs']) . '</strong></p>';
                echo '<p>Agama: <strong>' . htmlspecialchars($result1['data']['getPeragaanDp']['namaAgama']) . '</strong></p>';
                echo '<p>Jenis Kelamin: <strong>' . htmlspecialchars($result1['data']['getPeragaanDp']['jenisKelamin']) . '</strong></p>';
                echo '<p>Nama Ibu Kandung: <strong>' . htmlspecialchars($result1['data']['getPeragaanDp']['namaIbuKandung']) . '</strong></p>';
                echo '<p>Nama Provinsi: <strong>' . htmlspecialchars($result1['data']['getPeragaanDp']['namaProvinsi']) . '</strong></p>';
                echo '<p>Nama Kabupaten/Kota: <strong>' . htmlspecialchars($result1['data']['getPeragaanDp']['namaKabko']) . '</strong></p>';
                echo '<p>Nomor HP: <strong>' . htmlspecialchars($result1['data']['getPeragaanDp']['nomorHpMahasiswa']) . '</strong></p>';
                echo '</div>';

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
                echo '<h2 class="text-center">Daftar Mata Kuliah Berjalan</h2>';
                echo '<table class="text-center-table">';
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
                    echo '<tr><td colspan="3">---------------------------------------------------------------------------------------------------------------------------------------------------------------------------</td></tr>';  // Pemisah
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
