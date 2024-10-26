<?php
session_start();
include 'config.php';

$error = '';  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['candidate_email'];
    $password = $_POST['password'];

    if (empty($email)) {
        $error = 'Email field is empty.';
    } elseif (empty($password)) {
        $error = 'Password field is empty.';
    } else {
        $email = mysqli_real_escape_string($conn, $email);
        $sql = "SELECT * FROM candidates WHERE candidate_email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            
            $result = $stmt->get_result();

            if ($result) {
                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();

                    if (password_verify($password, $user['password'])) {
                        $_SESSION['candidate_email'] = $email;
                        $_SESSION['candidate_id'] = $user['candidate_id'];
                        
                        header("Location: voter_dashboard.php");
                        exit();
                    } else {
                        $error = "Invulid email or password.";
                    }
                } else {
                    $error = "Invlid email or password.";
                }
            } else {
                $error = "Query failed: " . $conn->error;
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
    <title>voter login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img src="uprlogo.png" alt="University Logo" class="logo">
            <h3>Voter Login</h3>
            <form id="login-form" method="post" action="voter.php">
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
