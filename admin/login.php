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

// Establish database connection
require_once "koneksi.php";

// Handle form submission
if (isset($_POST['submit'])) {
    // Sanitize and validate input
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Username atau password tidak boleh kosong.";
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
            if ($password == $row['password']) { // Perubahan pada baris ini
                $_SESSION['username'] = $row['username']; }
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit; // Stop further execution
            } else {
                // Password tidak valid
                $error = "Password salah.";
            }
        }
    }
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
    <main class="form-signin" style="max-width: 330px; padding: 15px; margin: auto;">
        <form action="login.php" method="post" autocomplete="off">
            <img class="mb-5" src="../assets/salut.png" alt="">
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
            if (isset($error) && !empty($error)) {
                echo "<p style='color: red;'>$error</p>";
            }
            ?>
            <p class="mt-5 mb-3 text-muted">&copy; 2024</p>
        </form>
    </main>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>