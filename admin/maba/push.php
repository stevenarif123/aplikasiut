<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');
    $dataMahasiswa = json_decode(file_get_contents('php://input'), true);

    // Convert date to the required format dd/mm/yyyy
    $dataMahasiswa['tanggalLahirMahasiswa'] = date('d/m/Y', strtotime($dataMahasiswa['tanggalLahirMahasiswa']));

    function pushDataToApi($dataMahasiswa) {
        $url = 'https://api-sia.ut.ac.id/backend-sia/api/graphql';

        $query = '
            mutation registerNewUser(
                $email: String!,
                $name: String!,
                $password: String!,
                $passwordConfirmation: String!,
                $tanggalLahirMahasiswa: String!,
                $tempatLahirMahasiswa: String!,
                $namaIbuKandung: String!,
                $nik: String,
                $noHpMahasiswa: String!,
                $idNegara: Int,
                $idUpbjj: Int!,
                $idJenjang: Int!,
                $idProdi: Int!,
                $isWna: Boolean,
                $isRpl: Boolean,
                $nomorPassport: String) {
                    registerNewUser(
                        email: $email,
                        name: $name,
                        password: $password,
                        passwordConfirmation: $password,
                        tanggalLahirMahasiswa: $tanggalLahirMahasiswa,
                        tempatLahirMahasiswa: $tempatLahirMahasiswa,
                        namaIbuKandung: $namaIbuKandung,
                        nik: $nik,
                        noHpMahasiswa: $noHpMahasiswa,
                        idNegara: $idNegara,
                        idUpbjj: $idUpbjj,
                        idJenjang: $idJenjang,
                        idProdi: $idProdi,
                        isWna: $isWna,
                        isRpl: $isRpl,
                        nomorPassport: $nomorPassport
                    ) {
                        id,
                        message
                    }
                }';

        $data = [
            'query' => $query,
            'variables' => $dataMahasiswa
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return json_encode(['error' => curl_error($ch)]);
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode != 200) {
            return json_encode(['error' => 'Request failed', 'http_code' => $httpcode, 'response' => $result]);
        }

        curl_close($ch);

        return $result;
    }

    $response = pushDataToApi($dataMahasiswa);

    if (isset($response['data']['registerNewUser']['id'])) {
        $id = $dataMahasiswa['id'];

        require_once "../koneksi.php";

        $stmt = $koneksi->prepare("UPDATE mahasiswabaru SET STATUS_INPUT_SIA = 'Input Admisi' WHERE No = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $koneksi->close();
    }

    echo $response;
    exit;
}

require_once "../koneksi.php";

if ($koneksi->connect_error) {
    die("Connection failed: " . $koneksi->connect_error);
}

$sql = "SELECT mb.*, pa.id_prodi
        FROM mahasiswabaru mb 
        JOIN prodi_admisi pa ON mb.jurusan = pa.nama_program_studi
        WHERE mb.STATUS_INPUT_SIA = 'Belum Terdaftar'";
$result = $koneksi->query($sql);

if ($result === false) {
    die("Error: Could not execute the query. " . $koneksi->error);
}

$mahasiswaData = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $mahasiswaData[] = [
            "id" => $row["No"],
            "email" => $row["Email"],
            "name" => $row["NamaLengkap"],
            "password" => $row["Password"],
            "passwordConfirmation" => $row["Password"],
            "tanggalLahirMahasiswa" => date('d/m/Y', strtotime($row["TanggalLahir"])),
            "tempatLahirMahasiswa" => $row["TempatLahir"],
            "namaIbuKandung" => $row["NamaIbuKandung"],
            "nik" => $row["NIK"],
            "noHpMahasiswa" => $row["NomorHP"],
            "idNegara" => null,
            "idUpbjj" => 29,
            "idJenjang" => 5,
            "idProdi" => (int)$row["id_prodi"],
            "isWna" => false,
            "isRpl" => false,
            "nomorPassport" => null
        ];
    }
} else {
    echo "No records found.";
}

$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Data Mahasiswa Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.1/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://unpkg.com/flowbite@1.0.4/dist/flowbite.min.css" rel="stylesheet" />
</head>
<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">Proses Data Mahasiswa Baru</h1>
    <button id="startButton" class="px-4 py-2 bg-blue-600 text-white rounded">Mulai Proses Semua</button>

    <ul id="statusList" class="mt-4 space-y-2"></ul>

    <script>
        const mahasiswaData = <?php echo json_encode($mahasiswaData); ?>;
        const statusList = document.getElementById('statusList');
        const startButton = document.getElementById('startButton');

        async function pushDataToApi(data) {
            const response = await fetch('push.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.text(); // To capture non-JSON responses
            try {
                return JSON.parse(result); // Attempt to parse the JSON response
            } catch (error) {
                return { error: "Invalid JSON response", details: result }; // Return error details if JSON parsing fails
            }
        }

        function createStatusItem(data, isProcessing = false) {
            const statusItem = document.createElement('li');
            statusItem.className = 'p-4 border rounded bg-white shadow-sm flex flex-col space-y-2';
            const info = document.createElement('span');
            info.textContent = `${data.name} (${data.email})`;
            const status = document.createElement('span');
            status.className = 'badge';
            status.textContent = isProcessing ? 'Processing...' : 'Pending';

            if (isProcessing) {
                status.className += ' text-yellow-600';
                const spinner = document.createElement('div');
                spinner.className = 'spinner-border animate-spin inline-block w-4 h-4 border-2 rounded-full border-yellow-600 border-t-transparent';
                statusItem.appendChild(spinner);
            } else {
                status.className += ' text-gray-500';
            }

            const processButton = document.createElement('button');
            processButton.textContent = "Proses";
            processButton.className = 'px-2 py-1 bg-green-500 text-white rounded';
            processButton.addEventListener('click', async () => {
                processButton.disabled = true;
                const response = await processSingleData(data);
                processButton.disabled = false;
            });

            statusItem.appendChild(info);
            statusItem.appendChild(status);
            statusItem.appendChild(processButton);

            return statusItem;
        }

        async function processSingleData(data) {
            const listItem = createStatusItem(data, true);
            statusList.appendChild(listItem);
            const response = await pushDataToApi(data);
            listItem.querySelector('.spinner-border').remove();
            listItem.querySelector('.badge').textContent = response.data && response.data.registerNewUser ? 'Success' : 'Failed';
            listItem.querySelector('.badge').className = response.data && response.data.registerNewUser ? 'badge text-green-600' : 'badge text-red-600';

            const responseDetails = document.createElement('pre');
            responseDetails.textContent = JSON.stringify(response, null, 2);
            responseDetails.className = 'mt-2 p-2 bg-gray-100 rounded text-sm';
            listItem.appendChild(responseDetails);

            if (response.data && response.data.registerNewUser) {
                updateStatusInputSIA(data.id, 'Input Admisi');
            }
        }

        async function processStudentData() {
            startButton.disabled = true;
            for (const data of mahasiswaData) {
                await processSingleData(data);
            }
            startButton.disabled = false;
        }

        async function updateStatusInputSIA(id, status) {
            const response = await fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({id, status})
            });

            return await response.json();
        }

        startButton.addEventListener('click', processStudentData);

        document.addEventListener('DOMContentLoaded', () => {
            mahasiswaData.forEach(data => {
                const listItem = createStatusItem(data);
                statusList.appendChild(listItem);
            });
        });
    </script>

    <script src="https://unpkg.com/flowbite@1.0.4/dist/flowbite.js"></script>
</body>
</html>
