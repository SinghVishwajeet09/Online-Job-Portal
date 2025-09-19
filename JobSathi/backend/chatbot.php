<?php

error_reporting(0);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

// Get the raw POST data and decode it
$data = json_decode(file_get_contents('php://input'), true);
$question = isset($data['message']) ? $data['message'] : '';

if (!$question) {
    echo json_encode(['reply' => "Please enter a message."]);
    exit;
}

// Gemini API endpoint and key
$api_key = '******************************'; // <-- Replace with your Gemini API key
$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $api_key;

// Prepare the payload for Gemini
$payload = [
    "contents" => [
        [
            "parts" => [
                ["text" => $question]
            ]
        ]
    ]
];

// cURL request to Gemini API
$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false || $http_code !== 200) {
    echo json_encode(['reply' => "Sorry, I couldn't connect to the assistant."]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Parse Gemini response
$result = json_decode($response, true);
$reply = "Sorry, I couldn't understand that.";

if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    $reply = $result['candidates'][0]['content']['parts'][0]['text'];
}

echo json_encode(['reply' => $reply]);

file_put_contents("log.txt", $response);


?>
