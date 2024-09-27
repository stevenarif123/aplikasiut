<?php
// Include koneksi ke database
require_once '../../koneksi.php';

// Inisialisasi variabel
$csvData = array();
$message = '';

// Jika form upload di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csvfile'])) {
    $csvFile = $_FILES['csvfile']['tmp_name'];

    // Baca file CSV
    if (($handle = fopen($csvFile, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            // Simpan data username dan password dari CSV ke array
            $csvData[] = array('username' => $data[0], 'password' => $data[1]);
        }
        fclose($handle);
    }
} else {
    // Jika tidak ada file yang di-upload, inisialisasi $csvData sebagai array kosong
    $csvData = array();
}

// Ambil data mahasiswa dari tabel tuton
$sql = "SELECT Nama, NIM, Password FROM tuton";
$result = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Mahasiswa</title>
    <!-- Tambahkan Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Daftar Mahasiswa</h2>

    <!-- Form Upload CSV -->
    <form action="" method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="form-group">
            <label for="csvfile">Pilih File CSV</label>
            <input type="file" name="csvfile" id="csvfile" class="form-control-file">
        </div>
        <button type="submit" class="btn btn-success">Upload CSV</button>
    </form>

    <!-- Tampilkan Pesan Sukses atau Error -->
    <?php
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $nim = isset($_GET['nim']) ? $_GET['nim'] : '';
        $message = isset($_GET['message']) ? $_GET['message'] : '';

        if ($status == 'success') {
            echo "<div class='alert alert-success' role='alert'>
                    Password untuk NIM $nim berhasil diupdate.
                  </div>";
        } elseif ($status == 'error') {
            echo "<div class='alert alert-danger' role='alert'>
                    Terjadi kesalahan: $message
                  </div>";
        }
    }
    ?>

    <!-- Tabel Daftar Mahasiswa -->
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Password di Database</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $no = 1; // Inisialisasi nomor urut
        while ($row = mysqli_fetch_assoc($result)) {
            $nim = $row['NIM'];
            $nama = stripslashes($row['Nama']);
            $password = $row['Password'];
            $status = 'Tidak Ditemukan';
            $passwordDariCSV = '';

            // Cek apakah NIM berada di file CSV
            foreach ($csvData as $data) {
                if ($data['username'] == $nim) {
                    $status = 'Password Ditemukan di CSV';
                    $passwordDariCSV = $data['password'];
                    break;
                }
            }

            // Tentukan tampilan password di database
            $passwordDisplay = ($password && $password != 'Tidak diketahui') ? $password : 'Tidak diketahui';

            echo "<tr>
                <td>$no</td>
                <td>$nama</td>
                <td>$nim</td>
                <td>$passwordDisplay</td>
                <td>$status</td>
                <td>";

            // Tombol Aksi
            if ($status == 'Password Ditemukan di CSV') {
                echo "<button class='btn btn-primary' data-toggle='modal' data-target='#modalChangePassword' data-nim='$nim' data-password='$passwordDariCSV' data-isnew='false'>Ganti Password</button>";
            } else {
                echo "<button class='btn btn-warning' data-toggle='modal' data-target='#modalChangePassword' data-nim='$nim' data-password='' data-isnew='true'>Input Password Baru</button>";
            }

            echo "</td>
            </tr>";

            $no++; // Increment nomor urut
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Modal untuk Ganti Password -->
<div class="modal fade" id="modalChangePassword" tabindex="-1" aria-labelledby="modalChangePasswordLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formChangePassword">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Ganti Password</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="nim" id="modalNIM">
            <div class="form-group" id="passwordInputGroup">
                <label for="modalNewPassword">Password Baru</label>
                <input type="password" class="form-control" name="new_password" id="modalNewPassword" required>
            </div>
            <div id="passwordInfo">
                <!-- Jika password dari CSV, informasi akan ditampilkan di sini -->
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Ganti Password</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Tambahkan Bootstrap JS dan dependensinya -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
// Ketika modal akan ditampilkan
$('#modalChangePassword').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Tombol yang diklik
    var nim = button.data('nim');
    var password = button.data('password');
    var isNew = button.data('isnew');

    var modal = $(this);
    modal.find('#modalNIM').val(nim);

    if (isNew == 'false') {
        // Jika password dari CSV
        modal.find('#passwordInputGroup').hide();
        modal.find('#modalNewPassword').removeAttr('required');
        modal.find('#passwordInfo').html('<p>Password akan diganti menggunakan password dari CSV.</p>');
    } else {
        // Jika perlu input password baru
        modal.find('#passwordInputGroup').show();
        modal.find('#modalNewPassword').attr('required', 'required');
        modal.find('#modalNewPassword').val('');
        modal.find('#passwordInfo').html('');
    }
});

// Ketika form di-submit
$('#formChangePassword').submit(function(e) {
    e.preventDefault(); // Mencegah submit default

    var form = $(this);
    var nim = form.find('#modalNIM').val();
    var newPassword = form.find('#modalNewPassword').val();

    // Jika password dari CSV, ambil password dari data attribute
    var isNew = $('#modalChangePassword').find('button[type="submit"]').data('isnew');
    if (isNew == 'false') {
        newPassword = $('#modalChangePassword').find('button[data-nim="' + nim + '"]').data('password');
    }

    // Mengirim data melalui AJAX
    $.ajax({
        url: 'proses_ganti_password.php',
        type: 'POST',
        data: {
            nim: nim,
            new_password: newPassword
        },
        success: function(response) {
            // Tampilkan pesan sukses atau error
            alert(response);
            location.reload(); // Reload halaman untuk memperbarui data
        },
        error: function() {
            alert('Terjadi kesalahan saat mengirim data.');
        }
    });

    // Tutup modal
    $('#modalChangePassword').modal('hide');
});
</script>
</body>
</html>
