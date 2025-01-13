<?php
// Path ke file JSON
$json_file = 'status_pengambilan.json';

// Cek apakah file JSON ada, jika tidak buat file baru
if (!file_exists($json_file)) {
    file_put_contents($json_file, '{}');
}

// Baca data status pengambilan dari file JSON
$status_pengambilan = json_decode(file_get_contents($json_file), true);

// Koneksi ke database
require_once "../koneksi.php";

// Query untuk mengambil data mahasiswa yang memenuhi syarat
$query = "
    SELECT catatan.nama_lengkap, catatan.almamater, catatan.status_admisi, 
           catatan.sisa, mahasiswa.UkuranBaju
    FROM catatan_bayarmaba20242 AS catatan
    JOIN mahasiswabaru20242 AS mahasiswa
    ON catatan.nama_lengkap = mahasiswa.NamaLengkap
    WHERE catatan.almamater = 200000 
    AND catatan.status_admisi = 'lunas' 
    AND catatan.sisa >= 0
";

$result = $koneksi->query($query);

// Cek apakah query berhasil dieksekusi
if (!$result) {
    die("Query error: " . $koneksi->error);
}

// Buat array untuk menghitung ukuran baju
$size_counts = ['S' => 0, 'M' => 0, 'L' => 0, 'XL' => 0, 'XXL' => 0];

// Inisialisasi counter untuk baju yang belum diambil
$belum_diambil = 0;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Status Pengambilan Baju Almamater</title>
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Tambahkan script dan stylesheet lainnya jika diperlukan -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- ... (script lainnya) ... -->
</head>
<body>
<div class="container">
    <h2 class="my-4">Daftar Mahasiswa Pemesan Baju Almamater</h2>
    <form method="POST" action="simpan_status.php">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Ukuran Baju</th>
                    <th>Aksi</th>
                    <th>Sudah Diambil</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        $nama_lengkap = stripslashes($row['nama_lengkap']);
                        $ukuran_baju = $row['UkuranBaju'];
                        $size_counts[$ukuran_baju]++;
                        
                        // Cek status pengambilan
                        $sudah_diambil = isset($status_pengambilan[$nama_lengkap]) 
                            ? $status_pengambilan[$nama_lengkap] 
                            : false;
                        
                        if (!$sudah_diambil) {
                            $belum_diambil++;
                        }

                        echo "<tr>";
                        echo "<td>" . $no . "</td>";
                        echo "<td>" . htmlspecialchars($nama_lengkap) . "</td>";
                        echo "<td id='ukuran-" . $no . "'>" . htmlspecialchars($ukuran_baju) . "</td>";
                        echo "<td><button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#editModal' data-nama='" . htmlspecialchars($nama_lengkap, ENT_QUOTES) . "' data-ukuran='" . htmlspecialchars($ukuran_baju, ENT_QUOTES) . "' data-id='" . $no . "'>Edit</button></td>";
                        echo "<td>";
                        echo "<input type='checkbox' name='status_pengambilan[" 
                             . htmlspecialchars($nama_lengkap, ENT_QUOTES) . "]' "
                             . ($sudah_diambil ? "checked" : "") . ">";
                        echo "</td>";
                        echo "</tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='5'>Tidak ada data yang sesuai.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Simpan Status</button>
    </form>

    <h4>Ringkasan Ukuran Baju</h4>
    <ul>
        <?php
        foreach ($size_counts as $size => $count) {
            echo "<li>Ukuran $size: $count</li>";
        }
        ?>
    </ul>
    <p>Baju yang belum diambil: <?php echo $belum_diambil; ?></p>
</div>

<!-- Modal untuk edit ukuran baju -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <!-- ... (modal content seperti sebelumnya) ... -->
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Header modal -->
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Ukuran Baju</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Body modal -->
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="studentId">
                    <div class="form-group">
                        <label for="namaLengkap">Nama Lengkap</label>
                        <input type="text" class="form-control" id="namaLengkap" readonly>
                    </div>
                    <div class="form-group">
                        <label for="ukuranBaju">Ukuran Baju</label>
                        <select class="form-control" id="ukuranBaju">
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                            <option value="XXL">XXL</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="simpanPerubahan()">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$('#editModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var nama = button.data('nama');
    var ukuran = button.data('ukuran');
    var id = button.data('id');

    var modal = $(this);
    modal.find('#namaLengkap').val(nama);
    modal.find('#ukuranBaju').val(ukuran);
    modal.find('#studentId').val(id);
});

function simpanPerubahan() {
    var ukuranBaru = document.getElementById('ukuranBaju').value;
    var id = document.getElementById('studentId').value;
    var namaLengkap = document.getElementById('namaLengkap').value;

    // Update ukuran baju di tampilan
    var element = document.getElementById('ukuran-' + id);
    if (element) {
        element.innerHTML = ukuranBaru;

        // Kirim data ke server melalui Ajax
        $.ajax({
            url: 'update_ukuran.php',
            type: 'POST',
            data: {
                nama_lengkap: namaLengkap,
                ukuran_baru: ukuranBaru
            },
            success: function(response) {
                console.log('Data berhasil diperbarui di database: ' + response);
            },
            error: function(xhr, status, error) {
                console.error('Gagal memperbarui database: ' + error);
            }
        });
    } else {
        console.log('Element dengan ID ukuran-' + id + ' tidak ditemukan.');
    }

    // Tutup modal
    $('#editModal').modal('hide');
}
</script>

</body>
</html>

<?php
$koneksi->close();
?>
