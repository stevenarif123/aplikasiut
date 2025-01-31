<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_admin = $_POST['id_admin'];
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // Verify old password
    $query = "SELECT password FROM admin WHERE id_admin = $id_admin";
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($result);

    if ($data['password'] != $password_lama) {
        echo "Old password incorrect.";
        exit();
    }

    // Check if new password matches confirmation
    if ($password_baru != $konfirmasi_password) {
        echo "New password and confirmation do not match.";
        exit();
    }

    // Update password
    $query = "UPDATE admin SET password = '$password_baru' WHERE id_admin = $id_admin";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        header("Location: settings.php");
        exit();
    } else {
        echo "Error updating password: " . mysqli_error($koneksi);
    }
}
?>