<?php
require_once '../../koneksi.php'; // Koneksi ke database

// Inisialisasi variabel
$tutonData = array();
$csvData = array();
$message = '';

// Cek apakah form upload di-submit dan file CSV sudah diunggah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csvfile'])) {
    $csvFile = $_FILES['csvfile']['tmp_name'];

    // Baca file CSV
    if (($handle = fopen($csvFile, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            // Simpan data NIM dan password lama dari CSV ke array
            $csvData[$data[0]] = $data[1]; // Menggunakan NIM sebagai key
        }
        fclose($handle);
    }

    // Ambil data dari tabel tuton
    $sql = "SELECT Nama, NIM, Password FROM tuton";
    $result = mysqli_query($koneksi, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $nim = $row['NIM'];
            $nama = stripslashes($row['Nama']);
            $password = $row['Password'];

            // Cek apakah NIM ada di CSV untuk mendapatkan password lama
            if (isset($csvData[$nim])) {
                $oldPassword = $csvData[$nim];
                $status = 'Password lama ditemukan di CSV';
            } else {
                $oldPassword = 'Tidak ditemukan';
                $status = 'Password lama tidak ditemukan di CSV';
            }

            // Simpan data ke array
            $tutonData[] = array(
                'nim' => $nim,
                'nama' => $nama,
                'password' => $password,
                'old_password' => $oldPassword,
                'status' => $status
            );
        }
    } else {
        $message = "Gagal mengambil data dari database.";
    }
} else {
    // Jika tidak ada file yang di-upload, ambil data dari tabel tuton saja
    $sql = "SELECT Nama, NIM, Password FROM tuton";
    $result = mysqli_query($koneksi, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $nim = $row['NIM'];
            $nama = $row['Nama'];
            $password = $row['Password'];

            // Karena tidak ada CSV, password lama tidak ditemukan
            $oldPassword = 'Tidak ditemukan';
            $status = 'Password lama tidak ditemukan di CSV';

            // Simpan data ke array
            $tutonData[] = array(
                'nim' => $nim,
                'nama' => $nama,
                'password' => $password,
                'old_password' => $oldPassword,
                'status' => $status
            );
        }
    } else {
        $message = "Gagal mengambil data dari database.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Mahasiswa</title>
    <!-- Tambahkan Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Tambahkan DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Daftar Mahasiswa</h2>

    <!-- Form Upload CSV -->
    <form action="" method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="form-group">
            <label for="csvfile">Pilih File CSV</label>
            <input type="file" name="csvfile" id="csvfile" class="form-control-file" required>
        </div>
        <button type="submit" class="btn btn-success">Upload CSV</button>
    </form>

    <!-- Tampilkan pesan jika ada -->
    <?php if ($message != ''): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <!-- Tabel Daftar Mahasiswa -->
    <?php if (!empty($tutonData)): ?>
        <table id="tabelMahasiswa" class="table table-bordered">
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
            $no = 1;
            foreach ($tutonData as $data):
                $nim = $data['nim'];
                $nama = $data['nama'];
                $password = $data['password'];
                $oldPassword = $data['old_password'];
                $status = $data['status'];

                // Tentukan tampilan password di database
                $passwordDisplay = ($password && $password != 'Tidak diketahui') ? $password : 'Tidak diketahui';
            ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($nim, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($passwordDisplay, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#modalChangePassword"
                                data-nim="<?php echo htmlspecialchars($nim, ENT_QUOTES, 'UTF-8'); ?>"
                                data-nama="<?php echo htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?>"
                                data-password="<?php echo htmlspecialchars($password, ENT_QUOTES, 'UTF-8'); ?>"
                                data-oldpassword="<?php echo htmlspecialchars($oldPassword, ENT_QUOTES, 'UTF-8'); ?>"
                                data-passworddisplay="<?php echo htmlspecialchars($passwordDisplay, ENT_QUOTES, 'UTF-8'); ?>">
                            Ganti Password
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
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
            <div class="form-group">
                <label for="modalNama">Nama</label>
                <input type="text" class="form-control" id="modalNama" readonly>
            </div>
            <div class="form-group">
                <label for="modalOldPassword">Password Lama</label>
                <input type="password" class="form-control" name="old_password" id="modalOldPassword">
                <small id="oldPasswordHelp" class="form-text text-muted"></small>
            </div>
            <div class="form-group">
                <label for="modalNewPassword">Password Baru</label>
                <input type="password" class="form-control" name="new_password" id="modalNewPassword">
                <small id="newPasswordHelp" class="form-text text-muted"></small>
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

<!-- Modal untuk Menampilkan Pesan -->
<div class="modal fade" id="modalMessage" tabindex="-1" aria-labelledby="modalMessageLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header alert" id="modalMessageHeader">
        <h5 class="modal-title" id="modalMessageTitle"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalMessageBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Tambahkan jQuery dan dependensinya -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Tambahkan DataTables JS -->
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<!-- Tambahkan Bootstrap JS dan dependensinya -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    // Inisialisasi DataTables
    $('#tabelMahasiswa').DataTable({
        "pageLength": 10
    });

    // Ketika modal akan ditampilkan
    $('#modalChangePassword').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Tombol yang diklik
        var nim = button.data('nim');
        var nama = button.data('nama');
        var password = button.data('password');
        var oldPassword = button.data('oldpassword');
        var passwordDisplay = button.data('passworddisplay');

        var modal = $(this);
        modal.find('#modalNIM').val(nim);
        modal.find('#modalNama').val(nama);

        // Reset form
        modal.find('#modalNewPassword').val('');
        modal.find('#modalNewPassword').removeAttr('readonly');
        modal.find('#modalNewPassword').removeAttr('required');
        modal.find('#newPasswordHelp').text('');
        modal.find('#modalOldPassword').val('');
        modal.find('#modalOldPassword').removeAttr('readonly');
        modal.find('#modalOldPassword').removeAttr('required');
        modal.find('#oldPasswordHelp').text('');

        // Atur tampilan sesuai kondisi
        if (passwordDisplay === 'Tidak diketahui' || password === 'Tidak diketahui') {
            // Jika password baru tidak diketahui, minta input password baru
            modal.find('#modalNewPassword').attr('required', 'required');
            modal.find('#newPasswordHelp').text('Masukkan password baru.');
        } else {
            // Jika password baru diketahui, isi otomatis
            modal.find('#modalNewPassword').val(password);
            modal.find('#modalNewPassword').attr('readonly', 'readonly');
        }

        if (oldPassword === 'Tidak ditemukan') {
            // Jika password lama tidak ditemukan di CSV, minta input password lama
            modal.find('#modalOldPassword').attr('required', 'required');
            modal.find('#oldPasswordHelp').text('Password lama tidak ditemukan, silakan masukkan password lama.');
        } else {
            // Jika password lama ditemukan, isi otomatis
            modal.find('#modalOldPassword').val(oldPassword);
            modal.find('#modalOldPassword').attr('readonly', 'readonly');
        }
    });

    // Ketika form di-submit
    $('#formChangePassword').submit(function(e) {
        e.preventDefault(); // Mencegah submit default

        var form = $(this);
        var nim = form.find('#modalNIM').val();
        var oldPassword = form.find('#modalOldPassword').val();
        var newPassword = form.find('#modalNewPassword').val();

        // Mengirim data melalui AJAX
        $.ajax({
            url: 'proses_ganti_password.php',
            type: 'POST',
            data: {
                nim: nim,
                old_password: oldPassword,
                new_password: newPassword
            },
            success: function(response) {
                // Tampilkan pesan sukses atau error dalam modal
                $('#modalMessage').modal('show');
                if (response.includes('berhasil')) {
                    $('#modalMessageHeader').addClass('alert-success');
                    $('#modalMessageTitle').text('Sukses');
                } else {
                    $('#modalMessageHeader').addClass('alert-danger');
                    $('#modalMessageTitle').text('Gagal');
                }
                $('#modalMessageBody').html(response);

                // Perbarui data di tabel tanpa reload halaman
                // Cari baris dengan NIM yang sesuai dan perbarui data
                var row = $('#tabelMahasiswa').find('td').filter(function() {
                    return $(this).text() == nim;
                }).closest('tr');

                if (newPassword !== '' && newPassword !== 'Tidak diketahui') {
                    row.find('td:eq(3)').text(newPassword);
                }

                // Tutup modal ganti password
                $('#modalChangePassword').modal('hide');
            },
            error: function() {
                alert('Terjadi kesalahan saat mengirim data.');
                $('#modalChangePassword').modal('hide');
            }
        });
    });

    // Reset modal pesan saat ditutup
    $('#modalMessage').on('hidden.bs.modal', function () {
        $('#modalMessageHeader').removeClass('alert-success alert-danger');
        $('#modalMessageTitle').text('');
        $('#modalMessageBody').text('');
    });
});
</script>
</body>
</html>
