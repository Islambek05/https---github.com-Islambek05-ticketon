<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

require_once 'Database.php';
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$user = new User();
$key = "2005";

if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    handleError("Authorization header missing");
}

$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$parts = explode(" ", $authHeader);

if (count($parts) < 2) {
    handleError("Invalid token format");
    return;
}

list($type, $token) = $parts;

if (strtolower($type) !== 'bearer' || empty($token)) {
    handleError("Invalid token");
    return; 
}

try {
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    $userEmail = $decoded->email;
} catch (Exception $e) {
    handleError("Invalid token: " . $e->getMessage());
    return;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);
    $firstName = isset($data['firstName']) ? filter_var($data['firstName'], FILTER_SANITIZE_STRING) : null;
    $lastName = isset($data['lastName']) ? filter_var($data['lastName'], FILTER_SANITIZE_STRING) : null;
    $userName = isset($data['userName']) ? filter_var($data['userName'], FILTER_SANITIZE_STRING) : null;

    if (empty($firstName)) {
        handleError('First name cannot be empty');
        exit();
    }
    if (empty($lastName)) {
        handleError('Last name cannot be empty');
        exit();
    }
    if (empty($userName)) {
        handleError('Username cannot be empty');
        exit();
    }

    if ($user->updateUser($userEmail, $firstName, $lastName, $userName)) {
        echo json_encode(['success' => 'Profile updated successfully']);
    }
}

function handleError($errorMessage) {
    echo json_encode(['error' => $errorMessage]);
    exit();
}
?>
