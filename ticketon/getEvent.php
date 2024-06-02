<?php
header('Access-Control-Allow-Origin: http://localhost:3000'); // Specify your front-end origin here for better security
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

require_once 'Database.php';
$event = new Event(); // Ensure the Event class has the method 'getEventByID'

function handleError($message) {
    http_response_code(404); // Send appropriate status code
    echo json_encode(['error' => $message]);
    exit();
}

// Get eventID from the URL parameters
$eventID = isset($_GET['eventID']) ? $_GET['eventID'] : null;

if (!$eventID) {
    handleError("Event ID is required");
}

try {
    $eventData = $event->getEventID($eventID); // Assume this method exists and fetches data correctly
    if (!$eventData) {
        handleError("Event not found");
    } else {
        echo json_encode($eventData); // Output the event data as JSON
    }
} catch (Exception $e) {
    handleError("Database error: " . $e->getMessage());
}
?>
