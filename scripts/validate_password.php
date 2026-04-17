<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../database/_connect.php';

// Set the response header to JSON
header('Content-Type: application/json');

// Retrieve the JSON request data
$data = json_decode(file_get_contents('php://input'), true);

// Check if paste ID and password are provided
if (!isset($data['pasteId']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters.']);
    exit;
}

$pasteId = mysqli_real_escape_string($connect, $data['pasteId']);
$password = $data['password'];

// Query to get the paste's hashed password
$sql = "SELECT password FROM pastes WHERE uid = '$pasteId' AND visibility = 'Private'";
$result = mysqli_query($connect, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $hashedPassword = $row['password'];

    // Verify the password
    if (password_verify($password, $hashedPassword)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid password.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Paste not found or is not private.']);
}
?>
