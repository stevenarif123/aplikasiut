<?php
// // Start session if it hasn't already started
// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }

// // Redirect to dashboard.php if session already exists
// if (isset($_SESSION['username'])) {
//     header("Location: dashboard.php");
//     exit; // Stop further execution
// }

// Sanitize and validate input
$error = "";
// if (isset($_GET['error']) && $_GET['error'] == 1) {
//     $error = "Akun Anda tidak memiliki izin untuk mengakses halaman tersebut.";
// }

// Establish database connection
require_once "koneksi.php";

// Handle form submission
if (isset($_POST['submit'])) {
    // Sanitize and validate input
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $peran = mysqli_real_escape_string($koneksi, $_POST['peran']);

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into database
    $query = "INSERT INTO admin (username, password, email, nama_lengkap, peran) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "sssss", $username, $hashed_password, $email, $nama_lengkap, $peran);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        // Redirect to login page after successful registration
        header("Location: login.php");
        exit; // Stop further execution
    } else {
        // Display error message if registration fails
        $error = "Registrasi gagal. Silakan coba lagi.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Admin Baru</title>
    <!-- Bootstrap core CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>
</head>
<body class="text-center">
    <main class="form-signin" style="max-width: 800px; padding: 15px; margin: auto;">
        <form action="registrasiadmin.php" method="post" autocomplete="off">
            <img class="mb-4" src="./assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">
            <h1 class="h3 mb-3 fw-normal">Registrasi Admin Baru</h1>
            <div class="form-floating">
                <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
                <label for="username">Username</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                <label for="password">Password</label>
            </div>
            <div class="form-floating">
                <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
                <label for="email">Email</label>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" placeholder="Nama Lengkap" required>
                <label for="nama_lengkap">Nama Lengkap</label>
            </div>
            <div class="form-floating">
                <select class="form-select" name="peran" id="peran" required>
                    <option value="" selected disabled>Pilih Peran</option>
                    <option value="Admin">Admin</option>
                    <option value="Super Admin">Super Admin</option>
                </select>
                <label for="peran">Peran</label>
            </div>
            <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Daftar</button>
            <?php
            if ($error != "") {
                echo "<p style='color: red;'>$error</p>";
            }
            ?>
            <p class="mt-5 mb-3 text-muted">&copy; 2024</p>
        </form>
    </main>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
