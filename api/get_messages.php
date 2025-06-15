<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
$conn = new mysqli("localhost", "root", "", "streamsync_db");
$roomCode = $_GET['room'] ?? '';

if (!$roomCode) {
    http_response_code(400);
    echo json_encode(["error" => "Missing room code"]);
    exit;
}
$stmt = $conn->prepare("SELECT id FROM rooms WHERE room_code = ?");
$stmt->bind_param("s", $roomCode);
$stmt->execute();
$result = $stmt->get_result();
if ($room = $result->fetch_assoc()) {
    $room_id = $room['id'];

    $stmt2 = $conn->prepare("SELECT username, message, timestamp FROM messages WHERE room_id = ? ORDER BY timestamp ASC");
    $stmt2->bind_param("i", $room_id);
    $stmt2->execute();
    $res = $stmt2->get_result();

    $messages = [];
    while ($row = $res->fetch_assoc()) {
        $messages[] = $row;
    }
    echo json_encode($messages);
    $stmt2->close();
} else {
    http_response_code(404);
    echo json_encode(["error" => "Room not found"]);
}
$stmt->close();
$conn->close();
?>