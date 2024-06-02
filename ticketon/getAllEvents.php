<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once 'Database.php';
$event = new Event();
$user = new User();

function handleError($message) {
    echo json_encode(['error' => $message]);
    exit();
}
try {
    $events = $event->getAllEvents();
    if (!$events) {
        handleError("No events found");
    } else {
        echo json_encode($events);
    }
} catch (Exception $e) {
    handleError("Database error: " . $e->getMessage());
}
?>
