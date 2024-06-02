<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

require_once 'Database.php';
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$key = "2005";
$user = new User();

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

if ($user->deleteUser($userEmail)) {
    $_SESSION = array();
    session_destroy();
    echo json_encode(['success' => 'User deleted successfully']);
    exit();
} else {
    handleError("Failed to delete user");
    exit();
}

function handleError($errorMessage) {
    echo json_encode(['error' => $errorMessage]);
    exit();
}
?>
