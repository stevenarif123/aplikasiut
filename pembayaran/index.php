<?php

// Include file koneksi database


// Query untuk mengambil data jenis pembayaran
$sql_jenispembayaran = "SELECT * FROM jenispembayaran";
$result_jenispembayaran = mysqli_query($conn, $sql_jenispembayaran);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Input Pembayaran</title>
</head>
<body>

    <h1>Input Pembayaran</h1>

    <form action="proses_pembayaran.php" method="post">

        <label for="jenispembayaran">Jenis Pembayaran:</label>
        <select name="jenispembayaran" id="jenispembayaran">
            <?php while ($row = mysqli_fetch_assoc($result_jenispembayaran)) { ?>
                <option value="<?php echo $row['id_jenispembayaran']; ?>"><?php echo $row['nama_jenispembayaran']; ?></option>
            <?php } ?>
        </select>

        <br>

        <label for="nim">NIM:</label>
        <input type="text" name="nim" id="nim">

        <br>

        <label for="nama">Nama:</label>
        <input type="text" name="nama" id="nama">

        <br>

        <button type="submit">Proses Pembayaran</button>

    </form>

</body>
</html>