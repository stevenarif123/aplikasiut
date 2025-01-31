<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_admin = $_POST['id_admin'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $nama_lengkap = $_POST['nama_lengkap'];

    $query = "UPDATE admin SET username='$username', email='$email', nama_lengkap='$nama_lengkap' WHERE id_admin=$id_admin";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $_SESSION['username'] = $username; // Update session variable if username is changed
        header("Location: my-account.php");
        exit();
    } else {
        echo "Error updating account: " . mysqli_error($koneksi);
    }
}
?>