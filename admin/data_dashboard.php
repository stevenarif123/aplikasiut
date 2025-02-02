<?php
// Start session if it hasn't already started
include 'koneksi.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ambil admin_id dari sesi
$admin_id = $_SESSION['id_admin'];

// Query untuk mengambil tugas berdasarkan admin_id
$query = "SELECT * FROM tugas WHERE admin_id = '$admin_id'";
$result = mysqli_query($koneksi, $query);

?>
<style>
    .card {
        margin-bottom: 20px;
        cursor: pointer;
    }

    .card-details {
        display: none;
        /* Sembunyikan detail awalnya */
    }
</style>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Total Data Mahasiswa
                    </div>
                    <div class="card-body">
                        <?php
                        // Menghubungkan ke database
                        require_once 'koneksi.php';

                        // Query untuk mengambil total data mahasiswa
                        $query = "SELECT COUNT(*) AS total_mahasiswa FROM mahasiswa";
                        $result = mysqli_query($koneksi, $query);

                        // Mengambil hasil query
                        if ($result) {
                            $row = mysqli_fetch_assoc($result);
                            $totalMahasiswa = $row['total_mahasiswa'];
                        } else {
                            $totalMahasiswa = 0;
                        }
                        ?>
                        <h5 class="card-title"><?php echo $totalMahasiswa; ?></h5>
                        <p class="card-text">Mahasiswa</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Total Data Mahasiswa Baru
                    </div>
                    <div class="card-body">
                        <?php
                        // Menghubungkan ke database
                        require_once 'koneksi.php';

                        // Query untuk mengambil total data mahasiswa baru
                        $query = "SELECT COUNT(*) AS total_mahasiswa FROM mahasiswabaru20242";
                        $result = mysqli_query($koneksi, $query);

                        // Mengambil hasil query
                        if ($result) {
                            $row = mysqli_fetch_assoc($result);
                            $totalMahasiswa = $row['total_mahasiswa'];
                        } else {
                            $totalMahasiswa = 0;
                        }
                        ?>
                        <h5 class="card-title"><?php echo $totalMahasiswa; ?></h5>
                        <p class="card-text">Mahasiswa Baru</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Total Admin
                    </div>
                    <div class="card-body">
                        <?php
                        // Query untuk mengambil total data admin
                        $query = "SELECT COUNT(*) AS total_admin FROM admin";
                        $result = mysqli_query($koneksi, $query);

                        // Mengambil hasil query
                        if ($result) {
                            $row = mysqli_fetch_assoc($result);
                            $totalAdmin = $row['total_admin'];
                        } else {
                            $totalAdmin = 0;
                        }
                        ?>
                        <h5 class="card-title"><?php echo $totalAdmin; ?></h5>
                        <p class="card-text">Admin</p>
                    </div>
                </div>
            </div>
            <div class="container mt-5">
    <h2>Daftar Tugas</h2>
    <div class="row">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="col-md-4">
                    <div class="card" onclick="toggleDetails(this)">
                        <div class="card-header">
                            <?= htmlspecialchars($row['judul_tugas']) ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Prioritas: <?= ucfirst($row['prioritas']) ?></h5>
                            <p class="card-text">Deadline: <?= htmlspecialchars($row['deadline']) ?></p>
                            <div class="card-details">
                                <p><strong>Deskripsi:</strong> <?= htmlspecialchars($row['deskripsi']) ?></p>
                                <p><strong>Status:</strong> <?= ucfirst($row['status']) ?></p>
                                <p><strong>Tag Tugas:</strong> <?= htmlspecialchars($row['tag_tugas']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="alert alert-info">Tidak ada tugas yang ditemukan.</div>';
        }
        ?>
    </div>
</div>

        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function toggleDetails(card) {
            const details = card.querySelector('.card-details');
            if (details.style.display === "none") {
                details.style.display = "block"; // Tampilkan detail
            } else {
                details.style.display = "none"; // Sembunyikan detail
            }
        }
    </script>
</body>