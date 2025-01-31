<?php
require_once "../../koneksi.php";

// Definisi API Key
$VALID_API_KEY = 'pantanmandiri25';

// Fungsi validasi API Key
function validateApiKey($providedKey) {
    global $VALID_API_KEY;
    return hash_equals($VALID_API_KEY, $providedKey);
}

// Fungsi untuk mendapatkan API Key dari request
function getApiKey() {
    // Coba dari header
    $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
    
    // Jika tidak ada di header, coba dari query parameter
    if (!$apiKey) {
        $apiKey = $_GET['api_key'] ?? null;
    }
    
    // Jika tidak ada di query parameter, coba dari request body
    if (!$apiKey && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $apiKey = $input['api_key'] ?? null;
    }
    
    return $apiKey;
}

// Mendapatkan API Key
$apiKey = getApiKey();

// Validasi API Key
if (!$apiKey) {
    sendResponse(['error' => 'Unauthorized', 'message' => 'API Key diperlukan'], 401);
}

if (!validateApiKey($apiKey)) {
    sendResponse(['error' => 'Unauthorized', 'message' => 'API Key tidak valid'], 401);
}

// Mendapatkan ID dari POST
$id = intval($_POST['id']);

// Validasi ID
if ($id <= 0) {
    sendResponse(['error' => 'Invalid ID', 'message' => 'ID harus lebih besar dari 0'], 400);
}

// Query untuk menghapus data
$sql = "DELETE FROM mabawebsite WHERE id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    sendResponse(['message' => 'Data berhasil dihapus', 'id' => $id], 200);
} else {
    sendResponse(['error' => 'Query execution failed: ' . $stmt->error], 500);
}

// Tutup koneksi
$stmt->close();
$koneksi->close();

// Fungsi untuk mengirim respons JSON
function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}
?>