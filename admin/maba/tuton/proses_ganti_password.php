<?php
require_once '../../koneksi.php';

if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $nim = isset($_GET['nim']) ? $_GET['nim'] : '';
    $message = isset($_GET['message']) ? $_GET['message'] : '';

    if ($status == 'success') {
        echo "<div class='alert alert-success' role='alert'>
                Password untuk NIM $nim berhasil diupdate.
              </div>";
    } elseif ($status == 'error') {
        echo "<div class='alert alert-danger' role='alert'>
                Terjadi kesalahan: $message
              </div>";
    }
}
// Dapatkan NIM dan password baru dari parameter URL
$nim = $_GET['nim'];
$newPassword = $_GET['new_password'];

// Ambil password lama dari database
$sql = "SELECT Password FROM tuton WHERE NIM = '$nim'";
$result = mysqli_query($koneksi, $sql);
$row = mysqli_fetch_assoc($result);
$oldPassword = $row['Password'];

// Lakukan login otomatis dan penggantian password
$loginUrl = 'https://elearning.ut.ac.id/login/index.php';
$changePasswordUrl = 'https://elearning.ut.ac.id/login/change_password.php?id=1';

// Gunakan cURL untuk melakukan login
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'username' => $nim,
    'password' => $oldPassword,
    'anchor' => '',
    'logintoken' => 'logintoken_value' // Logintoken diambil secara dinamis jika memungkinkan
]));
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);

// Lanjutkan ke halaman penggantian password
curl_setopt($ch, CURLOPT_URL, $changePasswordUrl);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'password' => $oldPassword,
    'newpassword1' => $newPassword,
    'newpassword2' => $newPassword
]));
$response = curl_exec($ch);

// Tutup koneksi cURL
curl_close($ch);

// Update password di database
$sql = "UPDATE tuton SET Password = '$newPassword' WHERE NIM = '$nim'";
if (mysqli_query($koneksi, $sql)) {
    echo "Password berhasil diupdate untuk NIM $nim.";
} else {
    echo "Gagal mengupdate password: " . mysqli_error($koneksi);
}

?>
