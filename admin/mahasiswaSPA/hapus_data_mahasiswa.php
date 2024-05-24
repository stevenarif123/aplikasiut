<?php
// Turn off error reporting to prevent HTML output
error_reporting(0);
header('Content-Type: application/json'); // Ensure the response is JSON

// Include database connection
require_once "../koneksi.php";

// Check if user is logged in
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Get the ID from the URL
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $_DELETE['No'];

    // Escape string to prevent SQL Injection
    $id = mysqli_real_escape_string($koneksi, $id);

    // Debugging information
    if (empty($id)) {
        echo json_encode(['success' => false, 'error' => 'ID is empty']);
        exit;
    }

    // Query to delete the student record
    $query = "DELETE FROM mahasiswa WHERE No='$id'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($koneksi)]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
