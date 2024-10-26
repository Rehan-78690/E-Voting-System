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
            //header("Location: candidate_dashboard.php");
            $result = $stmt->get_result();

            if ($result) {
                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();

                    if (password_verify($password, $user['password'])) {
                        $_SESSION['candidate_email'] = $email;
                        $_SESSION['candidate_id'] = $user['candidate_id'];
                        header("Location: candidate_dashboard.php");
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
    <title>Candidate Login</title>
    <!-- <link rel="stylesheet" href="styles.css"> -->
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #2b3e50;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.login-container {
    background-color: #fff;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    text-align: center;
    width: 90%;
    max-width: 400px;
    /* -webkit-border-radius:;
    -moz-border-radius:;
    -ms-border-radius:;
    -o-border-radius:; */
}

.logo {
    width: 100%;
    max-width: 330px;
    margin-bottom: 10px;
}

.login-box h2 {
    color: #2b3e50;
    margin: 0 0 10px;
    font-size: 20px;
}

.login-box h3 {
    color: #00bcd4;
    margin: 0 0 20px;
    font-size: 18px;
}

.login-box form {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}

.login-box input {
    width: 100%;
    max-width: 300px;
    margin-bottom: 10px;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.login-box button {
    width: 30%;
    max-width: 300px;
    padding: 10px;
    font-size: 16px;
    color: #fff;
    background-color: #00bcd4;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-left: 35px;
}

.login-box button:hover {
    background-color: #00bcd4;
}

.form-inline {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.form-inline label {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}
#remember{
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    margin-top: 10px;
}

.remember-forgot {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.remember-forgot a {
    color: #007ACC;
    text-decoration: none;
}
#forgot-password{
    color: #007ACC;
    text-decoration: none;
    margin-bottom: 6px;
    text-decoration: underline;
}

.remember-forgot a:hover {
    text-decoration: underline;
}

.links {
    text-align: left;
    width: 100%;
    margin-top: 10px;
}

.links a {
    display: block;
    color: #007ACC;
    text-decoration: none;
    margin-bottom: 5px;
}

.links a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .login-box input,
    .login-box button {
        width: 100%;
    }

    .form-inline {
        flex-direction: row;
        justify-content: space-between;
    }

    .form-inline label,
    .form-inline a,
    .form-inline button {
        margin: 5px 0;
    }
}

@media (max-width: 480px) {
    .login-box h3 {
        font-size: 16px;
    }

    .login-box input,
    .login-box button {
        font-size: 14px;
        padding: 8px;
    }
}

    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img src="uprlogo.png" alt="University Logo" class="logo">
            <h3>Candidate Login</h3>
            <form id="login-form" method="post" action="candidate.php">
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
                <a href="#">Candidate Portal</a>
                <a href="../elections/voter.php">Voter Portal</a>
                <a href="signup.php">No account? Sign up</a>

            </div>
        </div>
    </div>
    <!-- <script src="script.js"></script> -->
</body>
</html>
