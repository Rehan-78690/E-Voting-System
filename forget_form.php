<?php
include "config.php"; // Include your database connection
session_start(); // Start session to access stored token and email

$message = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Retrieve the stored token from session
    $generated_token = $_SESSION['reset_token'];
    $reset_email = $_SESSION['reset_email'];

    // Validate token
    if ($entered_token == $generated_token) {
        // Check if passwords match
        if ($new_password === $confirm_password) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $reset_email);

            if ($stmt->execute()) {
                $message = "Password has been reset successfully.";
                // Clear the session
                session_unset();
                session_destroy();
                echo "<div class='alert alert-success'>Password has been reset successfully. Redirecting to login...</div>";

                echo '<meta http-equiv="refresh" content="2;url=admin.php">';
            } else {
                $message = "Failed to reset password. Please try again.";
            }

            $stmt->close();
        } else {
            $message = "Passwords do not match. Please try again.";
        }
    } else {
        $message = "Invalid token. Please check your email and try again.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Reset Password</h4>
                    </div>
                    <div class="card-body">
                        <!-- Display message -->
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-info">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Reset Password Form -->
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="token" class="form-label">Enter the Token:</label>
                                <input type="text" class="form-control" id="token" name="token" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password:</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password:</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
