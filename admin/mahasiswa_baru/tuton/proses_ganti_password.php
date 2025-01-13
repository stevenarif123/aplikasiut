<?php
require_once '../../koneksi.php';

if (isset($_POST['nim']) && isset($_POST['old_password']) && isset($_POST['new_password'])) {
    $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $oldPassword = mysqli_real_escape_string($koneksi, $_POST['old_password']);
    $newPassword = mysqli_real_escape_string($koneksi, $_POST['new_password']);

    // Ambil password dari database (opsional, bisa dihapus jika tidak diperlukan)
    $sql = "SELECT Password FROM tuton WHERE NIM = '$nim'";
    $result = mysqli_query($koneksi, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        // Lakukan proses login dan penggantian password ke elearning
        // Inisialisasi cURL
        $ch = curl_init();

        // File cookie untuk menyimpan session
        $cookieFile = tempnam(sys_get_temp_dir(), 'cookie_');

        // Step 1: Mendapatkan halaman login untuk mengambil logintoken
        $loginUrl = 'https://elearning.ut.ac.id/login/index.php';
        curl_setopt($ch, CURLOPT_URL, $loginUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Non-aktifkan verifikasi SSL jika diperlukan
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $loginPage = curl_exec($ch);

        // Mengambil logintoken
        if (preg_match('/name="logintoken" value="(.*?)"/', $loginPage, $matches)) {
            $logintoken = $matches[1];
        } else {
            $logintoken = '';
        }

        // Step 2: Melakukan login
        $postFields = [
            'username' => $nim,
            'password' => $oldPassword,
            'logintoken' => $logintoken,
        ];

        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'Content-Type: application/x-www-form-urlencoded',
        ];

        curl_setopt($ch, CURLOPT_URL, $loginUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $loginResponse = curl_exec($ch);

        // Update password di database terlebih dahulu
        $sqlUpdate = "UPDATE tuton SET Password = '$newPassword' WHERE NIM = '$nim'";
        if (mysqli_query($koneksi, $sqlUpdate)) {
            $updateMessage = "Password di database berhasil diperbarui untuk NIM $nim.";
        } else {
            $updateMessage = "Gagal mengupdate password di database: " . mysqli_error($koneksi);
        }

        // Cek apakah login berhasil
        if (strpos($loginResponse, 'loginerrors') === false) {
            // Login berhasil

            // Step 3: Mengakses halaman dashboard untuk mendapatkan sesskey
            $dashboardUrl = 'https://elearning.ut.ac.id/my/';
            curl_setopt($ch, CURLOPT_URL, $dashboardUrl);
            curl_setopt($ch, CURLOPT_POST, 0);
            $dashboardPage = curl_exec($ch);

            // Mengambil sesskey
            if (preg_match('/"sesskey":"(.*?)"/', $dashboardPage, $matches)) {
                $sesskey = $matches[1];
            } elseif (preg_match('/name="sesskey" value="(.*?)"/', $dashboardPage, $matches)) {
                $sesskey = $matches[1];
            } else {
                $sesskey = '';
            }

            if ($sesskey != '') {
                // Step 4: Mengirim permintaan ubah password
                $changePasswordUrl = 'https://elearning.ut.ac.id/login/change_password.php';
                curl_setopt($ch, CURLOPT_URL, $changePasswordUrl);
                curl_setopt($ch, CURLOPT_POST, 0);
                $changePasswordPage = curl_exec($ch);

                $postFields = [
                    'id' => 1,
                    'sesskey' => $sesskey,
                    '_qf__login_change_password_form' => 1,
                    'password' => $oldPassword,
                    'newpassword1' => $newPassword,
                    'newpassword2' => $newPassword,
                    'submitbutton' => 'Save changes',
                ];

                curl_setopt($ch, CURLOPT_URL, $changePasswordUrl);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $changePasswordResponse = curl_exec($ch);

                // Cek apakah password berhasil diubah
                if (strpos($changePasswordResponse, 'Your password has been changed') !== false ||
                    strpos($changePasswordResponse, 'Password changed successfully') !== false) {
                    $changePasswordMessage = "Password berhasil diubah di elearning untuk NIM $nim.";
                } else {
                    $changePasswordMessage = "Gagal mengubah password di elearning untuk NIM $nim.";
                }
            } else {
                $changePasswordMessage = "Gagal mengambil sesskey. Tidak dapat mengubah password di elearning.";
            }

        } else {
            // Login gagal
            $changePasswordMessage = "Login gagal ke elearning untuk NIM $nim. Periksa kembali password lama Anda.";
        }

        // Tutup koneksi cURL
        curl_close($ch);

        // Hapus file cookie sementara
        unlink($cookieFile);

        // Tampilkan pesan akhir
        echo $updateMessage . "<br>" . $changePasswordMessage;

    } else {
        echo "NIM tidak ditemukan di database.";
    }
} else {
    echo "Data tidak lengkap. Pastikan NIM, password lama, dan password baru dikirim.";
}
?>
