<?php
// Allow POST requests only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Only POST requests are allowed."]);
    exit;
}

// CORS headers for cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Base directory for saving photos
$baseDir = __DIR__ . '/photos/';

// Create a folder for today's date
$dateFolder = date('Y-m-d');
$photoDir = $baseDir . $dateFolder . '/';

if (!is_dir($photoDir)) {
    if (!mkdir($photoDir, 0777, true)) {
        http_response_code(500);
        echo json_encode(["message" => "Failed to create date folder."]);
        exit;
    }
}

// Get the POST data
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['image'])) {
    $imageData = $data['image'];
    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    $imageData = str_replace(' ', '+', $imageData);

    $decodedData = base64_decode($imageData);
    $fileName = $photoDir . 'photo_' . date('H-i-s') . '.png';

    if (file_put_contents($fileName, $decodedData)) {
        http_response_code(200);
        echo json_encode(["message" => "Photo saved successfully.", "filePath" => $fileName]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to save photo."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "No image data received."]);
}
?>
