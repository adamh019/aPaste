<?php
session_start();

// Check if all required fields are set
if (!isset($_POST['firstName']) || !isset($_POST['lastName']) || !isset($_POST['username']) || !isset($_POST['txtEmail']) || !isset($_POST['txtPass']) || !isset($_POST['recaptchaToken'])) {
    echo json_encode(["success" => false, "error" => "Missing required fields."]);
    exit;
}

require_once("../database/_connect.php");

// Sanitize user inputs
$username = mysqli_real_escape_string($connect, $_POST['username']);
$email = mysqli_real_escape_string($connect, $_POST['txtEmail']);
$firstName = mysqli_real_escape_string($connect, $_POST['firstName']);
$lastName = mysqli_real_escape_string($connect, $_POST['lastName']);
$password = $_POST['txtPass'];

// Handle reCAPTCHA
$recaptchaSecret = '6Lc1GHUqAAAAAIQU84kc4sTuZdJqOiwVE6oEvkVp'; // Replace with your actual reCAPTCHA secret key
$recaptchaResponse = $_POST['recaptchaToken']; // The token from reCAPTCHA

// Verify reCAPTCHA
$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
$responseKeys = json_decode($response, true);

if (intval($responseKeys["success"]) !== 1) {
    echo json_encode(["success" => false, "error" => "reCAPTCHA verification failed."]);
    exit;
}

// Hash the password
$hashedPw = password_hash($password, PASSWORD_DEFAULT);

// SQL query to insert the user into the database
$sql = "INSERT INTO `users` (`username`, `email`, `firstName`, `lastName`, `password`) VALUES ('$username', '$email', '$firstName', '$lastName', '$hashedPw')";

// Execute the query
if (mysqli_query($connect, $sql)) {
    echo json_encode(["success" => true, "message" => "Account created successfully. You can now log in."]);
} else {
    // Send detailed error message back to AJAX
    echo json_encode(["success" => false, "error" => "Error: " . mysqli_error($connect)]);
}
?>
