<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}

// Koneksi ke database
require_once "../koneksi.php";

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$username = $_SESSION['username'];
$query = "SELECT * FROM admin WHERE username='$username'";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);
if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Mahasiswa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Tambah Data Mahasiswa</h1>
        <form action="proses_tambah.php" method="post" class="max-w-lg mx-auto bg-white p-8 rounded-lg shadow-md dark:bg-gray-800">
            <div class="mb-4">
                <label for="nama_lengkap" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Masukkan Nama Lengkap">
            </div>
            <div class="mb-4">
                <label for="nim" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">NIM</label>
                <input type="text" id="nim" name="nim" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Masukkan NIM">
            </div>
            <div class="mb-4">
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Email</label>
                <input type="email" id="email" name="email" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Masukkan Email">
            </div>
            <div class="mb-4">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Password</label>
                <input type="password" id="password" name="password" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Masukkan Password">
            </div>
            <div class="mb-4">
                <label for="status_sia" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Status SIA</label>
                <input type="text" id="status_sia" name="status_sia" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Masukkan Status SIA">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600">Tambah</button>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
