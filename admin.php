<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   // $login = false;
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if (!empty($email) && !empty($password)) {
        // Sanitize user input to prevent SQL injection
        $email = mysqli_real_escape_string($conn, $email);
        $password = mysqli_real_escape_string($conn, $password);
        
        $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) == 1) {
            // $login = true;
            $row = mysqli_fetch_assoc($result);
            $admin_id = $row['admin_id'];
           
            session_start();
            $_SESSION['admin_id'] = $admin_id;  // Store admin_id in the session
            $_SESSION['email'] = $email;  

            header("Location: welcome.php?login=success");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Please enter your email and password.";
    }

    mysqli_close($conn);
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
            <input type="hidden" name="admin_id" value="<?php echo htmlspecialchars($admin_id); ?>">

             <input type="email" id="email" placeholder="Email" name="email" required>
                <input type="password" id="password" placeholder="Password" name="password" required>
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
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
