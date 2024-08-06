<?php
// Koneksi ke database
require_once("../../koneksi.php");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$ids = isset($_GET['ids']) ? explode(",", $_GET['ids']) : [];
$total_admisi = $total_almamater = $total_salut = $total_spp = $total_total_bayar = $total_jumlah_pembayaran = $total_sisa = 0;
$tanggal_sekarang = date("d/m/Y");

if (!empty($ids)) {
    $ids_str = implode(",", array_map('intval', $ids));
    $sql_select = "SELECT nama_lengkap, jalur_program, jurusan, admisi, almamater, salut, spp, total_bayar, jumlah_pembayaran, sisa, status_admisi FROM catatan_bayarmaba20242 WHERE id IN ($ids_str)";
    $result = $koneksi->query($sql_select);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Print Data Terpilih</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body onload="window.print()">
<div class="container mt-5">
    <h2 class="text-center mb-4">Laporan Pembayaran</h2>
    <p class="text-right">Tana Toraja, <?php echo $tanggal_sekarang; ?></p>
    <table class="table-responsive table-bordered table-sm">
        <thead class="thead-dark">
            <tr>
                <th>Nama Lengkap</th>
                <th>Jalur Program</th>
                <th>Jurusan</th>
                <th>Admisi</th>
                <th>Almamater</th>
                <th>SALUT</th>
                <th>SPP</th>
                <th>Total Bayar</th>
                <th>Jumlah Pembayaran</th>
                <th>Sisa</th>
                <th>Status Admisi</th>
                <th>Status Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $admisi = number_format($row['admisi'], 0, ',', '.');
                    $almamater = number_format($row['almamater'], 0, ',', '.');
                    $salut = number_format($row['salut'], 0, ',', '.');
                    $spp = number_format($row['spp'], 0, ',', '.');
                    $total_bayar = number_format($row['total_bayar'], 0, ',', '.');
                    $jumlah_pembayaran = number_format($row['jumlah_pembayaran'], 0, ',', '.');
                    $sisa = number_format($row['sisa'], 0, ',', '.');
                    $admisi_status = $row['status_admisi'] == 'lunas' ? 'Lunas' : 'Belum Lunas';

                    // Status pembayaran
                    if ($row['jumlah_pembayaran'] == 0) {
                        $status_pembayaran = '<span>Belum Ada Data</span>';
                    } elseif ($row['jumlah_pembayaran'] < $row['total_bayar']) {
                        $status_pembayaran = '<span>Belum Lunas</span>';
                    } else {
                        $status_pembayaran = '<span>Lunas</span>';
                    }

                    // Hitung total
                    if ($row['status_admisi'] == 'lunas') {
                        $total_admisi += $row['admisi'];
                        if ($status_pembayaran != '<span>Belum Ada Data</span>') {
                            $total_almamater += $row['almamater'];
                            $total_salut += $row['salut'];
                            $total_spp += $row['spp'];
                            $total_total_bayar += $row['total_bayar'];
                            $total_jumlah_pembayaran += $row['jumlah_pembayaran'];
                            $total_sisa += $row['sisa'];
                        }
                    }

                    echo "<tr>";
                    echo "<td>" . $row['nama_lengkap'] . "</td>";
                    echo "<td>" . $row['jalur_program'] . "</td>";
                    echo "<td>" . $row['jurusan'] . "</td>";
                    echo "<td>Rp. " . $admisi . "</td>";
                    echo "<td>Rp. " . $almamater . "</td>";
                    echo "<td>Rp. " . $salut . "</td>";
                    echo "<td>Rp. " . $spp . "</td>";
                    echo "<td>Rp. " . $total_bayar . "</td>";
                    echo "<td>Rp. " . $jumlah_pembayaran . "</td>";
                    echo "<td>Rp. " . $sisa . "</td>";
                    echo "<td>" . $admisi_status . "</td>";
                    echo "<td>" . $status_pembayaran . "</td>";
                    echo "</tr>";
                }

                // Tambahkan baris total di akhir tabel
                echo "<tr>";
                echo "<td><strong>TOTAL</strong></td>";
                echo "<td><strong>SEMUA</strong></td>";
                echo "<td><strong>SEMUA</strong></td>";
                echo "<td><strong>Rp. " . number_format($total_admisi, 0, ',', '.') . "</strong></td>";
                echo "<td><strong>Rp. " . number_format($total_almamater, 0, ',', '.') . "</strong></td>";
                echo "<td><strong>Rp. " . number_format($total_salut, 0, ',', '.') . "</strong></td>";
                echo "<td><strong>Rp. " . number_format($total_spp, 0, ',', '.') . "</strong></td>";
                echo "<td><strong>Rp. " . number_format($total_total_bayar, 0, ',', '.') . "</strong></td>";
                echo "<td><strong>Rp. " . number_format($total_jumlah_pembayaran, 0, ',', '.') . "</strong></td>";
                echo "<td><strong>Rp. " . number_format($total_sisa, 0, ',', '.') . "</strong></td>";
                echo "<td><strong>NULL</strong></td>";
                echo "<td><strong>NULL</strong></td>";
                echo "</tr>";
            } else {
                echo "<tr><td colspan='12' class='text-center'>Tidak ada data</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <div class="mt-5 text-right">
        <p>Tana Toraja, <?php echo $tanggal_sekarang; ?></p>
        <p>Kepala Salut Tana Toraja</p>
        <br><br><br>
        <p>Ribka Padang, S.Pd., M.Pd., M.H.</p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
