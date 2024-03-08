<?php
// Informasi koneksi ke database
$host = 'localhost';
$username = 'username';
$password = 'password';
$database = 'nama_database';

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
<<<<<<< HEAD
?>
=======
?>
>>>>>>> 739b8ff1d06a9f8214457848fd8f81daa20f119d
