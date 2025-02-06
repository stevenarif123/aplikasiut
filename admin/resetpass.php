<?php
include_once "koneksi.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>RESET PASSWORD ECAMPUS</h2>
    <input type="text" id="search" class="form-control mb-3" placeholder="Cari berdasarkan NIM atau Nama">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="mahasiswaTable">
            <?php
            include_once 'koneksi.php';

            $sql = "SELECT NIM, NamaLengkap, TanggalLahir, NamaIbuKandung FROM mahasiswa";
            $result = $koneksi->query($sql);
            $no = 1;

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $namaLengkap = stripslashes($row['NamaLengkap']);
                    $namaIbuKandung = stripslashes($row['NamaIbuKandung']);
                    
                    echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['NIM']}</td>
                            <td>{$namaLengkap}</td>
                            <td>
                                <button class='btn btn-primary' onclick='resetPassword(
                                    \"{$row['NIM']}\", 
                                    \"".htmlspecialchars($namaLengkap, ENT_QUOTES)."\", 
                                    \"{$row['TanggalLahir']}\", 
                                    \"".htmlspecialchars($namaIbuKandung, ENT_QUOTES)."\"
                                )'>Reset Password</button>
                            </td>
                          </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='4'>Tidak ada data</td></tr>";
            }
            $koneksi->close();
            ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultModalLabel">Hasil Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Hasil cURL akan ditampilkan di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#search').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#mahasiswaTable tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

function resetPassword(nim, name, tanggalLahir, namaIbuKandung) {
    $.ajax({
        url: 'reset_password.php',
        type: 'POST',
        data: {
            nim: nim,
            name: name,
            tanggalLahir: tanggalLahir,
            namaIbuKandung: namaIbuKandung
        },
        success: function(response) {
            $('#modalBody').html(response);
            $('#resultModal').modal('show');
        },
        error: function(xhr, status, error) {
            $('#modalBody').html('<p class="text-danger">Terjadi kesalahan jaringan</p>');
            $('#resultModal').modal('show');
        }
    });
}
</script>
</body>
</html>
