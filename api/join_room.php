<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$conn = new mysqli("localhost", "root", "", "streamsync_db");
$data = json_decode(file_get_contents("php://input"));
$roomCode = $conn->real_escape_string($data->room_code);

$result = $conn->query("SELECT id FROM rooms WHERE room_code = '$roomCode'");
echo json_encode(["exists" => $result->num_rows > 0]);
?>
