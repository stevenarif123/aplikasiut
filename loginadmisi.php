<?php
// Set kredensial login
$username = 'f.ann.y.ka.rl.ind.a.b.n.c@gmail.com';
$password = '@26032005Ut';

// Data untuk dikirimkan dalam permintaan login
$data = array(
    'username' => $username,
    'password' => $password
);

// URL target untuk login
$url = 'https://admisi-sia.ut.ac.id/auth/login';

// Inisialisasi curl
$ch = curl_init();

// Set opsi curl
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// Eksekusi permintaan curl
$response = curl_exec($ch);

// Cek apakah login berhasil (misalnya, dengan memeriksa apakah Anda diarahkan ke halaman dashboard)
if (strpos($response, 'https://admisi-sia.ut.ac.id/dashboard/beranda/v2') !== false) {
    // Lanjutkan ke halaman dashboard atau lakukan tindakan lain yang diperlukan
} else {
    echo 'Login gagal!';
    // Lakukan penanganan kesalahan atau tindakan lainnya
}

// Tutup curl
curl_close($ch);
?>
