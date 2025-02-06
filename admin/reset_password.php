<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'];
    $name = $_POST['name'];
    $tanggalLahir = $_POST['tanggalLahir'];
    $namaIbuKandung = $_POST['namaIbuKandung'];

    // Format tanggal lahir menjadi dd/mm/yyyy
    $tanggalLahirFormatted = date('d/m/Y', strtotime($tanggalLahir));

    $data = [
        'query' => "
            mutation resetPasswordEcampus(
                \$nim: String!,
                \$name: String!,
                \$tanggalLahirMahasiswa: String!,
                \$namaIbuKandung: String!
            ) {
                resetPasswordEcampus(
                    namaIbuKandung: \$namaIbuKandung,
                    tanggalLahirMahasiswa: \$tanggalLahirMahasiswa,
                    name: \$name,
                    nim: \$nim
                ) {
                    id
                    kode
                    keterangan
                }
            }
        ",
        'variables' => [
            'namaIbuKandung' => $namaIbuKandung,
            'tanggalLahirMahasiswa' => $tanggalLahirFormatted,
            'nim' => $nim,
            'name' => $name
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api-sia.ut.ac.id/backend-sia/api/graphql');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json, text/plain, */*',
        'Content-Type: application/json',
        'Authorization: undefined' // Pastikan ini sesuai dengan apa yang diperlukan oleh API
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        echo '<p class="text-danger">Terjadi kesalahan jaringan: ' . curl_error($ch) . '</p>';
    } else {
        $responseData = json_decode($response, true);
        if (isset($responseData['errors'])) {
            echo '<p class="text-danger">Error: ' . implode(', ', array_column($responseData['errors'], 'message')) . '</p>';
        } else if (isset($responseData['data']['resetPasswordEcampus'])) {
            $result = $responseData['data']['resetPasswordEcampus'];
            echo '<p>ID: ' . htmlspecialchars($result['id']) . '</p>';
            echo '<p>Kode: ' . htmlspecialchars($result['kode']) . '</p>';
            echo '<p>Keterangan: ' . htmlspecialchars($result['keterangan']) . '</p>';
        } else {
            echo '<p class="text-danger">Respons tidak valid dari server</p>';
        }
    }

    curl_close($ch);
} else {
    echo '<p class="text-danger">Metode permintaan tidak diizinkan</p>';
}
?>