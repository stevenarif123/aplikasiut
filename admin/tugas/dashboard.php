<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['judul_tugas'])) {
    $judul_tugas = $_POST['judul_tugas'];
    $deskripsi = $_POST['deskripsi'];
    $deadline = $_POST['deadline'];
    $admin_id = $_POST['admin_id'];
    $prioritas = $_POST['prioritas'];
    $status = $_POST['status'];
    $tag_tugas = $_POST['tag_tugas'];

    $sql = "INSERT INTO tugas (judul_tugas, deskripsi, deadline, admin_id, prioritas, status, tag_tugas) 
            VALUES ('$judul_tugas', '$deskripsi', '$deadline', '$admin_id', '$prioritas', '$status', '$tag_tugas')";
    
    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Tugas baru berhasil disimpan.'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan tugas: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-3">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="#">Aplikasi Tugas</a>
            <button class="btn btn-primary" onclick="location.href='index.php?tambah=1'">Tambah Tugas</button>
            <div class="ml-auto">
                <span>Selamat datang, <?php echo $_SESSION['username']; ?></span>
            </div>
        </nav>

        <?php if (isset($_GET['tambah']) && $_GET['tambah'] == 1) { ?>
            <?php include 'form_tambah.php'; ?>
        <?php } else { ?>
            <?php include 'daftar_tugas.php'; ?>
        <?php } ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>