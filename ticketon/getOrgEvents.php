<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

require_once 'Database.php';
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = "2005";
$event = new Event();
$user = new User();

function handleError($message) {
    echo json_encode(['error' => $message]);
    exit();
}

// Check if Authorization header is present
if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    handleError("Authorization header missing");
}

$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$parts = explode(" ", $authHeader);

if (count($parts) < 2) {
    handleError("Invalid token format");
}

list($type, $token) = $parts;

if (strtolower($type) !== 'bearer' || empty($token)) {
    handleError("Invalid token format or missing token");
}

try {
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    $userEmail = $decoded->email;
    $userData = $user->getUserEmail($userEmail);
    if (!$userData) {
        handleError("User not found");
    }

    if ($userData['UserRole'] == 'organizer') {
        $events = $event->getOrgEvents($userData['Username']);
    } else {
        $events = $event->getAllEvents();
    }
    
    if (!$events) {
        handleError("No events found");
    }
    echo json_encode($events);

} catch (Exception $e) {
    handleError("Invalid token: " . $e->getMessage());
}
?>
