<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['username'])) {
  header("Location: ../login.php");
}

// Koneksi ke database
require_once "../koneksi.php";
// Di awal file atau di tempat Anda ingin konten dashboard.html muncul
//include 'dashboard.html';

if (!$koneksi) {
  die("Koneksi gagal: " . mysqli_connect_error());
}

// Inisialisasi variabel
$keyword = "";
$mahasiswa = [];

// Query untuk mendapatkan data user
$username = $_SESSION['username'];
$query = "SELECT * FROM admin WHERE username='$username'";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);
if (!$result) {
  die("Query gagal: " . mysqli_error($koneksi));
}

// Tentukan jumlah data per halaman
$jumlah_data_per_halaman = isset($_GET['jumlah_data_per_halaman']) ? $_GET['jumlah_data_per_halaman'] : 10;

// Cek jika jumlah data per halaman adalah 'all', maka query tanpa LIMIT
if ($jumlah_data_per_halaman == 'all') {
    $limit_sql = "";
} else {
    // Hitung halaman saat ini
    $halaman_saat_ini = isset($_GET['halaman']) ? $_GET['halaman'] : 1;

    // Hitung offset data
    $offset = ($halaman_saat_ini - 1) * (int)$jumlah_data_per_halaman;
    $limit_sql = "LIMIT $offset, $jumlah_data_per_halaman";
}

// Periksa apakah formulir pencarian telah disubmit
if (isset($_POST['search'])) {
  // Ambil kata kunci dari formulir
  $keyword = $_POST['keyword'];
}
// Query untuk mencari data mahasiswa berdasarkan kata kunci
$query = "SELECT * FROM mahasiswa WHERE NamaLengkap LIKE '%$keyword%' OR Nim LIKE '%$keyword%' ORDER BY No DESC $limit_sql";
$result = mysqli_query($koneksi, $query);
if (!$result) {
  die("Query gagal: " . mysqli_error($koneksi));
}
// Simpan hasil pencarian ke dalam array
while ($row = mysqli_fetch_assoc($result)) {
  $mahasiswa[] = $row;
}

// Hitung jumlah total data
$query_total = "SELECT COUNT(*) AS total FROM mahasiswa WHERE NamaLengkap LIKE '%$keyword%' OR Nim LIKE '%$keyword%'";
$result_total = mysqli_query($koneksi, $query_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_data = $row_total['total'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="p-4 sm:ml-64">
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
            <div id="content">
                <?php if (count($mahasiswa) > 0) { ?>
                    <form action="" method="POST" class="mb-3">
                        <div class="flex">
                            <input type="text" class="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Cari berdasarkan nama atau nim" name="keyword">
                            <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500" name="search">Cari</button>
                        </div>
                    </form>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">No</th>
                                    <th class="px-4 py-2">NIM</th>
                                    <th class="px-4 py-2">Nama Lengkap</th>
                                    <th class="px-4 py-2">Email</th>
                                    <th class="px-4 py-2">Password</th>
                                    <th class="px-4 py-2">Status SIA</th>
                                    <th class="px-4 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($mahasiswa as $mhs) {
                                    ?>
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-4 py-2"><?php echo $no++; ?></td>
                                        <td class="px-4 py-2"><?php echo $mhs['Nim']; ?></td>
                                        <td class="px-4 py-2"><?php echo $mhs['NamaLengkap']; ?></td>
                                        <td class="px-4 py-2"><?php echo $mhs['Email']; ?></td>
                                        <td class="px-4 py-2"><?php echo $mhs['Password']; ?></td>
                                        <td class="px-4 py-2"><?php echo $mhs['STATUS_INPUT_SIA']; ?></td>
                                        <td class="px-4 py-2">
                                            <a href="lihat_data_mahasiswa.php?No=<?php echo $mhs['No']; ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Detail</a>
                                            <a href="edit_data.php?No=<?php echo $mhs['No']; ?>" class="px-4 py-2 bg-yellow-400 text-black rounded-lg hover:bg-yellow-500">Edit</a>
                                            <button type="button" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" data-modal-toggle="exampleModal">Hapus</button>
                                            <div id="exampleModal" tabindex="-1" class="hidden overflow-y-auto fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full">
                                                <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                                                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                                        <div class="flex justify-end p-2">
                                                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-toggle="exampleModal">
                                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        <div class="p-6 text-center">
                                                            <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Apakah Anda yakin ingin menghapus data ini?</h3>
                                                            <a href="hapus_data_mahasiswa.php?No=<?php echo $mhs['No']; ?>" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                                                Hapus
                                                            </a>
                                                            <button data-modal-toggle="exampleModal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                                                                Batal
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="Page navigation example" class="mt-4">
                        <ul class="inline-flex -space-x-px">
                            <?php
                            $jumlah_halaman = ceil($total_data / $jumlah_data_per_halaman);
                            for ($i = 1; $i <= $jumlah_halaman; $i++) {
                                if ($i == $halaman_saat_ini) {
                                    $active = "bg-blue-500 text-white";
                                } else {
                                    $active = "bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700";
                                }
                                ?>
                                <li>
                                    <a href="mahasiswa.php?halaman=<?php echo $i; ?>&jumlah_data_per_halaman=<?php echo $jumlah_data_per_halaman; ?>" class="px-3 py-2 leading-tight <?php echo $active; ?> border border-gray-300">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </nav>
                <?php
                } else {
                    ?>
                    <div class="alert alert-warning" role="alert">
                        Data tidak ditemukan
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
