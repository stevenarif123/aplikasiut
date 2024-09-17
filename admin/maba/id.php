<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "datamahasiswa";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    // Set error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

    // Jika form submit untuk memindahkan mahasiswa
    if(isset($_POST['pindahkan'])) {
        $no_mahasiswa = $_POST['no_mahasiswa'];
        $nim = $_POST['nim'];

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
                // Jika NIK sudah ada, tampilkan pesan error
                header("Location: pindah_mahasiswa.php?error=nik_duplicate");
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
                `JenisSekolah`, `NamaSekolah`
            ) VALUES (
                NULL, :JalurProgram, :ID, :NIM, :NamaLengkap, :TempatLahir, :TanggalLahir,
                :NamaIbuKandung, :NIK, :Jurusan, :NomorHP, :Email, :Password,
                :Agama, :JenisKelamin, :StatusPerkawinan, :NomorHPAlternatif,
                :NomorIjazah, :TahunIjazah, :NISN, :Alamat, :LayananPaketSemester,
                :DiInputOleh, :DiInputPada, :DiEditPada, :STATUS_INPUT_SIA,
                :UkuranBaju, :AsalKampus, :TahunLulusKampus, :IPK, :JurusanSMK,
                :JenisSekolah, :NamaSekolah
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

            // Eksekusi insert
            try {
                $stmt_insert->execute();
                // Redirect dengan pesan sukses
                header("Location: pindah_mahasiswa.php?sukses=1");
                exit();
            } catch (PDOException $e) {
                // Jika terjadi error (misalnya duplikasi ID atau NIK)
                if ($e->getCode() == 23000) { // Integrity constraint violation
                    header("Location: pindah_mahasiswa.php?error=duplicate_entry");
                    exit();
                } else {
                    throw $e;
                }
            }
        }
    }

    // Pengaturan Pagination
    $records_per_page = 10; // Jumlah data per halaman
    $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($current_page - 1) * $records_per_page;

    // Mendapatkan total data
    $sql_count = "SELECT COUNT(*) FROM mahasiswabaru20242 WHERE STATUS_INPUT_SIA = 'MAHASISWA UT'";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->execute();
    $total_records = $stmt_count->fetchColumn();

    $total_pages = ceil($total_records / $records_per_page);

    // Ambil data mahasiswa baru dengan status dan pagination
    $sql_mahasiswa_baru = "SELECT mb.*, m.NIK AS NIK_mahasiswa
        FROM mahasiswabaru20242 mb
        LEFT JOIN mahasiswa m ON mb.NIK = m.NIK
        WHERE mb.STATUS_INPUT_SIA = 'MAHASISWA UT'
        LIMIT :limit OFFSET :offset";
    $stmt_mahasiswa_baru = $conn->prepare($sql_mahasiswa_baru);
    $stmt_mahasiswa_baru->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
    $stmt_mahasiswa_baru->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt_mahasiswa_baru->execute();
    $data_mahasiswa_baru = $stmt_mahasiswa_baru->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Koneksi atau query bermasalah: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Mahasiswa Baru</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Daftar Mahasiswa Baru</h2>
    <?php if(isset($_GET['sukses'])): ?>
        <!-- Modal Sukses -->
        <div class="modal fade" id="modalSukses" tabindex="-1" role="dialog" aria-labelledby="modalSuksesLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalSuksesLabel">Sukses</h5>
              </div>
              <div class="modal-body">
                Proses perpindahan telah sukses.
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
              </div>
            </div>
          </div>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['error']) && $_GET['error'] == 'duplicate_entry'): ?>
        <!-- Modal Error -->
        <div class="modal fade" id="modalError" tabindex="-1" role="dialog" aria-labelledby="modalErrorLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalErrorLabel">Error</h5>
              </div>
              <div class="modal-body">
                Terjadi duplikasi data (ID atau NIK sudah ada).
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
              </div>
            </div>
          </div>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['error']) && $_GET['error'] == 'nik_duplicate'): ?>
        <!-- Modal Error NIK -->
        <div class="modal fade" id="modalErrorNIK" tabindex="-1" role="dialog" aria-labelledby="modalErrorNIKLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalErrorNIKLabel">Error</h5>
              </div>
              <div class="modal-body">
                NIK sudah terdaftar di tabel mahasiswa.
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
              </div>
            </div>
          </div>
        </div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Jurusan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = $offset + 1; ?>
            <?php foreach($data_mahasiswa_baru as $mahasiswa): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($mahasiswa['NamaLengkap']); ?></td>
                    <td><?= htmlspecialchars($mahasiswa['Jurusan']); ?></td>
                    <td>
                        <?php if($mahasiswa['NIK_mahasiswa']): ?>
                            <span class="badge badge-success">Sudah Dipindahkan</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Belum Dipindahkan</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if(!$mahasiswa['NIK_mahasiswa']): ?>
                            <!-- Tombol untuk membuka modal input NIM -->
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalInputNIM<?= $mahasiswa['No']; ?>">Pindahkan</button>
                        <?php else: ?>
                            <!-- Jika sudah dipindahkan, tombol dinonaktifkan -->
                            <button class="btn btn-secondary" disabled>Selesai</button>
                        <?php endif; ?>

                        <!-- Modal Input NIM -->
                        <?php if(!$mahasiswa['NIK_mahasiswa']): ?>
                            <div class="modal fade" id="modalInputNIM<?= $mahasiswa['No']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalInputNIMLabel<?= $mahasiswa['No']; ?>" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <form method="post" action="pindah_mahasiswa.php">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="modalInputNIMLabel<?= $mahasiswa['No']; ?>">Input NIM Mahasiswa</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                          <span aria-hidden="true">&times;</span>
                                        </button>
                                      </div>
                                      <div class="modal-body">
                                          <div class="form-group">
                                              <label>NIM</label>
                                              <input type="text" name="nim" class="form-control" required>
                                          </div>
                                          <input type="hidden" name="no_mahasiswa" value="<?= $mahasiswa['No']; ?>">
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" name="pindahkan" class="btn btn-primary">Pindahkan</button>
                                      </div>
                                    </div>
                                </form>
                              </div>
                            </div>
                        <?php endif; ?>
                        <!-- Akhir Modal Input NIM -->
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <nav>
      <ul class="pagination">
        <?php if($current_page > 1): ?>
          <li class="page-item">
            <a class="page-link" href="pindah_mahasiswa.php?page=<?= $current_page -1 ?>" aria-label="Previous">
              <span aria-hidden="true">&laquo; Previous</span>
            </a>
          </li>
        <?php endif; ?>
        
        <?php for($page = 1; $page <= $total_pages; $page++): ?>
          <li class="page-item <?= ($page == $current_page) ? 'active' : ''; ?>">
            <a class="page-link" href="pindah_mahasiswa.php?page=<?= $page; ?>"><?= $page; ?></a>
          </li>
        <?php endfor; ?>
        
        <?php if($current_page < $total_pages): ?>
          <li class="page-item">
            <a class="page-link" href="pindah_mahasiswa.php?page=<?= $current_page +1 ?>" aria-label="Next">
              <span aria-hidden="true">Next &raquo;</span>
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>

</div>

<!-- Bootstrap JS dan jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Popper.js dan Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

<?php if(isset($_GET['sukses'])): ?>
<script>
    $(document).ready(function(){
        $('#modalSukses').modal('show');
    });
</script>
<?php endif; ?>

<?php if(isset($_GET['error']) && $_GET['error'] == 'duplicate_entry'): ?>
<script>
    $(document).ready(function(){
        $('#modalError').modal('show');
    });
</script>
<?php endif; ?>

<?php if(isset($_GET['error']) && $_GET['error'] == 'nik_duplicate'): ?>
<script>
    $(document).ready(function(){
        $('#modalErrorNIK').modal('show');
    });
</script>
<?php endif; ?>

</body>
</html>
