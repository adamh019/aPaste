<?php
// Start session
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering to prevent premature output
ob_start();

// Include database connection
require_once("../database/_connect.php");

// Check if email and password are provided
if (!isset($_POST['txtEmail']) || !isset($_POST['txtPass'])) {
    echo json_encode(['error' => 'Missing email or password']);
    exit;
}

// Validate reCAPTCHA
$recaptchaSecret = '6Lc1GHUqAAAAAIQU84kc4sTuZdJqOiwVE6oEvkVp';
$recaptchaToken = $_POST['recaptchaToken'];
$recaptchaResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaToken");
$recaptchaResult = json_decode($recaptchaResponse);

// If reCAPTCHA fails
if (!$recaptchaResult->success) {
    echo json_encode(['error' => 'reCAPTCHA verification failed']);
    exit;
}

// Sanitize and validate email
$email = filter_var($_POST['txtEmail'], FILTER_SANITIZE_EMAIL);
$password = $_POST['txtPass'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

// Query database for user
$stmt = $connect->prepare("SELECT * FROM `users` WHERE `email` = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $USER = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $USER['password'])) {
        $_SESSION['userID'] = $USER['uid'];
        $_SESSION['firstName'] = $USER['firstName'];
        $_SESSION['username'] = $USER['username'];
        
        // Send success response
        echo json_encode(['success' => true, 'redirect' => 'index.php']);
        exit;
    } else {
        echo json_encode(['error' => 'Incorrect username or password']);
        exit;
    }
} else {
    echo json_encode(['error' => 'Incorrect username or password']);
    exit;
}

// Clean up and end output buffering
ob_end_clean();
?>
