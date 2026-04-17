<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>aPaste</title>
    <?php include("includes/bootstrap.php") ?>
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="styles/sidebar.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <?php include "includes/fontGlobal.php"?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/php.min.js"></script>
    
</head>
<body>

<?php include("includes/sidebar.php");?>

<!-- Main Content -->
<div class="main-content">
    <h1 class="display-4">Welcome to aPaste!</h1>
    <p>aPaste is a code-paster. Just paste your code into the input-field and voilà!</p>

</div>

<?php 
include("includes/form.php")
?>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelector('form').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    // Collect form data
    const formData = new FormData(event.target);

    // Send AJAX request
    fetch('scripts/insert_pastes.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Show success alert with SweetAlert2
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                });

                // Clear the form
                event.target.reset();
            } else {
                // Show error alert with SweetAlert2
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error,
                });
            }
        })
        .catch((error) => {
            // Show generic error alert
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong. Please try again.',
            });
            console.error('Error:', error);
        });
});
</script>


<script>
    // Function to toggle the visibility of the Create New Paste form
    function toggleCreateForm() {
        const form = document.getElementById('createForm');
        form.classList.toggle('open');
    }

    // Toggle profile dropdown visibility and arrow direction
    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        const arrowIcon = document.getElementById('arrowIcon');
        dropdown.classList.toggle('show');
        arrowIcon.classList.toggle('arrow-up');
    }

    // Placeholder function for navigation links
    function navigateTo(page) {
        alert("Navigating to " + page);
        // Here, you'd implement the logic to navigate or load the selected page's content
    }

    // Sidebar toggle for mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('open');
    }
</script>

</body>
</html>
