document.addEventListener('DOMContentLoaded', function () {
    loadLaporan();
    setupPagination();
    setupTambahLaporanButton();
});

function loadLaporan(page = 1) {
    fetch(`api/laporan.php?page=${page}`)
        .then(response => response.json())
        .then(data => {
            renderLaporan(data.laporan);
            renderPagination(data.totalPages, page);
        })
        .catch(error => console.error('Error fetching data:', error));
}

function renderLaporan(laporan) {
    const laporanList = document.getElementById('laporan-list');
    laporanList.innerHTML = '';

    laporan.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${item.KodeLaporan}</td>
            <td>${item.JenisBayar}</td>
            <td>${item.TanggalInput}</td>
            <td>${item.NamaMahasiswa}</td>
            <td>${item.Nim}</td>
            <td>${item.Total}</td>
            <td>
                <button class="btn btn-info btn-sm" onclick="lihatDetail(${item.id})">Lihat</button>
                <button class="btn btn-danger btn-sm" onclick="hapusLaporan(${item.id})">Hapus</button>
            </td>
        `;
        laporanList.appendChild(row);
    });
}

function renderPagination(totalPages, currentPage) {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.classList.add('page-item', i === currentPage ? 'active' : '');
        li.innerHTML = `<a class="page-link" href="#" onclick="loadLaporan(${i})">${i}</a>`;
        pagination.appendChild(li);
    }
}

function setupPagination() {
    const filterForm = document.getElementById('filterForm');
    filterForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        fetch(`api/laporan.php?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                renderLaporan(data.laporan);
                renderPagination(data.totalPages, 1);
            })
            .catch(error => console.error('Error fetching data:', error));
    });
}

function lihatDetail(id) {
    fetch(`api/laporan_detail.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            const detailTableBody = document.getElementById('detail-table-body');
            detailTableBody.innerHTML = `
                <tr><th>Kode Laporan</th><td>${data.KodeLaporan}</td></tr>
                <tr><th>Nama Mahasiswa</th><td>${data.NamaMahasiswa}</td></tr>
                <tr><th>NIM</th><td>${data.Nim}</td></tr>
                <tr><th>Jurusan</th><td>${data.Jurusan}</td></tr>
                <tr><th>Jenis Bayar</th><td>${data.JenisBayar}</td></tr>
                <tr><th>Total</th><td>${data.Total}</td></tr>
                <tr><th>Catatan Khusus</th><td>${data.CatatanKhusus}</td></tr>
                <tr><th>Metode Bayar</th><td>${data.MetodeBayar}</td></tr>
            `;

            const imageContainer = document.getElementById('image-container');
            imageContainer.innerHTML = '';
            if (data.AlamatFile && data.MetodeBayar === 'Transfer') {
                const img = document.createElement('img');
                img.src = data.AlamatFile;
                img.alt = 'Bukti Transfer';
                img.classList.add('img-fluid');
                img.style.maxWidth = '300px';
                imageContainer.appendChild(img);
            }

            const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
            detailModal.show();
        })
        .catch(error => console.error('Error fetching detail:', error));
}

function hapusLaporan(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus laporan ini?')) return;

    fetch(`api/hapus_laporan.php?id=${id}`, {
        method: 'DELETE'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Laporan berhasil dihapus');
                loadLaporan();
            } else {
                alert('Gagal menghapus laporan');
            }
        })
        .catch(error => console.error('Error deleting data:', error));
}

function setupTambahLaporanButton() {
    const tambahLaporanBtn = document.querySelector('.tambah-laporan-btn');
    tambahLaporanBtn.addEventListener('click', function (event) {
        event.preventDefault();
        const url = tambahLaporanBtn.getAttribute('href');
        fetch(url)
            .then(response => response.text())
            .then(html => {
                document.body.innerHTML += html;
                const modal = new bootstrap.Modal(document.getElementById('tambahLaporanModal'));
                modal.show();
            })
            .catch(error => console.error('Error fetching tambah laporan page:', error));
    });
}
