<?php
require_once('../koneksi.php'); // Pastikan ini ada di bagian atas file

// Query untuk mengambil data tugas
$result = mysqli_query($koneksi, "SELECT * FROM tugas JOIN admin ON tugas.admin_id = admin.id_admin");

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi)); // Menampilkan error jika query gagal
}
?>

<div class="row mb-3">
    <div class="col-md-12">
        <a href="index.php?tambah=1" class="btn btn-success">Tambah Tugas Baru</a>
    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        <h5 class="card-title">Daftar Tugas</h5>
        <?php
            if (mysqli_num_rows($result) > 0) {
                ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th> Judul Tugas </th>
                            <th> Deskripsi </th>
                            <th> Deadline </th>
                            <th> Assignee </th>
                            <th> Prioritas </th>
                            <th> Status </th>
                            <th> Tag Tugas </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr>
                                    <td><?= $row['judul_tugas'] ?></td>
                                    <td><?= substr($row['deskripsi'], 0, 50) ?>...</td>
                                    <td><?= $row['deadline'] ?></td>
                                    <td><?= $row['nama_lengkap'] ?></td>
                                    <td><span class="badge badge-<?= ($row['prioritas'] == 'tinggi' ? 'danger' : ($row['prioritas'] == 'sedang' ? 'warning' : 'success')) ?>">
                                        <?= ucfirst($row['prioritas']) ?>
                                    </span></td>
                                    <td><span class="badge badge-<?= $row['status'] == 'selesai' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span></td>
                                    <td><?= $row['tag_tugas'] ?></td>
                                </tr>
                            <?php }
                        ?>
                    </tbody>
                </table>
                <?php } else { ?>
                    <div class="alert alert-info">Tidak ada tugas yang ditemukan.</div>
                <?php } ?>
    </div>
</div>
