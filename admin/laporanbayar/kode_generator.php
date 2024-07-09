<?php
// Include database connection file
require_once "koneksi.php";

// Function to generate report code
function generateKodeLaporan($jenis_pembayaran) {
    global $koneksi; // Make the database connection available inside the function

    // Array yang menghubungkan jenis pembayaran dengan kode 2 huruf
    $kodeJenisPembayaran = array(
        "SPP" => "SP",
        "Almamater" => "AL",
        "Pokjar" => "PJ",
        "Admisi" => "AD"
    );

    // Mendapatkan kode jenis pembayaran dari array
    if (isset($kodeJenisPembayaran[$jenis_pembayaran])) {
        $kodeJenis = $kodeJenisPembayaran[$jenis_pembayaran];
        $query = "SELECT KodeLaporan FROM laporanuangmasuk20242 WHERE KodeLaporan LIKE '$kodeJenis%' ORDER BY id DESC LIMIT 1";
        $result = mysqli_query($koneksi, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                $lastKode = $row['KodeLaporan'];

                // Memproses untuk menghasilkan kode numerik baru
                $numericPart = substr($lastKode, 2);
                $newNumericPart = str_pad(intval($numericPart) + 1, 4, '0', STR_PAD_LEFT);

                // Menggabungkan kode jenis dan kode numerik untuk menghasilkan kode pembayaran baru
                $newKode = $kodeJenis . $newNumericPart;

                return $newKode;
            } else {
                // Jika tidak ada data sebelumnya, return kode awal
                return $kodeJenis . "0001";
            }
        } else {
            // Jika terjadi error saat mengambil data, return kode awal
            echo "Error fetching last report code: " . mysqli_error($koneksi); // Debugging
            return $kodeJenis . "0001";
        }
    } else {
        // Handle the case where the payment type is not found
        echo "Invalid payment type.";
    }

    // Mendapatkan kode numerik terakhir dari database
    
}
?>