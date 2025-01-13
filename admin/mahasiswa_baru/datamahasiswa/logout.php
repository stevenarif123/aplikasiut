<?php
// Receive logout request and execute mutation
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['query'])) {
    $url = 'https://api-sia.ut.ac.id/backend-sia/api/graphql';
    $headers = [
        'Content-Type: application/json',
        'User-Agent: Mozilla/5.0',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
}
?>
