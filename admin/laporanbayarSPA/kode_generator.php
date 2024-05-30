<?php
function generateKodeLaporan() {
    global $koneksi;
    $query = "SELECT KodeLaporan FROM laporanuangmasuk ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            $lastKode = $row['KodeLaporan'];
            $numericPart = substr($lastKode, 2);
            $newNumericPart = str_pad(intval($numericPart) + 1, 4, '0', STR_PAD_LEFT);
            return "BA" . $newNumericPart;
        } else {
            return "BA0001";
        }
    } else {
        return "BA0001";
    }
}
