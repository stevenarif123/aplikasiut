<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
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

        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">SALUT TANA TORAJA</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="../dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                            Mahasiswa
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="../mahasiswa.php">Daftar Mahasiswa</a>
                            <a class="dropdown-item" href="../tambah_data.php">Tambah Mahasiswa</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown1" role="button" data-toggle="dropdown" aria-expanded="false">
                            Laporan Pembayaran
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown1">
                            <a class="dropdown-item" href="../laporanbayar">Laporan Bayar</a>
                            <a class="dropdown-item" href="../laporanbayar/tambah_laporan.php">Tambah Laporan</a>
                            <a class="dropdown-item" href="../laporanbayar/verifikasi_laporan.php">Verifikasi Laporan</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-toggle="dropdown" aria-expanded="false">
                            Mahasiswa Baru
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown2">
                            <a class="dropdown-item" href="../maba/dashboard.php">Daftar Mahasiswa</a>
                            <a class="dropdown-item active" href="../maba/tambah_data.php">Tambah Mahasiswa</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="../cekstatus/pencarian.php">Cek Status Mahasiswa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-warning text-dark font-weight-bold" href="../logout.php">Keluar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="alert-container"></div>

    <div class="container mt-5">
        <div class="box-form mx-auto">
            <h1 class="text-center mb-4">Tambah Data Mahasiswa Baru</h1>
            <form id="tambahMahasiswaForm">
                <div class="form-group">
                    <label for="jalur_program">Jalur Program:</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="jalur_program_rpl" name="JalurProgram" value="RPL" required>
                        <label class="form-check-label" for="jalur_program_rpl">RPL</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="jalur_program_reguler" name="JalurProgram" value="Reguler" required>
                        <label class="form-check-label" for="jalur_program_reguler">Reguler</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap:</label>
                    <input type="text" name="NamaLengkap" id="nama_lengkap" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tempat_lahir">Tempat Lahir:</label>
                        <input type="text" name="TempatLahir" id="tempat_lahir" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tanggal_lahir">Tanggal Lahir:</label>
                        <input type="date" name="TanggalLahir" id="tanggal_lahir" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nama_ibu_kandung">Nama Ibu Kandung:</label>
                    <input type="text" name="NamaIbuKandung" id="nama_ibu_kandung" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="nik">NIK:</label>
                    <input type="text" name="NIK" id="nik" class="form-control" required maxlength="16">
                </div>

                <div class="form-group">
                    <label for="jurusan">Jurusan:</label>
                    <select name="Jurusan" id="jurusan" class="form-control" required>
                        <option value="" disabled selected>Pilih Jurusan</option>
                        <!-- Options will be populated dynamically -->
                    </select>
                </div>

                <div class="form-group">
                    <label for="nomor_hp">Nomor HP:</label>
                    <input type="text" name="NomorHP" id="nomor_hp" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="email">Email:</label>
                        <input type="email" name="Email" id="email" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password">Password Mahasiswa:</label>
                        <input type="text" name="Password" id="password" class="form-control" disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label for="agama">Agama:</label>
                    <select name="Agama" id="agama" class="form-control" required>
                        <option value="" disabled selected>Silahkan Pilih Agama</option>
                        <option value="Islam">Islam</option>
                        <option value="Kristen">Protestan</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddha">Buddha</option>
                        <option value="Konghucu">Konghucu</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin:</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="jenis_kelamin_laki" name="JenisKelamin" value="Laki-laki" required>
                        <label class="form-check-label" for="jenis_kelamin_laki">Laki-laki</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="jenis_kelamin_perempuan" name="JenisKelamin" value="Perempuan" required>
                        <label class="form-check-label" for="jenis_kelamin_perempuan">Perempuan</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status_perkawinan">Status Perkawinan:</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="status_perkawinan_tidak_kawin" name="StatusPerkawinan" value="Tidak Kawin" required>
                        <label class="form-check-label" for="status_perkawinan_tidak_kawin">Tidak Kawin</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="status_perkawinan_kawin" name="StatusPerkawinan" value="Kawin" required>
                        <label class="form-check-label" for="status_perkawinan_kawin">Kawin</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nomor_hp_alternatif">Nomor HP Alternatif:</label>
                    <input type="text" name="NomorHPAlternatif" id="nomor_hp_alternatif" class="form-control">
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="nomor_ijazah">Nomor Ijazah:</label>
                        <input type="text" name="NomorIjazah" id="nomor_ijazah" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="tahun_ijazah">Tahun Ijazah:</label>
                        <input type="text" name="TahunIjazah" id="tahun_ijazah" class="form-control" maxlength="4">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="nisn">NISN:</label>
                        <input type="text" name="NISN" id="nisn" class="form-control">
                    </div>
                </div>

                <!-- RPL Specific Fields -->
                <div id="rpl-fields" class="hidden">
                    <div class="form-group">
                        <label for="asal_kampus">Asal Kampus:</label>
                        <input type="text" name="AsalKampus" id="asal_kampus" class="form-control">
                    </div>
                    <div class="form-group" id="jurusan_lama">
                        <label for="jurusan_lama">Jurusan:</label>
                        <input type="text" name="JurusanSMK" id="jurusan_lama" class="form-control">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="tahun_lulus_kampus">Tahun Lulus Kampus:</label>
                            <input type="text" name="TahunLulusKampus" id="tahun_lulus_kampus" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="ipk">IPK:</label>
                            <input type="text" name="IPK" id="ipk" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="jenis_sekolah">Jenis Sekolah:</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="jenis_sekolah_sma" name="JenisSekolah" value="SMA" required>
                            <label class="form-check-label" for="jenis_sekolah_sma">SMA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="jenis_sekolah_smk" name="JenisSekolah" value="SMK" required>
                            <label class="form-check-label" for="jenis_sekolah_smk">SMK</label>
                        </div>
                    </div>
                    <div class="form-group hidden" id="jurusan_smk_group">
                        <label for="jurusan_smk">Jurusan SMK:</label>
                        <input type="text" name="JurusanSMK" id="jurusan_smk" class="form-control">
                    </div>
                    <div class="form-group" id="nama_sekolah">
                        <label for="nama_sekolah">Nama Sekolah:</label>
                        <input type="text" name="NamaSekolah" id="nama_sekolah" class="form-control">
                    </div>
                </div>

                <!-- Reguler Specific Fields -->
                <div id="reguler-fields" class="hidden">
                    <div class="form-group">
                        <label for="jenis_sekolah">Jenis Sekolah:</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="jenis_sekolah_sma" name="JenisSekolah" value="SMA" required>
                            <label class="form-check-label" for="jenis_sekolah_sma">SMA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="jenis_sekolah_smk" name="JenisSekolah" value="SMK" required>
                            <label class="form-check-label" for="jenis_sekolah_smk">SMK</label>
                        </div>
                    </div>
                    <div class="form-group hidden" id="jurusan_smk_group2">
                        <label for="jurusan_smk">Jurusan SMK:</label>
                        <input type="text" name="JurusanSMK" id="jurusan_smk" class="form-control">
                    </div>
                    <div class="form-group" id="nama_sekolah">
                        <label for="nama_sekolah">Nama Sekolah:</label>
                        <input type="text" name="NamaSekolah" id="nama_sekolah" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="UkuranBaju">Ukuran Baju:</label>
                    <select name="UkuranBaju" id="UkuranBaju" class="form-control" required>
                        <option value="" disabled selected>Pilih Ukuran Baju</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="layanan_paket_semester">Layanan Paket Semester:</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="layanan_paket_semester_sipas" name="LayananPaketSemester" value="SIPAS" required>
                        <label class="form-check-label" for="layanan_paket_semester_sipas">SIPAS</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="layanan_paket_semester_non_sipas" name="LayananPaketSemester" value="NON SIPAS" required>
                        <label class="form-check-label" for="layanan_paket_semester_non_sipas">NON SIPAS</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status_input_sia">Status Input Sia:</label>
                    <select name="STATUS_INPUT_SIA" id="status_input_sia" class="form-control" required>
                        <option value="" disabled selected>Silahkan Pilih Status Input di SIA</option>
                        <option value="Belum Terdaftar">Belum Terdaftar</option>
                        <option value="Input admisi">Input admisi</option>
                        <option value="Pengajuan Admisi">Pengajuan Admisi</option>
                        <option value="Berkas Kurang">Berkas Kurang</option>
                        <option value="Admisi Diterima">Admisi Diterima</option>
                    </select>
                </div>

                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("tambahMahasiswaForm");
    const jalurProgramInputs = document.querySelectorAll('input[name="JalurProgram"]');
    const jenisSekolahInputs = document.querySelectorAll('input[name="JenisSekolah"]');
    const layananPaketInputs = document.querySelectorAll('input[name="LayananPaketSemester"]');
    const jurusanSelect = document.getElementById("jurusan");
    const jurusanSMKGroup = document.getElementById("jurusan_smk_group");
	const jurusanSMKGroup2 = document.getElementById("jurusan_smk_group2");
    const rplFields = document.getElementById("rpl-fields");
    const regulerFields = document.getElementById("reguler-fields");
    const tahunIjazahInput = document.getElementById("tahun_ijazah");
    const nisnInput = document.getElementById("nisn");
    const layananPaketSipas = document.getElementById("layanan_paket_semester_sipas");
    const layananPaketNonSipas = document.getElementById("layanan_paket_semester_non_sipas");

    // Sembunyikan input NISN secara default
    nisnInput.parentElement.classList.add('hidden');

    // Mengambil data jurusan dari API
    fetch('./api/jurusan.php')
        .then(response => response.json())
        .then(data => {
            populateJurusanDropdown(data);
        })
        .catch(error => console.error('Error fetching jurusan:', error));

    function populateJurusanDropdown(jurusanList) {
        jurusanList.forEach(function(jurusan) {
            const option = document.createElement("option");
            option.value = jurusan;  // Asumsikan jurusan adalah string sederhana
            option.text = jurusan;   // Asumsikan jurusan adalah string sederhana
            jurusanSelect.appendChild(option);
        });
    }

    jalurProgramInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value === 'RPL') {
                rplFields.classList.remove('hidden');
                regulerFields.classList.add('hidden');
            } else if (this.value === 'Reguler') {
                regulerFields.classList.remove('hidden');
                rplFields.classList.add('hidden');
            }
            handleLayananPaket();
        });
    });

    jenisSekolahInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value === 'SMK') {
                jurusanSMKGroup.classList.remove('hidden');
				jurusanSMKGroup2.classList.remove('hidden');
            } else {
                jurusanSMKGroup.classList.add('hidden');
				jurusanSMKGroup2.classList.add('hidden');
            }
        });
    });

    function handleLayananPaket() {
        const selectedJurusan = jurusanSelect.value;
        const prefixJurusan = selectedJurusan.split(" ").slice(0, 4).join(" ");

        if (prefixJurusan === 'Pendidikan Guru Sekolah Dasar' || prefixJurusan === 'Pendidikan Guru Anak Usia') {
            layananPaketSipas.checked = true;
            layananPaketNonSipas.disabled = true;
        } else {
            layananPaketNonSipas.disabled = false;
        }
    }

    jurusanSelect.addEventListener('change', handleLayananPaket);

    tahunIjazahInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);
        const tahunIjazah = parseInt(this.value, 10);
        if (tahunIjazah > 2014) {
            nisnInput.parentElement.classList.remove("hidden");
        } else {
            nisnInput.parentElement.classList.add("hidden");
        }
    });

    form.addEventListener("submit", async function(event) {
        event.preventDefault();

        try {
            const formData = new FormData(form);
            const response = await fetch("api/tambah_data.php", {
                method: "POST",
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const responseText = await response.text();

            try {
                const result = JSON.parse(responseText);

                if (result.success) {
                    anime({
                        targets: '.box-form',
                        duration: 1000,
                        elasticity: 0.5,
                        complete: function() {
                            showAlert('Data mahasiswa berhasil ditambahkan!', 'success');
                            setTimeout(() => window.location.reload(), 2000);
                        }
                    });
                } else {
                    showAlert('Gagal menambahkan data mahasiswa: ' + result.message, 'danger');
                }
            } catch (error) {
                showAlert('Unexpected server response. Please try again later.', 'danger');
                console.error("Unexpected response from server:", responseText);
            }

        } catch (error) {
            showAlert('Gagal menghubungi server: ' + error.message, 'danger');
            console.error('Fetch error:', error);
        }
    });

    function showAlert(message, type) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.role = 'alert';
        alert.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        document.querySelector('.alert-container').appendChild(alert);
        setTimeout(() => alert.remove(), 5000);
    }
});
    </script>
</body>

</html>
