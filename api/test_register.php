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

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput);

echo json_encode([
    "raw_input" => $rawInput,
    "json_decode_error" => json_last_error_msg(),
    "decoded" => $data,
    "email" => $data->email ?? null,
    "password" => $data->password ?? null
]);
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

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput);

echo json_encode([
    "raw_input" => $rawInput,
    "json_decode_error" => json_last_error_msg(),
    "decoded" => $data,
    "email" => $data->email ?? null,
    "password" => $data->password ?? null
]);
<<<<<<< HEAD
=======
>>>>>>> 2f8f0b16 (Fix GitHub Pages deploy for React)
>>>>>>> f6dfc581 (Add GitHub Actions workflow to deploy React)
