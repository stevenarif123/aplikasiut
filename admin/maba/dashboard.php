<?php
// Session status check
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['username'])) {
  header("Location: ../login.php");
  exit;
}

// Connect to the database
require_once "../koneksi.php";

// Check for connection error
if (!$koneksi) {
  die("Connection failed: " . mysqli_connect_error());
}

// Check get admin data
$username = $_SESSION['username'];
$query = "SELECT * FROM admin WHERE username='$username'";

$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);
if (!$result) {
  die("Query gagal: " . mysqli_error($koneksi));
}

// Inisialisasi variabel
$keyword = "";
$search_column = "NamaLengkap";  // Default search column
$mahasiswa = [];

// Cek apakah ada keyword dari request
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$search_column = isset($_GET['search_column']) ? $_GET['search_column'] : '';

// Query untuk mencari data mahasiswa berdasarkan keyword
$query = "SELECT * FROM mahasiswabaru WHERE ";
if (!empty($keyword) && !empty($search_column)) {
  $query .= "$search_column LIKE '%" . mysqli_real_escape_string($koneksi, $keyword) . "%' ";
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

if (isset($_GET['ajax'])) {
  include './api/results_partial.php';
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Mahasiswa Baru</title>
  <link rel="shortcut icon" href="https://coderthemes.com/ubold/layouts/assets/images/favicon.ico">
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="../assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-default-stylesheet" />
  <!-- <link href="../assets/css/bootstrap-dark.min.css" rel="stylesheet" type="text/css" id="bs-dark-stylesheet" /> -->
  <!-- <link href="../assets/css/app-dark.min.css" rel="stylesheet" type="text/css" id="app-dark-stylesheet" /> -->
  <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
      right: 1rem;
      top: 1rem;
      position: fixed;
      opacity: 0;
    }

    .editable-field {
      display: inline-block;
      margin-right: 10px;
    }

    .editable-field input,
    .editable-field select {
      width: 100px;
    }

    .edit-mode {
      display: none;
    }

    .save-button {
      margin-left: 10px;
      display: none;
    }

    .save-button.active {
      display: inline-block;
    }

    .edit-icon,
    .copy-icon {
      cursor: pointer;
      margin-left: 5px;
    }

    .edit-mode.active {
      display: inline-block;
      margin-left: 5px;
    }

    .accordion-body .edit-mode,
    .accordion-body .edit-mode input,
    .accordion-body .edit-mode select,
    .accordion-body .edit-mode span {
      display: none;
    }
  </style>
</head>

<body>
  <div id="toast" class="bg-success text-white p-3 rounded shadow-lg opacity-0" style="position: fixed; top: 1rem; right: 1rem;"></div>
  <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom border-secondary">
    <!-- Kode navigasi disini -->
  </nav>
  <div class="container mt-4">
    <h1 class="mb-4">Pengelolaan Calon Mahasiswa Baru</h1>
    <h2 class="mb-4">Mahasiswa Baru dari Website</h2>
    <div class="mb-4">
      <a href="./mabawebsite/" class="btn btn-primary me-2">Proses</a>
      <a href="./tambah_data.php" class="btn btn-primary me-2">Tambah Data</a>
      <a href="./push.php" class="btn btn-primary">Input Admisi</a>
    </div>

    <form id="searchForm" action="dashboard.php" method="get" class="mb-4">
      <div class="row mb-3">
        <div class="col">
          <label for="search_column" class="form-label fw-bold">Cari Berdasarkan:</label>
          <select id="search_column" name="search_column" class="form-control">
            <option value="NamaLengkap">Nama Lengkap</option>
            <option value="Jurusan">Jurusan</option>
            <option value="STATUS_INPUT_SIA">Status Input SIA</option>
          </select>
        </div>
        <div class="col">
          <label for="keyword" class="form-label fw-bold">Keyword:</label>
          <input type="text" id="keyword" name="keyword" placeholder="Masukkan Keyword" class="form-control" onkeyup="updateResults()">
        </div>
        <div class="col align-self-end">
          <button type="submit" name="search" class="btn btn-primary w-100 d-none">Cari</button>
        </div>
      </div>
      <div class="row">
        <div class="col-2">
          <label for="limit" class="form-label fw-bold">Tampilkan:</label>
          <select id="limit" name="limit" class="form-control">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="all">Semua</option>
          </select>
        </div>
      </div>
    </form>
    <!-- Edit modal content -->
    <!-- Edit modal content -->
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <div class="text-center mt-2 mb-4">
              <h5>Edit Data</h5>
            </div>

            <form id="editForm" class="px-3">
              <input type="hidden" id="edit-no">

              <div class="form-group">
                <label for="edit-namalengkap">Nama Lengkap</label>
                <input class="form-control" type="text" id="edit-namalengkap" required>
              </div>

              <div class="form-group">
                <label for="edit-nomorhp">Nomor HP</label>
                <input class="form-control" type="text" id="edit-nomorhp" required>
              </div>

              <div class="form-group">
                <label for="edit-email">Email</label>
                <input class="form-control" type="email" id="edit-email" required>
              </div>

              <div class="form-group">
                <label for="edit-password">Password</label>
                <input class="form-control" type="text" id="edit-password" required>
              </div>

              <div class="form-group">
                <label for="edit-statussia">Status Input SIA</label>
                <select class="form-control" id="edit-statussia" required>
                  <option value="Belum Input Admisi">Belum Input Admisi</option>
                  <option value="Pengajuan Admisi">Pengajuan Admisi</option>
                  <option value="Ditolak">Ditolak</option>
                </select>
              </div>

              <div class="form-group text-center">
                <button class="btn btn-primary" type="submit">Save Changes</button>
              </div>
            </form>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->



    <div id="results">
      <?php include './api/results_partial.php'; ?>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      updateCopyButtons();
      updateAccordion();
    });

    function updateCopyButtons() {
      const copyButtons = document.querySelectorAll('.copy-icon');
      copyButtons.forEach(button => {
        button.addEventListener('click', function() {
          const textToCopy = this.previousElementSibling.textContent.trim();
          copyToClipboard(textToCopy);
        });
      });
    }

    function copyToClipboard(text) {
      navigator.clipboard.writeText(text).then(() => {
        showToast('Teks berhasil disalin!');
      }).catch((err) => {
        showToast('Gagal menyalin teks', true);
        console.error('Gagal menyalin teks: ', err);
      });
    }

    function confirmDelete(no) {
      if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        window.location.href = 'hapus_data_mahasiswa.php?No=' + no;
      }
    }

    function updateResults() {
      const keyword = document.getElementById('keyword').value;
      const search_column = document.getElementById('search_column').value;
      const limit = document.getElementById('limit').value;

      const xhr = new XMLHttpRequest();
      xhr.open('GET', `dashboard.php?ajax=1&keyword=${keyword}&search_column=${search_column}&limit=${limit}`, true);
      xhr.onload = function() {
        if (this.status === 200) {
          document.getElementById('results').innerHTML = this.responseText;
          updateCopyButtons();
          updateAccordion();
        }
      };
      xhr.send();
    }

    $(document).ready(function() {
      updateCopyButtons(); // Make sure to call this to initialize the copy buttons

      // Trigger modal with data
      $(document).on('click', '.edit-btn', function() {
        var no = $(this).data('no');
        var namalengkap = $(this).data('namalengkap');
        var nomorhp = $(this).data('nomorhp');
        var email = $(this).data('email');
        var password = $(this).data('password');
        var statussia = $(this).data('statussia');

        $('#edit-no').val(no);
        $('#edit-namalengkap').val(namalengkap);
        $('#edit-nomorhp').val(nomorhp);
        $('#edit-email').val(email);
        $('#edit-password').val(password);
        $('#edit-statussia').val(statussia);
      });

      // Handle form submission
      $('#editForm').on('submit', function(e) {
        e.preventDefault();

        var no = $('#edit-no').val();
        var namalengkap = $('#edit-namalengkap').val();
        var nomorhp = $('#edit-nomorhp').val();
        var email = $('#edit-email').val();
        var password = $('#edit-password').val();
        var statussia = $('#edit-statussia').val();

        saveData(no, namalengkap, nomorhp, email, password, statussia);
      });

      $(document).on('click', '.copy-icon', function() {
        var textToCopy = $(this).siblings('span').text().trim();
        copyToClipboard(textToCopy);
      });

      function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
          showToast('Teks berhasil disalin!');
        }).catch((err) => {
          showToast('Gagal menyalin teks', true);
          console.error('Gagal menyalin teks: ', err);
        });
      }

      function saveData(no, namalengkap, nomorhp, email, password, statussia) {
        const data = {
          No: no,
          NamaLengkap: namalengkap,
          NomorHP: nomorhp,
          Email: email,
          Password: password,
          STATUS_INPUT_SIA: statussia
        };

        $.ajax({
          url: 'simpan_data.php',
          method: 'POST',
          data: data,
          success: function(response) {
            if (response === 'success') {
              showToast('Data berhasil disimpan!');
              $('#editModal').modal('hide');
              // Perbarui nilai di tampilan
              $(`span.nama[data-id="${no}"]`).text(namalengkap);
              $(`span.nomor-hp[data-id="${no}"]`).text(nomorhp);
              $(`span.email[data-id="${no}"]`).text(email);
              $(`span.password[data-id="${no}"]`).text(password);
              $(`span.status-sia[data-id="${no}"]`).text(statussia);
            } else {
              showToast('Gagal menyimpan data: ' + response, true);
            }
          },
          error: function() {
            showToast('Gagal menyimpan data', true);
          }
        });
      }

      function showToast(message, isError = false) {
        const toast = $('#toast');
        toast.text(message);
        toast.removeClass('opacity-0 bg-success bg-danger');
        toast.addClass(isError ? 'bg-danger' : 'bg-success');
        toast.addClass('opacity-100');

        setTimeout(() => {
          toast.removeClass('opacity-100');
          toast.addClass('opacity-0');
        }, 3000);
      }
    });
  </script>

</body>

</html>