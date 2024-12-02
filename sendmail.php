<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];
    $subject = $_POST["subject"];
    $captcha = $_POST['g-recaptcha-response'];

    // Set up the email headers
    $to = "info@dreamxsol.com";
    $subject = "$subject $name";
    $headers = "From: $email\r\n" .
               "Reply-To: $email\r\n" .
               "X-Mailer: PHP/" . phpversion();

    // Verify the captcha
    $secretKey = "6LfIzM8kAAAAAA99xHoESUnoKbaXUU2rceI3z6Ri";
    $userIP = $_SERVER['REMOTE_ADDR'];
    $url = "https://www.google.com/recaptcha/api/siteverify";
    $data = array(
        'secret' => $secretKey,
        'response' => $captcha,
        'remoteip' => $userIP
    );
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $response = json_decode($response);
    if ($response->success) {
        // Captcha passed validation
        // Send the email
        if (mail($to, $subject, $message, $headers)) {
            // Output the success message
            echo '<script>alert("Message sent successfully");</script>';
        } else {
            // Output the error message
            echo '<script>alert("Message sending failed");</script>';
        }
    } else {
        // Captcha failed validation
        echo '<script>alert("Please verify that you are not a robot.");</script>';
    }

    // Redirect the user back to the contact form
    echo '<script>window.location.href = "index.html#contact";</script>';
    exit;
} else {
    // Redirect the user back to the contact form if they accessed this script directly
    header("Location: index.html#contact");
    exit;
}
?>
