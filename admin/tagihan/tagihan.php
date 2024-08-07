    <h1 class="mb-4">Halaman Tagihan Mahasiswa</h1>
    <div class="form-group">
        <label for="search">Cari Mahasiswa</label>
        <input type="text" id="search" class="form-control" placeholder="Masukkan Nama atau NIM">
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            <th>Jurusan</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody id="search-results">
        <!-- Hasil pencarian akan ditampilkan di sini -->
        </tbody>
    </table>
    
    <!-- Modal Tambah Tagihan -->
    <div class="modal fade" id="addBillModal" tabindex="-1" aria-labelledby="addBillModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBillModalLabel">Tambah Tagihan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add-bill-form">
                        <div class="form-group">
                            <label for="nim">NIM</label>
                            <input type="text" class="form-control" id="nim" readonly>
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama Mahasiswa</label>
                            <input type="text" class="form-control" id="nama" readonly>
                        </div>
                        <div class="form-group">
                            <label for="jurusan">Jurusan</label>
                            <input type="text" class="form-control" id="jurusan" readonly>
                        </div>
                        <div class="form-group">
                            <label for="jenis-bayar">Jenis Tagihan</label>
                            <input type="text" class="form-control" id="jenis-bayar" required>
                        </div>
                        <div class="form-group">
                            <label for="jumlah-tagihan">Jumlah Tagihan</label>
                            <input type="number" class="form-control" id="jumlah-tagihan" required>
                        </div>
                        <input type="hidden" id="admin" value="Admin">
                        <button type="submit" class="btn btn-primary">Tambah Tagihan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function () {
        // Event listener untuk pencarian mahasiswa
        $('#search').on('keyup', function () {
            var query = $(this).val();
            if (query.length > 2) {
                $.ajax({
                    url: './proses/search.php', // Ganti dengan URL script PHP untuk pencarian
                    method: 'GET',
                    data: {query: query},
                    success: function (data) {
                        $('#search-results').html(data);
                    }
                });
            } else {
                $('#search-results').html('');
            }
        });

        // Event listener untuk menampilkan modal tambah tagihan
        $(document).on('click', '.add-bill-btn', function () {
            var nim = $(this).data('nim');
            var nama = $(this).data('nama');
            var jurusan = $(this).data('jurusan');
            $('#nim').val(nim);
            $('#nama').val(nama);
            $('#jurusan').val(jurusan);
            $('#addBillModal').modal('show');
        });

        // Event listener untuk form tambah tagihan
        $('#add-bill-form').on('submit', function (e) {
            e.preventDefault();
            var nim = $('#nim').val();
            var nama = $('#nama').val();
            var jurusan = $('#jurusan').val();
            var jenisBayar = $('#jenis-bayar').val();
            var jumlahTagihan = $('#jumlah-tagihan').val();
            var admin = $('#admin').val();
            
            $.ajax({
                url: './proses/add_bill.php', // Ganti dengan URL script PHP untuk menambah tagihan
                method: 'POST',
                data: {
                    nim: nim,
                    nama: nama,
                    jurusan: jurusan,
                    jenis_bayar: jenisBayar,
                    jumlah_tagihan: jumlahTagihan,
                    admin: admin
                },
                success: function (data) {
                    alert(data);
                    $('#addBillModal').modal('hide');
                    $('#add-bill-form')[0].reset();
                    // Update tabel saldo dan tagihan
                    updateSaldo(nim);
                }
            });
        });

        function updateSaldo(nim) {
            $.ajax({
                url: './proses/update_saldo.php', // Ganti dengan URL script PHP untuk memperbarui saldo
                method: 'POST',
                data: {nim: nim},
                success: function (data) {
                    console.log(data);
                }
            });
        }
    });
</script>