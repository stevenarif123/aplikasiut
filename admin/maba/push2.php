<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');
    $dataMahasiswa = json_decode(file_get_contents('php://input'), true);

    // Convert date to the required format dd/mm/yyyy
    $dataMahasiswa['tanggalLahirMahasiswa'] = date('d/m/Y', strtotime($dataMahasiswa['tanggalLahirMahasiswa']));

    function pushDataToApi($dataMahasiswa)
    {
        $url = 'https://api-sia.ut.ac.id/backend-sia/api/graphql';

        $query = '
            mutation registerNewUser(
                $email: String!,
                $name: String!,
                $password: String!,
                $passwordConfirmation: String!,
                $tanggalLahirMahasiswa: String!,
                $tempatLahirMahasiswa: String!,
                $namaIbuKandung: String!,
                $nik: String,
                $noHpMahasiswa: String!,
                $idNegara: Int,
                $idUpbjj: Int!,
                $idJenjang: Int!,
                $idProdi: Int!,
                $isWna: Boolean,
                $isRpl: Boolean,
                $nomorPassport: String) {
                    registerNewUser(
                        email: $email,
                        name: $name,
                        password: $password,
                        passwordConfirmation: $password,
                        tanggalLahirMahasiswa: $tanggalLahirMahasiswa,
                        tempatLahirMahasiswa: $tempatLahirMahasiswa,
                        namaIbuKandung: $namaIbuKandung,
                        nik: $nik,
                        noHpMahasiswa: $noHpMahasiswa,
                        idNegara: $idNegara,
                        idUpbjj: $idUpbjj,
                        idJenjang: $idJenjang,
                        idProdi: $idProdi,
                        isWna: $isWna,
                        isRpl: $isRpl,
                        nomorPassport: $nomorPassport
                    ) {
                        id,
                        message
                    }
                }';

        $data = [
            'query' => $query,
            'variables' => $dataMahasiswa
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return json_encode(['error' => curl_error($ch)]);
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode != 200) {
            return json_encode(['error' => 'Request failed', 'http_code' => $httpcode, 'response' => $result]);
        }

        curl_close($ch);

        return $result;
    }

    $response = pushDataToApi($dataMahasiswa);

    if (isset($response['data']['registerNewUser']['id'])) {
        $id = $dataMahasiswa['id'];

        require_once "../koneksi.php";

        $stmt = $koneksi->prepare("UPDATE mahasiswabaru SET STATUS_INPUT_SIA = 'Input Admisi' WHERE No = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $koneksi->close();
    }

    echo $response;
    exit;
}

require_once "../koneksi.php";

if ($koneksi->connect_error) {
    die("Connection failed: " . $koneksi->connect_error);
}

$sql = "SELECT mb.*, pa.id_prodi
        FROM mahasiswabaru mb 
        JOIN prodi_admisi pa ON mb.jurusan = pa.nama_program_studi
        WHERE mb.STATUS_INPUT_SIA = 'Belum Terdaftar'";
$result = $koneksi->query($sql);

if ($result === false) {
    die("Error: Could not execute the query. " . $koneksi->error);
}

$mahasiswaData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $mahasiswaData[] = [
            "id" => $row["No"],
            "email" => $row["Email"],
            "name" => $row["NamaLengkap"],
            "password" => $row["Password"],
            "passwordConfirmation" => $row["Password"],
            "tanggalLahirMahasiswa" => date('d/m/Y', strtotime($row["TanggalLahir"])),
            "tempatLahirMahasiswa" => $row["TempatLahir"],
            "namaIbuKandung" => $row["NamaIbuKandung"],
            "nik" => $row["NIK"],
            "noHpMahasiswa" => $row["NomorHP"],
            "idNegara" => null,
            "idUpbjj" => 29,
            "idJenjang" => 5,
            "idProdi" => (int)$row["id_prodi"],
            "isWna" => false,
            "isRpl" => false,
            "nomorPassport" => null
        ];
    }
} else {
    echo "No records found.";
}

$koneksi->close();
?>
<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from coderthemes.com/ubold/layouts/default/pages-starter.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 10 Sep 2020 17:26:43 GMT -->

<head>
    <meta charset="utf-8" />
    <title> Push | Automatic Admisi UT </title>
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
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">SALU TATOR</a></li>
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                            </div>
                            <div class="container mt-4">
                                <h1 class="text-center mb-4">Proses Data Mahasiswa Baru</h1>
                                <button id="startButton" class="btn btn-primary">Mulai Proses Semua</button>

                                <ul id="statusList" class="list-group mt-4"></ul>
                            </div>

                            <script>
                                const mahasiswaData = <?php echo json_encode($mahasiswaData); ?>;
                                const statusList = document.getElementById('statusList');
                                const startButton = document.getElementById('startButton');

                                async function pushDataToApi(data) {
                                    const response = await fetch('push.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify(data)
                                    });

                                    const result = await response.text(); // To capture non-JSON responses
                                    try {
                                        return JSON.parse(result); // Attempt to parse the JSON response
                                    } catch (error) {
                                        return {
                                            error: "Invalid JSON response",
                                            details: result
                                        }; // Return error details if JSON parsing fails
                                    }
                                }

                                function createStatusItem(data, isProcessing = false) {
                                    const statusItem = document.createElement('li');
                                    statusItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                                    const info = document.createElement('span');
                                    info.textContent = `${data.name} (${data.email})`;
                                    const status = document.createElement('span');
                                    status.className = 'badge';
                                    status.textContent = isProcessing ? 'Processing...' : 'Pending';

                                    if (isProcessing) {
                                        status.className += ' badge-warning';
                                        const spinner = document.createElement('div');
                                        spinner.className = 'spinner-border spinner-border-sm mr-2';
                                        statusItem.appendChild(spinner);
                                    } else {
                                        status.className += ' badge-secondary';
                                    }

                                    const processButton = document.createElement('button');
                                    processButton.textContent = "Proses";
                                    processButton.className = 'btn btn-success btn-sm';
                                    processButton.addEventListener('click', async () => {
                                        processButton.disabled = true;
                                        const response = await processSingleData(data);
                                        processButton.disabled = false;
                                    });

                                    statusItem.appendChild(info);
                                    statusItem.appendChild(status);
                                    statusItem.appendChild(processButton);

                                    return statusItem;
                                }

                                async function processSingleData(data) {
                                    const listItem = createStatusItem(data, true);
                                    statusList.appendChild(listItem);
                                    const response = await pushDataToApi(data);
                                    listItem.querySelector('.spinner-border').remove();
                                    listItem.querySelector('.badge').textContent = response.data && response.data.registerNewUser ? 'Success' : 'Failed';
                                    listItem.querySelector('.badge').className = response.data && response.data.registerNewUser ? 'badge badge-success' : 'badge badge-danger';

                                    const responseDetails = document.createElement('pre');
                                    responseDetails.textContent = JSON.stringify(response, null, 2);
                                    responseDetails.className = 'mt-2 p-2 bg-light rounded text-sm';
                                    listItem.appendChild(responseDetails);

                                    if (response.data && response.data.registerNewUser) {
                                        updateStatusInputSIA(data.id, 'Input Admisi');
                                    }
                                }

                                async function processStudentData() {
                                    startButton.disabled = true;
                                    for (const data of mahasiswaData) {
                                        await processSingleData(data);
                                    }
                                    startButton.disabled = false;
                                }

                                async function updateStatusInputSIA(id, status) {
                                    const response = await fetch('update_status.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            id,
                                            status
                                        })
                                    });

                                    return await response.json();
                                }

                                startButton.addEventListener('click', processStudentData);

                                document.addEventListener('DOMContentLoaded', () => {
                                    mahasiswaData.forEach(data => {
                                        const listItem = createStatusItem(data);
                                        statusList.appendChild(listItem);
                                    });
                                });
                            </script>
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

</body>

<!-- Mirrored from coderthemes.com/ubold/layouts/default/pages-starter.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 10 Sep 2020 17:26:43 GMT -->

</html>