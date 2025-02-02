<?php
session_start();
require_once('../koneksi.php');
require_once('functions.php');

if (!isLoggedIn()) {
    header('Location: ../login.php');
} else {
    include 'dashboard.php';
}
?>
