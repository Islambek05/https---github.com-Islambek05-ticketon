<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once 'Database.php';
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$user = new User();
$key = "2005";
$issuedAt = time();

function createJWT($email, $userName) {
    global $key, $issuedAt;
    $payload = [
        'iat' => $issuedAt,
        'exp' => $issuedAt + 3600, // Token expires after one hour
        'userName' => $userName,
        'email' => $email
    ];
    return JWT::encode($payload, $key, 'HS256');
}

function handleError($errorMessage) {
    echo json_encode(['error' => $errorMessage]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    handleError("Invalid request method");
}

$data = json_decode(file_get_contents('php://input'), true);

$email = filter_var($data['email'] ?? null, FILTER_SANITIZE_EMAIL);
$password = $data['password'] ?? null;

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    handleError("Invalid email format");
}
if (empty($password)) {
    handleError("Password cannot be empty");
}

$userData = $user->getUserEmail($email);
if (!$userData) {
    handleError("User not found");
}

$userName = $userData['Username'] ?? null;
if (!$userName) {
    handleError("User name not found for the given email.");
}

$userDataUsername = $user->getUser($userName);
if (!$userData) {
    handleError("User not found");
}

if (!password_verify($password, $userDataUsername['PasswordHash'])) {
    handleError("Incorrect password");
}

try {
    $token = createJWT($email, $userName);
    if ($token) {
        echo json_encode(['success' => true, 'token' => $token]);
        exit();
    } else {
        handleError("Token generation failed");
    }
} catch (Exception $e) {
    handleError("JWT generation error: " . $e->getMessage());
}
?>
