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
$query = "SELECT * FROM mahasiswabaru20242 WHERE ";
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
    <link rel="shortcut icon" href="https://coderthemes.com/ubold/layouts/assets/images/favicon.ico">

    <!-- App css -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />
    <link href="../assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-default-stylesheet" />

    <!-- icons -->
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
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
                            <img src="../assets/images/users/user-1.jpg" alt="user-image" class="rounded-circle">
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
                            <img src="../assets/images/logo-sm.png" alt="" height="22">
                            <!-- <span class="logo-lg-text-light">UBold</span> -->
                        </span>
                        <span class="logo-lg">
                            <img src="../assets/images/logo-dark.png" alt="" height="20">
                            <!-- <span class="logo-lg-text-light">U</span> -->
                        </span>
                    </a>

                    <a href="index.html" class="logo logo-light text-center">
                        <span class="logo-sm">
                            <img src="../assets/images/logo-sm.png" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="../assets/images/logo-light.png" alt="" height="20">
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
                    <img src="../assets/images/users/user-1.jpg" alt="user-img" title="Mat Helme" class="rounded-circle avatar-md">
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

                        <li class="menu-title">Navigation</li>

                        <li>
                            <a href="index.html">
                                <i data-feather="airplay"></i>
                                <span> Dashboard </span>
                            </a>
                        </li>

                        <li class="menu-title mt-2">Apps</li>

                        <li>
                            <a href="apps-chat.html">
                                <i data-feather="message-square"></i>
                                <span> Chat </span>
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
                                        <a href="crm-dashboard.html">Dashboard</a>
                                    </li>
                                    <li>
                                        <a href="crm-contacts.html">Contacts</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li>
                            <a href="#sidebarCrm" data-toggle="collapse">
                                <i data-feather="users"></i>
                                <span>Mahasiswa Baru</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarCrm">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="crm-dashboard.html">Dashboard</a>
                                    </li>
                                    <li>
                                        <a href="crm-contacts.html">Contacts</a>
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
                                        <a href="crm-dashboard.html">Dashboard</a>
                                    </li>
                                    <li>
                                        <a href="crm-contacts.html">Contacts</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li>
                            <a href="#sidebarTasks" data-toggle="collapse">
                                <i data-feather="clipboard"></i>
                                <span> Tasks </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarTasks">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="task-list.html">List</a>
                                    </li>
                                    <li>
                                        <a href="task-details.html">Details</a>
                                    </li>
                                    <li>
                                        <a href="task-kanban-board.html">Kanban Board</a>
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
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                            </div>
                            <h4 class="page-title">Starter</h4>
                            <div class="mb-4">
                                <a href="./mabawebsite/" class="btn btn-primary me-2">Proses</a>
                                <a href="./tambah_data.php" class="btn btn-primary me-2">Tambah Data</a>
                                <a href="./push.php" class="btn btn-primary">Input Admisi</a>
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

    <!-- Right Sidebar -->
    <div class="right-bar">
        <div data-simplebar class="h-100">

            <!-- Nav tabs -->
            <ul class="nav nav-tabs nav-bordered nav-justified" role="tablist">
                <li class="nav-item">
                    <a class="nav-link py-2" data-toggle="tab" href="#chat-tab" role="tab">
                        <i class="mdi mdi-message-text d-block font-22 my-1"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2" data-toggle="tab" href="#tasks-tab" role="tab">
                        <i class="mdi mdi-format-list-checkbox d-block font-22 my-1"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 active" data-toggle="tab" href="#settings-tab" role="tab">
                        <i class="mdi mdi-cog-outline d-block font-22 my-1"></i>
                    </a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content pt-0">
                <div class="tab-pane" id="chat-tab" role="tabpanel">

                    <form class="search-bar p-3">
                        <div class="position-relative">
                            <input type="text" class="form-control" placeholder="Search...">
                            <span class="mdi mdi-magnify"></span>
                        </div>
                    </form>

                    <h6 class="font-weight-medium px-3 mt-2 text-uppercase">Group Chats</h6>

                    <div class="p-2">
                        <a href="javascript: void(0);" class="text-reset notification-item pl-3 mb-2 d-block">
                            <i class="mdi mdi-checkbox-blank-circle-outline mr-1 text-success"></i>
                            <span class="mb-0 mt-1">App Development</span>
                        </a>

                        <a href="javascript: void(0);" class="text-reset notification-item pl-3 mb-2 d-block">
                            <i class="mdi mdi-checkbox-blank-circle-outline mr-1 text-warning"></i>
                            <span class="mb-0 mt-1">Office Work</span>
                        </a>

                        <a href="javascript: void(0);" class="text-reset notification-item pl-3 mb-2 d-block">
                            <i class="mdi mdi-checkbox-blank-circle-outline mr-1 text-danger"></i>
                            <span class="mb-0 mt-1">Personal Group</span>
                        </a>

                        <a href="javascript: void(0);" class="text-reset notification-item pl-3 d-block">
                            <i class="mdi mdi-checkbox-blank-circle-outline mr-1"></i>
                            <span class="mb-0 mt-1">Freelance</span>
                        </a>
                    </div>

                    <h6 class="font-weight-medium px-3 mt-3 text-uppercase">Favourites <a href="javascript: void(0);" class="font-18 text-danger"><i class="float-right mdi mdi-plus-circle"></i></a></h6>

                    <div class="p-2">
                        <a href="javascript: void(0);" class="text-reset notification-item">
                            <div class="media">
                                <div class="position-relative mr-2">
                                    <img src="../assets/images/users/user-10.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                    <i class="mdi mdi-circle user-status online"></i>
                                </div>
                                <div class="media-body overflow-hidden">
                                    <h6 class="mt-0 mb-1 font-14">Andrew Mackie</h6>
                                    <div class="font-13 text-muted">
                                        <p class="mb-0 text-truncate">It will seem like simplified English.</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="javascript: void(0);" class="text-reset notification-item">
                            <div class="media">
                                <div class="position-relative mr-2">
                                    <img src="../assets/images/users/user-1.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                    <i class="mdi mdi-circle user-status away"></i>
                                </div>
                                <div class="media-body overflow-hidden">
                                    <h6 class="mt-0 mb-1 font-14">Rory Dalyell</h6>
                                    <div class="font-13 text-muted">
                                        <p class="mb-0 text-truncate">To an English person, it will seem like simplified</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="javascript: void(0);" class="text-reset notification-item">
                            <div class="media">
                                <div class="position-relative mr-2">
                                    <img src="../assets/images/users/user-9.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                    <i class="mdi mdi-circle user-status busy"></i>
                                </div>
                                <div class="media-body overflow-hidden">
                                    <h6 class="mt-0 mb-1 font-14">Jaxon Dunhill</h6>
                                    <div class="font-13 text-muted">
                                        <p class="mb-0 text-truncate">To achieve this, it would be necessary.</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <h6 class="font-weight-medium px-3 mt-3 text-uppercase">Other Chats <a href="javascript: void(0);" class="font-18 text-danger"><i class="float-right mdi mdi-plus-circle"></i></a></h6>

                    <div class="p-2 pb-4">
                        <a href="javascript: void(0);" class="text-reset notification-item">
                            <div class="media">
                                <div class="position-relative mr-2">
                                    <img src="../assets/images/users/user-2.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                    <i class="mdi mdi-circle user-status online"></i>
                                </div>
                                <div class="media-body overflow-hidden">
                                    <h6 class="mt-0 mb-1 font-14">Jackson Therry</h6>
                                    <div class="font-13 text-muted">
                                        <p class="mb-0 text-truncate">Everyone realizes why a new common language.</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="javascript: void(0);" class="text-reset notification-item">
                            <div class="media">
                                <div class="position-relative mr-2">
                                    <img src="../assets/images/users/user-4.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                    <i class="mdi mdi-circle user-status away"></i>
                                </div>
                                <div class="media-body overflow-hidden">
                                    <h6 class="mt-0 mb-1 font-14">Charles Deakin</h6>
                                    <div class="font-13 text-muted">
                                        <p class="mb-0 text-truncate">The languages only differ in their grammar.</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="javascript: void(0);" class="text-reset notification-item">
                            <div class="media">
                                <div class="position-relative mr-2">
                                    <img src="../assets/images/users/user-5.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                    <i class="mdi mdi-circle user-status online"></i>
                                </div>
                                <div class="media-body overflow-hidden">
                                    <h6 class="mt-0 mb-1 font-14">Ryan Salting</h6>
                                    <div class="font-13 text-muted">
                                        <p class="mb-0 text-truncate">If several languages coalesce the grammar of the resulting.</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="javascript: void(0);" class="text-reset notification-item">
                            <div class="media">
                                <div class="position-relative mr-2">
                                    <img src="../assets/images/users/user-6.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                    <i class="mdi mdi-circle user-status online"></i>
                                </div>
                                <div class="media-body overflow-hidden">
                                    <h6 class="mt-0 mb-1 font-14">Sean Howse</h6>
                                    <div class="font-13 text-muted">
                                        <p class="mb-0 text-truncate">It will seem like simplified English.</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="javascript: void(0);" class="text-reset notification-item">
                            <div class="media">
                                <div class="position-relative mr-2">
                                    <img src="../assets/images/users/user-7.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                    <i class="mdi mdi-circle user-status busy"></i>
                                </div>
                                <div class="media-body overflow-hidden">
                                    <h6 class="mt-0 mb-1 font-14">Dean Coward</h6>
                                    <div class="font-13 text-muted">
                                        <p class="mb-0 text-truncate">The new common language will be more simple.</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="javascript: void(0);" class="text-reset notification-item">
                            <div class="media">
                                <div class="position-relative mr-2">
                                    <img src="../assets/images/users/user-8.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                    <i class="mdi mdi-circle user-status away"></i>
                                </div>
                                <div class="media-body overflow-hidden">
                                    <h6 class="mt-0 mb-1 font-14">Hayley East</h6>
                                    <div class="font-13 text-muted">
                                        <p class="mb-0 text-truncate">One could refuse to pay expensive translators.</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <div class="text-center mt-3">
                            <a href="javascript:void(0);" class="btn btn-sm btn-white">
                                <i class="mdi mdi-spin mdi-loading mr-2"></i>
                                Load more
                            </a>
                        </div>
                    </div>

                </div>

                <div class="tab-pane" id="tasks-tab" role="tabpanel">
                    <h6 class="font-weight-medium p-3 m-0 text-uppercase">Working Tasks</h6>
                    <div class="px-2">
                        <a href="javascript: void(0);" class="text-reset item-hovered d-block p-2">
                            <p class="text-muted mb-0">App Development<span class="float-right">75%</span></p>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </a>

                        <a href="javascript: void(0);" class="text-reset item-hovered d-block p-2">
                            <p class="text-muted mb-0">Database Repair<span class="float-right">37%</span></p>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: 37%" aria-valuenow="37" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </a>

                        <a href="javascript: void(0);" class="text-reset item-hovered d-block p-2">
                            <p class="text-muted mb-0">Backup Create<span class="float-right">52%</span></p>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 52%" aria-valuenow="52" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </a>
                    </div>

                    <h6 class="font-weight-medium px-3 mb-0 mt-4 text-uppercase">Upcoming Tasks</h6>

                    <div class="p-2">
                        <a href="javascript: void(0);" class="text-reset item-hovered d-block p-2">
                            <p class="text-muted mb-0">Sales Reporting<span class="float-right">12%</span></p>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 12%" aria-valuenow="12" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </a>

                        <a href="javascript: void(0);" class="text-reset item-hovered d-block p-2">
                            <p class="text-muted mb-0">Redesign Website<span class="float-right">67%</span></p>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 67%" aria-valuenow="67" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </a>

                        <a href="javascript: void(0);" class="text-reset item-hovered d-block p-2">
                            <p class="text-muted mb-0">New Admin Design<span class="float-right">84%</span></p>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 84%" aria-valuenow="84" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </a>
                    </div>

                    <div class="p-3 mt-2">
                        <a href="javascript: void(0);" class="btn btn-success btn-block waves-effect waves-light">Create Task</a>
                    </div>

                </div>
                <div class="tab-pane active" id="settings-tab" role="tabpanel">
                    <h6 class="font-weight-medium px-3 m-0 py-2 font-13 text-uppercase bg-light">
                        <span class="d-block py-1">Theme Settings</span>
                    </h6>

                    <div class="p-3">
                        <div class="alert alert-warning" role="alert">
                            <strong>Customize </strong> the overall color scheme, sidebar menu, etc.
                        </div>

                        <h6 class="font-weight-medium font-14 mt-4 mb-2 pb-1">Color Scheme</h6>
                        <div class="custom-control custom-switch mb-1">
                            <input type="radio" class="custom-control-input" name="color-scheme-mode" value="light" id="light-mode-check" checked />
                            <label class="custom-control-label" for="light-mode-check">Light Mode</label>
                        </div>

                        <div class="custom-control custom-switch mb-1">
                            <input type="radio" class="custom-control-input" name="color-scheme-mode" value="dark" id="dark-mode-check" />
                            <label class="custom-control-label" for="dark-mode-check">Dark Mode</label>
                        </div>

                        <!-- Width -->
                        <h6 class="font-weight-medium font-14 mt-4 mb-2 pb-1">Width</h6>
                        <div class="custom-control custom-switch mb-1">
                            <input type="radio" class="custom-control-input" name="width" value="fluid" id="fluid-check" checked />
                            <label class="custom-control-label" for="fluid-check">Fluid</label>
                        </div>
                        <div class="custom-control custom-switch mb-1">
                            <input type="radio" class="custom-control-input" name="width" value="boxed" id="boxed-check" />
                            <label class="custom-control-label" for="boxed-check">Boxed</label>
                        </div>

                        <!-- Menu positions -->
                        <h6 class="font-weight-medium font-14 mt-4 mb-2 pb-1">Menus (Leftsidebar and Topbar) Positon</h6>

                        <div class="custom-control custom-switch mb-1">
                            <input type="radio" class="custom-control-input" name="menus-position" value="fixed" id="fixed-check" checked />
                            <label class="custom-control-label" for="fixed-check">Fixed</label>
                        </div>

                        <div class="custom-control custom-switch mb-1">
                            <input type="radio" class="custom-control-input" name="menus-position" value="scrollable" id="scrollable-check" />
                            <label class="custom-control-label" for="scrollable-check">Scrollable</label>
                        </div>

                        <!-- Left Sidebar-->
                        <h6 class="font-weight-medium font-14 mt-4 mb-2 pb-1">Left Sidebar Color</h6>

                        <div class="custom-control custom-switch mb-1">
                            <input type="radio" class="custom-control-input" name="leftsidebar-color" value="light" id="light-check" checked />
                            <label class="custom-control-label" for="light-check">Light</label>
                        </div>

                        <div class="custom-control custom-switch mb-1">
                            <input type="radio" class="custom-control-input" name="leftsidebar-color" value="dark" id="dark-check" />
                            <label class="custom-control-label" for="dark-check">Dark</label>
                        </div>

                        <div class="custom-control custom-switch mb-1">
                            <input type="radio" class="custom-control-input" name="leftsidebar-color" value="brand" id="brand-check" />
                            <label class="custom-control-label" for="brand-check">Brand</label>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input type="radio" class="custom-control-input" name="leftsidebar-color" value="gradient" id="gradient-check" />
                            <label class="custom-control-label" for="gradient-check">Gradient</label>
                        </div>

                        <!-- size -->
                        <h6 class="font-weight-medium font-14 mt-4 mb-2 pb-1">Left Sidebar Size</h6>

                        <div class="custom-control custom-switch mb-1">
                            <input type="radio" class="custom-control-input" name="leftsidebar-size" value="default" id="default-size-check" checked />
                            <label class="custom-control-label" for="default-size-check">Default</label>
                        </div>

                        <div class="custom-control custom-switch mb-1">
                            <input type="radio" class="custom-control-input" name="leftsidebar-size" value="condensed" id="condensed-check" />
                            <label class="custom-control-label" for="condensed-check">Condensed <small>(Extra Small size)</small></label>
                        </div>

                        <div class="custom-control custom-switch mb-1">
                            <input type="radio" class="custom-control-input" name="leftsidebar-size" value="compact" id="compact-check" />
                            <label class="custom-control-label" for="compact-check">Compact <small>(Small size)</small></label>
                        </div>

                        <!-- User info -->
                        <h6 class="font-weight-medium font-14 mt-4 mb-2 pb-1">Sidebar User Info</h6>

                        <div class="custom-control custom-switch mb-1">
                            <input type="checkbox" class="custom-control-input" name="leftsidebar-user" value="fixed" id="sidebaruser-check" />
                            <label class="custom-control-label" for="sidebaruser-check">Enable</label>
                        </div>


                        <!-- Topbar -->
                        <h6 class="font-weight-medium font-14 mt-4 mb-2 pb-1">Topbar</h6>

                        <div class="custom-control custom-switch mb-1">
                            <input type="radio" class="custom-control-input" name="topbar-color" value="dark" id="darktopbar-check" checked />
                            <label class="custom-control-label" for="darktopbar-check">Dark</label>
                        </div>

                        <div class="custom-control custom-switch mb-1">
                            <input type="radio" class="custom-control-input" name="topbar-color" value="light" id="lighttopbar-check" />
                            <label class="custom-control-label" for="lighttopbar-check">Light</label>
                        </div>


                        <button class="btn btn-primary btn-block mt-4" id="resetBtn">Reset to Default</button>

                        <a href="https://1.envato.market/uboldadmin" class="btn btn-danger btn-block mt-3" target="_blank"><i class="mdi mdi-basket mr-1"></i> Purchase Now</a>

                    </div>

                </div>
            </div>

        </div> <!-- end slimscroll-menu-->
    </div>
    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- Vendor js -->
    <script src="../assets/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="../assets/js/app.min.js"></script>
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
            window.location.href = 'hapus_data_mahasiswa.php?No=' + no;
        }
    }

    function updateResults() {
    const keyword = document.getElementById('keyword').value;
    const search_column = document.getElementById('search_column').value;
    const limit = document.getElementById('limit').value;

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `dashboard.php?ajax=1&keyword=${keyword}&search_column=${search_column}&limit=${limit}`, true);
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
                url: 'ambil_data_mahasiswa.php', // Pastikan path ini benar
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
                url: 'simpan_data.php',
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
            url: 'get_saldo.php', // Pastikan path ini benar
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