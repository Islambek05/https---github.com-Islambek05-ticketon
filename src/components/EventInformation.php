<?php
session_start();
require_once 'dataBase/Database.php';
$user = new User();
$eventsData = New Event();
if (isset($_SESSION['userName'])) {
    $username = $_SESSION['userName'];
    $userData = $user->getUser($username);
} else {
    $userData = null;
}

$eventID = isset($_GET['eventID']) ? urldecode($_GET['eventID']) : '3';
$event = $eventsData->getEventID($eventID);
if (!$event) {
    echo "Event not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $event['EventName']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="ticketon.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-auto">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($event['EventPoster']); ?>" alt="<?php echo $event['EventName']; ?> Poster" class="card-img-info rounded">
        </div>
        <div class="col">
            <div>
                <h1 class="card-title"><?php echo $event['EventName']; ?></h1>
                <p class="card-text"><strong>Date:</strong> <?php echo $event['EventDate']; ?></p>
                <p class="card-text"><strong>Time:</strong> <?php echo $event['EventTime']; ?></p>
                <p class="card-text"><strong>Tickets:</strong> <?php echo $event['Tickets']; ?></p>
                <p class="card-text"><strong>Location:</strong> <?php echo $event['Location']; ?></p>
                <p class="card-text"><strong>Description:</strong> <?php echo $event['Description']; ?></p>
                <?php
                    if (isset($userData['UserRole']) && (($userData['UserRole'] == 'organizer' && $event['Organizer'] == $userData['Username']) || $userData['UserRole'] == 'admin') ) {
                        echo '<a href="edit_event.php?eventID=' . urlencode($event['EventID']) . '" class="btn btn-warning">Сhange</a><br><br>';
                        echo '<a href="eventTicket.php?eventID=' . urlencode($event['EventID']) . '" class="btn btn-warning">Info</a>';
                    }
                    if($userData !== null){
                        if ($userData['UserRole'] !== 'admin' && $userData['UserRole'] !== 'organizer') {
                            ?>
                            <form action="dataBase/buy_tickets.php" method="post">
                                <input type="hidden" name="EventID" id="EventID" class="form-control" value="<?=$event['EventID']?>">
                                <input type="number" name="nofTickets" id="nofTickets" value="1" min="1" max="<?=$event['Tickets']?>">
                                <button type='submit' class="btn btn-warning">Buy tickets</button>
                            </form>
                            <?php
                        }
                    } else {
                        if(new DateTime($event['EventDate']) > new DateTime()) {
                            echo '<a href="dataBase/buy_tickets.php?eventID=' . urlencode($event['EventID']) . '" class="btn btn-primary">Buy tickets</a>';
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>