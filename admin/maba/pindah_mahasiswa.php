<?php
// Koneksi ke database
$host = "localhost";
$user = "saluttan_user";
$pass = "mantapfb1234";
$dbname = "saluttan_datamahasiswa";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    // Set error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Koneksi atau query bermasalah: " . $e->getMessage();
    exit();
}

// Fungsi untuk menghasilkan ID unik
function generateUniqueId($kodeJurusan, $nim, $tglLahir, $nomorUrut) {
    // Menggabungkan semua elemen menjadi satu string
    $combinedString = $kodeJurusan . $nim . $tglLahir . $nomorUrut;

    // Menghasilkan hash dari string yang digabungkan
    $hash = hash('sha256', $combinedString);

    // Mengambil 8 karakter pertama dari hash
    $uniqueId = substr($hash, 0, 8);

    return $uniqueId;
}

// Jika permintaan AJAX untuk memindahkan mahasiswa
if(isset($_POST['action']) && $_POST['action'] == 'pindahkan') {
    $no_mahasiswa = $_POST['no_mahasiswa'];
    $nim = $_POST['nim'];
    $masa = '20242'; // Nilai Masa untuk database

    // Validasi NIM (hanya angka, 9 digit)
    if(!preg_match('/^\d{9}$/', $nim)) {
        // Jika NIM tidak valid, kirim respons error
        echo json_encode(['status' => 'error', 'message' => 'NIM tidak valid. Pastikan NIM terdiri dari 9 digit angka.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }

    try {
        // Ambil data mahasiswa berdasarkan No
        $sql_select = "SELECT * FROM mahasiswabaru20242 WHERE No = :no";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->bindParam(':no', $no_mahasiswa);
        $stmt_select->execute();
        $data_mahasiswa = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if($data_mahasiswa) {
            // Cek apakah NIK sudah ada di tabel mahasiswa
            $sql_check_nik = "SELECT NIK FROM mahasiswa WHERE NIK = :nik";
            $stmt_check_nik = $conn->prepare($sql_check_nik);
            $stmt_check_nik->bindParam(':nik', $data_mahasiswa['NIK']);
            $stmt_check_nik->execute();
            if($stmt_check_nik->rowCount() > 0) {
                // Jika NIK sudah ada, kirim respons error
                echo json_encode(['status' => 'error', 'message' => 'NIK sudah terdaftar di tabel mahasiswa.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit();
            }

            // Ambil kodeJurusan dari tabel prodi_admisi berdasarkan Jurusan mahasiswa
            $sql_prodi = "SELECT kode_program_studi FROM prodi_admisi WHERE nama_program_studi = :jurusan LIMIT 1";
            $stmt_prodi = $conn->prepare($sql_prodi);
            $stmt_prodi->bindParam(':jurusan', $data_mahasiswa['Jurusan']);
            $stmt_prodi->execute();
            $data_prodi = $stmt_prodi->fetch(PDO::FETCH_ASSOC);

            $kodeJurusan = isset($data_prodi['kode_program_studi']) ? $data_prodi['kode_program_studi'] : 'XX';

            // Ambil 2 karakter tanggal lahir (hari)
            $tglLahir = date('d', strtotime($data_mahasiswa['TanggalLahir']));

            // Dapatkan nomor urut (3 digit terakhir dari No)
            $nomorUrut = str_pad(substr($data_mahasiswa['No'], -3), 3, '0', STR_PAD_LEFT);

            // Generate ID unik
            $uniqueId = generateUniqueId($kodeJurusan, $nim, $tglLahir, $nomorUrut);

            // Insert data ke tabel mahasiswa
            $sql_insert = "INSERT INTO mahasiswa (
                `No`, `JalurProgram`, `ID`, `NIM`, `NamaLengkap`, `TempatLahir`, `TanggalLahir`,
                `NamaIbuKandung`, `NIK`, `Jurusan`, `NomorHP`, `Email`, `Password`,
                `Agama`, `JenisKelamin`, `StatusPerkawinan`, `NomorHPAlternatif`,
                `NomorIjazah`, `TahunIjazah`, `NISN`, `Alamat`, `LayananPaketSemester`,
                `DiInputOleh`, `DiInputPada`, `DiEditPada`, `STATUS_INPUT_SIA`,
                `UkuranBaju`, `AsalKampus`, `TahunLulusKampus`, `IPK`, `JurusanSMK`,
                `JenisSekolah`, `NamaSekolah`, `Masa`
            ) VALUES (
                NULL, :JalurProgram, :ID, :NIM, :NamaLengkap, :TempatLahir, :TanggalLahir,
                :NamaIbuKandung, :NIK, :Jurusan, :NomorHP, :Email, :Password,
                :Agama, :JenisKelamin, :StatusPerkawinan, :NomorHPAlternatif,
                :NomorIjazah, :TahunIjazah, :NISN, :Alamat, :LayananPaketSemester,
                :DiInputOleh, :DiInputPada, :DiEditPada, :STATUS_INPUT_SIA,
                :UkuranBaju, :AsalKampus, :TahunLulusKampus, :IPK, :JurusanSMK,
                :JenisSekolah, :NamaSekolah, :Masa
            )";
            $stmt_insert = $conn->prepare($sql_insert);

            // Bind parameters
            $stmt_insert->bindParam(':JalurProgram', $data_mahasiswa['JalurProgram']);
            $stmt_insert->bindParam(':ID', $uniqueId);
            $stmt_insert->bindParam(':NIM', $nim);
            $stmt_insert->bindParam(':NamaLengkap', $data_mahasiswa['NamaLengkap']);
            $stmt_insert->bindParam(':TempatLahir', $data_mahasiswa['TempatLahir']);
            $stmt_insert->bindParam(':TanggalLahir', $data_mahasiswa['TanggalLahir']);
            $stmt_insert->bindParam(':NamaIbuKandung', $data_mahasiswa['NamaIbuKandung']);
            $stmt_insert->bindParam(':NIK', $data_mahasiswa['NIK']);
            $stmt_insert->bindParam(':Jurusan', $data_mahasiswa['Jurusan']);
            $stmt_insert->bindParam(':NomorHP', $data_mahasiswa['NomorHP']);
            $stmt_insert->bindParam(':Email', $data_mahasiswa['Email']);
            $stmt_insert->bindParam(':Password', $data_mahasiswa['Password']);
            $stmt_insert->bindParam(':Agama', $data_mahasiswa['Agama']);
            $stmt_insert->bindParam(':JenisKelamin', $data_mahasiswa['JenisKelamin']);
            $stmt_insert->bindParam(':StatusPerkawinan', $data_mahasiswa['StatusPerkawinan']);
            $stmt_insert->bindParam(':NomorHPAlternatif', $data_mahasiswa['NomorHPAlternatif']);
            $stmt_insert->bindParam(':NomorIjazah', $data_mahasiswa['NomorIjazah']);
            $stmt_insert->bindParam(':TahunIjazah', $data_mahasiswa['TahunIjazah']);
            $stmt_insert->bindParam(':NISN', $data_mahasiswa['NISN']);
            $stmt_insert->bindParam(':Alamat', $data_mahasiswa['Alamat']);
            $stmt_insert->bindParam(':LayananPaketSemester', $data_mahasiswa['LayananPaketSemester']);
            $stmt_insert->bindParam(':DiInputOleh', $data_mahasiswa['DiInputOleh']);
            $stmt_insert->bindParam(':DiInputPada', $data_mahasiswa['DiInputPada']);
            $stmt_insert->bindParam(':DiEditPada', $data_mahasiswa['DiEditPada']);
            $stmt_insert->bindParam(':STATUS_INPUT_SIA', $data_mahasiswa['STATUS_INPUT_SIA']);
            $stmt_insert->bindParam(':UkuranBaju', $data_mahasiswa['UkuranBaju']);
            $stmt_insert->bindParam(':AsalKampus', $data_mahasiswa['AsalKampus']);
            $stmt_insert->bindParam(':TahunLulusKampus', $data_mahasiswa['TahunLulusKampus']);
            $stmt_insert->bindParam(':IPK', $data_mahasiswa['IPK']);
            $stmt_insert->bindParam(':JurusanSMK', $data_mahasiswa['JurusanSMK']);
            $stmt_insert->bindParam(':JenisSekolah', $data_mahasiswa['JenisSekolah']);
            $stmt_insert->bindParam(':NamaSekolah', $data_mahasiswa['NamaSekolah']);
            $stmt_insert->bindParam(':Masa', $masa);

            // Eksekusi insert
            try {
                $stmt_insert->execute();
                // Kirim respons sukses
                echo json_encode(['status' => 'success', 'message' => 'Mahasiswa berhasil dipindahkan.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit();
            } catch (PDOException $e) {
                // Jika terjadi error (misalnya duplikasi ID atau NIK)
                if ($e->getCode() == 23000) { // Integrity constraint violation
                    echo json_encode(['status' => 'error', 'message' => 'Terjadi duplikasi data (ID atau NIK sudah ada).'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit();
                } else {
                    // Tangani error lainnya
                    echo json_encode(['status' => 'error', 'message' => 'Kesalahan saat menyimpan data: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit();
                }
            }
        } else {
            // Jika data mahasiswa tidak ditemukan
            echo json_encode(['status' => 'error', 'message' => 'Data mahasiswa tidak ditemukan.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit();
        }
    } catch(PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Kesalahan pada server: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }
}

// Jika permintaan AJAX untuk mengupdate NIM mahasiswa
elseif(isset($_POST['action']) && $_POST['action'] == 'update_nim') {
    $no_mahasiswa = $_POST['no_mahasiswa'];
    $nim_baru = $_POST['nim'];

    // Validasi NIM (hanya angka, 9 digit)
    if(!preg_match('/^\d{9}$/', $nim_baru)) {
        // Jika NIM tidak valid, kirim respons error
        echo json_encode(['status' => 'error', 'message' => 'NIM tidak valid. Pastikan NIM terdiri dari 9 digit angka.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }

    try {
        // Update NIM di tabel mahasiswa
        $sql_update_nim = "UPDATE mahasiswa m
                           INNER JOIN mahasiswabaru20242 mb ON m.NIK = mb.NIK
                           SET m.NIM = :nim_baru
                           WHERE mb.No = :no_mahasiswa";
        $stmt_update_nim = $conn->prepare($sql_update_nim);
        $stmt_update_nim->bindParam(':nim_baru', $nim_baru);
        $stmt_update_nim->bindParam(':no_mahasiswa', $no_mahasiswa);
        $stmt_update_nim->execute();

        // Kirim respons sukses
        echo json_encode(['status' => 'success', 'message' => 'NIM berhasil diperbarui.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    } catch(PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Kesalahan saat mengupdate data: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }
}

// Jika permintaan AJAX untuk pencarian dan pagination
elseif(isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    $search_nama = isset($_GET['search_nama']) ? $_GET['search_nama'] : '';
    $search_jurusan = isset($_GET['search_jurusan']) ? $_GET['search_jurusan'] : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10; // Jumlah data per halaman
    $offset = ($page - 1) * $limit;

    try {
        // Membuat klausa WHERE untuk pencarian
        $where_clauses = [];
        $params = [];

        $where_clauses[] = "mb.STATUS_INPUT_SIA = 'MAHASISWA UT'";

        if (!empty($search_nama)) {
            $where_clauses[] = "mb.NamaLengkap LIKE :search_nama";
            $params[':search_nama'] = '%' . $search_nama . '%';
        }
        if (!empty($search_jurusan)) {
            $where_clauses[] = "mb.Jurusan LIKE :search_jurusan";
            $params[':search_jurusan'] = '%' . $search_jurusan . '%';
        }

        $where_sql = '';
        if (!empty($where_clauses)) {
            $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
        }

        // Menghitung total data
        $sql_count = "SELECT COUNT(*) as total FROM mahasiswabaru20242 mb $where_sql";
        $stmt_count = $conn->prepare($sql_count);
        foreach ($params as $key => $value) {
            $stmt_count->bindValue($key, $value);
        }
        $stmt_count->execute();
        $total_data = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
        $total_pages = ceil($total_data / $limit);

        // Membuat klausa ORDER BY untuk pengurutan
        $order_by_sql = "ORDER BY mb.NamaLengkap ASC";

        // Ambil data mahasiswa baru dengan filter dan pagination
        $sql_mahasiswa_baru = "SELECT mb.*, m.NIK AS NIK_mahasiswa, m.NIM AS NIM_mahasiswa
            FROM mahasiswabaru20242 mb
            LEFT JOIN mahasiswa m ON mb.NIK = m.NIK
            $where_sql
            $order_by_sql
            LIMIT :limit OFFSET :offset";
        $stmt_mahasiswa_baru = $conn->prepare($sql_mahasiswa_baru);

        foreach ($params as $key => $value) {
            $stmt_mahasiswa_baru->bindValue($key, $value);
        }
        $stmt_mahasiswa_baru->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt_mahasiswa_baru->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt_mahasiswa_baru->execute();
        $data_mahasiswa_baru = $stmt_mahasiswa_baru->fetchAll(PDO::FETCH_ASSOC);

        // Memproses data untuk dikirim ke JavaScript
        foreach ($data_mahasiswa_baru as &$mahasiswa) {
            // Menghilangkan backslash menggunakan stripslashes
            $mahasiswa['NamaLengkapJS'] = stripslashes($mahasiswa['NamaLengkap']);
        }

        // Mengembalikan data dalam format JSON
        echo json_encode([
            'data' => $data_mahasiswa_baru,
            'total_pages' => $total_pages,
            'current_page' => $page
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    } catch(PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Kesalahan saat mengambil data: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Mahasiswa Baru</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Font Awesome CSS untuk ikon pengurutan dan ikon edit -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Daftar Mahasiswa Baru</h2>

    <!-- Form Pencarian -->
    <form id="formPencarian" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label for="search_nama" class="mr-2">Nama Lengkap</label>
            <input type="text" name="search_nama" id="search_nama" class="form-control">
        </div>
        <div class="form-group mr-2">
            <label for="search_jurusan" class="mr-2">Jurusan</label>
            <input type="text" name="search_jurusan" id="search_jurusan" class="form-control">
        </div>
    </form>

    <!-- Notifikasi Sukses dan Error -->
    <!-- Modals akan ditampilkan melalui JavaScript -->

    <div id="dataMahasiswa">
        <!-- Tabel akan dimuat di sini melalui AJAX -->
    </div>

</div>

<!-- Bootstrap JS dan jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Popper.js dan Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

<!-- Script untuk Pencarian Real-time dan Penanganan Form AJAX -->
<script>
$(document).ready(function(){
    var current_page = 1;

    function load_data(query_nama = '', query_jurusan = '', page = 1) {
        $.ajax({
            url: "pindah_mahasiswa.php",
            method: "GET",
            data: {
                ajax: 1,
                search_nama: query_nama,
                search_jurusan: query_jurusan,
                page: page
            },
            dataType: "json",
            success: function(response) {
                var data = response.data;
                var total_pages = response.total_pages;
                current_page = response.current_page;

                $('#dataMahasiswa').html('');
                var no = (current_page - 1) * 10 + 1;
                var html = '<table class="table table-bordered">';
                html += '<thead>';
                html += '<tr>';
                html += '<th>No</th>';
                html += '<th>Nama Lengkap</th>';
                html += '<th>Jurusan</th>';
                html += '<th>Status</th>';
                html += '<th>Aksi</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                if(data.length > 0) {
                    $.each(data, function(index, mahasiswa) {
                        var nama_mahasiswa = mahasiswa.NamaLengkapJS;

                        html += '<tr>';
                        html += '<td>' + no++ + '</td>';
                        html += '<td>' + nama_mahasiswa + '</td>';
                        html += '<td>' + mahasiswa.Jurusan + '</td>';
                        html += '<td>';
                        if(mahasiswa.NIK_mahasiswa) {
                            html += '<span class="badge badge-success">Sudah Dipindahkan</span>';
                        } else {
                            html += '<span class="badge badge-warning">Belum Dipindahkan</span>';
                        }
                        html += '</td>';
                        html += '<td>';
                        if(!mahasiswa.NIK_mahasiswa) {
                            // Tombol Pindahkan
                            html += '<button class="btn btn-primary pindahkan-btn" data-no="' + mahasiswa.No + '" data-nama="' + mahasiswa.NamaLengkapJS + '">Pindahkan</button>';
                        } else {
                            // Tombol Edit NIM
                            html += '<button class="btn btn-secondary edit-nim-btn" data-no="' + mahasiswa.No + '" data-nama="' + mahasiswa.NamaLengkapJS + '" data-nim="' + mahasiswa.NIM_mahasiswa + '"><i class="fa fa-edit"></i> Edit NIM</button>';
                        }
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html += '<tr><td colspan="5">Tidak ada data ditemukan</td></tr>';
                }

                html += '</tbody>';
                html += '</table>';

                // Menambahkan navigasi pagination
                html += '<nav>';
                html += '<ul class="pagination justify-content-center">';
                if(current_page > 1) {
                    html += '<li class="page-item"><a class="page-link" href="#" data-page="' + (current_page - 1) + '">&laquo; Previous</a></li>';
                }
                for(var i = 1; i <= total_pages; i++) {
                    if(i == current_page) {
                        html += '<li class="page-item active"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
                    } else {
                        html += '<li class="page-item"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
                    }
                }
                if(current_page < total_pages) {
                    html += '<li class="page-item"><a class="page-link" href="#" data-page="' + (current_page + 1) + '">Next &raquo;</a></li>';
                }
                html += '</ul>';
                html += '</nav>';

                $('#dataMahasiswa').html(html);
            },
            error: function(xhr, status, error) {
                $('#dataMahasiswa').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat data.</div>');
            }
        });
    }

    // Panggil fungsi load_data saat halaman pertama kali dimuat
    load_data();

    // Event untuk input pencarian nama
    $('#search_nama').on('keyup', function(){
        var query_nama = $(this).val();
        var query_jurusan = $('#search_jurusan').val();
        load_data(query_nama, query_jurusan, 1);
    });

    // Event untuk input pencarian jurusan
    $('#search_jurusan').on('keyup', function(){
        var query_nama = $('#search_nama').val();
        var query_jurusan = $(this).val();
        load_data(query_nama, query_jurusan, 1);
    });

    // Event handler untuk pagination
    $(document).on('click', '.pagination a', function(e){
        e.preventDefault();
        var page = $(this).data('page');
        var query_nama = $('#search_nama').val();
        var query_jurusan = $('#search_jurusan').val();
        load_data(query_nama, query_jurusan, page);
    });

    // Delegated event handler untuk tombol Pindahkan
    $(document).on('click', '.pindahkan-btn', function(){
        var no_mahasiswa = $(this).data('no');
        var nama_mahasiswa = $(this).data('nama');

        // Escape karakter khusus dalam nama_mahasiswa
        var safe_nama_mahasiswa = $('<div>').text(nama_mahasiswa).html();

        var modal = '<div class="modal fade" id="modalInputNIM" tabindex="-1" role="dialog" aria-labelledby="modalInputNIMLabel" aria-hidden="true">';
        modal += '  <div class="modal-dialog" role="document">';
        modal += '    <div class="modal-content">';
        modal += '      <div class="modal-header">';
        modal += '        <h5 class="modal-title" id="modalInputNIMLabel">Input NIM Mahasiswa</h5>';
        modal += '        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">';
        modal += '          <span aria-hidden="true">&times;</span>';
        modal += '        </button>';
        modal += '      </div>';
        modal += '      <div class="modal-body">';
        modal += '        <p>Nama Mahasiswa: <strong>' + safe_nama_mahasiswa + '</strong></p>';
        modal += '        <div class="form-group">';
        modal += '          <label>NIM</label>';
        modal += '          <input type="text" name="nim" class="form-control" required pattern="^[0-9]{9}$" maxlength="9">';
        modal += '          <small class="form-text text-muted">Masukkan NIM (9 digit angka)</small>';
        modal += '        </div>';
        modal += '        <div class="form-group">';
        modal += '          <label>Masa</label>';
        modal += '          <input type="text" name="masa" class="form-control" value="2024.2" disabled>';
        modal += '        </div>';
        modal += '      </div>';
        modal += '      <div class="modal-footer">';
        modal += '        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>';
        modal += '        <button type="button" class="btn btn-primary submit-pindahkan" data-no="' + no_mahasiswa + '">Pindahkan</button>';
        modal += '      </div>';
        modal += '    </div>';
        modal += '  </div>';
        modal += '</div>';

        $('body').append(modal);
        $('#modalInputNIM').modal('show');
    });

    // Event handler untuk submit pindahkan
    $(document).on('click', '.submit-pindahkan', function(){
        var no_mahasiswa = $(this).data('no');
        var nim = $('#modalInputNIM').find('input[name="nim"]').val();

        $.ajax({
            url: 'pindah_mahasiswa.php',
            method: 'POST',
            data: {
                action: 'pindahkan',
                no_mahasiswa: no_mahasiswa,
                nim: nim
            },
            dataType: 'json',
            success: function(response) {
                $('#modalInputNIM').modal('hide');

                if(response.status == 'success') {
                    // Tampilkan notifikasi sukses dalam modal
                    showNotificationModal('Sukses', response.message);
                    // Muat ulang data
                    var query_nama = $('#search_nama').val();
                    var query_jurusan = $('#search_jurusan').val();
                    load_data(query_nama, query_jurusan, current_page);
                } else {
                    // Tampilkan notifikasi error dalam modal
                    showNotificationModal('Error', response.message);
                }
            },
            error: function(xhr, status, error) {
                $('#modalInputNIM').modal('hide');
                showNotificationModal('Error', 'Terjadi kesalahan saat memproses data: ' + error);
            }
        });
    });

    // Delegated event handler untuk tombol Edit NIM
    $(document).on('click', '.edit-nim-btn', function(){
        var no_mahasiswa = $(this).data('no');
        var nama_mahasiswa = $(this).data('nama');
        var nim_mahasiswa = $(this).data('nim');

        // Escape karakter khusus dalam nama_mahasiswa
        var safe_nama_mahasiswa = $('<div>').text(nama_mahasiswa).html();

        var modal = '<div class="modal fade" id="modalEditNIM" tabindex="-1" role="dialog" aria-labelledby="modalEditNIMLabel" aria-hidden="true">';
        modal += '  <div class="modal-dialog" role="document">';
        modal += '    <div class="modal-content">';
        modal += '      <div class="modal-header">';
        modal += '        <h5 class="modal-title" id="modalEditNIMLabel">Edit NIM Mahasiswa</h5>';
        modal += '        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">';
        modal += '          <span aria-hidden="true">&times;</span>';
        modal += '        </button>';
        modal += '      </div>';
        modal += '      <div class="modal-body">';
        modal += '        <p>Nama Mahasiswa: <strong>' + safe_nama_mahasiswa + '</strong></p>';
        modal += '        <div class="form-group">';
        modal += '          <label>NIM</label>';
        modal += '          <input type="text" name="nim" class="form-control" value="' + nim_mahasiswa + '" required pattern="^[0-9]{9}$" maxlength="9">';
        modal += '          <small class="form-text text-muted">Masukkan NIM baru (9 digit angka)</small>';
        modal += '        </div>';
        modal += '      </div>';
        modal += '      <div class="modal-footer">';
        modal += '        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>';
        modal += '        <button type="button" class="btn btn-primary submit-edit-nim" data-no="' + no_mahasiswa + '">Update NIM</button>';
        modal += '      </div>';
        modal += '    </div>';
        modal += '  </div>';
        modal += '</div>';

        $('body').append(modal);
        $('#modalEditNIM').modal('show');
    });

    // Event handler untuk submit edit NIM
    $(document).on('click', '.submit-edit-nim', function(){
        var no_mahasiswa = $(this).data('no');
        var nim = $('#modalEditNIM').find('input[name="nim"]').val();

        $.ajax({
            url: 'pindah_mahasiswa.php',
            method: 'POST',
            data: {
                action: 'update_nim',
                no_mahasiswa: no_mahasiswa,
                nim: nim
            },
            dataType: 'json',
            success: function(response) {
                $('#modalEditNIM').modal('hide');

                if(response.status == 'success') {
                    // Tampilkan notifikasi sukses dalam modal
                    showNotificationModal('Sukses', response.message);
                    // Muat ulang data
                    var query_nama = $('#search_nama').val();
                    var query_jurusan = $('#search_jurusan').val();
                    load_data(query_nama, query_jurusan, current_page);
                } else {
                    // Tampilkan notifikasi error dalam modal
                    showNotificationModal('Error', response.message);
                }
            },
            error: function(xhr, status, error) {
                $('#modalEditNIM').modal('hide');
                showNotificationModal('Error', 'Terjadi kesalahan saat memproses data: ' + error);
            }
        });
    });

    // Menghapus modal dari DOM setelah ditutup
    $('body').on('hidden.bs.modal', '.modal', function () {
        $(this).remove();
        // Hapus modal-backdrop jika masih ada
        $('.modal-backdrop').remove();
    });

    // Fungsi untuk menampilkan notifikasi dalam modal
    function showNotificationModal(title, message) {
        // Hapus modal notifikasi jika sudah ada
        $('#notificationModal').remove();

        var modal = '<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">';
        modal += '  <div class="modal-dialog" role="document">';
        modal += '    <div class="modal-content">';
        modal += '      <div class="modal-header">';
        modal += '        <h5 class="modal-title" id="notificationModalLabel">' + title + '</h5>';
        modal += '        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">';
        modal += '          <span aria-hidden="true">&times;</span>';
        modal += '        </button>';
        modal += '      </div>';
        modal += '      <div class="modal-body">';
        modal += '        <p>' + message + '</p>';
        modal += '      </div>';
        modal += '      <div class="modal-footer">';
        modal += '        <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>';
        modal += '      </div>';
        modal += '    </div>';
        modal += '  </div>';
        modal += '</div>';

        $('body').append(modal);
        $('#notificationModal').modal('show');

        // Hapus modal dan backdrop setelah ditutup
        $('#notificationModal').on('hidden.bs.modal', function () {
            $(this).remove();
            $('.modal-backdrop').remove();
        });
    }
});
</script>

</body>
</html>
