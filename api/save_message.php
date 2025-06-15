<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "streamsync_db");

// Get POST data
$data = json_decode(file_get_contents("php://input"));

$roomCode = $data->room_code ?? '';
$token = $data->token ?? '';
$username = $data->username ?? '';
$message = $data->message ?? '';

// Validate input
if (!$roomCode || !$token || !$username || !$message) {
    echo json_encode(["error" => "Missing data"]);
    exit;
}

// Find room by code and token
$stmt = $conn->prepare("SELECT id FROM rooms WHERE room_code = ? AND token = ?");
$stmt->bind_param("ss", $roomCode, $token);
$stmt->execute();
$result = $stmt->get_result();

if ($room = $result->fetch_assoc()) {
    $room_id = $room['id'];

    // Insert the message into messages table
    $stmt2 = $conn->prepare("INSERT INTO messages (room_id, username, message) VALUES (?, ?, ?)");
    $stmt2->bind_param("iss", $room_id, $username, $message);
    $stmt2->execute();

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Unauthorized or invalid token"]);
}
?>
