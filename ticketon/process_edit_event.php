<?php
session_start();
require_once 'Database.php';
$user = new User();
$eventsData = New Event();
if (isset($_SESSION['userName'])) {
    $username = $_SESSION['userName'];
    $userData = $user->getUser($username);
} else {
    $userData = null;
    header("Location: ../index.php");
}
$eventID = isset($_GET['eventID']) ? urldecode($_GET['eventID']) : 'Default Event ID';
$event = $eventsData->getEventID($eventID);
if (!$event) {
    echo "Event not found.";
    exit();
}
if($event['Organizer'] !== $userData['Username'] && $userData['UserRole'] !== 'admin'){
    header("Location: ../index.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['deleteEvent'])) {
        $eventsData->deleteEvent($event['EventID']);
        header("Location: ../index.php");
        exit();
    }

    $newEventName = $_POST['newEventName'];
    $newEventDate = $_POST['newEventDate'];
    $newEventTime = $_POST['newEventTime'];
    $newEventTickets = $_POST['newEventTickets'];
    $newLocation = $_POST['newLocation'];
    $newDescription = $_POST['newDescription'];
    $newEventPoster = null;
    if ($_FILES['newEventPoster']['error'] == UPLOAD_ERR_OK) {
        $posterTmpName = $_FILES['newEventPoster']['tmp_name'];
        $posterData = file_get_contents($posterTmpName);
    }

    $eventsData->updateEvent($event['EventID'], $newEventName, $newEventDate, $newEventTime, $newEventTickets, $newLocation, $newDescription, $posterData);
    header("Location: ../EventInformation.php?eventID=" . urlencode($event['EventID']));
    exit();
}
?>
