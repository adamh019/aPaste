<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <?php include("includes/bootstrap.php"); ?>
    <script src="https://www.google.com/recaptcha/api.js?render=6Lc1GHUqAAAAAPKCHjXer6LJFNaiYTGtu9qrwQ3p"></script>
    <link rel="stylesheet" href="styles/loginAndSignup.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container">
    <!-- Message Box for success or error -->
    <div id="messageBox" style="display:none; padding: 10px; margin-bottom: 20px; font-size: 16px; border-radius: 5px;"></div>

    <h1 class="text-center">Sign Up</h1>

    <form id="signUpForm">
        <p id="cred">Please fill in the details below to create an account</p>

        <div class="name-container">
            <input type="text" name="firstName" placeholder="First Name" required class="half-width"/>
            <input type="text" name="lastName" placeholder="Last Name" required class="half-width"/>
        </div>
        <br>

        <input type="text" name="username" placeholder="Username" required /><br><br>
        <input type="email" name="txtEmail" placeholder="Email" required /><br><br>
        <input type="password" name="txtPass" placeholder="Password" required /><br><br>

        <button id="submitBtn" type="submit">Sign Up</button>
        <br><br>

        <p id="membSignUp">Already have an account? <a href="login.php">Log in here!</a></p>
        <a href="index.php">Back to Homepage</a>

        <!-- Hidden reCAPTCHA Token Input -->
        <input type="hidden" name="recaptchaToken" id="recaptchaToken">
    </form>

    <div id="output"></div> <!-- Div for displaying output or error messages -->

    <script>
        // When the form is submitted, prevent the default behavior and execute reCAPTCHA
        document.getElementById('signUpForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form from submitting immediately

            // Execute reCAPTCHA
            grecaptcha.execute('6Lc1GHUqAAAAAPKCHjXer6LJFNaiYTGtu9qrwQ3p', {action: 'submit'}).then(function(token) {
                // Add the token to the hidden field
                document.getElementById('recaptchaToken').value = token;

                // Now perform the AJAX request with the form data
                const formData = $('#signUpForm').serialize();

                $.ajax({
                    url: "./scripts/signupScript.php", // Ensure the path is correct
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        try {
                            // Attempt to parse the response into JSON
                            var res = JSON.parse(response);

                            var messageBox = $('#messageBox');
                            if (res.success) {
                                messageBox.text(res.message).css('background-color', 'green').css('color', 'white').show();

                                // Redirect to login.php after 2 seconds
                                setTimeout(function() {
                                    window.location.href = 'login.php'; // Redirect to login page
                                }, 2000);
                            } else {
                                messageBox.text(res.error).css('background-color', 'red').css('color', 'white').show();
                            }
                        } catch (e) {
                            console.error('Error parsing JSON:', e);
                            $('#messageBox').text('An unexpected error occurred.').css('background-color', 'red').css('color', 'white').show();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: " + status + " - " + error);
                        $('#messageBox').text('Error! Something went wrong.').css('background-color', 'red').css('color', 'white').show();
                    }
                });
            });
        });
    </script>


</div>

</body>
</html>
