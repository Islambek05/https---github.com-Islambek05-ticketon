<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once 'Database.php';
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$key = "2005";
$issuedAt = time();
$user = new User();

function createJWT($email, $userName) {
    global $key, $issuedAt;
    $payload = [
        'iat' => $issuedAt,
        'exp' => $issuedAt + 3600,
        'userName' => $userName,
        'email' => $email
    ];
    return JWT::encode($payload, $key, 'HS256');
}

$data = json_decode(file_get_contents('php://input'), true);

$email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
$firstName = filter_var($data['firstName'] ?? '', FILTER_SANITIZE_STRING);
$lastName = filter_var($data['lastName'] ?? '', FILTER_SANITIZE_STRING);
$userName = filter_var($data['userName'] ?? '', FILTER_SANITIZE_STRING);
$password = filter_var($data['password'] ?? '', FILTER_SANITIZE_STRING);
$confirmPassword = filter_var($data['confirmPassword'] ?? '', FILTER_SANITIZE_STRING);

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    handleError("Invalid email format");
}
if ($confirmPassword !== $password) {
    handleError("Passwords do not match");
}
if (empty($password) || strlen($password) < 8 || !preg_match("/[a-z]/", $password) ||
    !preg_match("/[A-Z]/", $password) || !preg_match("/\d/", $password) || !preg_match("/[@$!%*?&]/", $password)) {
    handleError("Password requirements not met");
}

$userDataEmail = $user->getUserEmail($email);
if ($userDataEmail) {
    handleError("User with this email already exists");
}
$userDataUsername = $user->getUser($userName);
if ($userDataUsername) {
    handleError("User with this username already exists");
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$user->signUp($email, $firstName, $lastName, $userName, $hashedPassword);
if ($token = createJWT($email, $userName)) {
    echo json_encode(['success' => true, 'token' => $token]);
    exit();
} else {
    handleError("Registration failed");
}

function handleError($errorMessage) {
    echo json_encode(['error' => $errorMessage]);
    exit();
}
?>