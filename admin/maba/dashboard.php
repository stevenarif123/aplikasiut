<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['username'])) {
  header("Location: ../login.php");
}

// Koneksi ke database
require_once "../koneksi.php";

if (!$koneksi) {
  die("Koneksi gagal: " . mysqli_connect_error());
}

// Query untuk mendapatkan data user
$username = $_SESSION['username'];
$query = "SELECT * FROM admin WHERE username='$username'";

$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);
if (!$result) {
  die("Query gagal: " . mysqli_error($koneksi));
}

// Inisialisasi variabel
$keyword = "";
$search_column = "";
$mahasiswa = [];

// Cek apakah form pencarian disubmit
if (isset($_POST['search'])) {
  // Ambil keyword dari form
  $keyword = $_POST['keyword'];
  $search_column = $_POST['search_column'];
}

// Query untuk mencari data mahasiswa berdasarkan keyword
$query = "SELECT * FROM mahasiswabaru WHERE ";
if (!empty($keyword) && !empty($search_column)) {
  $query .= "$search_column LIKE '%$keyword%' ";
} else {
  $query .= "1 "; // Jika tidak ada keyword, tampilkan semua data
}
$query .= "ORDER BY No DESC";

$result = mysqli_query($koneksi, $query);
if (!$result) {
  die("Query gagal: " . mysqli_error($koneksi));
}

// Simpan hasil pencarian ke dalam array
while ($row = mysqli_fetch_assoc($result)) {
  $mahasiswa[] = $row;
}

// Pagination
$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * (intval($limit) == 'all' ? count($mahasiswa) : intval($limit));
$total_pages = ceil(count($mahasiswa) / (intval($limit) == 'all' ? 1 : intval($limit)));
$mahasiswa = array_slice($mahasiswa, $start, intval($limit) == 'all' ? count($mahasiswa) : intval($limit));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mahasiswa Baru</title>
     <!-- App favicon -->
    <link rel="shortcut icon" href="https://coderthemes.com/ubold/layouts/assets/images/favicon.ico">

		<!-- App css -->
		<link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />
		<link href="../assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-default-stylesheet" />

		<link href="../assets/css/bootstrap-dark.min.css" rel="stylesheet" type="text/css" id="bs-dark-stylesheet" />
		<link href="../assets/css/app-dark.min.css" rel="stylesheet" type="text/css" id="app-dark-stylesheet" />

		<!-- icons -->
		<link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
    }
    .container {
      margin-top: 50px;
    }
    h1 {
      font-size: 2.5rem;
      color: #007bff;
    }
    @media (max-width: 768px) {
      .table-responsive {
        overflow-x: auto;
      }
    }
    #toast {
      transition: opacity 0.3s ease-in-out;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom border-secondary">
  <!-- Kode navigasi disini -->
</nav>

<div class="container mt-4">
  <h1 class="mb-4">Pengelolaan Calon Mahasiswa Baru</h1>
  <h2 class="mb-4">Mahasiswa Baru dari Website</h2>
  <div class="d-flex mb-4">
    <a href="./mabawebsite/" class="btn btn-primary me-2">Proses</a>
    <a href="./tambah_data.php" class="btn btn-primary me-2">Tambah Data</a>
    <a href="./push.php" class="btn btn-primary">Input Admisi</a>
  </div>

  <form action="dashboard.php" method="post" class="mb-4">
    <div class="row mb-3">
      <div class="col">
        <label for="search_column" class="form-label fw-bold">Cari Berdasarkan:</label>
        <select id="search_column" name="search_column" class="form-select">
          <option value="NamaLengkap">Nama Lengkap</option>
          <option value="Jurusan">Jurusan</option>
          <option value="STATUS_INPUT_SIA">Status Input SIA</option>
        </select>
      </div>
      <div class="col">
        <label for="keyword" class="form-label fw-bold">Keyword:</label>
        <input type="text" id="keyword" name="keyword" placeholder="Masukkan Keyword" class="form-control">
      </div>
      <div class="col align-self-end">
        <button type="submit" name="search" class="btn btn-primary w-100">Cari</button>
      </div>
    </div>

    <div class="row">
      <div class="col-2">
        <label for="limit" class="form-label fw-bold">Tampilkan:</label>
        <select id="limit" name="limit" class="form-select">
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="all">Semua</option>
        </select>
      </div>
    </div>
  </form>

  <div id="toast" class="position-fixed top-0 end-0 bg-success text-white p-3 rounded shadow-lg transition-opacity opacity-0">
    Teks berhasil disalin!
  </div>

  <?php if (isset($mahasiswa) && count($mahasiswa) > 0) { ?>
    <div class="accordion" id="mahasiswaAccordion">
      <?php $no = 1 + $start; foreach ($mahasiswa as $mhs) { ?>
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading-<?php echo $mhs['No']; ?>">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $mhs['No']; ?>" aria-expanded="true" aria-controls="collapse-<?php echo $mhs['No']; ?>">
              <?php echo $no++; ?>. <?php echo htmlspecialchars($mhs['NamaLengkap']); ?> - <?php echo htmlspecialchars($mhs['Jurusan']); ?>
            </button>
          </h2>
          <div id="collapse-<?php echo $mhs['No']; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo $mhs['No']; ?>" data-bs-parent="#mahasiswaAccordion">
            <div class="accordion-body">
              <div class="row">
                <div class="col-md-6">
                  <p>
                    <strong>Nama Lengkap:</strong>
                    <span><?php echo htmlspecialchars($mhs['NamaLengkap']); ?></span>
                    <button class="btn btn-sm btn-link text-primary p-0" onclick="copyToClipboard('<?php echo htmlspecialchars($mhs['NamaLengkap'], ENT_QUOTES); ?>')" title="Salin nama lengkap">
                      <i class="fas fa-copy"></i>
                    </button>
                  </p>
                  <p>
                    <strong>Nomor HP:</strong>
                    <span><?php echo htmlspecialchars($mhs['NomorHP']); ?></span>
                    <button class="btn btn-sm btn-link text-primary p-0" onclick="copyToClipboard('<?php echo htmlspecialchars($mhs['NomorHP'], ENT_QUOTES); ?>')" title="Salin nomor HP">
                      <i class="fas fa-copy"></i>
                    </button>
                  </p>
                  <p>
                    <strong>Email:</strong>
                    <span><?php echo htmlspecialchars($mhs['Email']); ?></span>
                    <button class="btn btn-sm btn-link text-primary p-0" onclick="copyToClipboard('<?php echo htmlspecialchars($mhs['Email'], ENT_QUOTES); ?>')" title="Salin email">
                      <i class="fas fa-copy"></i>
                    </button>
                  </p>
                </div>
                <div class="col-md-6">
                  <p>
                    <strong>Password:</strong>
                    <span><?php echo htmlspecialchars($mhs['Password']); ?></span>
                    <button class="btn btn-sm btn-link text-primary p-0" onclick="copyToClipboard('<?php echo htmlspecialchars($mhs['Password'], ENT_QUOTES); ?>')" title="Salin password">
                      <i class="fas fa-copy"></i>
                    </button>
                  </p>
                  <p>
                    <strong>Status Input SIA:</strong>
                    <span><?php echo htmlspecialchars($mhs['STATUS_INPUT_SIA']); ?></span>
                    <button class="btn btn-sm btn-link text-primary p-0" onclick="copyToClipboard('<?php echo htmlspecialchars($mhs['STATUS_INPUT_SIA'], ENT_QUOTES); ?>')" title="Salin status input SIA">
                      <i class="fas fa-copy"></i>
                    </button>
                  </p>
                </div>
              </div>
              <div class="mt-2">
                <a href="lihat_data_mahasiswa.php?No=<?php echo $mhs['No']; ?>" class="btn btn-primary me-2">Detail Data</a>
                <a href="edit_data.php?No=<?php echo $mhs['No']; ?>" class="btn btn-warning me-2">Edit</a>
                <button type="button" class="btn btn-danger" onclick="confirmDelete(<?php echo $mhs['No']; ?>)">Hapus</button>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
      <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
        <a href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&search_column=<?php echo $search_column; ?>&keyword=<?php echo $keyword; ?>" class="btn <?php echo $i == $page ? 'btn-primary' : 'btn-secondary'; ?> me-2"><?php echo $i; ?></a>
      <?php } ?>
    </div>

  <?php } else { ?>
    <p class="text-center py-4">Data mahasiswa tidak ditemukan.</p>
  <?php } ?>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Efek klik untuk tombol salin
  const copyButtons = document.querySelectorAll('.btn-link');
  copyButtons.forEach(button => {
    button.addEventListener('click', function() {
      this.classList.add('text-success');
      setTimeout(() => {
        this.classList.remove('text-success');
      }, 300);
    });
  });
});

function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    showToast('Teks berhasil disalin: ' + text);
  }, (err) => {
    showToast('Gagal menyalin teks', true);
    console.error('Gagal menyalin teks: ', err);
  });
}

function showToast(message, isError = false) {
  const toast = document.getElementById('toast');
  toast.textContent = message;
  toast.classList.remove('opacity-0', 'bg-success', 'bg-danger');
  toast.classList.add(isError ? 'bg-danger' : 'bg-success', 'opacity-100');

  setTimeout(() => {
    toast.classList.remove('opacity-100');
    toast.classList.add('opacity-0');
  }, 3000);
}

function confirmDelete(id) {
  if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
    window.location.href = 'hapus_data_mahasiswa.php?No=' + id;
  }
}
</script>

</body>
</html>