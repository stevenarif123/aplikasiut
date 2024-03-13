<?php
// Informasi koneksi ke database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'mahasiswa';

// Nama file backup
$backupFile = 'backup_' . date("Ymd_His") . '.sql';

// Eksekusi perintah shell untuk backup database
$command = "mysqldump --user={$username} --password={$password} --host={$host} {$database} > {$backupFile}";
exec($command, $output, $return);

// Periksa apakah backup berhasil atau tidak
if ($return === 0) {
    echo "Backup database berhasil. File: {$backupFile}";
} else {
    echo "Backup database gagal.";
}