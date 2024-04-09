<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Data Mahasiswa</title>
</head>
<body>
    <h1>Pencarian Data Mahasiswa</h1>
    <form action="detaildata.php" method="GET">
        <label for="nim">Masukkan NIM Mahasiswa:</label><br>
        <input type="text" id="nim" name="nim"><br><br>
        
        <label for="nama">Masukkan Nama Mahasiswa:</label><br>
        <input type="text" id="nama" name="nama"><br><br>
        
        <label for="jurusan">Masukkan Jurusan Mahasiswa:</label><br>
        <input type="text" id="jurusan" name="jurusan"><br><br>
        
        <input type="submit" value="Cari">
    </form>
</body>
</html>
