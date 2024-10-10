<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');
    $dataMahasiswa = json_decode(file_get_contents('php://input'), true);

    // Convert date to the required format dd/mm/yyyy
    $dataMahasiswa['tanggalLahirMahasiswa'] = date('d/m/Y', strtotime($dataMahasiswa['tanggalLahirMahasiswa']));

    function pushDataToApi($dataMahasiswa)
    {
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
                        passwordConfirmation: $passwordConfirmation,
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

        $stmt = $koneksi->prepare("UPDATE mahasiswabaru20242 SET STATUS_INPUT_SIA = 'Input Admisi' WHERE No = ?");
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
        FROM mahasiswabaru20242 mb 
        JOIN prodi_admisi pa ON mb.jurusan = pa.nama_program_studi
        WHERE mb.STATUS_INPUT_SIA = 'Belum Terdaftar'";
$result = $koneksi->query($sql);

if ($result === false) {
    die("Error: Could not execute the query. " . $koneksi->error);
}

$mahasiswaData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $isRpl = $row["JalurProgram"] === 'RPL';

        $mahasiswaData[] = [
            "id" => $row["No"],
            "email" => stripslashes($row["Email"]),
            "name" => stripslashes($row["NamaLengkap"]),
            "password" => $row["Password"],
            "passwordConfirmation" => $row["Password"],
            "tanggalLahirMahasiswa" => date('d/m/Y', strtotime($row["TanggalLahir"])),
            "tempatLahirMahasiswa" => stripslashes($row["TempatLahir"]),
            "namaIbuKandung" => stripslashes($row["NamaIbuKandung"]),
            "nik" => $row["NIK"],
            "noHpMahasiswa" => $row["NomorHP"],
            "idNegara" => null,
            "idUpbjj" => 29,
            "idJenjang" => 5,
            "idProdi" => (int)$row["id_prodi"],
            "isWna" => false,
            "isRpl" => $isRpl,
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
    <div class="container mt-4">
        <h1 class="text-2xl font-bold mb-4">Proses Data Mahasiswa Baru</h1>
        <button id="startButton" class="btn btn-primary mb-4">Mulai Proses Semua</button>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Aksi</th>
                    <th>Hasil</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mahasiswaData as $data) : ?>
                    <tr data-id="<?php echo $data['id']; ?>">
                        <td><?php echo $data['name']; ?></td>
                        <td><?php echo $data['email']; ?></td>
                        <td><span class="badge badge-secondary status">Pending</span></td>
                        <td>
                            <button class="btn btn-success btn-sm process-button">Proses</button>
                        </td>
                        <td>
                            <pre class="response-details bg-light p-2 rounded"></pre>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="progress mt-4" style="height: 25px;">
            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>
    </div>

    <script>
        const mahasiswaData = <?php echo json_encode($mahasiswaData); ?>;
        const startButton = document.getElementById('startButton');
        const progressBar = document.getElementById('progressBar');
        const tableBody = document.querySelector('tbody');

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
                return {
                    error: "Invalid JSON response",
                    details: result
                }; // Return error details if JSON parsing fails
            }
        }

        async function processSingleData(data, row) {
            const status = row.querySelector('.status');
            const processButton = row.querySelector('.process-button');
            const responseDetails = row.querySelector('.response-details');

            status.textContent = 'Processing...';
            status.classList.replace('badge-secondary', 'badge-warning');
            processButton.disabled = true;

            const response = await pushDataToApi(data);

            status.textContent = response.data && response.data.registerNewUser && response.data.registerNewUser.message.includes("Anda Berhasil melakukan pendaftaran akun") ? 'Success' : 'Failed';
            status.classList.replace('badge-warning', response.data && response.data.registerNewUser ? 'badge-success' : 'badge-danger');

            if (response.data && response.data.registerNewUser && response.data.registerNewUser.id) {
                updateStatusInputSIA(data.id, 'Input Admisi');
            }

            responseDetails.textContent = JSON.stringify(response, null, 2);
        }

        async function processStudentData() {
            startButton.disabled = true;
            let completed = 0;

            for (const data of mahasiswaData) {
                const row = document.querySelector(`tr[data-id='${data.id}']`);
                await processSingleData(data, row);
                completed++;
                const progress = Math.round((completed / mahasiswaData.length) * 100);
                progressBar.style.width = `${progress}%`;
                progressBar.setAttribute('aria-valuenow', progress);
                progressBar.textContent = `${progress}%`;
            }

            startButton.disabled = false;
        }

        async function updateStatusInputSIA(id, status) {
            const response = await fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id,
                    status
                })
            });

            return await response.json();
        }

        startButton.addEventListener('click', processStudentData);

        document.querySelectorAll('.process-button').forEach(button => {
            button.addEventListener('click', async (event) => {
                const row = event.target.closest('tr');
                const id = row.getAttribute('data-id');
                const data = mahasiswaData.find(item => item.id == id);
                await processSingleData(data, row);
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
