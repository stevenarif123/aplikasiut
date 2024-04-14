<?php
// Memulai sesi
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Redirect to index.php if session already exists
if (isset($_SESSION['username'])) {
  header("Location: dashboard.php");
  exit; // Stop further execution
}

if (isset($_GET['error']) && $_GET['error'] == 1) {
  echo "<p style='color: red;'>Akun Anda tidak memiliki izin untuk mengakses halaman tersebut.</p>";
}
// Koneksikan ke database
require_once "koneksi.php";
// Inisialisasi variabel

$username = "";
$password = "";
$error = "";

// Cek apakah form login disubmit
if (isset($_POST['submit'])) {
  // Ambil username dan password dari form
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Query untuk mencari user dengan username dan password yang diinputkan
  $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
  $result = mysqli_query($koneksi, $query);

  // Cek apakah user ditemukan
    if (mysqli_num_rows($result) > 0) {
      // Fetch user data from the result
      $row = mysqli_fetch_assoc($result);
      // Start session and set session variables
      session_start();
      $_SESSION['username'] = $row['username']; // Set username
      $_SESSION['peran'] = $row['peran']; // Set user's role
      header("Location: dashboard.php"); // Redirect to dashboard
      exit; // Stop further execution
  } else {
      // User not found, display error message
      $error = "Username or password is incorrect!";
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
        <form action="login.php" method="post">
            <img class="mb-4" src="./assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">
            <h1 class="h3 mb-3 fw-normal">Silakan Masuk</h1>
            <div class="form-floating">
                <input type="text" class="form-control" name="username" id="username" value="<?php echo $username; ?>" placeholder="Username">
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
    </main>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
