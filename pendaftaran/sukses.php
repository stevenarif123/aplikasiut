<?php
// $required_fields = ['nama_lengkap', 'tempat_lahir', 'tanggal_lahir']; // Tambahkan field yang diperlukan
// foreach ($required_fields as $field) {
//     if (!isset($_POST[$field])) {
//         // Jika ada data yang belum terisi, kembalikan pengguna ke halaman form
//         header("Location: index.php");
//         exit();
//     }
// }
// // Jika terdapat data yang dikirimkan, tampilkan pesan sukses beserta data yang diterima
// $nama_lengkap = isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : '';
$nama = $_POST['nama_lengkap'];
echo $nama; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sukses</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .center {
            margin: auto;
            width: 50%;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="center">
        <div class="alert alert-success" role="alert">
            Data berhasil disimpan, tunggu informasi selanjutnya melalui Whatsapp. Terima kasih, <?php echo $nama_lengkap; ?>!
        </div>
        <p>Kembali ke halaman pendaftaran dalam <span id="countdown">5</span> detik.</p>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Timer untuk kembali ke halaman pendaftaran
        var seconds = 5; // Waktu dalam detik
        var countdown = setInterval(function() {
            seconds--;
            document.getElementById("countdown").textContent = seconds;
            if (seconds <= 0) {
                clearInterval(countdown);
                window.location.href = "index.php"; // Redirect ke halaman pendaftaran
            }
        }, 1000); // Setiap 1 detik
    </script>
</body>
</html>