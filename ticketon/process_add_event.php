<?php
session_start();
require_once 'Database.php';

$db = new Database();
$user = new User();
$eventsData = new Event();

if (isset($_SESSION['userName'])) {
    $username = $_SESSION['userName'];
    $userData = $user->getUser($username);
} else {
    $userData = null;
    header("Location: ../index.php");
    exit();
}

if ($userData['UserRole'] !== 'admin' && $userData['UserRole'] !== 'organizer') {
    header("Location: ../index.php");
    exit();
}

$eventName = $_POST['eventName'];
$eventDate = $_POST['eventDate'];
$eventTime = $_POST['eventTime'];
$eventTickets = $_POST['eventTickets'];
$location = $_POST['location'];
$description = $_POST['description'];
$organizer = $userData['Username'];

if ($_FILES['eventPoster']['error'] == UPLOAD_ERR_OK) {
    $posterTmpName = $_FILES['eventPoster']['tmp_name'];
    $posterData = file_get_contents($posterTmpName);
}

$result = $eventsData->addEvent($eventName, $eventDate, $eventTime, $eventTickets, $location, $description, $posterData, $organizer);

if ($result) {
    header("Location: ../index.php");
    exit();
} else {
    echo "Error: " . $result;
}
?>
