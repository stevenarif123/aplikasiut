<?php
require_once "koneksi.php"; // Pastikan ini adalah file koneksi ke database Anda

// Nama file backup
$backupFile = 'backup_' . date("Ymd_His") . '.sql';

// Direktori tempat menyimpan file backup
$backupDir = dirname(__FILE__) . '/backup/';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

$backupFileWithPath = $backupDir . $backupFile;

// Buka file untuk ditulis
$handle = fopen($backupFileWithPath, 'w+');

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Mendapatkan daftar semua tabel dalam database
$tables = array();
$result = $koneksi->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

// Iterasi setiap tabel dan menulis informasi ke file
foreach ($tables as $table) {
    $result = $koneksi->query("SELECT * FROM $table");
    $numColumns = $result->field_count;

    // Menulis header tabel
    $return = "DROP TABLE $table;";
    fwrite($handle, $return . "\n");
    $row2 = $koneksi->query("SHOW CREATE TABLE $table")->fetch_row();
    $return = "\n\n" . $row2[1] . ";\n\n";
    fwrite($handle, $return);

    // Menulis data tabel
    for ($i = 0; $i < $numColumns; $i++) {
        while ($row = $result->fetch_row()) {
            $return = "INSERT INTO $table VALUES(";
            for ($j = 0; $j < $numColumns; $j++) {
                $row[$j] = addslashes($row[$j]);
                $row[$j] = str_replace("\n", "\\n", $row[$j]);
                if (isset($row[$j])) {
                    $return .= '"' . $row[$j] . '"';
                } else {
                    $return .= '""';
                }
                if ($j < ($numColumns - 1)) {
                    $return .= ',';
                }
            }
            $return .= ");\n";
            fwrite($handle, $return);
        }
    }
    fwrite($handle, "\n\n\n");
}

// Tutup file
fclose($handle);

echo "Backup database telah berhasil disimpan ke lokasi: " . $backupFileWithPath;
?>
