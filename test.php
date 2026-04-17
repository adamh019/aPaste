<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recaptchaToken = $_POST['recaptcha_token'];
    $secretKey = '6Lc1GHUqAAAAAIQU84kc4sTuZdJqOiwVE6oEvkVp';

    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaToken");
    $responseKeys = json_decode($response, true);

    if ($responseKeys["success"] && $responseKeys["score"] >= 0.5) {
        // Continue processing the form (e.g., save data)
        echo 'Thank you for your submission!';
    } else {
        echo 'reCAPTCHA verification failed. Please try again.';
    }
}

?>