<?php
if (!isset($userData) || $userData['UserRole'] !== 'organizer') {
    echo '<h2>Error: Access is denied</h2>';
    exit();
} else {
    try {
        $search = $_POST['InputSearch'] ?? '';
        $username = $userData['Username'];

        if ($search) {
            $events = $eventsData->getEventsBySearch($search);
        } else {
            $events = $eventsData->getOrgEvents($username);
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>

<div class="container mt-4 d-flex align-content-start flex-wrap">
    <?php
    if ($events !== null) {
        foreach ($events as $event):
    ?>
            <div class="col-sm-5 col-lg-3 mb-4">
                <div class="card event-card">
                    <a href="EventInformation.php?eventID=<?= urlencode($event['EventID']); ?>">
                        <img src="data:image/jpeg;base64,<?= base64_encode($event['EventPoster']); ?>" alt="<?= $event['EventName']; ?> Poster" class="card-img">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title"><?= $event['EventName']; ?></h5>
                        <p class="card-text"><?= $event['EventDate']; ?></p>
                    </div>
                </div>
            </div>
    <?php
        endforeach;
    } else {
        echo "<p>No events found.</p>";
    }
    ?>
</div>