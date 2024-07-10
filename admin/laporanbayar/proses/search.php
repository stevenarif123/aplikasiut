<?php
include '../../koneksi.php'; // Sesuaikan dengan path file konfigurasi database

$query = $_GET['query'];
$searchTerm = "%$query%";

// Query untuk mahasiswa yang sudah ada NIM
$sql1 = "SELECT No, Nim, NamaLengkap, Jurusan FROM mahasiswa WHERE Nim LIKE ? OR NamaLengkap LIKE ?";

// Query untuk mahasiswa baru yang belum ada NIM
$sql2 = "SELECT No, '' AS Nim, NamaLengkap, Jurusan FROM mahasiswabaru20242 WHERE NamaLengkap LIKE ?";

// Menggabungkan kedua query dengan UNION
$sql = "($sql1) UNION ($sql2)";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$output = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $output .= '
            <tr>
                <td>' . $row['Nim'] . '</td>
                <td>' . $row['NamaLengkap'] . '</td>
                <td>' . $row['Jurusan'] . '</td>
                <td><button class="btn btn-primary add-bill-btn" data-nim="' . $row['Nim'] . '" data-nama="' . $row['NamaLengkap'] . '" data-jurusan="' . $row['Jurusan'] . '">Tambah Tagihan</button></td>
            </tr>
        ';
    }
} else {
    $output = '<tr><td colspan="4">Mahasiswa tidak ditemukan</td></tr>';
}

echo $output;
?>
