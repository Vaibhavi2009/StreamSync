<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$conn = new mysqli("localhost", "root", "", "streamsync_db");

$data = json_decode(file_get_contents("php://input"), true);
$roomCode = $conn->real_escape_string($data["room_code"] ?? '');

$result = $conn->query("SELECT token FROM rooms WHERE room_code = '$roomCode'");

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "exists" => true,
        "token" => $row["token"]
    ]);
} else {
    echo json_encode(["exists" => false]);
}
?>
