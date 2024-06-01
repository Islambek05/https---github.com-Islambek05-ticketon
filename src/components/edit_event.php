<?php
session_start();
require_once 'dataBase/Database.php';
$user = new User();
$eventsData = New Event();
if (!isset($_SESSION['userName'])) {
    header("Location: logIn.php");
    exit();
}
$username = $_SESSION['userName'];
$userData = $user->getUser($username);
$eventID = isset($_GET['eventID']) ? urldecode($_GET['eventID']) : 'Default Event ID';
$event = $eventsData->getEventID($eventID);
if (!$event) {
    echo "Event not found.";
    exit();
}
if($event['Organizer'] !== $userData['Username'] && $userData['UserRole'] !== 'admin'){
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="ticketon.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h2>Edit Event: <?php echo $event['EventName']; ?></h2>
        <form method="post" action="dataBase/process_edit_event" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="newEventName" class="form-label">New Event Name:</label>
                <input type="text" id="newEventName" name="newEventName" class="form-control" value="<?php echo $event['EventName']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="newEventDate" class="form-label">New Event Date:</label>
                <input type="date" id="newEventDate" name="newEventDate" class="form-control" value="<?php echo $event['EventDate']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="newEventTime" class="form-label">New Event Time:</label>
                <input type="time" id="newEventTime" name="newEventTime" class="form-control" value="<?php echo $event['EventTime']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="newEventTickets" class="form-label">New Event Tickets:</label>
                <input type="number" id="newEventTickets" name="newEventTickets" class="form-control" value="<?php echo $event['Tickets']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="newLocation" class="form-label">New Location:</label>
                <input type="text" id="newLocation" name="newLocation" class="form-control" value="<?php echo $event['Location']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="newDescription" class="form-label">New Description:</label>
                <textarea id="newDescription" name="newDescription" class="form-control" required><?php echo $event['Description']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="newEventPoster" class="form-label">Event Poster:</label><br>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($event['EventPoster']); ?>" alt="<?php echo $event['EventName']; ?> Poster" class="img-fluid">
                <input type="file" id="newEventPoster" name="newEventPoster">
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
        <form method="post" class="mt-3">
            <button type="submit" class="btn btn-danger" name="deleteEvent">Delete Event</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
