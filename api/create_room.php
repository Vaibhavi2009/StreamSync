<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$conn = new mysqli("localhost", "root", "", "streamsync_db");

$roomCode = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 6);
$sql = "INSERT INTO rooms (room_code) VALUES ('$roomCode')";
$conn->query($sql);

echo json_encode(["room_code" => $roomCode]);
?>
