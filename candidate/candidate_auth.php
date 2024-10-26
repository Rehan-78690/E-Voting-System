<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Auth</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #2b3e50;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .auth-container {
            width: 900px;
            height: 500px;
            display: flex;
            overflow: hidden;
            position: relative;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            background-color: #fff;
        }

        .form-container {
            position: absolute;
            width: 50%;
            height: 100%;
            transition: 0.6s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 50px;
        }

        .login-container {
            left: 0;
            z-index: 2;
        }

        .signup-container {
            right: 0;
            opacity: 0;
            z-index: 1;
        }

        .auth-container.right-panel-active .login-container {
            transform: translateX(100%);
            z-index: 1;
        }

        .auth-container.right-panel-active .signup-container {
            transform: translateX(-100%);
            opacity: 1;
            z-index: 2;
        }

        .overlay-container {
            position: absolute;
            width: 50%;
            height: 100%;
            top: 0;
            right: 0;
            background: linear-gradient(to right, #303064, #0A0E27);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 50px;
            z-index: 100;
            transition: 0.6s ease-in-out;
        }

        .auth-container.right-panel-active .overlay-container {
            transform: translateX(-100%);
        }

        .overlay-container h2 {
            font-size: 30px;
            margin-bottom: 20px;
        }

        .overlay-container p {
            margin: 20px 0;
        }

        .overlay-container button {
            background: transparent;
            border: 2px solid white;
            padding: 10px 30px;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        .overlay-container button:hover {
            background: white;
            color: #303064;
        }

        .form-container input {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-container button {
            background: #303064;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
            margin-top: 10px;
        }

        .form-container button:hover {
            background: #0A0E27;
        }
    </style>
</head>
<body>
    <div class="auth-container" id="auth-container">
        <!-- Include Login Form -->
        <div class="form-container login-container">
            <?php include 'candidate.php'; ?>
        </div>

        <!-- Include Signup Form -->
        <div class="form-container signup-container">
            <?php include 'signup.php'; ?>
        </div>

        <!-- Overlay -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h2>Welcome Back!</h2>
                    <p>To keep connected with us, please login with your personal info.</p>
                    <button id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h2>Hello, Friend!</h2>
                    <p>Enter your personal details and start your journey with us.</p>
                    <button id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('auth-container');
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');

        signUpButton.addEventListener('click', () => {
            container.classList.add('right-panel-active');
        });

        signInButton.addEventListener('click', () => {
            container.classList.remove('right-panel-active');
        });
    </script>
</body>
</html>
