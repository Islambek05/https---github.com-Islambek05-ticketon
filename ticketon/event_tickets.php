<?php
    require_once 'Database.php';

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
    $conn = new PDO("mysql:host=localhost;dbname=ticketon;", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT COUNT(*) AS totalSum FROM events";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    $totalSum = $result['totalSum'];

    $eventID = isset($_GET['eventID']) ? urldecode($_GET['eventID']) : 'Default Event ID';

    $query = "SELECT  events.EventName, events.EventDate, events.EventTime, events.Location, events.Organizer, orders.Quantity, orders.OrderDate , users.Username, users.UserStatus, users.UserRole 
    FROM events
    JOIN orders ON orders.EventID = events.EventID 
    JOIN users on users.UserID = orders.UserID
    WHERE events.EventID = :eventID";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":eventID", $eventID);
    $stmt->execute();
    $info = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>