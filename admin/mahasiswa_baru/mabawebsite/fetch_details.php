<?php
// Ganti dengan URL API Anda
$apiUrl = 'https://uttoraja.com/pendaftaran/api/pendaftar';

// Ambil ID dari query string
$id = intval($_GET['id']);

// Konfigurasi cURL untuk mengambil data dari API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl . '?id=' . $id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

// Tambahkan header untuk mendukung HTTPS jika diperlukan
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Eksekusi permintaan
$response = curl_exec($ch);

// Periksa kesalahan
if(curl_errno($ch)){
    echo "<p>Kesalahan koneksi: " . curl_error($ch) . "</p>";
    curl_close($ch);
    exit;
}

// Tutup koneksi cURL
curl_close($ch);

// Decode respons JSON
$row = json_decode($response, true);

// Periksa apakah data ditemukan
if (!empty($row)) {
    // Tampilkan data jika ditemukan
    echo "<div class='detail-mahasiswa'>";
    echo "<h2>Detail Mahasiswa</h2>";
    echo "<table class='table table-bordered'>";
    
    // Daftar kolom yang ingin ditampilkan dengan label yang lebih deskriptif
    $columns = [
        'nama_lengkap' => 'Nama Lengkap',
        'tempat_lahir' => 'Tempat Lahir',
        'tanggal_lahir' => 'Tanggal Lahir',
        'ibu_kandung' => 'Nama Ibu Kandung',
        'nik' => 'NIK',
        'jurusan' => 'Jurusan',
        'nomor_hp' => 'Nomor HP',
        'agama' => 'Agama',
        'jenis_kelamin' => 'Jenis Kelamin',
        'jalur_program' => 'Jalur Program',
        'alamat' => 'Alamat',
        'ukuran_baju' => 'Ukuran Baju',
        'tempat_kerja' => 'Tempat Kerja',
        'bekerja' => 'Status Bekerja',
        'pertanyaan' => 'Pertanyaan'
    ];

    // Tampilkan setiap kolom
    foreach ($columns as $key => $label) {
        // Ambil nilai, gunakan default jika kosong
        $value = !empty($row[$key]) ? $row[$key] : '-';
        
        // Khusus untuk beberapa kolom, tambahkan pemformatan
        if ($key == 'tanggal_lahir') {
            // Ubah format tanggal jika perlu
            $value = date('d F Y', strtotime($value));
        }
        
        echo "<tr>";
        echo "<th class='text-left'>" . htmlspecialchars($label) . "</th>"; // Rata kanan untuk label
        echo "<td class='text-left'>" . htmlspecialchars($value) . "</td>"; // Rata kanan untuk nilai
        echo "</tr>";
    }

    echo "</table>";
    echo "</div>";
} else {
    echo "<p>Data mahasiswa tidak ditemukan.</p>";
}
?>