<?php
require_once "../../koneksi.php";

// Mengambil data mahasiswa dari tabel mahasiswa
$sql_mahasiswa = "SELECT No, NIM, Email FROM mahasiswa ORDER BY No ASC";
$result = mysqli_query($koneksi, $sql_mahasiswa);

$data_mahasiswa = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Cek apakah mahasiswa sudah ada di tabel tuton
        $nim = $row['NIM'];
        $sql_check = "SELECT * FROM tuton WHERE NIM = ?";
        $stmt_check = mysqli_prepare($koneksi, $sql_check);

        // Tambahkan pengecekan error
        if (!$stmt_check) {
            die("Prepare failed: (" . mysqli_errno($koneksi) . ") " . mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param($stmt_check, "s", $nim);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        if (mysqli_num_rows($result_check) > 0) {
            $row['status'] = 'Selesai';
        } else {
            $row['status'] = 'Belum';
        }
        $data_mahasiswa[] = $row;
    }
} else {
    echo "Koneksi atau query bermasalah: " . mysqli_error($koneksi);
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Mahasiswa untuk Pendaftaran</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        .copy-btn {
            cursor: pointer;
            margin-left: 5px;
        }
        .status-selesai {
            color: green;
            font-weight: bold;
        }
        .status-belum {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>Daftar Mahasiswa untuk Pendaftaran</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Email</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($data_mahasiswa) > 0): ?>
                <?php foreach($data_mahasiswa as $index => $mahasiswa): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <?= htmlspecialchars($mahasiswa['NIM']) ?>
                            <i class="fas fa-copy copy-btn" data-copy="<?= htmlspecialchars($mahasiswa['NIM']) ?>"></i>
                        </td>
                        <td>
                            <?= htmlspecialchars($mahasiswa['Email']) ?>
                            <i class="fas fa-copy copy-btn" data-copy="<?= htmlspecialchars($mahasiswa['Email']) ?>"></i>
                        </td>
                        <td class="status-<?= strtolower($mahasiswa['status']) ?>">
                            <?= $mahasiswa['status'] ?>
                        </td>
                        <td>
                            <?php if($mahasiswa['status'] == 'Belum'): ?>
                                <button class="btn btn-primary proses-btn" data-no="<?= htmlspecialchars($mahasiswa['No']) ?>" data-nim="<?= htmlspecialchars($mahasiswa['NIM']) ?>" data-email="<?= htmlspecialchars($mahasiswa['Email']) ?>">Proses</button>
                            <?php else: ?>
                                <span class="text-success">Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Tidak ada data mahasiswa.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal untuk memproses pendaftaran -->
<div class="modal fade" id="prosesModal" tabindex="-1" role="dialog" aria-labelledby="prosesModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!-- Konten modal akan diisi melalui JavaScript -->
    </div>
  </div>
</div>

<!-- Modal untuk menampilkan status pendaftaran -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!-- Konten modal akan diisi melalui JavaScript -->
    </div>
  </div>
</div>

<!-- jQuery dan Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Popper.js dan Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

<script>
$(document).ready(function(){
    // Event handler untuk tombol Proses
    $('.proses-btn').on('click', function(){
        var no = $(this).data('no');
        var nim = $(this).data('nim');
        var email = $(this).data('email');

        // Mendapatkan nama mahasiswa
        $.ajax({
            url: 'get_mahasiswa.php',
            method: 'POST',
            data: { no: no },
            dataType: 'json',
            success: function(response) {
                if(response.status == 'success') {
                    var nama = response.nama;

                    // Menampilkan modal dengan detail mahasiswa
                    var modalContent = '<div class="modal-header">';
                    modalContent += '<h5 class="modal-title">Proses Pendaftaran</h5>';
                    modalContent += '<button type="button" class="close" data-dismiss="modal" aria-label="Tutup">';
                    modalContent += '<span aria-hidden="true">&times;</span>';
                    modalContent += '</button>';
                    modalContent += '</div>';
                    modalContent += '<div class="modal-body">';
                    modalContent += '<form id="prosesForm">';
                    modalContent += '<input type="hidden" name="no" value="' + no + '">';
                    modalContent += '<div class="form-group">';
                    modalContent += '<label>NIM</label>';
                    modalContent += '<input type="text" name="nim" class="form-control" value="' + nim + '" readonly>';
                    modalContent += '</div>';
                    modalContent += '<div class="form-group">';
                    modalContent += '<label>Nama</label>';
                    modalContent += '<input type="text" name="nama" class="form-control" value="' + nama + '" readonly>';
                    modalContent += '</div>';
                    modalContent += '<div class="form-group">';
                    modalContent += '<label>Email</label>';
                    modalContent += '<input type="email" name="email" class="form-control" value="' + email + '" required>';
                    modalContent += '</div>';
                    modalContent += '</form>';
                    modalContent += '</div>';
                    modalContent += '<div class="modal-footer">';
                    modalContent += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>';
                    modalContent += '<button type="button" class="btn btn-primary submit-proses">Proses</button>';
                    modalContent += '</div>';

                    $('#prosesModal .modal-content').html(modalContent);
                    $('#prosesModal').modal('show');
                } else {
                    alert('Data mahasiswa tidak ditemukan.');
                }
            },
            error: function(xhr, status, error){
                alert('Terjadi kesalahan: ' + error);
            }
        });
    });

    // Event handler untuk submit proses pendaftaran
    $(document).on('click', '.submit-proses', function(){
        var formData = $('#prosesForm').serialize();

        $.ajax({
            url: 'proses_pendaftaran.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function(){
                $('#prosesModal').modal('hide');
                // Tampilkan loading modal
                $('#statusModal .modal-content').html('<div class="modal-header"><h5 class="modal-title">Memproses Pendaftaran</h5></div><div class="modal-body"><p>Sedang memproses pendaftaran...</p></div>');
                $('#statusModal').modal('show');
            },
            success: function(response){
                // Tampilkan hasil respon dalam modal
                var modalContent = '<div class="modal-header">';
                modalContent += '<h5 class="modal-title">Status Pendaftaran</h5>';
                modalContent += '<button type="button" class="close" data-dismiss="modal" aria-label="Tutup">';
                modalContent += '<span aria-hidden="true">&times;</span>';
                modalContent += '</button>';
                modalContent += '</div>';
                modalContent += '<div class="modal-body">';
                modalContent += '<p>' + response.message + '</p>';
                modalContent += '</div>';
                modalContent += '<div class="modal-footer">';
                modalContent += '<button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="location.reload()">Tutup</button>';
                modalContent += '</div>';

                $('#statusModal .modal-content').html(modalContent);
            },
            error: function(xhr, status, error){
                // Tampilkan pesan error dalam modal
                var modalContent = '<div class="modal-header">';
                modalContent += '<h5 class="modal-title">Error</h5>';
                modalContent += '<button type="button" class="close" data-dismiss="modal" aria-label="Tutup">';
                modalContent += '<span aria-hidden="true">&times;</span>';
                modalContent += '</button>';
                modalContent += '</div>';
                modalContent += '<div class="modal-body">';
                modalContent += '<p>Terjadi kesalahan saat memproses data: ' + error + '</p>';
                modalContent += '</div>';
                modalContent += '<div class="modal-footer">';
                modalContent += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>';
                modalContent += '</div>';

                $('#statusModal .modal-content').html(modalContent);
            }
        });
    });

    // Event handler untuk tombol copy
    $(document).on('click', '.copy-btn', function(){
        var text = $(this).data('copy');
        var tempInput = $('<input>');
        $('body').append(tempInput);
        tempInput.val(text).select();
        document.execCommand('copy');
        tempInput.remove();
        alert('Teks berhasil disalin: ' + text);
    });
});
</script>

</body>
</html>