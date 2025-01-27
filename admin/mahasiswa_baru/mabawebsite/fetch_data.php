<?php  
require_once "../../koneksi.php";  
  
// API endpoint  
$apiUrl = "https://uttoraja.com/pendaftaran/api";  
  
// Fetch data from the API  
$response = file_get_contents($apiUrl);  
  
if ($response === FALSE) {  
    echo json_encode(['error' => 'Failed to fetch data from API'], 500);  
    exit;  
}  
  
// Decode JSON response  
$data = json_decode($response, true);  
  
if ($data === NULL) {  
    echo json_encode(['error' => 'Invalid JSON response from API'], 500);  
    exit;  
}  
  
// Prepare an array to store the final result  
$result = [];  
  
// Check if each 'nik' exists in the 'mahasiswabaru20242' table  
foreach ($data as $row) {  
    if (isset($row['nik'])) {  
        $nik = $row['nik'];  
        $sql_check = "SELECT COUNT(*) AS count FROM mahasiswabaru20242 WHERE nik = ?";  
        $stmt = $koneksi->prepare($sql_check);  
          
        if ($stmt === false) {  
            echo json_encode(['error' => 'Query preparation failed'], 500);  
            exit;  
        }  
          
        $stmt->bind_param("s", $nik);  
        $stmt->execute();  
        $stmt->bind_result($count);  
        $stmt->fetch();  
        $stmt->close();  
          
        $row['processed'] = $count > 0;  
        $result[] = $row;  
    } else {  
        // Handle cases where 'nik' is not present  
        $row['processed'] = false;  
        $result[] = $row;  
    }  
}  
  
// Return the modified data as JSON  
echo json_encode($result);  
  
// Close the database connection  
$koneksi->close();  
?>  
