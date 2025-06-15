<<<<<<< HEAD
=======
<<<<<<< HEAD
<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Read and decode the JSON body
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput);

// Validate JSON structure
if (!$data || !isset($data->email) || !isset($data->password)) {
    echo json_encode(["success" => false, "message" => "Email and password are required."]);
    exit;
}

$email = trim($data->email);
$password = trim($data->password);

// Check again in case only whitespace is sent
if ($email === "" || $password === "") {
    echo json_encode(["success" => false, "message" => "Email and password cannot be empty."]);
    exit;
}

// Connect to DB
$conn = new mysqli("localhost", "root", "", "streamsync_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email already registered."]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// ✅ FIXED: Insert into the correct column
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
$stmt->bind_param("ss", $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registration successful."]);
} else {
    echo json_encode(["success" => false, "message" => "Registration failed."]);
}

$stmt->close();
$conn->close();
?>
=======
>>>>>>> f6dfc581 (Add GitHub Actions workflow to deploy React)
<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Read and decode the JSON body
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput);

// Validate JSON structure
if (!$data || !isset($data->email) || !isset($data->password)) {
    echo json_encode(["success" => false, "message" => "Email and password are required."]);
    exit;
}

$email = trim($data->email);
$password = trim($data->password);

// Check again in case only whitespace is sent
if ($email === "" || $password === "") {
    echo json_encode(["success" => false, "message" => "Email and password cannot be empty."]);
    exit;
}

// Connect to DB
$conn = new mysqli("localhost", "root", "", "streamsync_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email already registered."]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// ✅ FIXED: Insert into the correct column
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
$stmt->bind_param("ss", $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registration successful."]);
} else {
    echo json_encode(["success" => false, "message" => "Registration failed."]);
}

$stmt->close();
$conn->close();
?>
<<<<<<< HEAD
=======
>>>>>>> 2f8f0b16 (Fix GitHub Pages deploy for React)
>>>>>>> f6dfc581 (Add GitHub Actions workflow to deploy React)
