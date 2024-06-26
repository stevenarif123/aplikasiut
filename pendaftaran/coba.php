<?php
// Connect to database
require_once "../admin/koneksi.php";

$nama_lengkap = '';
$tempat_lahir = '';
$tanggal_lahir = '';
$nama_ibu_kandung = '';
$nik = '';
$jurusan = '';
$nomor_hp = '';
$agama = '';
$jenis_kelamin = '';
$pesan = '';

// Process form data
if (isset($_POST['submit'])) {
    // Set variabel
    $nama_lengkap = isset($_POST['nama_lengkap']) ? $_POST['nama_lengkap'] : '';
    $tempat_lahir = isset($_POST['tempat_lahir']) ? $_POST['tempat_lahir'] : '';
    $tanggal_lahir = isset($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : '';
    $nama_ibu_kandung = isset($_POST['nama_ibu_kandung']) ? $_POST['nama_ibu_kandung'] : '';
    $nik = isset($_POST['nik']) ? $_POST['nik'] : '';
    $jurusan = isset($_POST['jurusan']) ? $_POST['jurusan'] : '';
    $nomor_hp = isset($_POST['nomor_hp']) ? $_POST['nomor_hp'] : '';
    $agama = isset($_POST['agama']) ? $_POST['agama'] : '';
    $jenis_kelamin = isset($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : '';
    $pesan = isset($_POST['pesan']) ? $_POST['pesan'] : '';

    // Input validation and sanitization
    if (empty($nama_lengkap)) {
        header('Content-Type: application/json');
        $response = array(
            'success' => false,
            'message' => 'Nama lengkap tidak boleh kosong.'
        );
        echo json_encode($response);
        exit;
    }
    // Insert data into database using prepared statement
    $query = "INSERT INTO mabawebsite (nama_lengkap, tempat_lahir, tanggal_lahir, nama_ibu_kandung, nik, jurusan, nomor_hp, agama, jenis_kelamin, pesan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'sssssssss', $nama_lengkap, $tempat_lahir, $tanggal_lahir, $nama_ibu_kandung, $nik, $jurusan, $nomor_hp, $agama, $jenis_kelamin, $pesan);

    if (mysqli_stmt_execute($stmt)) {
        $pesanstatus = "Data berhasil disimpan";
    } else {
        $pesanstatus = "Data gagal disimpan: " . mysqli_error($koneksi);
    }

    mysqli_stmt_close($stmt);

    header('Content-Type: application/json');
    $response = array(
        'status' => $pesanstatus
    );
    echo json_encode($response);
    exit;

}

// Get list of jurusan from database
$query_jurusan = "SELECT * FROM jurusan";
$result_jurusan = mysqli_query($koneksi, $query_jurusan);
$daftar_jurusan = array();
while ($row = mysqli_fetch_assoc($result_jurusan)) {
    $daftar_jurusan[] = $row["nama_jurusan"];
}
?>


<!-- HTML Content -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pendaftaran Mahasiswa Baru</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    </head>
<body>
    <style>
        /* Add a background color and box shadow to the form */
        #form-daftar {
            background-color: #f9f9f9;
            padding: 20px;
            border: 2px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            margin: 40px auto;
            border-radius: 1pc;
        }

        /* Make the form responsive */
        @media (max-width: 768px) {
            #form-daftar {
                max-width: 100%;
                padding: 10px;
            }
        }

        /* Add a background color to the submit button */
        #submit-button {
            background-color: #4CAF50;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #submit-button:hover {
            background-color: #3e8e41;
        }
    </style>
    <div class="container mx-auto p-4 pt-6 md:p-6 lg:p-12">
        <h1 class="text-3xl font-bold mb-4">Formulir Pendaftaran Mahasiswa Baru</h1>
        <form id="form-daftar" action="coba.php" method="post">
            <div class="grid grid-cols-1 gap-4 mb-4">
                <label class="block" for="nama_lengkap">Nama Lengkap:</label>
                <input class="block w-full px-4 py-2 text-gray-700" type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($nama_lengkap);?>" />
            </div>
            <div class="grid grid-cols-1 gap-4 mb-4">
                <label class="block" for="tempat_lahir">Tempat Lahir:</label>
                <input class="block w-full px-4 py-2 text-gray-700" type="text" id="tempat_lahir" name="tempat_lahir" value="<?php echo $tempat_lahir;?>" />
            </div>
            <div class="grid grid-cols-1 gap-4 mb-4">
                <label class="block" for="tanggal_lahir">Tanggal Lahir:</label>
                <input class="block w-full px-4 py-2 text-gray-700" type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo $tanggal_lahir;?>" />
            </div>
            <div class="grid grid-cols-1 gap-4 mb-4">
                <label class="block" for="nama_ibu_kandung">Nama Ibu Kandung:</label>
                <input class="block w-full px-4 py-2 text-gray-700" type="text" id="nama_ibu_kandung" name="nama_ibu_kandung" value="<?php echo $nama_ibu_kandung;?>" />
            </div>
            <div class="grid grid-cols-1 gap-4 mb-4">
                <label class="block" for="nik">NIK:</label>
                <input class="block w-full px-4 py-2 text-gray-700" type="text" id="nik" name="nik" value="<?php echo $nik;?>" />
            </div>
            <div class="grid grid-cols-1 gap-4 mb-4">
                <label class="block" for="jurusan">Jurusan:</label>
                <select class="block w-full px-4 py-2 text-gray-700" id="jurusan" name="jurusan">
                    <option value="">Pilih Jurusan</option>
                    <?php foreach ($daftar_jurusan as $jurusan) {?>
                    <option value="<?php echo $jurusan;?>"><?php echo $jurusan;?></option>
                    <?php }?>
                </select>
            </div>
            <div class="grid grid-cols-1 gap-4 mb-4">
                <label class="block" for="nomor_hp">Nomor HP:</label>
                <input class="block w-full px-4 py-2 text-gray-700" type="text" id="nomor_hp" name="nomor_hp" value="<?php echo $nomor_hp;?>" />
            </div>
            <div class="grid grid-cols-1 gap-4 mb-4">
                <label class="block" for="agama">Agama:</label>
                <select class="block w-full px-4 py-2 text-gray-700" id="agama" name="agama">
                    <option value="">Pilih Agama</option>
                    <option value="Islam">Islam</option>
                    <option value="Kristen">Kristen</option>
                    <option value="Katolik">Katolik</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Buddha">Buddha</option>
                    <option value="Konghucu">Konghucu</option>
                </select>
            </div>
            <div class="grid grid-cols-1 gap-4 mb-4">
                <label class="block" for="jenis_kelamin">Jenis Kelamin:</label>
                <select class="block w-full px-4 py-2 text-gray-700" id="jenis_kelamin" name="jenis_kelamin">
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            <div class="grid grid-cols-1 gap-4 mb-4">
                <label class="block" for="pesan">Pesan:</label>
                <textarea class="block w-full px-4 py-2 text-gray-700" id="pesan" name="pesan"><?php echo $pesan;?></textarea>
            </div>
            <button class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded" id="submit-button" name="submit" type="submit">Kirim</button>
            <button class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded" id="reset-button" type="reset">Reset</button>
        </form>
        <?php if (isset($pesanstatus)) {?>
            $response['success'] = true;
            $response['message'] = $pesanstatus;
        } else {
            $response['success'] = false;
            $response['message'] = 'Terjadi kesalahan. Silakan coba lagi.';
        }
        echo json_encode($response);
        exit;
        <?php }?>
    </div>
    <div id="message" class="mt-4"></div>
        <script>
            document.getElementById('form-daftar').addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                fetch('coba.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const messageElement = document.getElementById('message');

                    if (data.success) {
                        messageElement.classList.remove('text-danger');
                        messageElement.classList.add('text-success');
                        messageElement.textContent = 'Data berhasil disimpan';
                        setTimeout(() => {
                            window.location.href = 'success.php';
                        }, 2000);
                    } else {
                        messageElement.classList.remove('text-success');
                        messageElement.classList.add('text-danger');
                        messageElement.textContent = 'Data gagal disimpan';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const messageElement = document.getElementById('message');
                    messageElement.classList.remove('text-success');
                    messageElement.classList.add('text-danger');
                    messageElement.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
                });
            });
        </script>
    </div>


</body>
</html>
