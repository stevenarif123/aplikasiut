<?php
// Koneksi ke database
require_once("../../koneksi.php");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 25; // Default 25
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $per_page;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_by = isset($_GET['search_by']) ? $_GET['search_by'] : 'nama_lengkap';
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'nama_lengkap';
$order_dir = isset($_GET['order_dir']) ? $_GET['order_dir'] : 'ASC';

// Ambil data dengan pagination dan sorting
$sql_select = "SELECT id, nama_lengkap, jalur_program, jurusan, admisi, almamater, salut, spp, jumlah_pembayaran, status_admisi FROM catatan_bayarmaba20242";
if ($search != '') {
    $sql_select .= " WHERE $search_by LIKE '%$search%'";
}
$sql_select .= " ORDER BY $order_by $order_dir LIMIT $start, $per_page";
$result = $koneksi->query($sql_select);

if ($result->num_rows > 0) {
    $no = $start + 1;
    while($row = $result->fetch_assoc()) {
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
        echo "<td>" . stripslashes($row['nama_lengkap']) . "</td>";
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

$koneksi->close();
?>
