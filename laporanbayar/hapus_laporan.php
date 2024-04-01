<?php
require_once "koneksi.php";

// Check if laporan ID is provided via GET request
if(isset($_GET['id'])) {
    // Sanitize the laporan ID to prevent SQL injection
    $laporanId = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // Prepare and execute query to delete laporan from database
    $sql = "DELETE FROM laporanuangmasuk WHERE id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $laporanId);
    $stmt->execute();
    
    // Check if deletion was successful
    if($stmt->affected_rows > 0) {
        // Redirect to laporan list page after successful deletion
        header("Location: index.php");
        exit();
    } else {
        // Redirect to laporan list page if deletion failed
        header("Location: index.php");
        exit();
    }
} else {
    // Redirect to laporan list page if laporan ID is not provided
    header("Location: index.php");
    exit();
}
?>