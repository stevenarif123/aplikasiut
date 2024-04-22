<?php
require_once("../admin/koneksi.php");

$nama_lengkap = '';
$tempat_lahir = '';
$tanggal_lahir = '';
$nama_ibu_kandung = '';
$nik = '';
$jurusan = '';
$nomor_hp = '';
$agama = '';
$jenis_kelamin = '';
$pesan = '';

if(isset($_POST['submit'])){
    $nama_lengkap = $_POST['nama_lengkap'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $nama_ibu_kandung = $_POST['nama_ibu_kandung'];
    $nik = $_POST['nik'];
    $jurusan = $_POST['jurusan'];
    $nomor_hp = $_POST['nomor_hp'];
    $agama = $_POST['agama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $pesan = $_POST['pesan'];

    // Insert data into database table mabawebsite
    $query = "INSERT INTO mabawebsite (nama_lengkap, tempat_lahir, tanggal_lahir, nama_ibu_kandung, nik, jurusan, nomor_hp, agama, jenis_kelamin, pesan) VALUES ('$nama_lengkap', '$tempat_lahir', '$tanggal_lahir', '$nama_ibu_kandung', '$nik', '$jurusan', '$nomor_hp', '$agama', '$jenis_kelamin', '$pesan')";
    if(mysqli_query($koneksi, $query)){
        $nama_lengkap = '';
        $tempat_lahir = '';
        $tanggal_lahir = '';
        $nama_ibu_kandung = '';
        $nik = '';
        $jurusan = '';
        $nomor_hp = '';
        $agama = '';
        $jenis_kelamin = '';
        $pesan = '';
        header("Location: sukses.php");
        exit;
    } else {
        echo '<div class="alert alert-danger" role="alert">Gagal menyimpan data ke database.</div>';
        // Form direset menjadi kosong
        $nama_lengkap = '';
        $tempat_lahir = '';
        $tanggal_lahir = '';
        $nama_ibu_kandung = '';
        $nik = '';
        $jurusan = '';
        $nomor_hp = '';
        $agama = '';
        $jenis_kelamin = '';
        $pesan = '';
        exit;
    } 
} 

// Mendapatkan data jurusan dari tabel jurusan
$query_jurusan = "SELECT * FROM jurusan";
$result_jurusan = mysqli_query($koneksi, $query_jurusan);
$daftar_jurusan = array();
while($row = mysqli_fetch_assoc($result_jurusan)) {
    $daftar_jurusan[] = $row['nama_jurusan'];
}
mysqli_close($koneksi);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Personal - Start Bootstrap Theme</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Custom Google font-->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="../css/styles.css" rel="stylesheet" />
    <style>
        .resizeable {
            resize: both; /* Mengizinkan pengguna untuk meresize lebar dan tinggi input */
            overflow: auto; /* Menambahkan scroll jika konten input melebihi ukuran input */
        }
    </style>
</head>
<body class="d-flex flex-column h-100">
<main class="flex-shrink-0">
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-light bg-white py-3">
        <div class="container px-5">
            <a class="navbar-brand" href="./"><span class="fw-bolder text-primary">SALUT TANA TORAJA</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 small fw-bolder">
                    <li class="nav-item"><a class="nav-link" href="./">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="resume.html">Pengumuman</a></li>
                    <li class="nav-item active"><a class="nav-link" href="./pendaftaran">Daftar Mahasiswa Baru</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.html">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-6">
                <h2 class="text-center">Formulir Pendaftaran Mahasiswa Baru Universitas Terbuka Tana Toraja</h2>
                <form id="form-daftar" action="index.php" method="post" class="mt-5">
                    <!-- Lebar form diperbesar menjadi 80% -->
                    <table style="width: 100%;" class="navbar navbar-expand-lg navbar-light bg-white py-3">
                        <!-- Lebar tabel diperbesar menjadi 100% -->
                        <tr>
                            <th><label for="nama_lengkap">Nama Lengkap:</label></th>
                            <td><input type="text" name="nama_lengkap" class="form-control" value="<?php echo htmlspecialchars($nama_lengkap); ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="tempat_lahir">Tempat Lahir:</label></th>
                            <td><input type="text" name="tempat_lahir" class="form-control" value="<?php echo $tempat_lahir; ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="tanggal_lahir">Tanggal Lahir:</label></th>
                            <td><input type="date" name="tanggal_lahir" class="form-control" value="<?php echo $tanggal_lahir; ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="nama_ibu_kandung">Nama Ibu Kandung:</label></th>
                            <td><input type="text" name="nama_ibu_kandung" class="form-control" value="<?php echo $nama_ibu_kandung; ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="nik">NIK:</label></th>
                            <td><input type="text" name="nik" class="form-control" value="<?php echo $nik; ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="jurusan">Jurusan:</label></th>
                            <td>
                                <select name="jurusan" class="form-select" required>
                                    <option value="">Pilih Jurusan</option>
                                    <?php foreach ($daftar_jurusan as $jurusan_item) : ?>
                                    <option value="<?php echo $jurusan_item; ?>" <?php if($jurusan == $jurusan_item) echo 'selected'; ?>><?php echo $jurusan_item; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="nomor_hp">Nomor HP:</label></th>
                            <td><input type="text" name="nomor_hp" class="form-control" value="<?php echo $nomor_hp; ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="agama">Agama:</label></th>
                            <td>
                                <select name="agama" class="form-select" required>
                                    <option value="">Pilih Agama</option>
                                    <option value="Islam" <?php if($agama == 'Islam') echo 'selected'; ?>>Islam</option>
                                    <option value="Kristen" <?php if($agama == 'Kristen') echo 'selected'; ?>>Kristen</option>
                                    <option value="Katolik" <?php if($agama == 'Katolik') echo 'selected'; ?>>Katolik</option>
                                    <option value="Hindu" <?php if($agama == 'Hindu') echo 'selected'; ?>>Hindu</option>
                                    <option value="Buddha" <?php if($agama == 'Buddha') echo 'selected'; ?>>Buddha</option>
                                    <option value="Konghucu" <?php if($agama == 'Konghucu') echo 'selected'; ?>>Konghucu</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="jenis_kelamin">Jenis Kelamin:</label></th>
                            <td>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki" <?php if($jenis_kelamin == 'Laki-laki') echo 'selected'; ?>>Laki-laki</option>
                                    <option value="Perempuan" <?php if($jenis_kelamin == 'Perempuan') echo 'selected'; ?>>Perempuan</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="pesan">Pesan:</label></th>
                            <td>
                                <textarea type="text" name="pesan" class="form-control resizeable" required maxlength="255"><?php echo $pesan; ?></textarea>
                            </td>
                        </tr>
                    </table>
                    <button type="submit" name="submit" class="btn btn-primary mt-3">Kirim</button>
                </form>
            </div>
            <!-- Kolom untuk gambar -->
            <div class="col-md-6">
                <img src="gambar.jpg" class="img-fluid" alt="Gambar">
            </div>
        </div>
    </div>
</main>
<footer class="bg-white py-4 mt-auto">
    <div class="container px-5">
        <div class="row align-items-center justify-content-between flex-column flex-sm-row">
            <div class="col-auto"><div class="small m-0">Copyright &copy; SALUT TATOR 2023</div></div>
            <div class="col-auto">
                <a class="small" href="#!">Privacy</a>
                <span class="mx-1">&middot;</span>
                <a class="small" href="#!">Terms</a>
                <span class="mx-1">&middot;</span>
                <a class="small" href="#!">Contact</a>
            </div>
        </div>
    </div>
</footer>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Core theme JS-->
<script src="js/scripts.js"></script>
</body>
</html>
