<?php
require_once "../../koneksi.php";

if(isset($_POST['no']) && isset($_POST['nim']) && isset($_POST['email'])) {
    $no = $_POST['no'];
    $nim = $_POST['nim'];
    $email = $_POST['email'];

    // Mengambil data mahasiswa dari database
    $sql = "SELECT NamaLengkap, Jurusan, TanggalLahir FROM mahasiswa WHERE No = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
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

        // Data yang akan dikirim melalui cURL
        $data = [
            'action' => 'newuser',
            'sesskey' => 'P1uTaCWkHd', // Sesuaikan jika diperlukan
            '_qf__newuseract_form' => '1',
            'nim_user' => $nim,
            'dd' => $dd,
            'mm' => $mm,
            'yyyy' => $yyyy,
            'email_user' => $email,
            'nomor_hp' => '',
            'submitbutton' => 'KIRIM'
        ];

        // Debugging: Log data yang dikirim
        error_log("Data yang dikirim melalui cURL: " . print_r($data, true));

        // Initialize a cURL session
        $ch = curl_init();

        // Set the URL
        curl_setopt($ch, CURLOPT_URL, 'https://elearning.ut.ac.id/apput/newuser/act.php');

        // Set the HTTP method to POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Set the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        // Set headers
        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: Mozilla/5.0'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Return the response instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Optional: Ignore SSL verification (tidak direkomendasikan untuk produksi)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute the request
        $response = curl_exec($ch);

        // Debugging: Log respon dari server
        error_log("Respon dari server: " . $response);

        // Check for errors
        if (curl_errno($ch)) {
            $error_msg = 'Error:' . curl_error($ch);
            error_log("cURL Error: " . $error_msg);
            echo json_encode(['status' => 'error', 'message' => $error_msg]);
        } else {
            // Proses respon dari server
            // Misalnya, mencari teks tertentu dalam respon untuk menentukan status pendaftaran

            // Contoh: Jika pendaftaran berhasil, mungkin ada teks "Pendaftaran berhasil"
            if (strpos($response, 'Pendaftaran berhasil') !== false || strpos($response, 'Akun Anda telah dibuat') !== false) {
                // Ekstrak password dari respon jika ada
                // Anda perlu menyesuaikan ekstraksi ini sesuai dengan struktur HTML respon
                $password = extractPassword($response);

                // Simpan data ke tabel tuton
                $sql_insert = "INSERT INTO tuton (NIM, Nama, Jurusan, Email, Password) VALUES (?, ?, ?, ?, ?)";
                $stmt_insert = mysqli_prepare($koneksi, $sql_insert);
                mysqli_stmt_bind_param($stmt_insert, "sssss", $nim, $nama, $jurusan, $email, $password);
                mysqli_stmt_execute($stmt_insert);

                // Kembalikan respon
                $message = "Pendaftaran berhasil. <br> NIM: $nim <br> Password: $password";
                echo json_encode(['status' => 'success', 'message' => $message]);
            } else {
                // Jika pendaftaran gagal, cari pesan error dalam respon
                $error_message = extractErrorMessage($response);

                // Kembalikan respon error
                $message = "Pendaftaran gagal. Pesan error: $error_message";
                echo json_encode(['status' => 'error', 'message' => $message]);
            }
        }

        // Close the cURL session
        curl_close($ch);

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Mahasiswa tidak ditemukan.']);
        exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
}

// Fungsi untuk mengekstrak password dari respon HTML
function extractPassword($html) {
    // Contoh implementasi
    // Sesuaikan dengan struktur HTML respon sebenarnya

    // Misalnya, mencari teks antara tag tertentu
    $password = '';

    // Gunakan DOMDocument untuk memparsing HTML
    $dom = new DOMDocument();

    // Supress warnings dari HTML yang tidak valid
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    // Cari elemen yang berisi password
    // Misalnya, elemen dengan id 'password'
    $element = $dom->getElementById('password');

    if ($element) {
        $password = trim($element->textContent);
    } else {
        // Jika tidak ditemukan, mungkin perlu metode lain
        // Contoh menggunakan regex
        if (preg_match('/Password Anda adalah:\s*(\w+)/i', $html, $matches)) {
            $password = $matches[1];
        }
    }

    // Jika tidak berhasil mengekstrak password, gunakan default atau kosong
    if (empty($password)) {
        $password = 'Tidak diketahui';
    }

    // Debugging: Log password yang diekstrak
    error_log("Password yang diekstrak: " . $password);

    return $password;
}

// Fungsi untuk mengekstrak pesan error dari respon HTML
function extractErrorMessage($html) {
    // Contoh implementasi
    // Sesuaikan dengan struktur HTML respon sebenarnya

    // Gunakan DOMDocument untuk memparsing HTML
    $dom = new DOMDocument();

    // Supress warnings dari HTML yang tidak valid
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    // Cari elemen yang berisi pesan error
    // Misalnya, elemen dengan class 'error'
    $xpath = new DOMXPath($dom);
    $nodes = $xpath->query("//*[contains(@class, 'error')]");

    $error_message = 'Tidak diketahui';

    if ($nodes->length > 0) {
        $error_message = trim($nodes->item(0)->textContent);
    } else {
        // Jika tidak ditemukan, mungkin perlu metode lain
        // Contoh menggunakan regex
        if (preg_match('/<div class="error">([^<]+)<\/div>/i', $html, $matches)) {
            $error_message = $matches[1];
        }
    }

    // Debugging: Log pesan error yang diekstrak
    error_log("Pesan error yang diekstrak: " . $error_message);

    return $error_message;
}
?>
