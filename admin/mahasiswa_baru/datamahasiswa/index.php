<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function cetakDP(Email, Password) {
            // Membuka jendela pop-up untuk menampilkan halaman cetak DP
            var popUp = window.open('print_dp.php?email=' + Email + '&password=' + Password, '_blank', 'width=800,height=600');
            
            // Memantau apakah jendela pop-up sudah ditutup
            var timer = setInterval(function() {
                if (popUp.closed) {
                    clearInterval(timer);
                    // Setelah pop-up ditutup, lakukan query logout
                    fetch('logout.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            query: `#graphql
                            mutation{
                                logout
                            }`,
                            variables: {}
                        })
                    })
                    .then(response => response.json())
                    .then(data => console.log("Logout sukses:", data))
                    .catch(error => console.error("Error:", error));
                }
            }, 1000);
        }
    </script>
</head>
<body>

<h1>Daftar Mahasiswa</h1>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Mahasiswa</th>
            <th>Program Studi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Koneksi ke database
        include_once('../../koneksi.php');

        // Cek koneksi
        if ($koneksi->connect_error) {
            die("Koneksi gagal: " . $koneksi->connect_error);
        }

        // Ambil data mahasiswa dari tabel mahasiswabaru20242
        $sql = "SELECT No, NamaLengkap, Email, Password, Jurusan FROM mahasiswabaru20242";
        $result = $koneksi->query($sql);

        if ($result->num_rows > 0) {
            $no = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . htmlspecialchars($row['NamaLengkap']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Jurusan']) . "</td>";
                echo "<td><button class='btn' onclick=\"cetakDP('" . htmlspecialchars($row['Email']) . "', '" . htmlspecialchars($row['Password']) . "')\">Cetak DP</button></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Tidak ada data mahasiswa</td></tr>";
        }

        $koneksi->close();
        ?>
    </tbody>
</table>

</body>
</html>
