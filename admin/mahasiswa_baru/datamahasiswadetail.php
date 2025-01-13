<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <style>
        /* Styling umum untuk halaman */
        body {
            font-family: 'Times New Roman', serif;
            color: #000;
            margin: 20px;
            font-size: 16px; /* Ukuran font lebih besar */
        }

        /* Styling kop surat */
        .kop-surat {
            text-align: center;
            margin-bottom: 20px;
        }

        .kop-surat h1 {
            font-size: 20px;
            text-transform: uppercase;
            margin: 5px 0;
        }

        .kop-surat p {
            margin: 5px 0;
            font-size: 14px;
        }

        hr {
            border: 1px solid #000;
            margin-bottom: 20px;
        }

        /* Data diri mahasiswa */
        .data-diri {
            margin-bottom: 20px;
        }

        /* Mengatur dua kolom menggunakan Flexbox */
        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .col {
            flex: 1;
            min-width: 50%; /* Mengatur agar kolom minimum 50% lebar */
            box-sizing: border-box;
            padding: 10px; /* Menambahkan padding agar lebih renggang */
        }

        .col p {
            margin: 10px 0;
            font-size: 16px; /* Ukuran font lebih besar */
        }

        /* Tabel Mata Kuliah dan Tagihan */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 12px; /* Baris dan kolom lebih renggang */
            text-align: left;
            font-size: 16px; /* Ukuran font lebih besar */
        }

        table th {
            background-color: #f0f0f0;
        }

        /* Rincian Biaya */
        .rincian-biaya {
            font-size: 16px;
            margin-top: 20px;
        }

        .rincian-biaya p {
            margin: 10px 0;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            font-size: 14px;
        }

        /* Styling untuk cetak */
        @media print {
            body {
                margin: 10mm 15mm 10mm 15mm; /* Mengatur margin halaman */
                color: black;
            }

            .kop-surat h1 {
                font-size: 18px;
            }

            .kop-surat p, .data-diri p, .footer p {
                font-size: 14px;
            }

            table th, table td {
                font-size: 14px;
            }

            /* Mengatur agar tabel tidak terpotong di halaman berikutnya */
            .data-diri, table, .rincian-biaya, .footer {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Kop Surat -->
    <div class="kop-surat">
        <h1>KEMENTERIAN PENDIDIKAN KEBUDAYAAN, RISET, DAN TEKNOLOGI</h1>
        <p>UNIVERSITAS TERBUKA</p>
        <p>Jln. Cabe Raya Pamulang Tangerang 15418</p>
        <p>http://www.ut.ac.id</p>
        <hr>
    </div>

    <?php
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
            echo "<div class='alert alert-danger'>Failed to get access token. Response: <pre>" . htmlspecialchars($response) . "</pre></div>";
            return null;
        }

        return $responseArray['data']['signInUser']['access_token'];
    }

    // Fungsi untuk melakukan query dengan access token
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

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($decodedResponse['data'])) {
            echo "<div class='alert alert-danger'>Query failed. Response: <pre>" . htmlspecialchars($response) . "</pre></div>";
            return null;
        }

        return $decodedResponse['data'];
    }

    // Fungsi untuk memformat key menjadi lebih ramah pengguna
    function formatKey($key) {
        $keyMap = [
            'nim' => 'NIM',
            'namaMahasiswa' => 'Nama Mahasiswa',
            'batch' => 'Batch',
            'kodeStatusDp' => 'Kode Status DP',
            'keteranganStatusDp' => 'Keterangan Status DP',
            'kodeUpbjj' => 'Kode UPBJJ',
            'namaUpbjj' => 'Nama UPBJJ',
            'kodeJenjang' => 'Kode Jenjang',
            'namaJenjang' => 'Nama Jenjang',
            'kodeFakultas' => 'Kode Fakultas',
            'namaFakultas' => 'Nama Fakultas',
            'kodeProgramStudi' => 'Kode Program Studi',
            'namaProgramStudi' => 'Nama Program Studi',
            'kodeSipas' => 'Kode SIPAS',
            'namaSipas' => 'Nama SIPAS',
            'nik' => 'NIK',
            'tempatLahirMhs' => 'Tempat Lahir',
            'tanggalLahirMhs' => 'Tanggal Lahir',
            'namaAgama' => 'Agama',
            'jenisKelamin' => 'Jenis Kelamin',
            'kewarganegaraan' => 'Kewarganegaraan',
            'namaIbuKandung' => 'Nama Ibu Kandung',
            'statusKawin' => 'Status Kawin',
            'masaRegistrasiAwal' => 'Masa Registrasi Awal',
            'keteranganMasaRegistrasiAwal' => 'Keterangan Masa Registrasi Awal',
            'alamatMahasiswa' => 'Alamat',
            'namaProvinsi' => 'Nama Provinsi',
            'namaKabko' => 'Nama Kabupaten/Kota',
            'kodePos' => 'Kode Pos',
            'nomorHpMahasiswa' => 'Nomor HP',
            'nomorTeleponMhs' => 'Nomor Telepon',
            'nomorTeleponMhs2' => 'Nomor Telepon 2',
            'nomorTeleponKantor' => 'Nomor Telepon Kantor',
            'nomorKontakKerabat' => 'Nomor Kontak Kerabat',
            'alamatEmailMhs' => 'Email',
            'alamatEmailAlternatif' => 'Email Alternatif',
            'akunFb' => 'Akun Facebook',
            'akunIg' => 'Akun Instagram',
            'akunTwitter' => 'Akun Twitter',
            'namaPendidikanAkhir' => 'Pendidikan Akhir',
            'nomorIjazah' => 'Nomor Ijazah',
            'tahunIjazah' => 'Tahun Ijazah',
            'tahunIjazahPendidikanAkhir' => 'Tahun Ijazah Pendidikan Akhir',
            'namaJurusanAsal' => 'Nama Jurusan Asal',
            'ipkDp' => 'IPK DP',
            'statusPengajuanAlihKredit' => 'Status Pengajuan Alih Kredit',
            'nirmAsal' => 'NIRM Asal',
            'perguruanTinggiAsal' => 'Perguruan Tinggi Asal',
            'statusKerja' => 'Status Kerja',
            'sumberInformasi' => 'Sumber Informasi',
            'tujuanMasuk' => 'Tujuan Masuk',
            'lamaStudi' => 'Lama Studi',
            'keteranganLamaStudi' => 'Keterangan Lama Studi',
            'namaBank' => 'Nama Bank',
            'nomorRekening' => 'Nomor Rekening',
            'namaPemilikRekening' => 'Nama Pemilik Rekening',
            'menggunakanAlatBantu' => 'Menggunakan Alat Bantu',
            'namaKategoriDisabilitas' => 'Nama Kategori Disabilitas',
            'keteranganDisabilitas' => 'Keterangan Disabilitas',
            'namaTingkatMengajar' => 'Nama Tingkat Mengajar',
            'bidangStudiGuru' => 'Bidang Studi Guru',
            'bidangStudiDiajarkan' => 'Bidang Studi Diajarkan',
            'nomorNuptk' => 'Nomor NUPTK',
            'namaLamaAjar' => 'Nama Lama Ajar',
            'tempatMengajar' => 'Tempat Mengajar',
            'statusGuru' => 'Status Guru',
            'idJenisProgram' => 'ID Jenis Program',
            'idPokjar' => 'ID Pokjar',
            'namaPokjar' => 'Nama Pokjar',
            'alamatPokjar' => 'Alamat Pokjar',
            'emailPokjar' => 'Email Pokjar',
            'nomorHpPokjar' => 'Nomor HP Pokjar',
        ];

        return $keyMap[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    // Fungsi untuk menampilkan nilai, menangani kasus di mana nilai bisa berupa array
    function displayValue($value) {
        if (is_array($value)) {
            return implode(', ', array_map('htmlspecialchars', $value));
        }
        return htmlspecialchars($value);
    }

    // Penggunaan query
    $email = 'f.ann.y.ka.rli.n.d.a.bnc@gmail.com';
    $password = '@09082003Ut';

    $accessToken = getAccessToken($email, $password);

    if (!$accessToken) {
        echo '<p class="text-danger">Gagal mendapatkan access token. Pastikan email dan password benar, atau coba lagi nanti.</p>';
    } else {
        // Query untuk getPeragaanDp yang lebih lengkap
        $query1 = '
            query getDp{
                getPeragaanDp {
                    nim
                    namaMahasiswa
                    batch
                    kodeStatusDp
                    keteranganStatusDp
                    kodeUpbjj
                    namaUpbjj
                    kodeJenjang
                    namaJenjang
                    kodeFakultas
                    namaFakultas
                    kodeProgramStudi
                    namaProgramStudi
                    kodeSipas
                    namaSipas
                    nik
                    tempatLahirMhs
                    tanggalLahirMhs
                    namaAgama
                    jenisKelamin
                    kewarganegaraan
                    namaIbuKandung
                    statusKawin
                    masaRegistrasiAwal
                    keteranganMasaRegistrasiAwal:masaRegistrasiAwal
                    alamatMahasiswa
                    namaProvinsi
                    namaKabko
                    kodePos
                    nomorHpMahasiswa
                    nomorTeleponMhs
                    nomorTeleponMhs2
                    nomorTeleponKantor
                    nomorKontakKerabat
                    alamatEmailMhs
                    alamatEmailAlternatif
                    akunFb
                    akunIg
                    akunTwitter
                    namaPendidikanAkhir
                    nomorIjazah
                    tahunIjazah
                    tahunIjazahPendidikanAkhir
                    namaJurusanAsal
                    ipkDp
                    statusPengajuanAlihKredit
                    nirmAsal
                    perguruanTinggiAsal
                    statusKerja
                    sumberInformasi
                    tujuanMasuk
                    lamaStudi
                    keteranganLamaStudi
                    namaBank
                    nomorRekening
                    namaPemilikRekening
                    menggunakanAlatBantu
                    namaKategoriDisabilitas
                    keteranganDisabilitas
                    namaTingkatMengajar
                    bidangStudiGuru
                    bidangStudiDiajarkan
                    nomorNuptk
                    namaLamaAjar
                    tempatMengajar
                    statusGuru
                    idJenisProgram
                    idPokjar
                    namaPokjar
                    alamatPokjar
                    emailPokjar
                    nomorHpPokjar
                }
            }
        ';
        $result1 = performQuery($accessToken, $query1);

        // Tampilkan data dari query pertama (Peragaan DP)
        if (isset($result1['getPeragaanDp']) && is_array($result1['getPeragaanDp'])) {
            echo '<div class="data-diri">';
            echo '<div class="row">';
            foreach ($result1['getPeragaanDp'] as $key => $value) {
                echo '<div class="col">';
                echo '<p><strong>' . htmlspecialchars(formatKey($key)) . ':</strong> ' . displayValue($value) . '</p>';
                echo '</div>';
            }
            echo '</div>'; // Penutup row
            echo '</div>'; // Penutup data-diri
        }

        // Query untuk getPerkembanganAkademik
        $query2 = '
            query {
                getPerkembanganAkademik {
                    ipk
                    ips
                    masaKurikulum
                    masaRegistrasiAwal
                    masaRegistrasiAkhir
                    jumlahSksAmbil
                    jumlahSksLulus
                    jumlahSksSisa
                }
            }
        ';
        $result2 = performQuery($accessToken, $query2);

        // Tampilkan data dari query kedua (Perkembangan Akademik)
        if (isset($result2['getPerkembanganAkademik']) && is_array($result2['getPerkembanganAkademik'])) {
            echo '<div class="data-diri">';
            foreach ($result2['getPerkembanganAkademik'] as $key => $value) {
                echo '<p><strong>' . htmlspecialchars(formatKey($key)) . ':</strong> ' . displayValue($value) . '</p>';
            }
            echo '</div>';
        }

        // Query untuk getAllBillMhs
        $query3 = '
            query {
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
                        keteranganstatusbayar
                        tanggallunas
                    }
                }
            }
        ';
        $result3 = performQuery($accessToken, $query3);

        // Tampilkan data dari query ketiga (Billing Mahasiswa)
        if (isset($result3['getAllBillMhs']['data']) && is_array($result3['getAllBillMhs']['data'])) {
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>No</th>';
            echo '<th>ID Masa</th>';
            echo '<th>No Billing</th>';
            echo '<th>Total Bayar</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            $no = 1;
            foreach ($result3['getAllBillMhs']['data'] as $billing) {
                echo '<tr>';
                echo '<td>' . $no++ . '</td>';
                echo '<td>' . htmlspecialchars($billing['idmasa']) . '</td>';
                echo '<td>' . htmlspecialchars($billing['nobilling']) . '</td>';
                echo '<td>' . htmlspecialchars($billing['totalbayar']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }

        // Query untuk getMataKuliahBerjalan
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

        // Tampilkan data dari query keempat (Mata Kuliah Berjalan)
        if (isset($result4['getMataKuliahBerjalan']) && is_array($result4['getMataKuliahBerjalan'])) {
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
            foreach ($result4['getMataKuliahBerjalan'] as $mataKuliah) {
                echo '<tr>';
                echo '<td>' . $no++ . '</td>';
                echo '<td>' . htmlspecialchars($mataKuliah['kodeMataKuliah']) . '</td>';
                echo '<td>' . htmlspecialchars($mataKuliah['namaMataKuliah']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
    }
    ?>
</div>

</body>
</html>
