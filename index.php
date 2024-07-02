<?php
    $do = explode("/", $_REQUEST['do']);
    $opsi = $do[0];
    define('PUB_DIR', dirname(__FILE__) . '/');

    switch($opsi) {
        default:
            $halaman = $opsi;
            if ($halaman == '') {
                $halaman = 'home'; // Biasanya file default adalah 'home.php'
                require_once(PUB_DIR . 'home.php');    
            } else {
                $namafile = PUB_DIR . $halaman . '.php'; // Pastikan path lengkap
                if (file_exists($namafile)) {
                    require_once($namafile);        
                } else {
                    require_once(PUB_DIR . 'error.php');  // Pastikan error.php selalu diakses dari direktori awal                
                }
            }
    }
?>
