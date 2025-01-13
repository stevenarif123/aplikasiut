<?php
require_once "../../koneksi.php";

$id = intval($_POST['id']);
$sql = "DELETE FROM mabawebsite WHERE id = $id";
$koneksi->query($sql);

$koneksi->close();
?>
