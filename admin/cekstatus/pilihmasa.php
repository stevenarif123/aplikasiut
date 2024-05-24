<?php
// Ambil data mahasiswa dari parameter GET
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['No']) && !empty($_GET['No'])) {
    } else {
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Masa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">SALUT TANA TORAJA</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="../dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="../mahasiswa.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Mahasiswa
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../mahasiswa/mahasiswa.php">Daftar Mahasiswa</a></li>
            <li><a class="dropdown-item" href="../mahasiswa/tambah_data.php">Tambah Mahasiswa</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="./" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Laporan Pembayaran
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="./">Laporan Bayar</a></li>
            <li><a class="dropdown-item" href="./tambah_laporan.php">Tambah Laporan</a></li>
            <li><a class="dropdown-item" href="">Verifikasi Laporan</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Mahasiswa Baru
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../maba/dashboard.php">Daftar Mahasiswa</a></li>
            <li><a class="dropdown-item" href="../maba/tambah_data.php">Tambah Mahasiswa</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../cekstatus/pencarian.php">Cek Status Mahasiswa</a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn btn-warning text-dark fw-bold" href="../logout.php">Keluar</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
    <div class="container mt-3">
        <h2 class="mb-4">Pilih Masa</h2>
        <form action="prosesceknilai.php" method="POST">
            <div class="mb-3">
                <label for="masa" class="form-label">Masa:</label>
                <select class="form-select" name="masa" id="masa">
                    <?php
                    $selectedMasa = isset($_POST['masa']) ? $_POST['masa'] : '20201';
                    $masaList = ['20201', '20202', '20211', '20212', '20221', '20222', '20231', '20232', '20241', '20242'];

                    foreach ($masaList as $masa) {
                        $selected = ($masa == $selectedMasa) ? 'selected' : '';
                        echo "<option value=\"$masa\" $selected>$masa</option>";
                    }
                    ?>
                </select>
            </div>
            <?php if (isset($_GET['No']) && !empty($_GET['No'])) : ?>
                <input type="hidden" name="No" value="<?php echo htmlspecialchars($_GET['No']); ?>">
            <?php endif; ?>
            <button type="submit" class="btn btn-primary" name="submit">Submit</button>
        </form>
    </div>
</body>
</html>
