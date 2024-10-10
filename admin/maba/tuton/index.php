<?php
require_once "../../koneksi.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Mahasiswa</title>
    <!-- Tambahkan Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Tambahkan Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- Tambahkan SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <style>
        /* Optional: Style untuk tombol copy */
        .copy-btn {
            margin-left: 5px;
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mt-5">Data Mahasiswa</h1>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>No</th>
                <th>
                    NIM 
                    <button class="btn btn-sm btn-light copy-btn" data-clipboard-text="NIM"><i class="fas fa-copy"></i></button>
                </th>
                <th>
                    Nama 
                    <button class="btn btn-sm btn-light copy-btn" data-clipboard-text="Nama"><i class="fas fa-copy"></i></button>
                </th>
                <th>Jurusan</th>
                <th>
                    Email 
                    <button class="btn btn-sm btn-light copy-btn" data-clipboard-text="Email"><i class="fas fa-copy"></i></button>
                </th>
                <th>
                    Password 
                    <button class="btn btn-sm btn-light copy-btn" data-clipboard-text="Password"><i class="fas fa-copy"></i></button>
                </th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM mahasiswa";
            $result = mysqli_query($koneksi, $sql);
            while($row = mysqli_fetch_assoc($result)) {
                $nim = $row['NIM'];

                // Cek apakah NIM ada di tabel tuton
                $sql_check = "SELECT * FROM tuton WHERE NIM = ?";
                $stmt_check = mysqli_prepare($koneksi, $sql_check);
                mysqli_stmt_bind_param($stmt_check, "s", $nim);
                mysqli_stmt_execute($stmt_check);
                $result_check = mysqli_stmt_get_result($stmt_check);
                $existing = mysqli_fetch_assoc($result_check);

                // Tentukan status
                if($existing) {
                    $status = 'Sukses';
                    $password = $existing['Password']; // Mengambil password dari tabel tuton
                } else {
                    $status = 'Belum';
                    $password = '';
                }

                echo '<tr>';
                echo '<td>'.$row['No'].'</td>';
                echo '<td class="copy-nim">'.$row['NIM'].' <button class="btn btn-sm btn-light copy-btn" data-clipboard-text="'.$row['NIM'].'"><i class="fas fa-copy"></i></button></td>';
                echo '<td class="copy-nama">'.stripslashes($row['NamaLengkap']).' <button class="btn btn-sm btn-light copy-btn" data-clipboard-text="'.stripslashes($row['NamaLengkap']).'"><i class="fas fa-copy"></i></button></td>';
                echo '<td>'.$row['Jurusan'].'</td>';
                echo '<td class="copy-email">'.$row['Email'].' <button class="btn btn-sm btn-light copy-btn" data-clipboard-text="'.$row['Email'].'"><i class="fas fa-copy"></i></button></td>';
                echo '<td class="copy-password">'.$password.' <button class="btn btn-sm btn-light copy-btn" data-clipboard-text="'.$password.'"><i class="fas fa-copy"></i></button></td>';
                echo '<td>'.$status.'</td>';
                echo '<td>';

                // Tombol Proses
                if($status != 'Sukses') {
                    echo '<button class="btn btn-primary proses-pendaftaran" data-no="'.$row['No'].'" data-nim="'.$row['NIM'].'" data-nama="'.$row['NamaLengkap'].'" data-email="'.$row['Email'].'">Proses</button> ';
                } else {
                    echo '<button class="btn btn-secondary" disabled>Proses</button> ';
                }

                // Tombol Migrasi
                if($status != 'Sukses') {
                    echo '<button class="btn btn-success migrasi-data" data-no="'.$row['No'].'" title="Migrasi"><i class="fas fa-exchange-alt"></i></button> ';
                } else {
                    echo '<button class="btn btn-secondary" disabled><i class="fas fa-exchange-alt"></i></button> ';
                }

                // Tombol WhatsApp
                if($status == 'Sukses') {
                    $nomor_wa = $row['NomorHP'];
                    if(!empty($nomor_wa)) {
                        // Mengganti awalan '0' menjadi '62' jika nomor dimulai dengan '0'
                        if(substr($nomor_wa, 0, 1) == '0') {
                            $nomor_wa = '62' . substr($nomor_wa, 1);
                        }

                        // Mendapatkan waktu saat ini
                        date_default_timezone_set('Asia/Jakarta');
                        $jam = date('H');
                        $salam = 'Selamat siang';

                        if($jam >= 1 && $jam < 10){
                            $salam = 'Selamat pagi';
                        } elseif($jam >= 10 && $jam < 14){
                            $salam = 'Selamat siang';
                        } elseif($jam >= 14 && $jam < 18){
                            $salam = 'Selamat sore';
                        } else {
                            $salam = 'Selamat malam';
                        }

                        // Isi pesan WhatsApp
                        $pesan_wa = "$salam, saya kirimkan detail akun mahasiswa yang akan digunakan untuk Tutorial Online (TUTON).\n";
                        $pesan_wa .= "Username : ".$row['NIM']."\n";
                        $pesan_wa .= "Password : ".$password."\n\n";
                        $pesan_wa .= "Silahkan melakukan login di alamat https://elearning.ut.ac.id/login/index.php dengan memasukkan username dan passwordnya. Sekian dan terima kasih.";

                        $pesan_wa = urlencode($pesan_wa);
                        echo '<a href="https://wa.me/'.$nomor_wa.'?text='.$pesan_wa.'" class="btn btn-success" title="WhatsApp" target="_blank"><i class="fab fa-whatsapp"></i></a>';
                    } else {
                        echo '<button class="btn btn-secondary" disabled><i class="fab fa-whatsapp"></i></button>';
                    }
                } else {
                    echo '<button class="btn btn-secondary" disabled><i class="fab fa-whatsapp"></i></button>';
                }

                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal untuk konfirmasi dan pengeditan email -->
<div class="modal fade" id="prosesModal" tabindex="-1" aria-labelledby="prosesModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="prosesForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="prosesModalLabel">Konfirmasi Data Mahasiswa</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="no" id="modalNo">
            <div class="form-group">
                <label for="modalNIM">NIM</label>
                <input type="text" class="form-control" id="modalNIM" name="nim" readonly>
            </div>
            <div class="form-group">
                <label for="modalNama">Nama</label>
                <input type="text" class="form-control" id="modalNama" name="nama" readonly>
            </div>
            <div class="form-group">
                <label for="modalEmail">Email</label>
                <input type="email" class="form-control" id="modalEmail" name="email">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary submit-proses">Proses</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Tambahkan jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Tambahkan Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<!-- Tambahkan SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<!-- Tambahkan ClipboardJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>

<script>
    // Inisialisasi ClipboardJS
    var clipboard = new ClipboardJS('.copy-btn');

    clipboard.on('success', function(e) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Data berhasil disalin!',
            timer: 1500,
            showConfirmButton: false
        });
        e.clearSelection();
    });

    clipboard.on('error', function(e) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Gagal menyalin data.',
        });
    });

    // Event handler untuk tombol Proses
    $(document).on('click', '.proses-pendaftaran', function(){
        var no = $(this).data('no');
        var nim = $(this).data('nim');
        var nama = $(this).data('nama');
        var email = $(this).data('email');

        // Isi data ke dalam modal
        $('#modalNo').val(no);
        $('#modalNIM').val(nim);
        $('#modalNama').val(nama);
        $('#modalEmail').val(email);

        // Tampilkan modal
        $('#prosesModal').modal('show');
    });

    // Event handler untuk submit Proses
    $(document).on('click', '.submit-proses', function(){
        var formData = $('#prosesForm').serialize();

        $.ajax({
            url: 'proses_pendaftaran.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function(){
                $('#prosesModal').modal('hide');
                Swal.fire({
                    title: 'Memproses Pendaftaran',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
            },
            success: function(response){
                Swal.close();
                if(response.status == 'success'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        html: response.message,
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message,
                    });
                }
            },
            error: function(xhr, status, error){
                Swal.close();
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memproses data.',
                });
            }
        });
    });

    // Event handler untuk tombol Migrasi
    $(document).on('click', '.migrasi-data', function(){
        var no = $(this).data('no');

        $.ajax({
            url: 'migrasi_data.php',
            method: 'POST',
            data: { no: no },
            dataType: 'json',
            beforeSend: function(){
                Swal.fire({
                    title: 'Migrasi Data',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
            },
            success: function(response){
                Swal.close();
                if(response.status == 'success'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message,
                    });
                }
            },
            error: function(xhr, status, error){
                Swal.close();
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memproses data.',
                });
            }
        });
    });
</script>
</body>
</html>
