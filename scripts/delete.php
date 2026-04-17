<?php
session_start();
require_once '../database/_connect.php'; // Include your database connection script

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: /project/login.php");
    exit;
}

// Get the paste ID from the query string
if (!isset($_GET['id'])) {
    // If no ID is provided, redirect to the main page
    header("Location: /pastes.php");
    exit;
}

$id = intval($_GET['id']); // Sanitize the ID to prevent SQL injection

// Get the username of the logged-in user
$username = $_SESSION['username'];

// Check if the paste belongs to the logged-in user
$sql = "SELECT * FROM pastes WHERE uid = ? AND author = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("is", $id, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // If the paste doesn't exist or doesn't belong to the user, redirect
    header("Location: /pastes.php?error=unauthorized");
    exit;
}

// Delete the paste
$sql = "DELETE FROM pastes WHERE uid = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // If successful, redirect with a success message
    header("Location: /pastes.php?success=deleted");
} else {
    // If an error occurs, redirect with an error message
    header("Location: /pastes.php?error=delete_failed");
}

$stmt->close();
$connect->close();
?>
