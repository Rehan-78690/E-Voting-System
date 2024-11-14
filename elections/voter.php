<?php
include '../security/login_security.php'; // Include the security file
include 'config.php'; // Include database connection

start_secure_session(); // Start a secure session

$error = ''; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token verification (if needed)
    if (isset($_POST['csrf_token'])) {
        verify_csrf_token($_POST['csrf_token']);
    }

    // Get and sanitize form input
    $email = sanitize_input($_POST['candidate_email']);
    $password = sanitize_input($_POST['password']);
    $table = 'candidates'; // Specify the table
    $email_field = 'candidate_email'; // Candidate email field in candidates table
    $id_field = 'candidate_id'; // Candidate ID field in candidates table

    // Check if fields are empty
    if (empty($email)) {
        $error = 'Email field is empty.';
    } elseif (empty($password)) {
        $error = 'Password field is empty.';
    } else {
        // Use secure login function from the security file
        if (secure_login($email, $password, $table, $email_field, $id_field, $conn)) {
            // Store candidate ID and email in the session
            $_SESSION['candidate_id'] = $_SESSION['user_id']; // Assign 'user_id' from secure_login to 'candidate_id'
            $_SESSION['candidate_email'] = $email;

            // Redirect to candidate dashboard
            header("Location: voter_dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>voter login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img src="uprlogo.png" alt="University Logo" class="logo">
            <h3>Voter Login</h3>
            <form id="login-form" method="post" action="voter.php">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="email" id="email" name="candidate_email" placeholder="Email" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <div class="form-inline">
                    <button type="submit">LOGIN</button>
                    <label><input type="checkbox" id="remember"> Remember</label>
                    <a href="forget_password.php" id="forgot-password">Forgot Password?</a>
                </div>
            </form>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($error)) {
                echo "<p class='error'>$error</p>";
            }
            ?>
            <div class="links">
                <a href="../admin.php">Admin Portal</a>
                <a href="../candidate/candidate.php">Candidate Portal</a>
                <a href="voter.php">Voter Portal</a>
                <a href="../candidate/signup.php">No account? Sign up</a>

            </div>
        </div>
    </div>
    <!-- <script src="script.js"></script> -->
</body>
</html>
