<?php
include 'config.php';

session_start(); // Start the session at the beginning

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email)) {
        $error = 'Email field is empty.';
    } elseif (empty($password)) {
        $error = 'Password field is empty.';
    } else {
        $email = mysqli_real_escape_string($conn, $email);
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            
            $result = $stmt->get_result();

            if ($result && $result->num_rows == 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['admin_id'] = $user['admin_id'];

                    // Redirect to welcome page
                    header("Location: welcome.php?success");
                    exit();
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }

            $stmt->close();
        } else {
            $error = "Failed to prepare SQL statement: " . $conn->error;
        }
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
                <a href="candidate/signup.php">No account? Sign up</a>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
