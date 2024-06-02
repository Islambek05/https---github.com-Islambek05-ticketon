<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
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

$userData = $user->getUserEmail($userEmail); 
if (!$userData) {
    handleError("User not found");
    return;
}

function handleError($errorMessage) {
    echo json_encode(['error' => $errorMessage]);
    exit();
}

echo json_encode([
    'email' => $userData['Email'],
    'firstName' => $userData['FirstName'],
    'lastName' => $userData['LastName'],
    'userName' => $userData['Username'],
    'userRole' => $userData['UserRole']
]);
?>
