<?php
// Ambil nilai jenis surat, bulan, dan tahun dari permintaan
$letterType = isset($_GET['type']) ? $_GET['type'] : '';
$month = isset($_GET['month']) ? $_GET['month'] : date('n'); // Jika bulan tidak disediakan, gunakan bulan saat ini
$year = isset($_GET['year']) ? $_GET['year'] : date('Y'); // Jika tahun tidak disediakan, gunakan tahun saat ini

// Fungsi untuk menghasilkan nomor surat berdasarkan jenis surat, bulan, dan tahun
function generateLetterNumber($letterType, $month, $year) {
    // Di sini Anda dapat menambahkan logika untuk menghasilkan nomor surat sesuai dengan kebutuhan perusahaan Anda
    // Contoh sederhana: Nomor surat = jenis surat + bulan (dalam dua digit) + tahun (dalam dua digit) + nomor urut
    // Misalnya, jika jenis surat adalah 'A', bulan adalah 5, dan tahun adalah 2024, nomor surat bisa menjadi 'A0524-001'
    
    // Contoh sederhana:
    $sequentialNumber = 1; // Misalnya nomor urut dimulai dari 1
    $formattedMonth = sprintf("%02d", $month); // Format bulan menjadi dua digit dengan leading zero jika diperlukan
    $formattedYear = substr($year, -2); // Ambil dua digit terakhir dari tahun

    // Gabungkan semua komponen untuk membuat nomor surat
    $letterNumber = $letterType . $formattedMonth . $formattedYear . '-' . sprintf("%03d", $sequentialNumber);

    return $letterNumber;
}

// Panggil fungsi untuk menghasilkan nomor surat
$generatedNumber = generateLetterNumber($letterType, $month, $year);

// Keluarkan nomor surat sebagai respons
echo $generatedNumber;
?>
