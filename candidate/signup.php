<?php
// Include database configuration
include 'config.php';

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load Composer's autoloader

$error = '';

// Handle sign-up form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['candidate_name'];
    $email = $_POST['candidate_email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if (empty($name)) {
        $error = 'Name field is empty.';
    } elseif (empty($email)) {
        $error = 'Email field is empty.';
    } elseif (empty($password)) {
        $error = 'Password field is empty.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Hash password and sanitize inputs
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $name = mysqli_real_escape_string($conn, $name);
        $email = mysqli_real_escape_string($conn, $email);

        // Prepare SQL to insert a new candidate with 'Pending' status
        $sql = "INSERT INTO candidates (candidate_name, candidate_email, password, status) VALUES (?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            // Execute and send email if successful
            if ($stmt->execute()) {
                sendEmailToAdmin($email, $name);
                echo "Registration successful. Awaiting admin approval.";
            } else {
                $error = "Registration failed: " . $conn->error;
            }
            $stmt->close();
        } else {
            $error = "Failed to prepare SQL statement: " . $conn->error;
        }
    }

    mysqli_close($conn);
}

// Function to send email to admin using PHPMailer
function sendEmailToAdmin($candidate_email, $candidate_name) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'rehankhan.upr@gmail.com'; // Your Gmail address
        $mail->Password = 'ccqu utkq itfm lznb'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('UPRSenate@upr.edu.pk', 'UPR Senate22');
        $mail->addAddress('rehankhan.upr@gmail.com', 'Rehan Khan');
        $mail->isHTML(true);
        $mail->Subject = 'New Candidate Signup Request';
        $mail->Body = "
            <p>A new candidate has signed up.</p>
            <p><strong>Name:</strong> $candidate_name</p>
            <p><strong>Email:</strong> $candidate_email</p>
            <p>Please review and approve or reject the request in the admin portal.</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Signup</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img src="uprlogo.png" alt="University Logo" class="logo">
            <h3>Candidate Signup</h3>
            <form id="signup-form" method="post" action="signup.php">
                <input type="text" name="candidate_name" placeholder="Enter your full name" required>
                <input type="email" name="candidate_email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">SIGN UP</button>
            </form>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($error)) {
                echo "<p class='error'>$error</p>";
            }
            ?>
            <div class="links">
                <a href="../admin.php">Admin Portal</a>
                <a href="../elections/voter.php">Voter Portal</a>
                <a href="candidate.php">Already have an account? Login</a>
            </div>
        </div>
    </div>
</body>
</html>
