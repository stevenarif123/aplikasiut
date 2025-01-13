<?php
// Koneksi ke database
require_once("../../koneksi.php");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil data mahasiswa baru
$sql_select = "SELECT id, nama_lengkap, jalur_program, jurusan, admisi, almamater, salut, spp, total_bayar, jumlah_pembayaran, sisa, status_admisi FROM catatan_bayarmaba20242";
$result = $koneksi->query($sql_select);

$selected_ids = isset($_POST['selected_ids']) ? $_POST['selected_ids'] : [];
$total_admisi = $total_almamater = $total_salut = $total_spp = $total_total_bayar = $total_jumlah_pembayaran = $total_sisa = 0;

if (isset($_POST['print'])) {
    $selected_ids_str = implode(",", $selected_ids);
    header("Location: print_selected.php?ids=$selected_ids_str");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Print Laporan Pembayaran Mahasiswa Baru</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Print Laporan Pembayaran Mahasiswa Baru</h2>
    <form method="POST" action="">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
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
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><input type='checkbox' name='selected_ids[]' value='{$row['id']}'></td>";
                        echo "<td>" . stripslashes($row['nama_lengkap']) . "</td>";
                        echo "<td>" . $row['jalur_program'] . "</td>";
                        echo "<td>" . $row['jurusan'] . "</td>";
                        echo "<td>Rp. " . number_format($row['admisi'], 0, ',', '.') . "</td>";
                        echo "<td>Rp. " . number_format($row['almamater'], 0, ',', '.') . "</td>";
                        echo "<td>Rp. " . number_format($row['salut'], 0, ',', '.') . "</td>";
                        echo "<td>Rp. " . number_format($row['spp'], 0, ',', '.') . "</td>";
                        echo "<td>Rp. " . number_format($row['total_bayar'], 0, ',', '.') . "</td>";
                        echo "<td>Rp. " . number_format($row['jumlah_pembayaran'], 0, ',', '.') . "</td>";
                        echo "<td>Rp. " . number_format($row['sisa'], 0, ',', '.') . "</td>";
                        echo "<td>" . ($row['status_admisi'] == 'lunas' ? '<span class="badge badge-success">Lunas</span>' : '<span class="badge badge-warning">Belum Lunas</span>') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='12' class='text-center'>Tidak ada data</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <button type="submit" name="print" class="btn btn-primary">Print</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){
    $("#checkAll").click(function(){
        $('input[type="checkbox"][name="selected_ids[]"]').prop('checked', this.checked);
    });

    $('input[type="checkbox"][name="selected_ids[]"]').change(function(){
        if (!$(this).prop("checked")){
            $("#checkAll").prop("checked", false);
        }
        if ($('input[type="checkbox"][name="selected_ids[]"]:checked').length == $('input[type="checkbox"][name="selected_ids[]"]').length ){
            $("#checkAll").prop("checked", true);
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
