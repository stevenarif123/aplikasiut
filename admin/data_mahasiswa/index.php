<?php
// Session status check
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

// Connect to the database
require_once "../koneksi.php";

// Check for connection error
if (!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check get admin data
$username = $_SESSION['username'];
$query = "SELECT * FROM admin WHERE username='$username'";

$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);
if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Inisialisasi variabel
$keyword = "";
$search_column = "NamaLengkap";  // Default search column
$mahasiswa = [];

// Cek apakah ada keyword dari request
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$search_column = isset($_GET['search_column']) ? $_GET['search_column'] : '';

// Query untuk mencari data mahasiswa berdasarkan keyword
$query = "SELECT * FROM mahasiswa WHERE ";
if (!empty($keyword) && !empty($search_column)) {
    $query .= "$search_column LIKE '%" . mysqli_real_escape_string($koneksi, $keyword) . "%' ";
} else {
    $query .= "1 "; // Jika tidak ada keyword, tampilkan semua data
}
$query .= "ORDER BY No DESC";

$result = mysqli_query($koneksi, $query);
if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Simpan hasil pencarian ke dalam array
while ($row = mysqli_fetch_assoc($result)) {
    $mahasiswa[] = $row;
}

// Pagination
$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * (intval($limit) == 'all' ? count($mahasiswa) : intval($limit));
$total_pages = ceil(count($mahasiswa) / (intval($limit) == 'all' ? 1 : intval($limit)));
$mahasiswa = array_slice($mahasiswa, $start, intval($limit) == 'all' ? count($mahasiswa) : intval($limit));

if (isset($_GET['ajax'])) {
    include './api/results_partial.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Daftar Mahasiswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="https://coderthemes.com/ubold/layouts/aset/images/favicon.ico">

    <!-- App css -->
    <link href="../aset/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />
    <link href="../aset/css/app.min.css" rel="stylesheet" type="text/css" id="app-default-stylesheet" />

    <!-- icons -->
    <link href="../aset/css/icons.min.css" rel="stylesheet" type="text/css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        h1 {
            font-size: 2.5rem;
            color: #007bff;
        }

        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
        }

        #toast {
            transition: opacity 0.3s ease-in-out;
            right: 1rem;
            top: 1rem;
            position: fixed;
            opacity: 0;
        }

        .editable-field {
            display: inline-block;
            margin-right: 10px;
        }

        .editable-field input,
        .editable-field select {
            width: 100px;
        }

        .edit-mode {
            display: none;
        }

        .save-button {
            margin-left: 10px;
            display: none;
        }

        .save-button.active {
            display: inline-block;
        }

        .edit-icon,
        .copy-icon {
            cursor: pointer;
            margin-left: 5px;
        }

        .edit-mode.active {
            display: inline-block;
            margin-left: 5px;
        }

        .accordion-body .edit-mode,
        .accordion-body .edit-mode input,
        .accordion-body .edit-mode select,
        .accordion-body .edit-mode span {
            display: none;
        }

        .pagination {
            flex-wrap: wrap;
            /* Membuat pagination membungkus ke baris berikutnya */
        }

        .page-item {
            margin: 0 0 2px;
            /* Menambahkan margin antar tombol */
        }
    </style>
</head>

<body class="loading">

    <!-- Begin page -->
    <div id="wrapper">

        <!-- Topbar Start -->
        <div class="navbar-custom">
            <div class="container-fluid">
                <ul class="list-unstyled topnav-menu float-right mb-0">

                    <li class="dropdown d-inline-block d-lg-none">
                        <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="fe-search noti-icon"></i>
                        </a>
                        <div class="dropdown-menu dropdown-lg dropdown-menu-right p-0">
                            <form class="p-3">
                                <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                            </form>
                        </div>
                    </li>

                    <li class="dropdown d-none d-lg-inline-block">
                        <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-toggle="fullscreen" href="#">
                            <i class="fe-maximize noti-icon"></i>
                        </a>
                    </li>
                    <li class="dropdown notification-list topbar-dropdown">
                        <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <img src="../aset/images/users/user-1.jpg" alt="user-image" class="rounded-circle">
                            <span class="pro-user-name ml-1">
                                Geneva <i class="mdi mdi-chevron-down"></i>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                            <!-- item-->
                            <div class="dropdown-header noti-title">
                                <h6 class="text-overflow m-0">Welcome !</h6>
                            </div>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="fe-user"></i>
                                <span>My Account</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="fe-settings"></i>
                                <span>Settings</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="fe-lock"></i>
                                <span>Lock Screen</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="fe-log-out"></i>
                                <span>Logout</span>
                            </a>

                        </div>
                    </li>

                    <li class="dropdown notification-list">
                        <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect waves-light">
                            <i class="fe-settings noti-icon"></i>
                        </a>
                    </li>

                </ul>

                <!-- LOGO -->
                <div class="logo-box">
                    <a href="index.html" class="logo logo-dark text-center">
                        <span class="logo-sm">
                            <img src="../aset/images/logo-sm.png" alt="" height="22">
                            <!-- <span class="logo-lg-text-light">UBold</span> -->
                        </span>
                        <span class="logo-lg">
                            <img src="../aset/images/logo-dark.png" alt="" height="20">
                            <!-- <span class="logo-lg-text-light">U</span> -->
                        </span>
                    </a>

                    <a href="index.html" class="logo logo-light text-center">
                        <span class="logo-sm">
                            <img src="../aset/images/logo-sm.png" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="../aset/images/logo-light.png" alt="" height="20">
                        </span>
                    </a>
                </div>

                <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                    <li>
                        <button class="button-menu-mobile waves-effect waves-light">
                            <i class="fe-menu"></i>
                        </button>
                    </li>

                    <li>
                        <!-- Mobile menu toggle (Horizontal Layout)-->
                        <a class="navbar-toggle nav-link" data-toggle="collapse" data-target="#topnav-menu-content">
                            <div class="lines">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </a>
                        <!-- End mobile menu toggle-->
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
        </div>
        <!-- end Topbar -->

        <!-- ========== Left Sidebar Start ========== -->
        <div class="left-side-menu">

            <div class="h-100" data-simplebar>

                <!-- User box -->
                <div class="user-box text-center">
                    <img src="../aset/images/users/user-1.jpg" alt="user-img" title="Mat Helme" class="rounded-circle avatar-md">
                    <div class="dropdown">
                        <a href="javascript: void(0);" class="text-dark dropdown-toggle h5 mt-2 mb-1 d-block" data-toggle="dropdown">Geneva Kennedy</a>
                        <div class="dropdown-menu user-pro-dropdown">

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="fe-user mr-1"></i>
                                <span>My Account</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="fe-settings mr-1"></i>
                                <span>Settings</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="fe-lock mr-1"></i>
                                <span>Lock Screen</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="fe-log-out mr-1"></i>
                                <span>Logout</span>
                            </a>

                        </div>
                    </div>
                    <p class="text-muted">Admin Head</p>
                </div>

                <!--- Sidemenu -->
                <div id="sidebar-menu">

                    <ul id="side-menu">
                        <li class="menu-title">Menu</li>
                        <li>
                            <a href="../">
                                <i data-feather="airplay"></i>
                                <span> Dashboard </span>
                            </a>
                        </li>
                        <li>
                            <a href="#sidebarmhs" data-toggle="collapse">
                                <i data-feather="users"></i>
                                <span>Mahasiswa</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarmhs">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="../data_mahasiswa/">Data Mahasiswa</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <a href="#sidebarMaba" data-toggle="collapse">
                                <i data-feather="user-plus"></i>
                                <span>Mahasiswa Baru</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarMaba">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="../mahasiswa_baru/dashboard.php">Dashboard</a>
                                    </li>
                                    <li>
                                        <a href="../mahasiswa_baru/daftarmahasiswa.php">Pendaftaran</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li>
                            <a href="#sidebarlapkeu" data-toggle="collapse">
                                <i class="icon-credit-card"></i>
                                <span>Laporan Pembayaran</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarlapkeu">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="../laporan_pembayaran/index.php">Daftar Laporan</a>
                                    </li>
                                    <li>
                                        <a href="../laporan_pembayaran/laporanbayarmaba.php">Laporan Maba</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li>
                            <a href="#sidebarTagihan" data-toggle="collapse">
                                <i class="icon-tag"></i>
                                <span>Data Tagihan</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarTagihan">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="../data_tagihan/index.php">Data Tagihan</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li>
                            <a href="#sidebarCekStatus" data-toggle="collapse">
                                <i class="icon-check"></i>
                                <span>Cek Status</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarCekStatus">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="../cek_status/pilihmasa.php">Cek Status</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li>
                            <a href="#sidebarAlatAdmin" data-toggle="collapse">
                                <i class="icon-wrench"></i>
                                <span>Alat Admin</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarAlatAdmin">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="../alat_admin/backup_database.php">Backup Database</a>
                                    </li>
                                    <li>
                                        <a href="../alat_admin/daftaradmin.php">Daftar Admin</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li>
                            <a href="#sidebarAset" data-toggle="collapse">
                                <i class="icon-layers"></i>
                                <span>Aset</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarAset">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="../aset/css/styles.css">CSS</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>

                </div>
                </li>
                </ul>

            </div>
            <!-- End Sidebar -->

            <div class="clearfix"></div>

        </div>
        <!-- Sidebar -left -->

    </div>
    <!-- Left Sidebar End -->

    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->

    <div class="content-page">
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">

                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">SALUT TATOR</a></li>
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">DATA MAHASISWA</a></li>
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                            </div>
                            <h4 class="page-title">DATA MAHASISWA</h4>
                            <div class="mb-4">
                                <a href="tambah_data.php" class="btn btn-primary me-2">Tambah Data</a>
                            </div>

                            <form id="searchForm" action="index.php" method="get" class="mb-4">
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="search_column" class="form-label fw-bold">Cari Berdasarkan:</label>
                                        <select id="search_column" name="search_column" class="form-control">
                                            <option value="NamaLengkap">Nama Lengkap</option>
                                            <option value="Jurusan">Jurusan</option>
                                            <option value="STATUS_INPUT_SIA">Status Input SIA</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label for="keyword" class="form-label fw-bold">Keyword:</label>
                                        <input type="text" id="keyword" name="keyword" placeholder="Masukkan Keyword" class="form-control" onkeyup="updateResults()">
                                    </div>
                                    <div class="col align-self-end">
                                        <button type="submit" name="search" class="btn btn-primary w-100 d-none">Cari</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-2">
                                        <label for="limit" class="form-label fw-bold">Tampilkan:</label>
                                        <select id="limit" name="limit" class="form-control">
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="all">Semua</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                            <!-- Edit modal content -->
                            <div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="text-center mt-2 mb-4">
                                                <h5>Edit Data</h5>
                                            </div>
                                            <form id="editForm" class="px-3">
                                                <input type="hidden" id="edit-no">

                                                <div class="form-group">
                                                    <label for="edit-namalengkap">Nama Lengkap</label>
                                                    <input class="form-control" type="text" id="edit-namalengkap" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="edit-nomorhp">Nomor HP</label>
                                                    <input class="form-control" type="text" id="edit-nomorhp" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="edit-email">Email</label>
                                                    <input class="form-control" type="email" id="edit-email" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="edit-password">Password</label>
                                                    <input class="form-control" type="text" id="edit-password" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="edit-statussia">Status Input SIA</label>
                                                    <select class="form-control" id="edit-statussia" required>
                                                        <option value="Belum Terdaftar">Belum Terdaftar</option>
                                                        <option value="Input Admisi">Input Admisi</option>
                                                        <option value="Pengajuan Admisi">Pengajuan Admisi</option>
                                                        <option value="Berkas Kurang">Berkas Kurang</option>
                                                        <option value="Admisi Diterima">Admisi Diterima</option>
                                                        <option value="Menunggu SPP">Menunggu SPP</option>
                                                        <option value="MAHASISWA UT">MAHASISWA UT</option>
                                                    </select>
                                                </div>

                                                <div class="form-group text-center">
                                                    <button class="btn btn-primary" type="submit">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                            <div id="results">
                                <?php include './api/results_partial.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page title -->
            </div> <!-- container -->

        </div> <!-- content -->

        <!-- Footer Start -->
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        2015 - <script>
                            document.write(new Date().getFullYear())
                        </script> &copy; UBold theme by <a href="#">Coderthemes</a>
                    </div>
                    <div class="col-md-6">
                        <div class="text-md-right footer-links d-none d-sm-block">
                            <a href="javascript:void(0);">About Us</a>
                            <a href="javascript:void(0);">Help</a>
                            <a href="javascript:void(0);">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->

    </div>

    <!-- ============================================================== -->
    <!-- End Page content -->
    <!-- ============================================================== -->


    </div>
    <!-- END wrapper -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- Vendor js -->
    <script src="../aset/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="../aset/js/app.min.js"></script>
    <!-- Include jQuery -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updateCopyButtons();
            updateAccordion();
            updateStatusPembayaran();
        });

        function updateCopyButtons() {
            const copyButtons = document.querySelectorAll('.copy-icon');
            copyButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const textToCopy = this.previousElementSibling.textContent.trim();
                    copyToClipboard(textToCopy);
                });
            });
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('Teks berhasil disalin!');
            }).catch((err) => {
                showToast('Gagal menyalin teks', true);
                console.error('Gagal menyalin teks: ', err);
            });
        }

        function confirmDelete(no) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                window.location.href = './api/hapus_data_mahasiswa.php?No=' + no;
            }
        }

        function updateResults() {
            const keyword = document.getElementById('keyword').value;
            const search_column = document.getElementById('search_column').value;
            const limit = document.getElementById('limit').value;

            const xhr = new XMLHttpRequest();
            xhr.open('GET', `index.php?ajax=1&keyword=${keyword}&search_column=${search_column}&limit=${limit}`, true);
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById('results').innerHTML = this.responseText;
                    updateCopyButtons();
                    updateAccordion();
                    updateStatusPembayaran(); // Tambahkan ini untuk memperbarui status pembayaran setelah konten dimuat
                }
            };
            xhr.send();
        }

        $(document).ready(function() {
            updateCopyButtons(); // Make sure to call this to initialize the copy buttons

            // Trigger modal with data
            $(document).on('click', '.edit-btn', function() {
                var no = $(this).data('no');

                $.ajax({
                    url: './api/ambil_data_mahasiswa.php', // Pastikan path ini benar
                    method: 'POST',
                    data: {
                        No: no
                    },
                    success: function(data) {
                        try {
                            var user = JSON.parse(data);
                            if (user) {
                                // Ensure all elements are in user before accessing them
                                $('#edit-no').val(user.No);
                                $('#edit-namalengkap').val(user.NamaLengkap);
                                $('#edit-nomorhp').val(user.NomorHP);
                                $('#edit-email').val(user.Email);
                                $('#edit-password').val(user.Password);
                                $('#edit-statussia').val(user.STATUS_INPUT_SIA);
                                $('#editModal').modal('show'); // Show modal after data is loaded
                            } else {
                                console.error('No user data returned');
                            }
                        } catch (e) {
                            console.error("Error parsing JSON!", e);
                            console.log("Raw response:", data); // Tambahkan ini untuk menampilkan respons mentah
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                    }
                });
            });

            // Handle form submission
            $('#editForm').on('submit', function(e) {
                e.preventDefault();

                var no = $('#edit-no').val();
                var namalengkap = $('#edit-namalengkap').val();
                var nomorhp = $('#edit-nomorhp').val();
                var email = $('#edit-email').val();
                var password = $('#edit-password').val();
                var statussia = $('#edit-statussia').val();

                saveData(no, namalengkap, nomorhp, email, password, statussia);
            });

            function saveData(no, namalengkap, nomorhp, email, password, statussia) {
                const data = {
                    No: no,
                    NamaLengkap: namalengkap,
                    NomorHP: nomorhp,
                    Email: email,
                    Password: password,
                    STATUS_INPUT_SIA: statussia
                };

                $.ajax({
                    url: './api/simpan_data.php',
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        if (response === 'success') {
                            showToast('Data berhasil disimpan!');
                            $('#editModal').modal('hide');
                            location.reload(); // Optionally refresh the page to show changes
                        } else {
                            showToast('Gagal menyimpan data: ' + response, true);
                        }
                    },
                    error: function() {
                        showToast('Gagal menyimpan data', true);
                    }
                });
            }

            function showToast(message, isError = false) {
                const toast = $('#toast');
                toast.text(message);
                toast.removeClass('opacity-0 bg-success bg-danger');
                toast.addClass(isError ? 'bg-danger' : 'bg-success');
                toast.addClass('opacity-100');

                setTimeout(() => {
                    toast.removeClass('opacity-100');
                    toast.addClass('opacity-0');
                }, 3000);
            }

            function updateStatusPembayaran() {
                $('.status-pembayaran').each(function() {
                    var nim = $(this).data('nim');
                    var nama = $(this).data('nama');
                    var identifier = nim ? nim : nama;

                    $.ajax({
                        url: 'api/get_saldo.php', // Pastikan path ini benar
                        method: 'POST',
                        data: {
                            identifier: identifier
                        },
                        success: function(response) {
                            var saldoData;
                            try {
                                saldoData = JSON.parse(response);
                            } catch (e) {
                                console.error("Error parsing JSON!", e);
                                console.log("Raw response:", response); // Tambahkan ini untuk menampilkan respons mentah
                                return;
                            }
                            var statusPembayaranElement = $('.status-pembayaran[data-id="' + identifier + '"]');
                            if (saldoData && !saldoData.error) {
                                var statusText = saldoData.isLunas ? 'Lunas' : 'Belum Lunas';
                                var statusColor = saldoData.isLunas ? 'text-success' : 'text-danger';
                                statusPembayaranElement.html(`<p class="${statusColor}"> ${statusText}</p>`);
                            } else {
                                statusPembayaranElement.html('<p>Data saldo tidak ditemukan</p>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX error:", status, error);
                            $('.status-pembayaran[data-id="' + identifier + '"]').html('<p>Terjadi kesalahan saat mengambil data saldo</p>');
                        }
                    });
                });
            }

            $(document).ready(function() {
                updateStatusPembayaran(); // Pastikan untuk memanggil fungsi ini setelah dokumen siap
            });


        });
    </script>
</body>

<!-- Mirrored from coderthemes.com/ubold/layouts/default/pages-starter.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 10 Sep 2020 17:26:43 GMT -->

</html>