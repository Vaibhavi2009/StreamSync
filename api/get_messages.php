<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$conn = new mysqli("localhost", "root", "", "streamsync_db");
$roomCode = $_GET['room_code'];

$roomRes = $conn->query("SELECT id FROM rooms WHERE room_code = '$roomCode'");
if ($room = $roomRes->fetch_assoc()) {
    $room_id = $room['id'];
    $res = $conn->query("SELECT username, message, timestamp FROM messages WHERE room_id = '$room_id' ORDER BY timestamp ASC");

    $messages = [];
    while ($row = $res->fetch_assoc()) {
        $messages[] = $row;
    }
    echo json_encode($messages);
}
?>
