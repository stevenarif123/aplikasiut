<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db = "datamahasiswa";

$koneksi = mysqli_connect($host, $user, $pass, $db);

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
    // User ditemukan, buat session dan arahkan ke halaman dashboard
    session_start();
    $_SESSION['username'] = $username;
    header("Location: dashboard.php");
  } else {
    // User tidak ditemukan, tampilkan pesan error
    $error = "Username atau password salah!";
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