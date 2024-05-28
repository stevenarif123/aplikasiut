document.addEventListener("DOMContentLoaded", () => {
    const app = document.getElementById('app');

    function loadContent(page, params = {}, pushState = true) {
        fetch(`templates/${page}.html`)
            .then(response => response.text())
            .then(data => {
                app.innerHTML = data;

                // Update browser history
                if (pushState) {
                    const url = new URL(window.location);
                    url.searchParams.set('page', page);
                    for (const key in params) {
                        url.searchParams.set(key, params[key]);
                    }
                    history.pushState({ page, params }, '', url);
                }

                if (page === 'daftarMahasiswa') {
                    loadMahasiswa(params.page || 1);

                    // Add event listener to search form
                    const searchForm = document.getElementById('searchForm');
                    if (searchForm) {
                        searchForm.addEventListener('submit', function(event) {
                            event.preventDefault();
                            loadMahasiswa(1);  // Load first page of search results
                        });
                    }

                    // Add event listener to "Tambah Data" button
                    const tambahBtn = document.querySelector('.tambah-btn');
                    if (tambahBtn) {
                        tambahBtn.addEventListener('click', function() {
                            loadContent('tambahMahasiswa');
                        });
                    }
                } else if (page === 'editMahasiswa') {
                    loadEditForm(params.id);
                } else if (page === 'detailMahasiswa') {
                    loadDetail(params.id);
                } else if (page === 'tambahMahasiswa') {
                    loadTambahForm();
                }

                // Add event listener to "Kembali" button
                const kembaliBtn = document.querySelector('.kembali-btn');
                if (kembaliBtn) {
                    kembaliBtn.addEventListener('click', function() {
                        loadContent('daftarMahasiswa');
                    });
                }
            })
            .catch(error => console.error('Error loading content:', error));
    }

    // Load the initial page
    const initialPage = new URL(window.location).searchParams.get('page') || 'daftarMahasiswa';
    loadContent(initialPage, {}, false);

    window.addEventListener('popstate', (event) => {
        if (event.state) {
            loadContent(event.state.page, event.state.params, false);
        }
    });

    document.addEventListener('click', (event) => {
        if (event.target.matches('[data-page]')) {
            const page = event.target.getAttribute('data-page');
            loadContent(page);
        } else if (event.target.matches('.edit-btn')) {
            const id = event.target.getAttribute('data-id');
            loadContent('editMahasiswa', { id: id });
        } else if (event.target.matches('.detail-btn')) {
            const id = event.target.getAttribute('data-id');
            loadContent('detailMahasiswa', { id: id });
        } else if (event.target.matches('.delete-btn')) {
            const id = event.target.getAttribute('data-id');
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                deleteMahasiswa(id);
            }
        }
    });

    function loadMahasiswa(page = 1) {
        const keyword = document.getElementById('keyword') ? document.getElementById('keyword').value : '';
        fetch(`mahasiswa_data.php?keyword=${keyword}&halaman=${page}`)
            .then(response => response.json())
            .then(data => {
                const mahasiswaList = document.getElementById('mahasiswa-list');
                if (mahasiswaList) {
                    mahasiswaList.innerHTML = data.mahasiswa.map((mhs, index) => `
                        <tr>
                            <th scope="row">${index + 1}</th>
                            <td>${mhs.Nim}</td>
                            <td>${mhs.NamaLengkap}</td>
                            <td>${mhs.Email}</td>
                            <td>${mhs.Password}</td>
                            <td>${mhs.STATUS_INPUT_SIA}</td>
                            <td>
                                <button class="btn btn-primary edit-btn" data-id="${mhs.No}">Edit</button>
                                <button class="btn btn-secondary detail-btn" data-id="${mhs.No}">Detail</button>
                                <button class="btn btn-danger delete-btn" data-id="${mhs.No}">Hapus</button>
                            </td>
                        </tr>
                    `).join('');
                }

                const pagination = document.getElementById('pagination');
                if (pagination) {
                    const totalPages = Math.ceil(data.total / data.perPage);
                    let paginationHTML = '';
                    for (let i = 1; i <= totalPages; i++) {
                        paginationHTML += `<li class="page-item ${i === page ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                    }
                    pagination.innerHTML = paginationHTML;

                    // Add event listener for pagination
                    document.querySelectorAll('#pagination .page-link').forEach(link => {
                        link.addEventListener('click', function(event) {
                            event.preventDefault();
                            loadMahasiswa(parseInt(this.getAttribute('data-page')));
                        });
                    });
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function deleteMahasiswa(id) {
        fetch(`hapus_data_mahasiswa.php?No=${id}`, {
            method: 'DELETE'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadMahasiswa();
                } else {
                    alert(`Error deleting data: ${data.error}`);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function loadEditForm(id) {
        fetch(`get_mahasiswa.php?No=${id}`)
            .then(response => response.json())
            .then(data => {
                const form = document.getElementById('editForm');

                // Populate form fields
                if (form) {
                    form.Nim.value = data.Nim;
                    form.JalurProgram.value = data.JalurProgram;
                    form.NamaLengkap.value = data.NamaLengkap;
                    form.TempatLahir.value = data.TempatLahir;
                    form.TanggalLahir.value = data.TanggalLahir;
                    form.NamaIbuKandung.value = data.NamaIbuKandung;
                    form.NIK.value = data.NIK;
                    form.NomorHP.value = data.NomorHP;
                    form.Email.value = data.Email;
                    form.Password.value = data.Password;
                    form.Agama.value = data.Agama;
                    form.JenisKelamin.value = data.JenisKelamin;
                    form.StatusPerkawinan.value = data.StatusPerkawinan;
                    form.NomorHPAlternatif.value = data.NomorHPAlternatif;
                    form.NomorIjazah.value = data.NomorIjazah;
                    form.TahunIjazah.value = data.TahunIjazah;
                    form.NISN.value = data.NISN;
                    form.LayananPaketSemester.value = data.LayananPaketSemester;
                    form.STATUS_INPUT_SIA.value = data.STATUS_INPUT_SIA;

                    // Populate jurusan select
                    const jurusanSelect = form.Jurusan;
                    if (jurusanSelect) {
                        jurusanSelect.innerHTML = '';
                        data.jurusanOptions.forEach(jurusan => {
                            const option = document.createElement('option');
                            option.value = jurusan;
                            option.text = jurusan;
                            if (data.Jurusan === jurusan) {
                                option.selected = true;
                            }
                            jurusanSelect.appendChild(option);
                        });
                    }

                    // Add event listener for form submission
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        saveEditForm(id);
                    });
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function saveEditForm(id) {
        const form = document.getElementById('editForm');
        const formData = new FormData(form);

        fetch(`edit_data.php?No=${id}`, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadContent('daftarMahasiswa');
                } else {
                    alert('Error saving data');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function loadDetail(id) {
        fetch(`get_mahasiswa.php?No=${id}`)
            .then(response => response.json())
            .then(data => {
                const detailTable = document.getElementById('mahasiswa-detail');
                if (detailTable) {
                    detailTable.innerHTML = `
                        <tr><th>NIM</th><td>${data.Nim}</td></tr>
                        <tr><th>Jalur Program</th><td>${data.JalurProgram}</td></tr>
                        <tr><th>Nama Lengkap</th><td>${data.NamaLengkap}</td></tr>
                        <tr><th>Tempat, Tanggal Lahir</th><td>${data.TempatLahir}, ${data.TanggalLahir}</td></tr>
                        <tr><th>Nama Ibu Kandung</th><td>${data.NamaIbuKandung}</td></tr>
                        <tr><th>NIK</th><td>${data.NIK}</td></tr>
                        <tr><th>Jurusan</th><td>${data.Jurusan}</td></tr>
                        <tr><th>Nomor HP</th><td>${data.NomorHP}</td></tr>
                        <tr><th>Email</th><td>${data.Email}</td></tr>
                        <tr><th>Password</th><td>${data.Password}</td></tr>
                        <tr><th>Agama</th><td>${data.Agama}</td></tr>
                        <tr><th>Jenis Kelamin</th><td>${data.JenisKelamin}</td></tr>
                        <tr><th>Status Perkawinan</th><td>${data.StatusPerkawinan}</td></tr>
                        <tr><th>Nomor HP Alternatif</th><td>${data.NomorHPAlternatif}</td></tr>
                        <tr><th>Nomor Ijazah</th><td>${data.NomorIjazah}</td></tr>
                        <tr><th>Tahun Ijazah</th><td>${data.TahunIjazah}</td></tr>
                        <tr><th>NISN</th><td>${data.NISN}</td></tr>
                        <tr><th>Layanan Paket Semester</th><td>${data.LayananPaketSemester}</td></tr>
                        <tr><th>Di Input Oleh</th><td>${data.DiInputOleh}</td></tr>
                        <tr><th>Tanggal dan Waktu Input</th><td>${data.DiInputPada}</td></tr>
                        <tr><th>Status Input Sia</th><td>${data.STATUS_INPUT_SIA}</td></tr>
                    `;
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function loadTambahForm() {
        fetch('jurusan_data.php')
            .then(response => response.json())
            .then(data => {
                const jurusanSelect = document.getElementById('jurusan');
                if (jurusanSelect) {
                    jurusanSelect.innerHTML = '';
                    data.forEach(jurusan => {
                        const option = document.createElement('option');
                        option.value = jurusan;
                        option.text = jurusan;
                        jurusanSelect.appendChild(option);
                    });

                    const form = document.getElementById('tambahMahasiswaForm');
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        saveTambahForm();
                    });
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function saveTambahForm() {
        const form = document.getElementById('tambahMahasiswaForm');
        const formData = new FormData(form);

        fetch('tambah_data.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadContent('daftarMahasiswa');
                } else {
                    alert('Error saving data');
                }
            })
            .catch(error => console.error('Error:', error));
    }
});
