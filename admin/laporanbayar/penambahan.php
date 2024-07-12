<?php
require_once "../koneksi.php";
require_once "kode_generator.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$nim = $_GET['nim'] ?? '';
$nama = urldecode($_GET['nama'] ?? '');
$jurusan = $_GET['jurusan'] ?? '';

$jenis_pembayaran = "Pembayaran";
?>

    <h1 class="mb-4">Penambahan Laporan Pembayaran</h1>
    <form action="proses_penambahan.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="admin" value="<?php echo $_SESSION['username']; ?>">
    <input type="hidden" name="kode_laporan" value="<?php echo generateKodeLaporan($jenis_pembayaran); ?>">
        <div class="mb-3">
            <label for="nim" class="form-label">NIM</label>
            <input type="text" class="form-control" id="nim" name="nim" value="<?php echo $nim; ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="nama_mahasiswa" class="form-label">Nama Mahasiswa</label>
            <input type="text" class="form-control" id="nama_mahasiswa" name="nama_mahasiswa" value="<?php echo $nama; ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="jurusan" class="form-label">Jurusan</label>
            <input type="text" class="form-control" id="jurusan" name="jurusan" value="<?php echo $jurusan; ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="jumlah_bayar" class="form-label">Jumlah Bayar</label>
            <input type="number" class="form-control" id="jumlah_bayar" name="jumlah_bayar" required>
        </div>
        <div class="mb-3">
            <label for="catatan_khusus" class="form-label">Catatan Khusus</label>
            <textarea class="form-control" id="catatan_khusus" name="catatan_khusus"></textarea>
        </div>
        <div class="mb-3">
            <label for="metode_bayar" class="form-label">Metode Bayar</label>
            <select class="form-control" id="metode_bayar" name="metode_bayar" required>
                <option value="Cash">Cash</option>
                <option value="Transfer">Transfer</option>
            </select>
        </div>
        <div class="mb-3" id="bukti_transfer" style="display: none;">
            <label for="bukti_file" class="form-label">Upload Bukti Transfer</label>
            <input type="file" class="form-control" id="bukti_file" name="bukti_file">
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
<script>
document.getElementById('metode_bayar').addEventListener('change', function() {
    var metode = this.value;
    var buktiTransferDiv = document.getElementById('bukti_transfer');
    if (metode === 'Transfer') {
        buktiTransferDiv.style.display = 'block';
    } else {
        buktiTransferDiv.style.display = 'none';
    }
});
</script>
