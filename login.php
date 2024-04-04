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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Halaman Login</title>
</head>
<body>
  <h1>Halaman Login</h1>

  <form action="login.php" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" value="<?php echo $username; ?>">

    <label for="password">Password:</label>
    <input type="password" name="password" id="password">

    <input type="submit" name="submit" value="Login">

    <?php
    if ($error != "") {
      echo "<p style='color: red;'>$error</p>";
    }
    ?>
  </form>
</body>
</html>