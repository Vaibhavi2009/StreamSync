<?php
// Enable error reporting (for development only — remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Dynamic CORS to allow requests from any local frontend port (e.g., 3000, 3008, 3009)
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Decode incoming JSON
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput);

// Validate input
if (!$data || !isset($data->room_code)) {
    echo json_encode(["exists" => false, "message" => "Room code is required."]);
    exit;
}

$roomCode = trim($data->room_code);

// Connect to database
$conn = new mysqli("localhost", "root", "", "streamsync_db");

if ($conn->connect_error) {
    echo json_encode(["exists" => false, "message" => "Database connection failed."]);
    exit;
}

// Look up room
$stmt = $conn->prepare("SELECT token FROM rooms WHERE room_code = ?");
$stmt->bind_param("s", $roomCode);
$stmt->execute();
$result = $stmt->get_result();

if ($room = $result->fetch_assoc()) {
    echo json_encode([
        "exists" => true,
        "token" => $room['token']
    ]);
} else {
    echo json_encode(["exists" => false, "message" => "Room not found."]);
}

$stmt->close();
$conn->close();
?>
