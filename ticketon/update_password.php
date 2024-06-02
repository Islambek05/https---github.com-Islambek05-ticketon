<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

require_once 'Database.php';
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$user = new User();
$key = "2005";

if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    handleError('Authorization header missing');
}

$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$parts = explode(" ", $authHeader);

if (count($parts) < 2) {
    handleError('Invalid token format');
    return;
}

list($type, $token) = $parts;

if (strtolower($type) !== 'bearer' || empty($token)) {
    handleError('Invalid token');
    return; 
}

try {
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    $userEmail = $decoded->email;
} catch (Exception $e) {
    handleError('Invalid token: ' . $e->getMessage());
    return;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);

    $currentPassword = isset($data['currentPassword']) ? filter_var($data['currentPassword'], FILTER_SANITIZE_STRING) : null;
    $newPassword = isset($data['newPassword']) ? filter_var($data['newPassword'], FILTER_SANITIZE_STRING) : null;
    $confirmNewPassword = isset($data['confirmNewPassword']) ? filter_var($data['confirmNewPassword'], FILTER_SANITIZE_STRING) : null;

    if ($currentPassword === null || $newPassword === null || $confirmNewPassword === null) {
        handleError('Please fill in all password fields.');
        exit();
    }
    if ($confirmNewPassword !== $newPassword) {
        handleError('Passwords do not match');
        exit();
    }
    if (empty($newPassword)) {
        handleError('New password cannot be empty');
        exit();
    }
    if (strlen($newPassword) < 8) {
        handleError('Password must be at least 8 characters long');
        exit();
    }
    if (!preg_match('/[0-9]/', $newPassword) || !preg_match('/[A-Z]/', $newPassword) || !preg_match('/[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/', $newPassword)) {
        handleError('Password must contain at least one number, one uppercase letter, and one special character');
        exit();
    }

    if ($user->updatePassword($userEmail, $currentPassword, $newPassword)) {
        echo json_encode(['success' => 'Password updated successfully']);
        exit();
    } else {
        handleError('Incorrect current password');
        exit();
    }
}
function handleError($errorMessage) {
    echo json_encode(['error' => $errorMessage]);
    exit();
}
?>
