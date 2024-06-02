<?php
class Database {
  protected $conn;

  public function __construct() {
    try {
      $this->conn = new PDO("mysql:host=localhost;dbname=ticketon", "root", "");
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (Exception $e) {
      echo "Error: " . $e->getMessage();
      exit();
    }
  }

  public function getTotalSum() {
    $sql = "SELECT COUNT(*) AS totalSum FROM events";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['totalSum'];
  }
}

class User extends Database {
  public function getUser($username) {
    $query = "SELECT * FROM users WHERE Username = :Username";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":Username", $username);
    if (!$stmt->execute()) {
      throw new Exception("Error executing the query");
    }
    return $stmt->fetch();
  }

  public function getUserEmail($email){
     $query = "SELECT Email, FirstName, LastName, Username, UserRole, UserStatus FROM users WHERE Email = :email";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":email", $email);
    if (!$stmt->execute()) {
      throw new Exception("Error executing the query");
    }
    return $stmt->fetch();
  }

  public function signUp($email, $FirstName, $LastName, $UserName, $hashedPassword) {
    $insertStmt = $this->conn->prepare("INSERT INTO users (Email, FirstName, LastName, Username, PasswordHash) VALUES (:Email, :FirstName, :LastName, :UserName, :hashedPassword)");
    $insertStmt->bindParam(':Email', $email);
    $insertStmt->bindParam(':FirstName', $FirstName);
    $insertStmt->bindParam(':LastName', $LastName);
    $insertStmt->bindParam(':UserName', $UserName);
    $insertStmt->bindParam(':hashedPassword', $hashedPassword);
    $insertStmt->execute();
  }

  public function updateUser($email, $firstName, $lastName, $userName) {
    $updateDataStmt = $this->conn->prepare("UPDATE users SET FirstName = :FirstName, LastName = :LastName, Username = :UserName WHERE Email = :Email");
    $updateDataStmt->bindParam(':FirstName', $firstName);
    $updateDataStmt->bindParam(':LastName', $lastName);
    $updateDataStmt->bindParam(':UserName', $userName);
    $updateDataStmt->bindParam(':Email', $email);
    $updateDataStmt->execute();
  }

  public function updatePassword($email, $currentPassword, $newPassword) {
    $stmt = $this->conn->prepare("SELECT PasswordHash FROM users WHERE Email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $row = $stmt->fetch();

    if ($row && password_verify($currentPassword, $row['PasswordHash'])) {
      $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
      $updatePasswordStmt = $this->conn->prepare("UPDATE users SET PasswordHash = :hashedNewPassword WHERE Email = :email");
      $updatePasswordStmt->bindParam(':hashedNewPassword', $hashedNewPassword);
      $updatePasswordStmt->bindParam(':email', $email);
      $updatePasswordStmt->execute();
      return true;
    } else {
      return false;
    }
  }

  public function deleteUser($email) {
    $deleteQuery = "DELETE FROM users WHERE Email = :userEmail";
    $deleteStmt = $this->conn->prepare($deleteQuery);
    $deleteStmt->bindParam(":userEmail", $email);
    if ($deleteStmt->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function changeRole($newRole, $username) {
    $updateQuery = "UPDATE users SET UserRole = :newRole WHERE Username = :username";
    $updateStmt = $this->conn->prepare($updateQuery);
    $updateStmt->bindParam(":newRole", $newRole);
    $updateStmt->bindParam(":username", $username);
    
    if ($updateStmt->execute()) {
        $successMessage = "User role updated successfully.";
    } else {
        $errorMessage = "Error updating user role.";
    }
  }

  public function banUser($username){
    $banQuery = "UPDATE users SET UserStatus = 'banned' WHERE Username = :username";
    $banStmt = $this->conn->prepare($banQuery);
    $banStmt->bindParam(":username", $username);

    if ($banStmt->execute()) {
        $successMessage = "User banned successfully.";
    } else {
        $errorMessage = "Error banning user.";
    }
  }

  public function unbanUser($username){
    $unbanQuery = "UPDATE users SET UserStatus = 'active' WHERE Username = :username";
    $unbanStmt = $this->conn->prepare($unbanQuery);
    $unbanStmt->bindParam(":username", $username);

    if ($unbanStmt->execute()) {
        $successMessage = "User unbanned successfully.";
    } else {
        $errorMessage = "Error unbanning user.";
    }
  }
}

class Event extends Database {
  public function getEventsBySearch($search) {
    try {
      $stmt = $this->conn->prepare("SELECT * FROM events WHERE EventName LIKE :search");
      $searchValue = '%' . $search . '%';
      $stmt->bindParam(':search', $searchValue);
      if (!$stmt->execute()) {
        throw new Exception("Error executing the query");
      }
      return $stmt->fetchAll();
    } catch (Exception $e) {
      echo 'Error: ' . $e->getMessage();
      return array();
    }
  }

  function getAllEvents() {
    $stmt = $this->conn->query("SELECT EventID, EventName, EventDate, EventPoster FROM events");
    $events = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      if ($row['EventPoster']) {
        $row['EventPoster'] = base64_encode($row['EventPoster']);
      }
      $events[] = $row;
    }
    return $events;
  }

  public function getEventID($eventID) {
    $query = "SELECT * FROM events WHERE EventID = :eventID";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":eventID", $eventID);
    if (!$stmt->execute()) {
      throw new Exception("Error executing the query");
    }
    return $stmt->fetch();
  }

  public function getOrgEvents($username) {
    $stmt = $this->conn->query("SELECT * FROM events WHERE Organizer = :Username");
    $stmt->bindParam(':Username', $username);
    $events = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      if ($row['EventPoster']) {
        $row['EventPoster'] = base64_encode($row['EventPoster']);
      }
      $events[] = $row;
    }
    return $events;
  }

  public function addEvent($eventName, $eventDate, $eventTime, $eventTickets, $location, $description, $eventPoster, $organizer) {
    $insertQuery = "INSERT INTO events (EventName, EventDate, EventTime, Tickets , Location, Description, EventPoster, Organizer) VALUES (:eventName, :eventDate, :eventTime, :eventTickets , :location, :description, :eventPoster, :organizer)";
    $insertStmt = $this->conn->prepare($insertQuery);
    $insertStmt->bindParam(":eventName", $eventName);
    $insertStmt->bindParam(":eventDate", $eventDate);
    $insertStmt->bindParam(":eventTime", $eventTime);
    $insertStmt->bindParam(":eventTickets", $eventTickets);
    $insertStmt->bindParam(":location", $location);
    $insertStmt->bindParam(":description", $description);
    $insertStmt->bindParam(":eventPoster", $eventPoster);
    $insertStmt->bindParam(":organizer", $organizer);
    $insertStmt->execute();
    return true;
  }

  public function updateEvent($eventID, $newEventName, $newEventDate, $newEventTime, $newEventTickets, $newLocation, $newDescription, $newEventPoster) {
    if ($newEventPoster !== null) {
      $updateQuery = "UPDATE events SET EventName = :newEventName, EventDate = :newEventDate, EventTime = :newEventTime, Tickets = :newEventTickets, Location = :newLocation, Description = :newDescription, EventPoster = :newEventPoster WHERE EventID = :eventID";
    } else {
      $updateQuery = "UPDATE events SET EventName = :newEventName, EventDate = :newEventDate, EventTime = :newEventTime, Tickets = :newEventTickets, Location = :newLocation, Description = :newDescription WHERE EventID = :eventID";
    }

    $updateStmt = $this->conn->prepare($updateQuery);
    $updateStmt->bindParam(":newEventName", $newEventName);
    $updateStmt->bindParam(":newEventDate", $newEventDate);
    $updateStmt->bindParam(":newEventTime", $newEventTime);
    $updateStmt->bindParam(":newEventTickets", $newEventTickets);
    $updateStmt->bindParam(":newLocation", $newLocation);
    $updateStmt->bindParam(":newDescription", $newDescription);
    if ($newEventPoster !== null) {
      $updateStmt->bindParam(":newEventPoster", $newEventPoster);
    }
    $updateStmt->bindParam(":eventID", $eventID);
    $updateStmt->execute();
}

  public function deleteEvent($eventID) {
    $deleteQuery = "DELETE FROM events WHERE EventID = :eventID";
    $deleteStmt = $this->conn->prepare($deleteQuery);
    $deleteStmt->bindParam(":eventID", $eventID);
    $deleteStmt->execute();
  }
}

class Order extends Database {
  public function getOrder($eventID, $userID) {
    $query = "SELECT * FROM orders WHERE EventID = :eventID AND UserID = :userID";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":eventID", $eventID);
    $stmt->bindParam(":userID", $userID);
    if (!$stmt->execute()) {
        throw new Exception("Error executing the query");
    }
    return $stmt->fetch();
  }

  public function setOrder($eventID, $userID, $quantity) {
    $insertQuery = "INSERT INTO orders (EventID, UserID, Quantity) VALUES (:eventID, :userID, :quantity)";
    $insertStmt = $this->conn->prepare($insertQuery);
    $insertStmt->bindParam(":eventID", $eventID);
    $insertStmt->bindParam(":userID", $userID);
    $insertStmt->bindParam(":quantity", $quantity);
    $insertStmt->execute();
  }

  public function updateOrder($eventID, $userID, $quantity) {
    $order = $this->getOrder($eventID, $userID);
    $updateQuery = "UPDATE orders SET Quantity = :quantity WHERE EventID = :eventID AND UserID = :userID";
    $updateStmt = $this->conn->prepare($updateQuery);
    $updateStmt->bindParam(":eventID", $eventID);
    $updateStmt->bindParam(":userID", $userID);
    $updateStmt->bindValue(":quantity", $order['Quantity'] + $quantity);
    $updateStmt->execute();
  }

  public function updateTickets($eventID, $quantity) {
    $eventData = new Event();
    $event = $eventData->getEventID($eventID);
    $updateQuery = "UPDATE events SET Tickets = :newEventTickets WHERE EventID = :eventID";
    $updateStmt = $this->conn->prepare($updateQuery);
    $updateStmt->bindParam(":eventID", $event['EventID']);
    $updateStmt->bindValue(":newEventTickets", $event['Tickets'] - $quantity);
    $updateStmt->execute();
  }
}
?>