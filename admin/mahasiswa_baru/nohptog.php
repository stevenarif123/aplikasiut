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

// Fungsi untuk mengubah setiap kata menjadi huruf pertama kapital
function formatNama($nama) {
    return ucwords(strtolower($nama));
}

// Fungsi untuk menghapus (S1) dari nama jurusan
function hapusS1($jurusan) {
    return preg_replace('/\s*\(S1\)$/', '', $jurusan);
}

// Fungsi untuk membuat singkatan dari jurusan, tapi hanya jika jurusan memiliki 3 kata atau lebih
function singkatanJurusan($jurusan) {
    $jurusan = hapusS1($jurusan); // Hapus (S1) dari jurusan
    $kata = explode(' ', $jurusan);
    if (count($kata) >= 3) {
        $singkatan = '';
        foreach ($kata as $k) {
            $singkatan .= strtoupper(substr($k, 0, 1));
        }
        return $singkatan;
    } else {
        return ucwords(strtolower($jurusan)); // Tidak disingkat jika hanya satu atau dua kata
    }
}

// Proses data mahasiswa dan tuliskan ke file CSV
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Format nama mahasiswa
        $namaLengkap = formatNama(stripslashes($row["NamaLengkap"]));
        $jurusan = singkatanJurusan($row["Jurusan"]);

        // Tentukan prefix berdasarkan status
        $prefix = ($row["STATUS_INPUT_SIA"] == "MAHASISWA UT") ? "24.2" : "CAMABA 24.2";

        // Format nama sesuai dengan format yang baru
        $namaFormatted = "$prefix $jurusan - $namaLengkap";

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
            $row["Jurusan"], // Organization Name (untuk menjelaskan kepanjangan jurusan, jika ada singkatan)
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
