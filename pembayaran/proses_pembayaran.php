<?php

// Include file koneksi database
require_once "koneksi.php";

// Ambil data dari form
$jenis_pembayaran = $_POST['jenispembayaran'];
$nim = $_POST['Nim'];
$nama = $_POST['Nama'];

// Query untuk mencari data mahasiswa
$sql_mahasiswa = "SELECT * FROM mahasiswa WHERE Nim = '$nim' OR NamaLengkap = '$nama'";
$result_mahasiswa = mysqli_query($conn, $sql_mahasiswa);

if (mysqli_num_rows($result_mahasiswa) > 0) {

    // Ambil data mahasiswa
    $mahasiswa = mysqli_fetch_assoc($result_mahasiswa);

    // Query untuk mengambil detail jenis pembayaran
    $sql_detail_jenis_pembayaran = "SELECT * FROM detail_jenis_pembayaran WHERE id_jenis_pembayaran = '$jenis_pembayaran'";
    $result_detail_jenis_pembayaran = mysqli_query($conn, $sql_detail_jenis_pembayaran);

    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Form Pembayaran</title>
    </head>
    <body>

        <h1>Form Pembayaran</h1>

        <form action="simpan_pembayaran.php" method="post">

            <input type="hidden" name="jenispembayaran" value="<?php echo $jenis_pembayaran; ?>">
            <input type="hidden" name="nim" value="<?php echo $nim; ?>">
            <input type="hidden" name="nama" value="<?php echo $nama; ?>">

            <?php while ($row = mysqli_fetch_assoc($result_detail_jenis_pembayaran)) { ?>
                <label for="<?php echo $row['nama_item']; ?>"><?php echo $row['nama_item']; ?>:</label>
                <input type="number" name="<?php echo $row['nama_item']; ?>" id="<?php echo $row['nama_item']; ?>">
                <br>
            <?php } ?>

            <button type="submit">Simpan Pembayaran</button>

        </form>

    </body>
    </html>

    <?php

} else {
    echo "Data mahasiswa tidak ditemukan.";
}

?>