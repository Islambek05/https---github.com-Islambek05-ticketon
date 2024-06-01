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
if (!isset($userData['UserRole']) && (($userData['UserRole'] !== 'organizer' && $event['Organizer'] !== $userData['Username']) || $userData['UserRole'] !== 'admin')) {
    header("Location: index.php");
    exit();
}
require_once 'dataBase/event_tickets.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="ticketon.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <?php if (!empty($info)): ?>
        <h1><?php echo $info[0]['EventName']; ?></h1>
        <p>Date: <?php echo isset($info[0]['EventDate']) ? $info[0]['EventDate'] : ''; ?></p>
        <p>Time: <?php echo isset($info[0]['EventTime']) ? $info[0]['EventTime'] : ''; ?></p>
        <p>Location: <?php echo isset($info[0]['Location']) ? $info[0]['Location'] : ''; ?></p>
        <p>Organizer: <?php echo isset($info[0]['Organizer']) ? $info[0]['Organizer'] : ''; ?></p>
        
        <h2>Users and Orders:</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>User Role</th>
                    <th>User Status</th>
                    <th>Quantity</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($info as $row): ?>
                    <tr>
                        <td><?php echo isset($row['Username']) ? $row['Username'] : ''; ?></td>
                        <td><?php echo isset($row['UserRole']) ? $row['UserRole'] : ''; ?></td>
                        <td><?php echo isset($row['UserStatus']) ? $row['UserStatus'] : ''; ?></td>
                        <td><?php echo isset($row['Quantity']) ? $row['Quantity'] : ''; ?></td>
                        <td><?php echo isset($row['OrderDate']) ? $row['OrderDate'] : ''; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No information available for this event.</p>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>