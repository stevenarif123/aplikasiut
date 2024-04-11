<?php
require_once "koneksi.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit; // tambahkan exit setelah header
}


if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Nama file backup
$backupFile = 'backup_' . date("Ymd_His") . '.sql';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Direktori tempat menyimpan file backup
$backupDir = dirname(__FILE__) . '/backup/';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true); // Buat direktori jika belum ada
}

$backupFileWithPath = $backupDir . $backupFile;

echo "<h3>Backing up database to `<code>{$backupFile}</code>`</h3>";

// Jalur lengkap ke mysqldump (sesuaikan dengan instalasi MySQL Anda)
$mysqldumpPath = "C:\xampp\mysql\bin\mysqldump.exe"; // misalnya: '/usr/bin/mysqldump'

// Eksekusi perintah untuk backup database
exec("{$mysqldumpPath} --user={$username} --password={$password} --host={$host} {$database} --result-file={$backupFileWithPath} 2>&1", $output);

// Tampilkan output dari eksekusi perintah
var_dump($output);
?>