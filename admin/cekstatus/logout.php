<?php
// Permintaan GraphQL untuk logout
$query = '{"query":"mutation { logout }","variables":{}}';

// URL GraphQL endpoint
$graphql_endpoint = 'https://example.com/graphql'; // Ganti dengan URL GraphQL endpoint yang sesuai

// Konfigurasi cURL untuk melakukan permintaan POST
$curl = curl_init($graphql_endpoint);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Eksekusi permintaan dan tanggapiannya
$response = curl_exec($curl);

// Cek jika ada kesalahan
if ($response === false) {
    echo 'Error: ' . curl_error($curl);
} else {
    // Proses tanggapan
    $decoded_response = json_decode($response, true);

    // Cek jika ada kesalahan dalam tanggapan GraphQL
    if (isset($decoded_response['errors'])) {
        echo 'GraphQL Error: ' . print_r($decoded_response['errors'], true);
    } else {
        // Logout berhasil
        echo 'Logout berhasil!';

        // Redirect ke pencarian.php setelah 2 detik
        header("refresh:2; url=pencarian.php");
        exit; // Hentikan eksekusi skrip setelah melakukan redirect
    }
}

// Tutup koneksi cURL
curl_close($curl);
?>