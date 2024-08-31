<?php
// Koneksi ke database
require_once("../../koneksi.php");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil data mahasiswa baru
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 25; // Default 25
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $per_page;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_by = isset($_GET['search_by']) ? $_GET['search_by'] : 'nama_lengkap';
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'nama_lengkap';
$order_dir = isset($_GET['order_dir']) ? $_GET['order_dir'] : 'ASC';

// Hitung total data
$sql_count = "SELECT COUNT(*) as total FROM catatan_bayarmaba20242";
if ($search != '') {
    $sql_count .= " WHERE $search_by LIKE '%$search%'";
}
$result_count = $koneksi->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_data = $row_count['total'];
$total_pages = ceil($total_data / $per_page);

// Ambil data dengan pagination dan sorting
$sql_select = "SELECT id, nama_lengkap, jalur_program, jurusan, admisi, almamater, salut, spp, jumlah_pembayaran, status_admisi FROM catatan_bayarmaba20242";
if ($search != '') {
    $sql_select .= " WHERE $search_by LIKE '%$search%'";
}
$sql_select .= " ORDER BY $order_by $order_dir LIMIT $start, $per_page";
$result = $koneksi->query($sql_select);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Pengecekan Pembayaran Mahasiswa Baru</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        table.table th,
        table.table td {
            font-size: 0.9em;
        }

        .whatsapp-btn {
            background-color: #25D366;
            color: white;
        }

        .currency-input {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
            font-size: 16px;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .currency-input:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Pengecekan Pembayaran Mahasiswa Baru</h2>

        <!-- Form Pencarian -->
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" action="">
                    <div class="input-group">
                        <input type="text" name="search" id="search" class="form-control" placeholder="Cari..." value="<?php echo $search; ?>">
                        <div class="input-group-append">
                            <select name="search_by" id="search_by" class="form-control">
                                <option value="nama_lengkap" <?php if ($search_by == 'nama_lengkap') echo 'selected'; ?>>Nama</option>
                                <option value="jurusan" <?php if ($search_by == 'jurusan') echo 'selected'; ?>>Jurusan</option>
                                <option value="jalur_program" <?php if ($search_by == 'jalur_program') echo 'selected'; ?>>Jalur Program</option>
                            </select>
                            <button class="btn btn-outline-secondary" type="submit">Cari</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>No</th>
                    <th><a href="?page=<?php echo $page; ?>&per_page=<?php echo $per_page; ?>&search=<?php echo $search; ?>&search_by=<?php echo $search_by; ?>&order_by=nama_lengkap&order_dir=<?php echo ($order_by == 'nama_lengkap' && $order_dir == 'ASC') ? 'DESC' : 'ASC'; ?>">Nama Lengkap</a></th>
                    <th><a href="?page=<?php echo $page; ?>&per_page=<?php echo $per_page; ?>&search=<?php echo $search; ?>&search_by=<?php echo $search_by; ?>&order_by=jalur_program&order_dir=<?php echo ($order_by == 'jalur_program' && $order_dir == 'ASC') ? 'DESC' : 'ASC'; ?>">Jalur Program</a></th>
                    <th><a href="?page=<?php echo $page; ?>&per_page=<?php echo $per_page; ?>&search=<?php echo $search; ?>&search_by=<?php echo $search_by; ?>&order_by=jurusan&order_dir=<?php echo ($order_by == 'jurusan' && $order_dir == 'ASC') ? 'DESC' : 'ASC'; ?>">Jurusan</a></th>
                    <th>Admisi</th>
                    <th>Almamater</th>
                    <th>SALUT</th>
                    <th>SPP</th>
                    <th>Total Bayar</th>
                    <th><a href="?page=<?php echo $page; ?>&per_page=<?php echo $per_page; ?>&search=<?php echo $search; ?>&search_by=<?php echo $search_by; ?>&order_by=jumlah_pembayaran&order_dir=<?php echo ($order_by == 'jumlah_pembayaran' && $order_dir == 'ASC') ? 'DESC' : 'ASC'; ?>">Jumlah Pembayaran</a></th>
                    <th>Sisa</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="data-table">
                <?php
                if ($result->num_rows > 0) {
                    $no = $start + 1;
                    while ($row = $result->fetch_assoc()) {
                        // Hitung total bayar
                        $total_bayar = $row['almamater'] + $row['salut'] + $row['spp'];

                        // Format nilai mata uang
                        $admisi = number_format($row['admisi'], 0, ',', '.');
                        $almamater = number_format($row['almamater'], 0, ',', '.');
                        $salut = number_format($row['salut'], 0, ',', '.');
                        $spp = number_format($row['spp'], 0, ',', '.');
                        $total_bayar_formatted = number_format($total_bayar, 0, ',', '.');
                        $jumlah_pembayaran = number_format($row['jumlah_pembayaran'], 0, ',', '.');

                        // Hitung sisa
                        $sisa = $row['jumlah_pembayaran'] - $total_bayar;
                        $sisa_formatted = number_format($sisa, 0, ',', '.');

                        // Status admisi badge
                        $admisi_badge_color = $row['status_admisi'] == 'lunas' ? 'badge-success' : 'badge-warning';
                        $admisi_status = $row['status_admisi'] == 'lunas' ? 'Lunas' : 'Belum Lunas';
                        $admisi_badge = "<span class='badge $admisi_badge_color'>Rp. $admisi - $admisi_status</span>";

                        // Status pembayaran
                        if ($row['status_admisi'] != 'lunas') {
                            $status = '<span class="badge badge-danger">Belum Lunas</span>';
                        } elseif ($row['jumlah_pembayaran'] == 0) {
                            $status = '<span class="badge badge-warning">Belum Ada Data</span>';
                        } elseif ($sisa < 0) {
                            $status = '<span class="badge badge-danger">Belum Lunas</span>';
                        } elseif ($sisa == 0) {
                            $status = '<span class="badge badge-success">Lunas</span>';
                        } else {
                            $status = '<span class="badge badge-primary">Uang Lebih</span>';
                        }

                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . $row['nama_lengkap'] . "</td>";
                        echo "<td>" . $row['jalur_program'] . "</td>";
                        echo "<td>" . $row['jurusan'] . "</td>";
                        echo "<td>" . $admisi_badge . "</td>";
                        echo "<td>Rp. " . $almamater . "</td>";
                        echo "<td>Rp. " . $salut . "</td>";
                        echo "<td>Rp. " . $spp . "</td>";
                        echo "<td>Rp. " . $total_bayar_formatted . "</td>";
                        echo "<td>Rp. " . $jumlah_pembayaran . "</td>";
                        echo "<td>Rp. " . $sisa_formatted . "</td>";
                        echo "<td>" . $status . "</td>";
                        echo "<td>
                            <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#editModal' data-id='{$row['id']}' data-type='admisi' data-value='{$row['admisi']}' data-status='{$row['status_admisi']}'><i class='fas fa-edit'></i></button>
                            <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#editModal' data-id='{$row['id']}' data-type='tagihan'><i class='fas fa-plus'></i></button>
                            <button class='btn btn-success btn-sm' data-toggle='modal' data-target='#payModal' data-id='{$row['id']}' data-value='{$row['jumlah_pembayaran']}'><i class='fas fa-dollar-sign'></i></button>
                            <button class='btn whatsapp-btn btn-sm' data-toggle='modal' data-target='#whatsappModal' data-id='{$row['id']}' data-nama='{$row['nama_lengkap']}' data-jurusan='{$row['jurusan']}' data-spp='{$row['spp']}' data-salut='{$row['salut']}' data-almamater='{$row['almamater']}' data-total='{$total_bayar}' data-pembayaran='{$row['jumlah_pembayaran']}' data-sisa='{$sisa}'><i class='fab fa-whatsapp'></i></button>
                          </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='13' class='text-center'>Tidak ada data</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <?php
                for ($i = 1; $i <= $total_pages; $i++) {
                    $active = $i == $page ? 'active' : '';
                    echo "<li class='page-item $active'><a class='page-link' href='?page=$i&per_page=$per_page&search=$search&search_by=$search_by&order_by=$order_by&order_dir=$order_dir'>$i</a></li>";
                }
                ?>
            </ul>
        </nav>

        <div class="text-center">
            <form method="GET" action="">
                <label for="per_page">Data per halaman:</label>
                <select name="per_page" id="per_page" onchange="this.form.submit()">
                    <option value="10" <?php if ($per_page == 10) echo 'selected'; ?>>10</option>
                    <option value="25" <?php if ($per_page == 25) echo 'selected'; ?>>25</option>
                    <option value="50" <?php if ($per_page == 50) echo 'selected'; ?>>50</option>
                    <option value="100" <?php if ($per_page == 100) echo 'selected'; ?>>100</option>
                    <option value="<?php echo $total_data; ?>" <?php if ($per_page == $total_data) echo 'selected'; ?>>Semua</option>
                </select>
            </form>
        </div>

        <button class="btn btn-info mt-4" onclick="window.location.href='print_page.php'">Print Laporan</button>
    </div>

    <!-- Modal Tambahkan/Edit Admisi -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Admisi/Tagihan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="editId">
                        <input type="hidden" id="editType">
                        <div class="form-group" id="admisiGroup">
                            <label for="admisi">Admisi</label>
                            <input type="text" id="admisi" class="form-control currency-input">
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="admisiLunas">
                                <label class="form-check-label" for="admisiLunas">Lunas</label>
                            </div>
                        </div>
                        <div id="tagihanGroup">
                            <div class="form-group">
                                <label for="almamater">Almamater</label>
                                <input type="text" id="almamater" class="currency-input" value="200000">
                            </div>
                            <div class="form-group">
                                <label for="spp">SPP</label>
                                <input type="text" id="spp" class="currency-input">
                            </div>
                            <div class="form-group">
                                <label for="salut">SALUT</label>
                                <select class="form-control" id="salut">
                                    <option value="350000" selected>350000</option>
                                    <option value="300000">300000</option>
                                    <option value="250000">250000</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambahkan/Edit Pembayaran -->
    <div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="payModalLabel">Tambahkan/Edit Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="payForm">
                        <input type="hidden" id="payId">
                        <div class="form-group">
                            <label for="jumlahBayar">Jumlah Bayar</label>
                            <input type="text" class="form-control currency-input" id="jumlahBayar">
                        </div>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal WhatsApp Preview -->
    <div class="modal fade" id="whatsappModal" tabindex="-1" role="dialog" aria-labelledby="whatsappModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="whatsappModalLabel">WhatsApp Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="whatsappMessage"></p>
                    <button id="sendWhatsApp" class="btn btn-success">Kirim WhatsApp</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var type = button.data('type');
                var value = button.data('value') || '';
                var status = button.data('status') || 'belum lunas';

                var modal = $(this);
                modal.find('#editId').val(id);
                modal.find('#editType').val(type);

                if (type === 'admisi') {
                    modal.find('#admisiGroup').show();
                    modal.find('#tagihanGroup').hide();
                    modal.find('#admisi').val(value);
                    modal.find('#admisiLunas').prop('checked', status === 'lunas');
                } else {
                    modal.find('#admisiGroup').hide();
                    modal.find('#tagihanGroup').show();
                    modal.find('#almamater').val(200000);
                    modal.find('#salut').val(350000);
                }
            });

            $('#editForm').submit(function(event) {
                event.preventDefault();
                var id = $('#editId').val();
                var type = $('#editType').val();
                var data = {
                    id: id,
                    type: type
                };

                if (type === 'admisi') {
                    data.admisi = $('#admisi').val().replace(/[^0-9]/g, '');
                    data.status_admisi = $('#admisiLunas').is(':checked') ? 'lunas' : 'belum lunas';
                } else {
                    data.almamater = $('#almamater').val().replace(/[^0-9]/g, '');
                    data.spp = $('#spp').val().replace(/[^0-9]/g, '');
                    data.salut = $('#salut').val();
                }

                $.post('edit_payment.php', data, function(response) {
                    alert(response);
                    location.reload();
                });
            });

            $('#payModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var value = button.data('value') || '';

                var modal = $(this);
                modal.find('#payId').val(id);
                modal.find('#jumlahBayar').val(value);
            });

            $('#payForm').submit(function(event) {
                event.preventDefault();
                var id = $('#payId').val();
                var jumlahBayar = $('#jumlahBayar').val().replace(/[^0-9]/g, '');

                $.post('add_payment.php', {
                    id: id,
                    jumlah_bayar: jumlahBayar
                }, function(response) {
                    alert(response);
                    location.reload();
                });
            });

            $('#whatsappModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var nama = button.data('nama');
                var jurusan = button.data('jurusan');
                var spp = button.data('spp');
                var salut = button.data('salut');
                var almamater = button.data('almamater');
                var total = button.data('total');
                var pembayaran = button.data('pembayaran');
                var sisa = button.data('sisa');
                var waktu = new Date().getHours();
                var salam = "Selamat ";

                if (waktu >= 4 && waktu < 12) {
                    salam += "pagi";
                } else if (waktu >= 12 && waktu < 18) {
                    salam += "siang";
                } else {
                    salam += "malam";
                }

                var message = `${salam},\n\nKami sampaikan bahwa ${nama} mendaftar ${jurusan} telah Lulus dan di terima di Universitas Terbuka. Berikut kami sertakan detail pembayaran untuk semester 1.\n\n`;
                message += `SPP : Rp${Number(spp).toLocaleString('id-ID')}\nSALUT : Rp${Number(salut).toLocaleString('id-ID')}\nAlmamater* : Rp${Number(almamater).toLocaleString('id-ID')}\n\n*TOTAL* : *Rp${Number(total).toLocaleString('id-ID')}*\n\n`;

                if (pembayaran > 0) {
                    message += `*Terbayar* : Rp${Number(pembayaran).toLocaleString('id-ID')}\n`;
                    if (sisa != 0) {
                        message += `*Kurang/Lebih* : Rp${Number(sisa).toLocaleString('id-ID')}\n\n`;
                    }
                }

                message += "Ket: *Almamater bersifat opsional atau tidak wajib.\n\n";
                message += "Pembayaran dapat dilakukan melalui transfer atau cash yang diantar ke kantor. Jika melakukan pembayaran secara transfer dapat mengirim ke rekening berikut:\nNama : Ribka Padang\nBank : Mandiri\nNomor Rekening : 1700000588917\n\nKirim Bukti Transfer ke nomor 082293924242.\n\nDemikianlah penyampaian ini kami ucapkan terima kasih banyak.";

                var whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;

                $('#whatsappMessage').text(message);
                $('#sendWhatsApp').attr('href', whatsappUrl);
            });

            $('#sendWhatsApp').click(function() {
                var whatsappUrl = $(this).attr('href');
                window.open(whatsappUrl, '_blank');
            });

            // Event listener untuk input pencarian
            $('#search').on('input', function() {
                var search = $(this).val();
                var search_by = $('#search_by').val();
                var order_by = '<?php echo $order_by; ?>';
                var order_dir = '<?php echo $order_dir; ?>';
                var per_page = '<?php echo $per_page; ?>';

                $.get('search.php', {
                    search: search,
                    search_by: search_by,
                    order_by: order_by,
                    order_dir: order_dir,
                    per_page: per_page,
                    page: 1
                }, function(data) {
                    $('#data-table').html(data);
                });
            });

            // Format input currency hanya pada input dengan class "currency-input"
            $(document).on('input', '.currency-input', function() {
                var value = $(this).val();
                value = value.replace(/\D/g, '');
                value = new Intl.NumberFormat('id-ID').format(value);
                $(this).val('Rp' + value);
            });
        });
    </script>
</body>

</html>