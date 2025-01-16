<?php
// Start session if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: ./halaman/login.html");
    exit;
}
?>

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
                        // Menghubungkan ke database
                        require_once 'koneksi.php';

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
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
