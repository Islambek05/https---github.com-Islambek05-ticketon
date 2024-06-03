<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

require_once 'Database.php';
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$event = new Event();
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

$inputData = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$requiredFields = ['eventName', 'eventDate', 'eventTime', 'tickets', 'location', 'description', 'eventID'];
foreach ($requiredFields as $field) {
    if (empty($inputData[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing field: $field"]);
        exit;
    }
}

$eventID = $inputData['eventID'];
$newEventName = $inputData['eventName'];
$newEventDate = $inputData['eventDate'];
$newEventTime = $inputData['eventTime'];
$newTickets = $inputData['tickets'];
$newLocation = $inputData['location'];
$newDescription = $inputData['description'];
$newEventPoster = $_FILES['newEventPoster']['tmp_name'] ?? null;


try {
    if ($newEventPoster) {
        $posterData = file_get_contents($newEventPoster);
    } else {
        $posterData = null;
    }

    $userData = $user->getUserEmail($userEmail);
    $eventData = $event->getEventID($eventID);
    if(($userData['UserRole'] == 'organizer' && $userData['Username'] == $eventData['Organizer']) || $userData['UserRole'] == 'admin'){
        $updateResult = $event->updateEvent($eventID, $newEventName, $newEventDate, $newEventTime, $newTickets, $newLocation, $newDescription, $posterData);
    }
    if ($updateResult) {
        echo json_encode(['success' => 'Event updated successfully']);
    } else {
        throw new Exception("Failed to update the event");
    }
} catch (Exception $e) {
    http_response_code(500);
    handleError(['error' => $e->getMessage()]);
}

function handleError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit();
}
?>
