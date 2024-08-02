<?php

require_once("../koneksi.php");

// Mulai sesi
session_start();

// Ambil data mahasiswa dari database
$sql = "SELECT * FROM mahasiswabaru20242";
$result = $koneksi->query($sql);

// Nama file CSV
$csvFile = 'contacts.csv';

// Buka file CSV untuk ditulis
$fileHandle = fopen($csvFile, 'w');

// Tulis header CSV sesuai dengan format Google Contacts
$header = [
    'Name', 'First Name', 'Middle Name', 'Last Name', 'Yomi Name', 'Phonetic First Name', 
    'Phonetic Middle Name', 'Phonetic Last Name', 'Name Prefix', 'Name Suffix', 'Nickname', 
    'File As', 'Organization Name', 'Organization Title', 'Organization Department', 'Birthday', 
    'Gender', 'Location', 'Billing Information', 'Directory Server', 'Mileage', 'Occupation', 
    'Hobby', 'Sensitivity', 'Priority', 'Notes', 'Language', 'Photo', 'Group Membership', 
    'E-mail 1 - Type', 'E-mail 1 - Value', 'Phone 1 - Type', 'Phone 1 - Value', 'Phone 2 - Type', 
    'Phone 2 - Value', 'Address 1 - Type', 'Address 1 - Formatted', 'Address 1 - Street', 
    'Address 1 - City', 'Address 1 - PO Box', 'Address 1 - Region', 'Address 1 - Postal Code', 
    'Address 1 - Country', 'Address 1 - Extended Address', 'Website 1 - Type', 'Website 1 - Value'
];
fputcsv($fileHandle, $header);

// Proses data mahasiswa dan tuliskan ke file CSV
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Format nama sesuai permintaan: 24.2 JURUSAN - Nama Mahasiswa
        $namaLengkap = $row["NamaLengkap"];
        $jurusan = strtoupper($row["Jurusan"]);
        $namaFormatted = "CAMABA 24.2 $jurusan - " . ucwords($namaLengkap);

        // Siapkan data untuk setiap kontak
        $contactData = [
            $namaFormatted, // Name
            '', // First Name
            '', // Middle Name
            '', // Last Name
            '', // Yomi Name
            '', // Phonetic First Name
            '', // Phonetic Middle Name
            '', // Phonetic Last Name
            '', // Name Prefix
            '', // Name Suffix
            '', // Nickname
            '', // File As
            '', // Organization Name
            '', // Organization Title
            '', // Organization Department
            '', // Birthday
            '', // Gender
            '', // Location
            '', // Billing Information
            '', // Directory Server
            '', // Mileage
            '', // Occupation
            '', // Hobby
            '', // Sensitivity
            '', // Priority
            '', // Notes
            '', // Language
            '', // Photo
            '', // Group Membership
            '', // E-mail 1 - Type
            '', // E-mail 1 - Value
            'Mobile', // Phone 1 - Type
            $row["NomorHP"], // Phone 1 - Value
            '', // Phone 2 - Type
            '', // Phone 2 - Value
            '', // Address 1 - Type
            '', // Address 1 - Formatted
            '', // Address 1 - Street
            '', // Address 1 - City
            '', // Address 1 - PO Box
            '', // Address 1 - Region
            '', // Address 1 - Postal Code
            '', // Address 1 - Country
            '', // Address 1 - Extended Address
            '', // Website 1 - Type
            '', // Website 1 - Value
        ];

        // Tulis data kontak ke file CSV
        fputcsv($fileHandle, $contactData);
    }

    echo "Data berhasil diekspor ke $csvFile.\n";
} else {
    // Tampilkan pesan jika tidak ada data
    echo "Tidak ada data mahasiswa yang ditemukan.";
}

// Tutup file CSV
fclose($fileHandle);

// Tutup koneksi ke database
$koneksi->close();

?>
