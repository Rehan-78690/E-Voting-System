<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Started - E-Voting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            overflow-x: hidden;
        }

        /* Carousel Styling */
        .carousel-item {
            height: 100vh;
            background-size: cover;
            background-position: center;
            position: relative;
            transition: all 0.5s ease-in-out;
        }

        .carousel-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }

        .carousel-caption {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 2;
            color: #fff;
            padding: 0 20px;
            width: 80%;
        }

        .carousel-caption h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .carousel-caption p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .btn-custom {
            background-color: red;
            border: none;
            padding: 12px 30px;
            font-size: 1.2rem;
            border-radius: 5px;
            color: white;
            transition: all 0.3s;
        }

        .btn-custom:hover {
            background-color: #ff4747;
        }

        .btn-skip {
            background: none;
            border: none;
            color: skyblue;
            text-decoration: underline;
            font-size: 1rem;
            margin-top: 10px;
            cursor: pointer;
        }

        .btn-skip:hover {
            color: #d0d0d0;
        }

        /* Info Section */
        .info-section {
            padding: 50px 0;
            background-color: #ffffff;
            text-align: center;
        }

        .info-card {
            padding: 20px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        .info-card h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .info-card p {
            font-size: 1rem;
            color: #666;
        }

        /* Footer Styling */
        .footer {
            background-color: #2b3e50;
            color: lightgray;
            padding: 20px 0;
            text-align: center;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        .footer-links a {
            color: lightgrey;
            margin: 0 10px;
            text-decoration: none;
        }

        .footer-links a:hover {
            color: #ff4747;
        }

        @media (min-width: 768px) {
            .carousel-caption h1 {
                font-size: 3rem;
            }
            .info-card h2 {
                font-size: 2rem;
            }
        }

        @media (min-width: 992px) {
            .carousel-caption h1 {
                font-size: 4rem;
            }
            .carousel-caption p {
                font-size: 1.5rem;
            }
            .info-card p {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>

    <!-- Hero Slider Section -->
    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000" data-bs-pause="false">
        
        <!-- Carousel Indicators -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="3" aria-label="Slide 4"></button>
        </div>

        <!-- Carousel Inner -->
        <div class="carousel-inner">
            <!-- Slide 1 -->
            <div class="carousel-item active" style="background-image: url('voting 1.jpg');">
                <div class="carousel-overlay"></div>
                <div class="carousel-caption">
                    <h1>Welcome to Secure E-Voting!</h1>
                    <p>Experience a faster, safer, and more convenient way to vote.</p>
                    <a href="candidate/signup.php" class="btn btn-custom mb-3">Get Started</a>
                    <a href="admin.php" class="btn-skip">Skip to Login</a>
                   
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="carousel-item" style="background-image: url('voting4.jpeg');">
                <div class="carousel-overlay"></div>
                <div class="carousel-caption">
                    <h1>Why E-Voting?</h1>
                    <p>Convenient, fast, and secure voting from anywhere.</p>
                    <a href="candidate/signup.php" class="btn btn-custom mb-3">Get Started</a>
                    <a href="admin.php" class="btn-skip">Skip to Login</a>
                   
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="carousel-item" style="background-image: url('voting5.jpeg');">
                <div class="carousel-overlay"></div>
                <div class="carousel-caption">
                    <h1>How It Works</h1>
                    <p>Log in, select an election, and cast your vote securely.</p>
                    <a href="candidate/signup.php" class="btn btn-custom mb-3">Get Started</a>
                    <a href="admin.php" class="btn-skip">Skip to Login</a>                 
                </div>
            </div>

            <!-- Slide 4 -->
            <div class="carousel-item" style="background-image: url('voting6.jpg');">
                <div class="carousel-overlay"></div>
                <div class="carousel-caption">
                    <h1>Need Help?</h1>
                    <p>We are here to assist you throughout the voting process.</p>
                    <a href="candidate/signup.php" class="btn btn-custom mb-3">Get Started</a>
                    <a href="admin.php" class="btn-skip">Skip to Login</a>
                </div>
            </div>
        </div>

        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Information Section -->
    <div class="info-section">
        <div class="container">
            <div class="row">
                <!-- Why E-Voting? -->
                <div class="col-md-6">
                    <div class="info-card">
                        <h2>Why E-Voting?</h2>
                        <p>E-voting offers unmatched convenience, allowing you to vote from anywhere. It speeds up the voting process, ensuring quicker results while maintaining secure and transparent procedures.</p>
                    </div>
                </div>

                <!-- How It Works -->
                <div class="col-md-6">
                    <div class="info-card">
                        <h2>How It Works</h2>
                        <p>Simply log in, select an election, review candidates, and cast your vote securely. Each step is designed to be user-friendly, ensuring an effortless voting experience.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <div class="footer">
        <div class="container">
            <div class="footer-links">
                <a href="https://portals.upr.edu.pk/CSELIBRARY/">E-Library</a> | 
                <a href="https://lms.upr.edu.pk/login">LMS</a> | 
                <a href="https://upr.edu.pk/home">UPR home</a>
            </div>
            <p class="mt-3">&copy; 2024 E-Voting System. All rights reserved.</p>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Redirect to login page
        function redirectToLogin() {
            window.location.href = 'login.php';
        }
    </script>
</body>
</html>
