<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasiswa Billing Info</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .success {
            background-color: #d4edda;
        }
        .error {
            background-color: #f8d7da;
        }
    </style>
</head>
<body>
    <h1>Mahasiswa Billing Info</h1>
    <table>
        <thead>
            <tr>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Nobilling</th>
                <th>NAC</th>
                <th>Status</th>
                <th>Response JSON</th>
            </tr>
        </thead>
        <tbody>
<?php
require_once("../koneksi.php");
// Mengambil semua email dan password dari tabel mahasiswabaru20242
$sql = "SELECT Email, Password, NamaLengkap FROM mahasiswabaru20242";
$result = $koneksi->query($sql);

if ($result->num_rows > 0) {
    // Membuka file untuk menyimpan hasil
    $file = fopen('billing_info.txt', 'w');

    while($row = $result->fetch_assoc()) {
        $email = $row['Email'];
        $password = $row['Password'];
        $namaLengkap = $row['NamaLengkap'];

        // Mendapatkan access token
        $token = get_access_token($email, $password);

        if ($token) {
            // Mendapatkan informasi billing
            $billing_info = query_billing_info($token);
            $response_json = json_encode($billing_info);

            if ($billing_info && isset($billing_info['data']['getBillingAdmisi'])) {
                $nobilling = $billing_info['data']['getBillingAdmisi']['nobilling'];
                $nac = $billing_info['data']['getBillingAdmisi']['nac'];
                $status = "Success";
                $status_class = "success";
                fwrite($file, "Nama Lengkap: $namaLengkap\nNobilling: $nobilling\nNAC: $nac\nResponse JSON: $response_json\n\n");
            } else {
                $nobilling = "Data not found";
                $nac = "Data not found";
                $status = "Error: Data not found";
                $status_class = "error";
                fwrite($file, "Nama Lengkap: $namaLengkap\nNobilling: Data not found\nNAC: Data not found\nResponse JSON: $response_json\n\n");
            }

            // Logout
            logout($token);
        } else {
            $nobilling = "No token";
            $nac = "No token";
            $status = "Error: No token";
            $status_class = "error";
            fwrite($file, "Nama Lengkap: $namaLengkap\nNobilling: No token\nNAC: No token\n\n");
        }

        // Menampilkan hasil ke tabel HTML
        echo "<tr class='$status_class'>";
        echo "<td>$namaLengkap</td>";
        echo "<td>$email</td>";
        echo "<td>$nobilling</td>";
        echo "<td>$nac</td>";
        echo "<td>$status</td>";
        echo "<td><pre>$response_json</pre></td>";
        echo "</tr>";
    }

    fclose($file);
} else {
    echo "<tr><td colspan='6'>No data found in the table.</td></tr>";
}

// Menutup koneksi ke database
$koneksi->close();

// Fungsi untuk mendapatkan access token
function get_access_token($email, $password) {
    $url = "https://api-sia.ut.ac.id/backend-sia/api/graphql";
    $headers = [
        'Host: api-sia.ut.ac.id',
        'Content-Type: application/json',
        'Access-Control-Allow-Origin: https://admisi-sia.ut.ac.id/',
        'Accept: application/json, text/plain, */*',
        'Access-Control-Allow-Credentials: true',
        'Origin: https://admisi-sia.ut.ac.id',
        'Sec-Fetch-Site: same-site',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Dest: empty',
        'Referer: https://admisi-sia.ut.ac.id/'
    ];
    $data = json_encode([
        "query" => "
            mutation {
                signInUser(email: \"$email\", password: \"$password\"){
                    access_token
                }
            }
        ",
        "variables" => new stdClass()
    ]);

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $data
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die('Curl error: ' . curl_error($ch));
    }
    curl_close($ch);

    $response_data = json_decode($response, true);
    return isset($response_data['data']['signInUser']['access_token']) ? $response_data['data']['signInUser']['access_token'] : null;
}

// Fungsi untuk mendapatkan informasi billing
function query_billing_info($access_token) {
    $url = "https://api-sia.ut.ac.id/backend-sia/api/graphql";
    $headers = [
        'Host: api-sia.ut.ac.id',
        'Content-Type: application/json',
        'Access-Control-Allow-Origin: https://admisi-sia.ut.ac.id/',
        'Accept: application/json, text/plain, */*',
        'Access-Control-Allow-Credentials: true',
        'Origin: https://admisi-sia.ut.ac.id',
        'Sec-Fetch-Site: same-site',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Dest: empty',
        'Referer: https://admisi-sia.ut.ac.id/',
        "Authorization: Bearer $access_token"
    ];
    $data = json_encode([
        "query" => "
            query {
                getBillingAdmisi {
                    nobilling
                    nac
                }
            }
        ",
        "variables" => new stdClass()
    ]);

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $data
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die('Curl error: ' . curl_error($ch));
    }
    curl_close($ch);

    return json_decode($response, true);
}

// Fungsi untuk logout
function logout($access_token) {
    $url = "https://api-sia.ut.ac.id/backend-sia/api/graphql";
    $headers = [
        'Host: api-sia.ut.ac.id',
        'Content-Type: application/json',
        'Access-Control-Allow-Origin: https://admisi-sia.ut.ac.id/',
        'Accept: application/json, text/plain, */*',
        'Access-Control-Allow-Credentials: true',
        'Origin: https://admisi-sia.ut.ac.id',
        'Sec-Fetch-Site: same-site',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Dest: empty',
        'Referer: https://admisi-sia.ut.ac.id/',
        "Authorization: Bearer $access_token"
    ];
    $data = json_encode([
        "query" => "
            mutation {
                logout
            }
        ",
        "variables" => new stdClass()
    ]);

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $data
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die('Curl error: ' . curl_error($ch));
    }
    curl_close($ch);

    return json_decode($response, true);
}
?>
        </tbody>
    </table>
</body>
</html>
