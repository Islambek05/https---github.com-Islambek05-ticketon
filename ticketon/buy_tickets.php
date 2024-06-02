<?php
session_start();
require_once 'Database.php';

$user = new User();
$eventsData = new Event();
$OrdersData = new Order();

if (!isset($_SESSION['userName'])) {
    header("Location: ../logIn.php");
    exit();
}

$username = $_SESSION['userName'];
$userData = $user->getUser($username);
$userID = $userData['UserID'];
$eventID = isset($_POST['EventID']) ? urldecode($_POST['EventID']) : 'Default Event ID';
$event = $eventsData->getEventID($eventID);
$numTickets = isset($_POST['nofTickets']) ? urldecode($_POST['nofTickets']) : null;

if ($numTickets > 0) {
    $order = $OrdersData->getOrder($event['EventID'], $userID);
    if (!$order) {
        $OrdersData->setOrder($event['EventID'], $userID, $numTickets);
    } else {
        $OrdersData->updateOrder($event['EventID'], $userID, $numTickets);
    }
    $OrdersData->updateTickets($event['EventID'], $numTickets);
} else {
    header("Location: ../EventInformation.php?eventID=" . $event['EventID']);
}

header("refresh:5;url=../index.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="ticketon.css">
</head>
<body>
    <div class="container mt-5">
        <p>You will be redirected to the main page in 5 seconds. If you don't want to wait, go to <a href="../index.php">link</a>.</p>
        <h1>You have successfully purchased <?=$numTickets?> ticket(s) for the event <?=$event['EventName']?></h1>
    </div>
</body>
</html>
