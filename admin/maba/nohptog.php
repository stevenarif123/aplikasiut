<?php

require_once("../koneksi.php");

// Konfigurasi Google Contacts API
$client_id = "YOUR_CLIENT_ID";
$client_secret = "YOUR_CLIENT_SECRET";
$redirect_uri = "YOUR_REDIRECT_URI";

// Buat Google Client
$client = new Google_Client();
$client->setApplicationName("My Contact App");
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->setScopes(Google_Service_People::CONTACTS_READWRITE);

// Arahkan pengguna ke halaman otorisasi jika diperlukan
if (!isset($_SESSION['access_token'])) {
  $authUrl = $client->createAuthUrl();
  header('Location: ' . $authUrl);
  exit;
} else {
  $client->setAccessToken($_SESSION['access_token']);
}

// Periksa jika token akses telah kedaluwarsa
if ($client->isAccessTokenExpired()) {
  $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
  $_SESSION['access_token'] = $client->getAccessToken();
}

// Buat layanan Google Contacts
$service = new Google_Service_People($client);

// Ambil data mahasiswa dari database
$sql = "SELECT * FROM mahasiswa";
$result = $conn->query($sql);

// Proses data mahasiswa dan tambahkan ke Google Contacts
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {

    // Format nama sesuai permintaan
    $namaLengkap = $row["NamaLengkap"];
    $jurusan = strtoupper($row["Jurusan"]);
    $namaFormatted = "$jurusan - " . ucwords($namaLengkap);

    // Buat objek kontak baru
    $contact = new Google_Service_People_Person();
      
    
    // Tambahkan nomor telepon
    $phoneNumber = new Google_Service_People_PersonPhoneNumber();
    $phoneNumber->setValue($row["NomorHP"]);
    $phoneNumber->setType("mobile");
    $contact->setPhoneNumbers(array($phoneNumber));

    // Tambahkan kontak ke Google Contacts
    $response = $service->people->createContact($contact);

    // Tampilkan pesan berhasil
    echo "Kontak " . $namaFormatted . " berhasil ditambahkan.\n";
  }
} else {
  // Tampilkan pesan jika tidak ada data
  echo "Tidak ada data mahasiswa yang ditemukan.";
}

// Tutup koneksi ke database
$conn->close();

?>