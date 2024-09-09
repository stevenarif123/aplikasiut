<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title id="page-title">Data Pribadi Mahasiswa</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
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

    .tlb {
      font-size: 1rem;
    }

    .data-pribadi {
      margin-top: 15px;
      margin-bottom: 15px;
    }

    .nama-mahasiswa {
      margin-bottom: 10px;
    }

    .flex-container {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
    }

    .flex-item {
      flex-basis: 48%;
      box-sizing: border-box;
    }

    .data-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }

    .label {
      width: 40%;
    }

    .value {
      width: 80%;
      text-align: left;
    }

    .mkb {
      margin-top: 10px;
      margin-bottom: 10px;
    }

    h1 {
      margin-top: 20px;
    }

    .turun {
      margin-top: 10px;
    }

    @media print {
      body {
        width: 21cm;
      }
    }
  </style>
</head>
<body onload="window.print()">
<div class="container">
    <div class="kop-surat">
        <h1 class="text-center font-bold text-xl">SENTRA LAYANAN UNIVERSITAS TERBUKA TANA TORAJA</h1>
        <p class="text-center tlb">Jln. Buntu Pantan No. 22, Makale</p>
        <p class="text-center tlb">Tana Toraja, Sulawesi Selatan, 91811</p>
        <p class="text-center tlb">Website: uttoraja.com, Email: saluttanatoraja@gmail.com</p>
        <hr class="turun">
        <h2 class="text-center font-bold data-pribadi">DATA PRIBADI MAHASISWA</h2>
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
                // NIM dan Nama Mahasiswa untuk nama file PDF dan title
                $nim = $result1['data']['getPeragaanDp']['nim']; // Tidak perlu htmlspecialchars untuk file name
                $namaMahasiswa = $result1['data']['getPeragaanDp']['namaMahasiswa']; // Tidak perlu htmlspecialchars untuk file name
                
                // Mengubah title halaman secara dinamis
                echo "<script>document.getElementById('page-title').textContent = 'DP_" . $nim . "_" . htmlspecialchars($namaMahasiswa, ENT_QUOTES) . "';</script>";

                echo '<p class="mx-8">Nama Mahasiswa:</p>';
                echo '<h2 class="nama-mahasiswa mx-8"><strong>' . htmlspecialchars($namaMahasiswa, ENT_QUOTES) . '</strong></h2>';
                echo '<div class="flex-container data-diri mx-8">';

                // Kolom 1 (kiri)
                echo '<div class="flex-item">';
                echo '<div class="data-row"><span class="label">NIM</span><span class="value"><strong>: ' . htmlspecialchars($nim) . '</strong></span></div>';
                echo '<div class="data-row"><span class="label">Tempat Lahir</span><span class="value"><strong>: ' . htmlspecialchars($result1['data']['getPeragaanDp']['tempatLahirMhs']) . '</strong></span></div>';
                echo '<div class="data-row"><span class="label">Tanggal Lahir</span><span class="value"><strong>: ' . htmlspecialchars($result1['data']['getPeragaanDp']['tanggalLahirMhs']) . '</strong></span></div>';
                echo '<div class="data-row"><span class="label">Agama</span><span class="value"><strong>: ' . htmlspecialchars($result1['data']['getPeragaanDp']['namaAgama']) . '</strong></span></div>';
                echo '<div class="data-row"><span class="label">Jenis Kelamin</span><span class="value"><strong>: ' . htmlspecialchars($result1['data']['getPeragaanDp']['jenisKelamin']) . '</strong></span></div>';
                echo '<div class="data-row"><span class="label">Nama Ibu Kandung</span><span class="value"><strong>: ' . htmlspecialchars($result1['data']['getPeragaanDp']['namaIbuKandung']) . '</strong></span></div>';
                echo '<div class="data-row"><span class="label">NIK</span><span class="value"><strong>: ' . htmlspecialchars($result1['data']['getPeragaanDp']['nik']) . '</strong></span></div>';
                echo '</div>';

                // Kolom 2 (kanan)
                echo '<div class="flex-item">';
                echo '<div class="data-row"><span class="label">UPBJJ</span><span class="value"><strong>: ' . htmlspecialchars($result1['data']['getPeragaanDp']['namaUpbjj']) . '</strong></span></div>';
                echo '<div class="data-row"><span class="label">Fakultas</span><span class="value"><strong>: ' . htmlspecialchars($result1['data']['getPeragaanDp']['namaFakultas']) . '</strong></span></div>';
                echo '<div class="data-row"><span class="label">Program Studi</span><span class="value"><strong>: ' . htmlspecialchars($result1['data']['getPeragaanDp']['namaProgramStudi']) . '</strong></span></div>';
                echo '<div class="data-row"><span class="label">SIPAS</span><span class="value"><strong>: ' . htmlspecialchars($result1['data']['getPeragaanDp']['namaSipas']) . '</strong></span></div>';
                echo '<div class="data-row"><span class="label">Provinsi</span><span class="value"><strong>: ' . htmlspecialchars($result1['data']['getPeragaanDp']['namaProvinsi']) . '</strong></span></div>';
                echo '<div class="data-row"><span class="label">Kabupaten/Kota</span><span class="value"><strong>: ' . htmlspecialchars($result1['data']['getPeragaanDp']['namaKabko']) . '</strong></span></div>';
                echo '<div class="data-row"><span class="label">Nomor HP</span><span class="value"><strong>: ' . htmlspecialchars($result1['data']['getPeragaanDp']['nomorHpMahasiswa']) . '</strong></span></div>';
                echo '</div>';
            
            echo '</div>'; // Penutup flex-container
            
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
                echo '<h2 class="text-center mkb">DAFTAR MATA KULIAH</h2>';
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
                    echo '<tr><td colspan="3">------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</td></tr>';  // Pemisah
                }
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p class="error-message">Error: Data mata kuliah tidak ditemukan.</p>';
            }

            // Konversi halaman menjadi PDF dan simpan
            echo "
                <script>
                    html2canvas(document.body).then(canvas => {
                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF('p', 'mm', 'a4');
                        const imgData = canvas.toDataURL('image/png');
                        doc.addImage(imgData, 'PNG', 0, 0);
                        doc.save('DP_" . $nim . "_" . $namaMahasiswa . ".pdf');
                    });
                </script>
            ";
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
