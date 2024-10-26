<?php
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
    $department = $_POST['candidate_department'];
    $role = $_POST['role'];
    $designation = $_POST['designation'];
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
        // Sanitize inputs
        $name = mysqli_real_escape_string($conn, $name);
        $email = mysqli_real_escape_string($conn, $email);
        $department = mysqli_real_escape_string($conn, $department);
        $role = mysqli_real_escape_string($conn, $role);
        $designation = mysqli_real_escape_string($conn, $designation);

        // Check if email already exists
        $checkEmailSql = "SELECT * FROM candidates WHERE candidate_email = ?";
        $checkStmt = $conn->prepare($checkEmailSql);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'This email is already registered. Please log in or use a different email.';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare SQL to insert a new candidate or voter with 'Pending' status
            $sql = "INSERT INTO candidates (candidate_name, candidate_email, password, department, role, candidate_role, status) 
                    VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ssssss", $name, $email, $hashed_password, $department, $role, $designation);

                // Execute and send email if successful
                if ($stmt->execute()) {
                    sendEmailToAdmin($email, $name, $role, $designation, $department);
                    echo "Registration successful. Awaiting admin approval.";
                } else {
                    $error = "Registration failed: " . $conn->error;
                }
                $stmt->close();
            } else {
                $error = "Failed to prepare SQL statement: " . $conn->error;
            }
        }

        $checkStmt->close();
    }

    mysqli_close($conn);
}

// Function to send email to admin using PHPMailer
function sendEmailToAdmin($candidate_email, $candidate_name, $role, $designation, $department) {
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
        $mail->Subject = 'New user Signup Request';
        $mail->Body = "
            <p>A new candidate/voter has signed up.</p>
            <p><strong>Name:</strong> $candidate_name</p>
            <p><strong>Email:</strong> $candidate_email</p>
            <p><strong>Role:</strong> $role</p>
            <p><strong>Designation:</strong> $designation</p>
            <p><strong>Department:</strong> $department</p>
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
    <title>Signup to E-Voting System</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // Confirmation before form submission
        function confirmSignup() {
            var role = document.getElementById('role').value;
            return confirm('Are you sure you want to sign up as a ' + role + '?');
        }
    </script>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img src="uprlogo.png" alt="University Logo" class="logo">
            <h3>Signup to E-Voting System</h3>
            <form id="signup-form" method="post" action="signup.php" onsubmit="return confirmSignup();">
                <input type="text" name="candidate_name" placeholder="Enter your full name" required>
                <input type="text" name="candidate_department" placeholder="Department" required>
                <input type="email" name="candidate_email" placeholder="Email" required>
                
                
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <select name="role" id="role" required>
                    <option value="voter">Voter</option>
                    <option value="candidate">Candidate</option>
                </select>
                <select name="designation" id="designation" required>
                    <option value="lecturer">Lecturer</option>
                    <option value="assistant_professor">Assistant Professor</option>
                    <option value="associate_professor">Associate Professor</option>
                    <option value="professor">Professor</option>
                </select>
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
                <a href="candidate.php">Candidate Portal</a>
                <a href="candidate.php">Already have an account? Login</a>
            </div>
        </div>
    </div>
</body>
</html>
