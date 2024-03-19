<?php

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Database connection details
$host = "localhost";
$user = "root";
$pass = "";
$db = "datamahasiswa";

// Connect to database
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Check for connection error
if (!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve student ID from URL
$no = $_GET['No'];

// Prepare and execute query to fetch student data
$query = "SELECT * FROM mahasiswa WHERE No = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $no);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check for query error
if (!$result) {
    die("Error retrieving data: " . mysqli_error($koneksi));
}

$username = $_SESSION['username'];
$query = "SELECT * FROM admin WHERE username='$username'";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);
if (!$result) {
  die("Query gagal: " . mysqli_error($koneksi));
}

$mahasiswa = mysqli_fetch_assoc($result);

// Check if form is submitted
if (isset($_POST['submit'])) {

    // Sanitize and validate input data
    $nim = filter_input(INPUT_POST, 'Nim', FILTER_SANITIZE_STRING);
    $jalur_program = filter_input(INPUT_POST, 'JalurProgram', FILTER_SANITIZE_STRING);
    // ... (sanitize and validate other fields)

    // Hash password securely
    $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);

    // Prepare UPDATE query with placeholders
    $updateQuery = "UPDATE mahasiswa SET 
        Nim = ?, 
        JalurProgram = ?, 
        NamaLengkap = ?, 
        Password = ? 
        WHERE No = ?";

    // Prepare statement
    $stmt = mysqli_prepare($koneksi, $updateQuery);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "sssssssssssssssssi", 
        $nim, 
        $jalur_program, 
        // ... (other fields)
        $password, 
        $no
    );

    // Execute update
    if (mysqli_stmt_execute($stmt)) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error updating data: " . mysqli_error($koneksi);
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Close database connection
mysqli_close($koneksi);

$jurusan = [
  "Pembangunan" => "Pembangunan",
  "Ekonomi Syariah" => "Ekonomi Syariah",
  "Akuntansi" => "Akuntansi",
  "Akuntansi Keuangan Publik" => "Akuntansi Keuangan Publik",
  "Pariwisata" => "Pariwisata",
  "Pendidikan Bahasa Dan Sastra Indonesia" => "Pendidikan Bahasa Dan Sastra Indonesia",
  "Pendidikan Bahasa Inggris" => "Pendidikan Bahasa Inggris",
  "Pendidikan Biologi" => "Pendidikan Biologi",
  "Pendidikan Fisika" => "Pendidikan Fisika",
  "Pendidikan Kimia" => "Pendidikan Kimia",
  "Pendidikan Matematika" => "Pendidikan Matematika",
  "Pendidikan Ekonomi" => "Pendidikan Ekonomi",
  "Pendidikan Pancasila Dan Kewarganegaraan" => "Pendidikan Pancasila Dan Kewarganegaraan",
  "Teknologi Pendidikan" => "Teknologi Pendidikan",
  "PGSD" => "PGSD",
  "PGPAUD" => "PGPAUD",
  "PPG" => "PPG",
  "Statistika" => "Statistika",
  "Matematika" => "Matematika",
  "Biologi" => "Biologi",
  "Teknologi Pangan" => "Teknologi Pangan",
  "Agribisnis" => "Agribisnis",
  "Perencanaan Wilayah Dan Kota" => "Perencanaan Wilayah Dan Kota",
  "Sistem Informasi" => "Sistem Informasi",
  "Kearsipan (D4)" => "Kearsipan (D4)",
  "Perpajakan (D3)" => "Perpajakan (D3)",
  "Perpustakaan" => "Perpustakaan",
  "Administrasi Publik" => "Administrasi Publik"
];

$selectedJurusan = $mahasiswa['Jurusan'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Mahasiswa</title>
</head>
<body>
  <h1>Edit Data Mahasiswa</h1>

<!-- HTML form remains similar, but password field should be type="password" -->
<form action="edit_data_mahasiswa.php?No=<?php echo $no; ?>" method="post">
    <label for="jalur_program">Jalur Program:</label>
    <select name="JalurProgram" id="jalur_program">
      <option value="RPL" <?php if ($mahasiswa['JalurProgram'] == "RPL") echo "selected"; ?>>RPL</option>
      <option value="Reguler" <?php if ($mahasiswa['JalurProgram'] == "Reguler") echo "selected"; ?>>Reguler</option>
    </select>
    <br>

    <label for="nama_lengkap">Nama Lengkap:</label>
    <input type="text" name="NamaLengkap" id="nama_lengkap" value="<?php echo $mahasiswa['NamaLengkap']; ?>">
    <br>

    <label for="tempat_lahir">Tempat Lahir:</label>
    <input type="text" name="TempatLahir" id="tempat_lahir" value="<?php echo $mahasiswa['TempatLahir']; ?>">
    <br>

    <label for="tanggal_lahir">Tanggal Lahir:</label>
    <input type="date" name="TanggalLahir" id="tanggal_lahir" value="<?php echo $mahasiswa['TanggalLahir']; ?>">
    <br>

    <label for="nama_ibu_kandung">Nama Ibu Kandung:</label>
    <input type="text" name="NamaIbuKandung" id="nama_ibu_kandung" value="<?php echo $mahasiswa['NamaIbuKandung']; ?>">
    <br>

    <label for="nik">NIK:</label>
    <input type="text" name="NIK" id="nik" value="<?php echo $mahasiswa['NIK']; ?>">
    <br>

    <label for="jurusan">Jurusan:</label>
    <select name="Jurusan" id="jurusan">
      <?php foreach ($jurusan as $value => $label): ?>
          <option value="<?php echo $value; ?>" <?php if ($selectedJurusan == $value) echo "selected"; ?>>
              <?php echo $label; ?>
          </option>
      <?php endforeach; ?>
    </select>

<!-- Script JavaScript untuk fungsi pencarian -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  var select = document.getElementById("jurusan");
  var options = select.getElementsByTagName("option");
  
  // Fungsi untuk menampilkan semua opsi
  function showAllOptions() {
    for (var i = 0; i < options.length; i++) {
      options[i].style.display = "block";
    }
  }
  
  // Fungsi untuk menyembunyikan opsi pencarian
  function hideSearchOption() {
    options[options.length - 1].style.display = "none";
  }
  
  // Fungsi untuk menambahkan event listener pada dropdown
  select.addEventListener("change", function() {
    var selectedValue = this.value;
    if (selectedValue === "Search") {
      showAllOptions();
      this.value = "";
      hideSearchOption();
    }
  });
  
  // Fungsi untuk melakukan pencarian
  select.addEventListener("input", function() {
    var input = this.value.toLowerCase();
    for (var i = 0; i < options.length - 1; i++) {
      var optionText = options[i].text.toLowerCase();
      if (optionText.indexOf(input) > -1) {
        options[i].style.display = "block";
      } else {
        options[i].style.display = "none";
      }
    }
  });
  
  // Sembunyikan opsi pencarian saat memuat halaman
  hideSearchOption();
});
</script>
    </select>

    <br>

    <label for="NomorHP">Nomor HP:</label>
    <input type="text" name="NomorHP" id="nomor_hp" value="<?php echo $mahasiswa['NomorHP']; ?>">
    <br>

    <label for="Email">Email:</label>
    <input type="email" name="Email" id="email" value="<?php echo $mahasiswa['Email']; ?>">
    <br>

    <label for="Password">Password:</label>
    <input type="password" name="Password" id="password" value="<?php echo $mahasiswa['Password']; ?>">
    <br>

    <label for="agama">Agama:</label>
    <select name="Agama" id="agama">
      <option value="Islam" <?php if ($mahasiswa['Agama'] == "Islam") echo "selected"; ?>>Islam</option>
      <option value="Kristen" <?php if ($mahasiswa['Agama'] == "Kristen") echo "selected"; ?>>Kristen</option>
      <option value="Katolik" <?php if ($mahasiswa['Agama'] == "Katolik") echo "selected"; ?>>Katolik</option>
      <option value="Hindu" <?php if ($mahasiswa['Agama'] == "Hindu") echo "selected"; ?>>Hindu</option>
      <option value="Buddha" <?php if ($mahasiswa['Agama'] == "Buddha") echo "selected"; ?>>Buddha</option>
      <option value="Konghuchu" <?php if ($mahasiswa['Agama'] == "Konghuchu") echo "selected"; ?>>Konghuchu</option>
      <!-- Tambahkan semua option agama sesuai dengan yang diminta -->
    </select>
    <br>

    <label for="JenisKelamin">Jenis Kelamin:</label>
    <select name="JenisKelamin" id="jenis_kelamin">
      <option value="Laki-laki" <?php if ($mahasiswa['JenisKelamin'] == "Laki-laki") echo "selected"; ?>>Laki-laki</option>
      <option value="Perempuan" <?php if ($mahasiswa['JenisKelamin'] == "Perempuan") echo "selected"; ?>>Perempuan</option>
      <!-- Tambahkan semua option jenis kelamin sesuai dengan yang diminta -->
    </select>
    <br>

    <label for="StatusPerkawinan">Status Perkawinan:</label>
    <select name="StatusPerkawinan" id="status_perkawinan">
      <option value="Kawin" <?php if ($mahasiswa['StatusPerkawinan'] == "Kawin") echo "selected"; ?>>Kawin</option>
      <option value="Belum Kawin" <?php if ($mahasiswa['StatusPerkawinan'] == "Belum Kawin") echo "selected"; ?>>Belum Kawin</option>
      <!-- Tambahkan semua option status perkawinan sesuai dengan yang diminta -->
    </select>
    <br>

    <label for="NomorHPAlternatif">Nomor HP Alternatif:</label>
    <input type="text" name="NomorHPAlternatif" id="nomor_hp_alternatif" value="<?php echo $mahasiswa['NomorHPAlternatif']; ?>">
    <br>

    <label for="NomorIjazah">Nomor Ijazah:</label>
    <input type="text" name="NomorIjazah" id="nomor_ijazah" value="<?php echo $mahasiswa['NomorIjazah']; ?>">
    <br>

    <label for="TahunIjazah">Tahun Ijazah:</label>
    <input type="text" name="TahunIjazah" id="tahun_ijazah" value="<?php echo $mahasiswa['TahunIjazah']; ?>">
    <br>

    <label for="NISN">NISN:</label>
    <input type="text" name="NISN" id="nisn" value="<?php echo $mahasiswa['NISN']; ?>">
    <br>

    <label for="LayananPaketSemester">Layanan Paket Semester:</label>
    <select name="LayananPaketSemester" id="layanan_paket_semester">
      <option value="SIPAS" <?php if ($mahasiswa['LayananPaketSemester'] == "SIPAS") echo "selected"; ?>>SIPAS</option>
      <option value="NON SIPAS" <?php if ($mahasiswa['LayananPaketSemester'] == "NON SIPAS") echo "selected"; ?>>NON SIPAS</option>
      <!-- Tambahkan semua option layanan paket semester sesuai dengan yang diminta -->
    </select>
    <br>

    <label for="DiInputOleh">Di Input Oleh:</label>
    <input type="text" name="DiInputOleh" id="di_input_oleh" value="<?php echo $mahasiswa['DiInputOleh']; ?>">
    <br>

    <label for="DiInputPada">Di Input Pada:</label>
    <input type="text" name="DiInputPada" id="di_input_pada" value="<?php echo $mahasiswa['DiInputPada']; ?>">
    <br>

    <label for="STATUS_INPUT_SIA">Status Input SIA:</label>
    <select name="STATUS_INPUT_SIA" id="status_input_sia">
      <option value="Belum Terdaftar" <?php if ($mahasiswa['STATUS_INPUT_SIA'] == "Belum Terdaftar") echo "selected"; ?>>Belum Terdaftar</option>
      <option value="Input admisi" <?php if ($mahasiswa['STATUS_INPUT_SIA'] == "Input admisi") echo "selected"; ?>>Input admisi</option>
      <option value="Pengajuan Admisi" <?php if ($mahasiswa['STATUS_INPUT_SIA'] == "Pengajuan Admisi") echo "selected"; ?>>Pengajuan Admisi</option>
      <option value="Berkas Kurang" <?php if ($mahasiswa['STATUS_INPUT_SIA'] == "Berkas Kurang") echo "selected"; ?>>Berkas Kurang</option>
      <option value="Admisi Diterima" <?php if ($mahasiswa['STATUS_INPUT_SIA'] == "Admisi Diterima") echo "selected"; ?>>Admisi Diterima</option>
      <!-- Tambahkan semua option status input SIA sesuai dengan yang diminta -->
    </select>
    <br>

    <input type="submit" name="submit" value="Simpan">
  </form>
</body>
</html>