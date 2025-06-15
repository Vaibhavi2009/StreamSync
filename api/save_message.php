<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
} else {
    header("Access-Control-Allow-Origin: *");
}
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$conn = new mysqli("localhost", "root", "", "streamsync_db");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

$roomCode = $data->room_code ?? '';
$token = $data->token ?? '';
$username = $data->username ?? '';
$message = $data->message ?? '';

if (!$roomCode || !$token || !$username || !$message) {
    echo json_encode(["error" => "Missing data"]);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM rooms WHERE room_code = ? AND token = ?");
$stmt->bind_param("ss", $roomCode, $token);
$stmt->execute();
$result = $stmt->get_result();

if ($room = $result->fetch_assoc()) {
    $room_id = $room['id'];

    $stmt2 = $conn->prepare("INSERT INTO messages (room_id, username, message) VALUES (?, ?, ?)");
    $stmt2->bind_param("iss", $room_id, $username, $message);
    $stmt2->execute();
    $stmt2->close();

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Unauthorized or invalid token"]);
}

$stmt->close();
$conn->close();
?>
