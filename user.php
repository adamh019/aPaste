<?php 
session_start(); // Start the session to access session variables

// Check if the 'username' parameter is passed in the URL
if(isset($_GET['username'])) {
    // Get the username from the URL
    $username = $_GET['username'];
    
    // Here you can fetch the user data from the database, for example:
    // $user_data = getUserData($username);  // This is just a placeholder function

    // Alternatively, if the username matches the session's username, you can show user-specific content
    if (isset($_SESSION['username']) && $_SESSION['username'] == $username) {
        // Show user-specific profile or settings content
        echo "<h1>Welcome to your profile, $username</h1>";
        echo "<p>Here you can edit your settings, view your posts, etc.</p>";
        
        // Example of adding a logout link or other dynamic content
        echo "<a href='logout.php'>Logout</a>";
    } else {
        echo "<h1>User not found or not logged in.</h1>";
    }
} else {
    echo "<h1>Username not provided.</h1>";
}

?>
