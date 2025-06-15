<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$conn = new mysqli("localhost", "root", "", "streamsync_db");

$roomCode = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 6);

$token = bin2hex(random_bytes(32));

$stmt = $conn->prepare("INSERT INTO rooms (room_code, token) VALUES (?, ?)");
$stmt->bind_param("ss", $roomCode, $token);
$stmt->execute();


echo json_encode(["room_code" => $roomCode, "token" => $token]);
?>
