<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

require_once 'Database.php';
$user = new User();
$event = new Event();

$eventID = filter_input(INPUT_GET, 'eventID', FILTER_VALIDATE_INT);

if (!$eventID) {
    handleError("Event ID is required", 400);
}

try {
    $eventData = $event->getEventID($eventID);
    if (!$eventData) {
        handleError("Event not found", 404);
    } else {
        echo json_encode($eventData);
    }
} catch (Exception $e) {
    handleError("Database error: " . $e->getMessage(), 500);
}

function handleError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit();
}
?>
