<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <?php include("includes/bootstrap.php"); ?>
    <script src="https://www.google.com/recaptcha/api.js?render=6Lc1GHUqAAAAAPKCHjXer6LJFNaiYTGtu9qrwQ3p"></script>
    <link rel="stylesheet" href="styles/loginAndSignup.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php include "includes/fontGlobal.php"?>
</head>
<body>

<div class="container">
    <!-- Message Box for success or error -->
    <div id="messageBox" style="display:none; padding: 10px; margin-bottom: 20px; font-size: 16px; border-radius: 5px;"></div>

    <form id="myForm">
        <h1>LOGIN</h1>
        <p id="cred">Please enter credentials below</p>
        <input type="email" name="txtEmail" placeholder="Email" required /><br><br>
        <input type="password" name="txtPass" placeholder="Password" required /><br><br>
        <button id="submitBtn" type="submit">Login</button><br><br>
        <p id="membSignUp">Not a member? <a href="signup.php">Sign Up Now!</a></p>
        <a href="index.php">Back to Homepage</a>

        <!-- Hidden reCAPTCHA Token Input -->
        <input type="hidden" name="recaptchaToken" id="recaptchaToken">
    </form>

    <script>
    document.getElementById('myForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Execute reCAPTCHA
        grecaptcha.execute('6Lc1GHUqAAAAAPKCHjXer6LJFNaiYTGtu9qrwQ3p', {action: 'submit'}).then(function(token) {
            // Set the token value to the hidden input field
            document.getElementById('recaptchaToken').value = token;

            // Now collect form data using FormData
            var formData = new FormData(document.getElementById('myForm'));

            // Send the data via AJAX
            $.ajax({
                url: "./scripts/loginScript.php", // Ensure path is correct
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log("Raw server response:", response);  // Log the entire raw response
                    
                    try {
                        // Attempt to parse the response into JSON
                        var res = JSON.parse(response);

                        // Check the parsed response
                        console.log("Parsed response:", res);

                        var messageBox = $('#messageBox');
                        if (res.success) {
                            messageBox.text('Login successful!').css('background-color', 'green').css('color', 'white').show();
                            setTimeout(function() {
                                window.location.href = res.redirect; // Redirect after 2 seconds
                            }, 2000);
                        } else {
                            messageBox.text(res.error).css('background-color', 'red').css('color', 'white').show();
                        }
                    } catch (e) {
                        // Catch any error when parsing JSON
                        console.error('Error parsing JSON:', e);  // Log the error if parsing fails
                        console.log('Raw server response:', response);  // Log the raw response
                        $('#messageBox').text('An unexpected error occurred.').css('background-color', 'red').css('color', 'white').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + status + " - " + error);
                    alert("Error! Something went wrong!"); // Display general error
                }
            });
        });
    });
</script>


</div>

</body>
</html>
