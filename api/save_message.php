<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$conn = new mysqli("localhost", "root", "", "streamsync_db");
$data = json_decode(file_get_contents("php://input"));

$roomCode = $conn->real_escape_string($data->room_code);
$username = $conn->real_escape_string($data->username);
$message = $conn->real_escape_string($data->message);

$roomRes = $conn->query("SELECT id FROM rooms WHERE room_code = '$roomCode'");
if ($room = $roomRes->fetch_assoc()) {
    $room_id = $room['id'];
    $conn->query("INSERT INTO messages (room_id, username, message) VALUES ('$room_id', '$username', '$message')");
    echo json_encode(["success" => true]);
}
?>
