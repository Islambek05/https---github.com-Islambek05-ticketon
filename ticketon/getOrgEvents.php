<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

require_once 'Database.php';
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$user = new User();
$event = new Event();
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

    $userData = $user->getUserEmail($userEmail);
    if (!$userData) {
        handleError("User not found");
    }

    if ($userData['UserRole'] == 'organizer') {
        $events = $event->getOrgEvents($userData['Username']);
    } 
    
    if (!$events) {
        handleError("No events found");
    }
    echo json_encode($events);


function handleError($errorMessage) {
    echo json_encode(['error' => $errorMessage]);
    exit();
}
?>
