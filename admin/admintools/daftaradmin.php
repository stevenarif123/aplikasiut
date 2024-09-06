<?php
// Start session if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to dashboard.php if session already exists
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit; // Stop further execution
}

// Sanitize and validate input
$error = "";
if (isset($_GET['error']) && $_GET['error'] == 1) {
    $error = "Akun Anda tidak memiliki izin untuk mengakses halaman tersebut.";
}

// Establish database connection
require_once "koneksi.php";

// Handle form submission
if (isset($_POST['submit'])) {
    // Sanitize and validate username input
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);

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
        if (password_verify($_POST['password'], $row['password'])) {
            // Start a new session and set session variables
            session_start();
            $_SESSION['username'] = $row['username'];
            //$_SESSION['peran'] = $row['peran'];
            header("Location: dashboard.php");
            exit; // Stop further execution
        } else {
            // Password salah, tampilkan pesan kesalahan
            $error = "Password salah!";
        }
    } else {
        // User tidak ditemukan, tampilkan pesan kesalahan
        $error = "Username tidak ditemukan!";
    }
}

// Query to retrieve list of admins
$query = "SELECT * FROM admin";
$result = mysqli_query($koneksi, $query);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Login</title>
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
        <form action="login.php" method="post" autocomplete="off">
            <img class="mb-4" src="./assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">
            <h1 class="h3 mb-3 fw-normal">Silakan Masuk</h1>
            <div class="form-floating">
                <input type="text" class="form-control" name="username" id="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" placeholder="Username">
                <label for="username">Username</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                <label for="password">Password</label>
            </div>
            <div class="checkbox mb-3">
                <label>
                    <input type="checkbox" value="remember-me"> Ingat Saya
                </label>
            </div>
            <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Masuk</button>
            <?php
            if ($error != "") {
                echo "<p style='color: red;'>$error</p>";
            }
            ?>
            <p class="mt-5 mb-3 text-muted">&copy; 2024</p>
        </form>

        <div class="mt-5">
            <h2>Daftar Admin</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Nama Lengkap</th>
                        <th>Tanggal Dibuat</th>
                        <th>Peran</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['id_admin']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['nama_lengkap']; ?></td>
                            <td><?php echo $row['tanggal_dibuat']; ?></td>
                            <td><?php echo $row['peran']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
