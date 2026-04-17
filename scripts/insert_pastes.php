<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


session_start();

// Function to generate a unique 12-digit ID
function generateUniqueId($connect) {
    do {
        $randomId = mt_rand(100000000000, 999999999999); // Generate 12-digit random number
        // Check if this ID already exists in the database
        $checkQuery = "SELECT COUNT(*) FROM pastes WHERE uid = '$randomId'";
        $result = mysqli_query($connect, $checkQuery);
        $row = mysqli_fetch_row($result);
    } while ($row[0] > 0); // If ID already exists, try again

    return $randomId;
}

require_once("../database/_connect.php");

// Generate unique ID
$newId = generateUniqueId($connect);

// Set the response header to JSON
header('Content-Type: application/json');

// Check if all required fields are set
if (!isset($_POST['title']) || !isset($_POST['content']) || !isset($_POST['visibility']) || !isset($_POST['category']) || !isset($_POST['expiry'])) {
    echo json_encode(["success" => false, "error" => "Missing required fields."]);
    exit;
}



// Sanitize user inputs
$title = mysqli_real_escape_string($connect, $_POST['title']);
$content = mysqli_real_escape_string($connect, $_POST['content']);
$visibility = mysqli_real_escape_string($connect, $_POST['visibility']);
$category = mysqli_real_escape_string($connect, $_POST['category']);
$expiry = mysqli_real_escape_string($connect, $_POST['expiry']);
$password = isset($_POST['password']) ? $_POST['password'] : null;

// Check if the user is logged in and set the author
$author = isset($_SESSION['username']) ? mysqli_real_escape_string($connect, $_SESSION['username']) : null;

// Hash the password if provided
$hashedPassword = $password ? password_hash($password, PASSWORD_DEFAULT) : null;

// SQL query to insert the paste into the database
$sql = "INSERT INTO `pastes` (`uid`, `title`, `content`, `visibility`, `category`, `expiry`, `password`, `author`) 
        VALUES ('$newId', '$title', '$content', '$visibility', '$category', '$expiry', " . 
        ($hashedPassword ? "'$hashedPassword'" : "NULL") . ", " . 
        ($author ? "'$author'" : "NULL") . ")";

if (mysqli_query($connect, $sql)) {
    echo json_encode(["success" => true, "message" => "Paste created successfully."]);
} else {
    echo json_encode(["success" => false, "error" => "Error: " . mysqli_error($connect)]);
}
?>
