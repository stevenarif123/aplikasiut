<?php
header('Content-Type: application/json');

// Start session if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Establish database connection
require_once "koneksi.php";

// Handle form submission via AJAX
$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $response['status'] = 'error';
        $response['message'] = 'Username atau password tidak boleh kosong.';
    } else {
        // Query the database to find the user
        $query = "SELECT * FROM admin WHERE username = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Check if a user is found
        if (mysqli_num_rows($result) > 0) {
            // Fetch user data from the result
            $row = mysqli_fetch_assoc($result);

            // Verify password
            if ($password == $row['password']) {
                // Set session variables
                $_SESSION['id_admin'] = $row['id_admin'];
                $_SESSION['profilepicture'] = $row['profilepicture'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['peran'] = $row['peran']; // Save the role in session

                // Return success response
                $response['status'] = 'success';
                $response['message'] = 'Login berhasil.';
            } else {
                // Password is incorrect
                $response['status'] = 'error';
                $response['message'] = 'Password salah.';
            }
        } else {
            // Username not found
            $response['status'] = 'error';
            $response['message'] = 'Username tidak ditemukan.';
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

// Send the JSON response
echo json_encode($response);
?>
