<?php
// Nonaktifkan penampilan kesalahan ke output
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Atur header untuk JSON
header('Content-Type: application/json');

// Mulai output buffering
ob_start();

require_once "../../koneksi.php";

// Pastikan semua input yang diperlukan tersedia
if(isset($_POST['no']) && isset($_POST['nim']) && isset($_POST['email'])) {
    $no = $_POST['no'];
    $nim = $_POST['nim'];
    $email = $_POST['email'];

    // Ambil data mahasiswa dari database
    $sql = "SELECT NamaLengkap, Jurusan, TanggalLahir FROM mahasiswa WHERE No = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan pernyataan SQL.']);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "i", $no);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $mahasiswa = mysqli_fetch_assoc($result);

    if($mahasiswa) {
        $nama = $mahasiswa['NamaLengkap'];
        $jurusan = $mahasiswa['Jurusan'];
        $tanggal_lahir = $mahasiswa['TanggalLahir'];
        $dd = date('d', strtotime($tanggal_lahir));
        $mm = date('m', strtotime($tanggal_lahir));
        $yyyy = date('Y', strtotime($tanggal_lahir));

        // Langkah 1: Permintaan GET untuk mendapatkan sesskey dan cookies
        $cookie_file = tempnam(sys_get_temp_dir(), 'cookie');

        // Initialize cURL untuk GET request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://elearning.ut.ac.id/apput/newuser/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // User-Agent yang lebih lengkap
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        // Jangan verifikasi SSL untuk pengujian (tidak direkomendasikan untuk produksi)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = 'Error during GET request: ' . curl_error($ch);
            error_log($error_msg);
            echo json_encode(['status' => 'error', 'message' => $error_msg]);
            exit();
        }

        // Simpan respon GET untuk debugging
        file_put_contents('get_response_debug.html', $response);

        // Langkah 2: Ekstrak sesskey menggunakan regex
        $sesskey = '';
        if (preg_match('/<input\s+type=["\']hidden["\']\s+name=["\']sesskey["\']\s+value=["\']([^"\']+)["\']/i', $response, $matches)) {
            $sesskey = $matches[1];
        } else {
            $error_msg = 'Tidak dapat menemukan sesskey.';
            error_log($error_msg);
            echo json_encode(['status' => 'error', 'message' => $error_msg]);
            exit();
        }

        // Langkah 3: Permintaan POST dengan data dan header lengkap
        $data = [
            'action' => 'newuser',
            'sesskey' => $sesskey,
            '_qf__newuseract_form' => '1',
            'nim_user' => $nim,
            'dd' => $dd,
            'mm' => $mm,
            'yyyy' => $yyyy,
            'email_user' => $email,
            'nomor_hp' => '', // Nomor HP dihapus atau dikosongkan
            'submitbutton' => 'KIRIM'
        ];

        $headers = [
            'Host: elearning.ut.ac.id',
            'Origin: https://elearning.ut.ac.id',
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'Referer: https://elearning.ut.ac.id/apput/newuser/',
            'Accept-Encoding: gzip, deflate, br',
        ];

        curl_setopt($ch, CURLOPT_URL, 'https://elearning.ut.ac.id/apput/newuser/act.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = 'Error during POST request: ' . curl_error($ch);
            error_log($error_msg);
            echo json_encode(['status' => 'error', 'message' => $error_msg]);
            exit();
        } else {
            // Simpan respon POST untuk debugging
            file_put_contents('post_response_debug.html', $response);

            // Periksa keberhasilan pendaftaran dengan mencari elemen dengan class 'alert alert-info'
            if (preg_match('/<div\s+class=["\']alert\s+alert-info["\']>(.*?)<\/div>/is', $response, $matches)) {
                $alert_content = $matches[1];
                $message = strip_tags(trim($alert_content));

                // Ekstrak password jika tersedia
                $password = 'Tidak diketahui';
                if (preg_match('/Password(?: Anda adalah)?:\s*([^\s<]+)/i', $response, $pw_matches)) {
                    $password = trim($pw_matches[1]);
                }

                // Simpan data ke tabel tuton
                $sql_insert = "INSERT INTO tuton (NIM, Nama, Jurusan, Email, Password) VALUES (?, ?, ?, ?, ?)";
                $stmt_insert = mysqli_prepare($koneksi, $sql_insert);
                if (!$stmt_insert) {
                    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan pernyataan SQL untuk insert tuton.']);
                    exit();
                }
                mysqli_stmt_bind_param($stmt_insert, "sssss", $nim, $nama, $jurusan, $email, $password);
                if (mysqli_stmt_execute($stmt_insert)) {
                    echo json_encode(['status' => 'success', 'message' => 'Pendaftaran berhasil. ' . $message]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data ke tabel tuton.']);
                }
            } else {
                // Jika pendaftaran gagal, cari pesan error
                $error_message = 'Tidak diketahui.';
                if (preg_match('/<div\s+class=["\']error["\']>(.*?)<\/div>/is', $response, $error_matches)) {
                    $error_message = strip_tags(trim($error_matches[1]));
                } elseif (preg_match('/<div\s+class=["\']alert\s+alert-danger["\']>(.*?)<\/div>/is', $response, $error_matches)) {
                    $error_message = strip_tags(trim($error_matches[1]));
                } elseif (preg_match('/<div\s+class=["\'].*?alert.*?["\']>(.*?)<\/div>/is', $response, $error_matches)) {
                    $error_message = strip_tags(trim($error_matches[1]));
                }

                echo json_encode(['status' => 'error', 'message' => "Pendaftaran gagal. Pesan error: $error_message"]);
            }
        }

        curl_close($ch);
        unlink($cookie_file);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    }
}
?>