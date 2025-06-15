<?php
// Enable CORS for development (dynamic origin)
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
} else {
    header("Access-Control-Allow-Origin: *");
}
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Connect to database
$conn = new mysqli("localhost", "root", "", "streamsync_db");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Generate unique 6-character room code (excluding confusing chars like I, O, 1, 0)
$roomCode = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 6);

// Generate secure token
$token = bin2hex(random_bytes(32));

// Insert into database
$stmt = $conn->prepare("INSERT INTO rooms (room_code, token) VALUES (?, ?)");
$stmt->bind_param("ss", $roomCode, $token);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "room_code" => $roomCode, "token" => $token]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to create room."]);
}

$stmt->close();
$conn->close();
?>
