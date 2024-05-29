document.addEventListener('DOMContentLoaded', () => {
    const app = document.getElementById('app');

    function loadContent(page, params = {}, pushState = true) {
        fetch(`templates/${page}.html`)
            .then(response => response.text())
            .then(html => {
                app.innerHTML = html;

                if (page === 'tambahLaporan') {
                    const form = document.getElementById('tambahLaporanForm');
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        const formData = new FormData(form);
                        fetch('add_laporan.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                loadContent('laporanBayar');
                            } else {
                                alert(data.error);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                    });
                }

                if (page === 'laporanBayar') {
                    loadLaporan(params.page || 1);

                    const filterForm = document.getElementById('filterForm');
                    if (filterForm) {
                        filterForm.addEventListener('submit', function(event) {
                            event.preventDefault();
                            loadLaporan(1);
                        });
                    }

                    const tambahBtn = document.querySelector('.tambah-laporan-btn');
                    if (tambahBtn) {
                        tambahBtn.addEventListener('click', function() {
                            loadContent('tambahLaporan');
                        });
                    }
                }

                const kembaliBtn = document.querySelector('.kembali-btn');
                if (kembaliBtn) {
                    kembaliBtn.addEventListener('click', function() {
                        loadContent('laporanBayar');
                    });
                }

                if (pushState) {
                    const url = new URL(window.location);
                    url.searchParams.set('page', page);
                    for (const key in params) {
                        url.searchParams.set(key, params[key]);
                    }
                    history.pushState({ page, params }, '', url);
                }
            })
            .catch(error => console.error('Error loading content:', error));
    }

    function loadLaporan(page = 1) {
        fetch(`get_laporan.php?page=${page}`)
            .then(response => response.json())
            .then(data => {
                const laporanContainer = document.getElementById('laporan-list');
                if (laporanContainer) {
                    laporanContainer.innerHTML = data.html;

                    const deleteButtons = document.querySelectorAll('.delete-btn');
                    deleteButtons.forEach(btn => {
                        btn.addEventListener('click', function() {
                            const id = this.dataset.id;
                            if (confirm('Are you sure you want to delete this report?')) {
                                fetch(`delete_laporan.php`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({ id })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        loadLaporan(page);
                                    } else {
                                        alert(data.error);
                                    }
                                })
                                .catch(error => console.error('Error:', error));
                            }
                        });
                    });
                }

                const pagination = document.getElementById('pagination');
                if (pagination) {
                    pagination.innerHTML = data.pagination;
                    const pageLinks = pagination.querySelectorAll('.page-link');
                    pageLinks.forEach(link => {
                        link.addEventListener('click', function(event) {
                            event.preventDefault();
                            const page = this.dataset.page;
                            loadLaporan(page);
                        });
                    });
                }
            })
            .catch(error => console.error('Error fetching laporan:', error));
    }

    window.addEventListener('popstate', function(event) {
        if (event.state) {
            loadContent(event.state.page, event.state.params, false);
        }
    });

    loadContent('laporanBayar');
});
