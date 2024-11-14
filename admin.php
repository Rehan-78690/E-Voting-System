<?php
include 'security/login_security.php'; 
include 'config.php'; 

start_secure_session(); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['csrf_token'])) {
        verify_csrf_token($_POST['csrf_token']);
    }

    // Get and sanitize form input
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);
    $id_field = 'admin_id';
  

    if (empty($email)) {
        $error = 'Email field is empty.';
    } elseif (empty($password)) {
        $error = 'Password field is empty.';
    } else {
        if (secure_login($email, $password, 'users', 'email', $id_field, $conn)) {
            $_SESSION['admin_id'] = $_SESSION['user_id'];

            header("Location: welcome.php");
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
    <title>Login Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img src="uprlogo.png" alt="University Logo" class="logo">
            <h3>E-voting System</h3>
            <form method="POST" action="admin.php">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="password" id="password" name="password" placeholder="Password" required>                
                <div class="form-inline">
                    <button type="submit">LOGIN</button>
                    <label><input type="checkbox" id="remember"> Remember</label>
                    <a href="forget_password.php" id="forgot-password">Forgot Password?</a>
                </div>
            </form>
            
            <?php
            if (isset($error)) {
                echo "<p class='error'>$error</p>";
            }
            ?>
            <div class="links">
                <a href="#">Admin Portal</a>
                <a href="candidate/candidate.php">Candidate Portal</a>
                <a href="elections/voter.php">Voter Portal</a>
                <a href="candidate/signup.php">No account? Sign up</a>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
