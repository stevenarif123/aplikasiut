<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .nav-item:hover .dropdown-menu {
            display: block;
        }

        .box-form {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
        }

        .box-form-image {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: -1;
            opacity: 0.2;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-gray-200 border-b border-gray-300 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">SALUT TANA TORAJA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="../dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Mahasiswa
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../mahasiswa.php">Daftar Mahasiswa</a></li>
                            <li><a class="dropdown-item" href="../tambah_data.php">Tambah Mahasiswa</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown1" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Laporan Pembayaran
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown1">
                            <li><a class="dropdown-item" href="../laporanbayar">Laporan Bayar</a></li>
                            <li><a class="dropdown-item" href="../laporanbayar/tambah_laporan.php">Tambah Laporan</a></li>
                            <li><a class="dropdown-item" href="../laporanbayar/verifikasi_laporan.php">Verifikasi Laporan</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Mahasiswa Baru
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown2">
                            <li><a class="dropdown-item" href="../maba/dashboard.php">Daftar Mahasiswa</a></li>
                            <li><a class="dropdown-item active" href="../maba/tambah_data.php">Tambah Mahasiswa</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="../cekstatus/pencarian.php">Cek Status Mahasiswa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-warning text-dark fw-bold" href="../logout.php">Keluar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 jrkctn">
        <div class="box-form jrk">
            <h1 class="text-center mb-4 text-2xl jrk">Tambah Data Mahasiswa Baru</h1>
            <form class="jrk" id="tambahMahasiswaForm">
                <div class="center grid gap-4 jrk">
                    <div class="col w-1/2 mb-1">
                        <label for="jalur_program">Jalur Program:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="jalur_program_rpl" name="JalurProgram" value="RPL" required>
                            <label class="form-check-label" for="jalur_program_rpl">RPL</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="jalur_program_reguler" name="JalurProgram" value="Reguler" required>
                            <label class="form-check-label" for="jalur_program_reguler">Reguler</label>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap:</label>
                        <input type="text" name="NamaLengkap" id="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="mb-1 flex flex-wrap -mx-3">
                        <div class="w-1/2 px-3 mb-1">
                            <label for="tempat_lahir" class="form-label">Tempat Lahir:</label>
                            <input type="text" name="TempatLahir" id="tempat_lahir" class="form-control" required>
                        </div>
                        <div class="w-1/2 px-3 mb-1">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir:</label>
                            <input type="date" name="TanggalLahir" id="tanggal_lahir" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label for="nama_ibu_kandung" class="form-label">Nama Ibu Kandung:</label>
                        <input type="text" name="NamaIbuKandung" id="nama_ibu_kandung" class="form-control" required>
                    </div>
                    <div class="mb-1">
                        <label for="nik" class="form-label">NIK:</label>
                        <input type="text" name="NIK" id="nik" class="form-control" required>
                    </div>
                    <div class="mb-1">
                        <label for="jurusan" class="form-label">Jurusan:</label>
                        <select name="Jurusan" id="jurusan" class="form-select" required>
                            <option value="" disabled selected>Pilih Jurusan</option>
                            <!-- Options will be populated dynamically -->
                        </select>
                    </div>
                    <div class="mb-1">
                        <label for="nomor_hp" class="form-label">Nomor HP:</label>
                        <input type="text" name="NomorHP" id="nomor_hp" class="form-control" required>
                    </div>
                    <div class="mb-1 flex flex-wrap -mx-3">
                        <div class="w-1/2 px-3 mb-6">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="Email" id="email" class="form-control" required>
                        </div>
                        <div class="w-1/2 px-3 mb-6">
                            <label for="password" class="form-label" disabled>Password Mahasiswa:</label>
                            <input type="text" name="Password" id="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label for="agama" class="form-label">Agama:</label>
                        <select name="Agama" id="agama" class="form-select" required>
                            <option value="" disabled selected>Silahkan Pilih Agama</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Protestan</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                    </div>
                    <div class="w-1/2 mb-1">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="jenis_kelamin_laki" name="JenisKelamin" value="Laki-laki" required>
                            <label class="form-check-label" for="jenis_kelamin_laki">Laki-laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="jenis_kelamin_perempuan" name="JenisKelamin" value="Perempuan" required>
                            <label class="form-check-label" for="jenis_kelamin_perempuan">Perempuan</label>
                        </div>
                    </div>
                    <div class="w-1/2 mb-1">
                            <label for="status_perkawinan" class="form-label">Status Perkawinan:</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="status_perkawinan_tidak_kawin" name="StatusPerkawinan" value="Tidak Kawin" required>
                                <label class="form-check-label" for="status_perkawinan_tidak_kawin">Tidak Kawin</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="status_perkawinan_kawin" name="StatusPerkawinan" value="Kawin" required>
                                <label class="form-check-label" for="status_perkawinan_kawin">Kawin</label>
                            </div>
                        </div>
                    <div class="mb-1">
                        <label for="nomor_hp_alternatif" class="form-label">Nomor HP Alternatif:</label>
                        <input type="text" name="NomorHPAlternatif" id="nomor_hp_alternatif" class="form-control">
                    </div>
                    <div class="mb-1 flex flex-wrap -mx-3">
                        <div class="w-1/3 px-3 mb-1">
                            <label for="nomor_ijazah" class="form-label">Nomor Ijazah:</label>
                            <input type="text" name="NomorIjazah" id="nomor_ijazah" class="form-control">
                        </div>
                        <div class="w-1/3 px-3 mb-1">
                            <label for="tahun_ijazah" class="form-label">Tahun Ijazah:</label>
                            <input type="text" name="TahunIjazah" id="tahun_ijazah" class="form-control">
                        </div>
                        <div class="w-1/3 px-3 mb-1">
                            <label for="nisn" class="form-label">NISN:</label>
                            <input type="text" name="NISN" id="nisn" class="form-control">
                        </div>
                    </div>
                    <div class="mb-1">
                        <label for="layanan_paket_semester" class="form-label">Layanan Paket Semester:</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="layanan_paket_semester_sipas" name="LayananPaketSemester" value="SIPAS" required>
                                <label class="form-check-label" for="layanan_paket_semester_sipas">SIPAS</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="layanan_paket_semester_non_sipas" name="LayananPaketSemester" value="NON SIPAS" required>
                                <label Name="form-check-label" for="layanan_paket_semester_non_sipas">NON SIPAS</label>
                            </div>
                        </div>
                    <div class="mb-1">
                        <label for="UkuranBaju" class="form-label">Ukuran Baju:</label>
                        <select name="UkuranBaju" id="UkuranBaju" class="form-select" required>
                            <option value="" disabled selected>Pilih Ukuran Baju</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                            <option value="XXL">XXL</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label for="status_input_sia" class="form-label">Status Input Sia:</label>
                        <select name="STATUS_INPUT_SIA" id="status_input_sia" class="form-select" required>
                            <option value="" disabled selected>Silahkan Pilih Status Input di SIA</option>
                            <option value="Belum Terdaftar">Belum Terdaftar</option>
                            <option value="Input admisi">Input admisi</option>
                            <option value="Pengajuan Admisi">Pengajuan Admisi</option>
                            <option value="Berkas Kurang">Berkas Kurang</option>
                            <option value="Admisi Diterima">Admisi Diterima</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("tambahMahasiswaForm");
            const jurusanSelect = document.getElementById("jurusan");

            // Mengambil daftar jurusan dari file jurusan.php menggunakan AJAX
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        const jurusanList = JSON.parse(xhr.responseText);
                        populateJurusanSelect(jurusanList);
                    } else {
                        console.error("Gagal mengambil daftar jurusan dari server.");
                    }
                }
            };
            xhr.open("GET", "./api/jurusan.php", true);
            xhr.send();

            // Fungsi untuk mengisi dropdown jurusan dengan data dari server
            function populateJurusanSelect(jurusanList) {
                jurusanList.forEach(function(jurusan) {
                    const option = document.createElement("option");
                    option.value = jurusan;
                    option.text = jurusan;
                    jurusanSelect.appendChild(option);
                });
            }

            form.addEventListener("submit", async function(event) {
                event.preventDefault();

                const formData = new FormData(form);
                const response = await fetch("api/tambah_data.php", {
                    method: "POST",
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    anime({
                        targets: '.box-form',
                        scale: 1.1,
                        duration: 1000,
                        elasticity: 0.5,
                        complete: function() {
                            alert('Data mahasiswa berhasil ditambahkan!');
                        }
                    });
                } else {
                    anime({
                        targets: '.box-form',
                        scale: 0.9,
                        duration: 1000,
                        elasticity: 0.5,
                        complete: function() {
                            alert('Gagal menambahkan data mahasiswa: ' + result.message);
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>