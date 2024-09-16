<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Forgot Password</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="forget_password.php">
                            <div class="mb-3">
                                <label for="email" class="form-label">Enter your email address:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load Composer's autoloader

include "config.php"; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT voter_id, name FROM voters WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    

    if ($stmt->num_rows > 0) {
        
        $stmt->fetch();

        // Generate a unique token
        $token = md5(uniqid(date('YmdHis'), true));

        // Update the token in the voters table
        $stmt = $conn->prepare("UPDATE voters SET token = ? WHERE voter_id = ?");
        $stmt->bind_param("si", $token, $voter_id);
        $stmt->execute();

        // Prepare the password reset link
       $reset_link = "http://localhost/fyp2/forget_form.php?token=" . $token;


        // Send the email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'rehankhan.upr@gmail.com'; // Your Gmail address
    $mail->Password = 'ccqu utkq itfm lznb'; // App Password or Gmail Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
    $mail->Port = 587; // TCP port to connect to

    // Recipients
    $mail->setFrom('UPRSenate@upr.edu.pk', 'UPR Senate22');
    $mail->addAddress($email, 'Recipient Name');

    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body in bold!';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "No account found with that email address.";
    }

    $stmt->close();
}

$conn->close();
?>
