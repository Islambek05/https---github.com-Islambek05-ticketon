<?php
session_start();
require_once 'dataBase/Database.php';
$user = new User();

if (!isset($_SESSION['userName'])) {
    header("Location: logIn.php");
    exit();
}

$username = $_SESSION['userName'];
$userData = $user->getUser($username);

if ($userData['UserRole'] !== 'admin' && $userData['UserRole'] !== 'organizer') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>Add New Event</h2>

        <form method="post" action="dataBase/process_add_event.php" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="eventName" class="form-label">Event Name:</label>
                <input type="text" id="eventName" name="eventName" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="eventDate" class="form-label">Event Date:</label>
                <input type="date" id="eventDate" name="eventDate" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="eventTime" class="form-label">Event Time:</label>
                <input type="time" id="eventTime" name="eventTime" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="eventTickets" class="form-label">Tickets:</label>
                <input type="number" id="eventTickets" name="eventTickets" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location:</label>
                <input type="text" id="location" name="location" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea id="description" name="description" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label for="eventPoster" class="form-label">Event Poster:</label>
                <input type="file" id="eventPoster" name="eventPoster" accept="image/*" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Add Event</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
