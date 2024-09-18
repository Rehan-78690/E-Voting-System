<?php
include "config.php";
session_start();
if (!isset($_SESSION['candidate_email'])) {
    header("Location: candidate.php");
    exit();

}
$sql = "SELECT election_date, start_time, end_time, status FROM elections WHERE status = 'upcoming' ORDER BY election_date ASC LIMIT 1";
$result = $conn->query($sql);
$upcoming_election = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General styling */
        body, html {
            height: 100%;
            background-color: #f0f4f8;
            font-family: 'Poppins', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container-fluid {
            min-height: 100%;
            padding-bottom: 60px; /* Make space for the footer */
        }

        .dashboard-content {
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 15px;
            margin-top: 20px;
            min-height: calc(100vh - 60px);
        }

        /* Offcanvas menu specific styling */
        .offcanvas {
            width: 250px;
        }

        .dashboard-pushed {
            transform: translateX(150px); /* Push content to the right */
        }

        .card {
            border-radius: 15px;
            border: none;
            margin-bottom: 20px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background-color: #2b3e50;
            color: #ffffff;
            border-radius: 15px 15px 0 0;
            padding: 15px;
            font-size: 1.3rem;
            font-weight: 500;
            text-align: center;
        }

        .card-body {
            padding: 20px;
        }

        .offcanvas-body {
            background-color: #1e3d58;
            padding: 20px;
        }

        .offcanvas-body a {
            color: #ffffff;
            padding: 10px;
            font-size: 1.1rem;
            display: block;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .offcanvas-body a:hover {
            background-color: #3b637f;
            text-decoration: none;
        }

        .offcanvas-body a.active {
            background-color: #0d2536;
            font-weight: bold;
        }

        /* Navbar styling */
        .navbar {
            background-color: #2b3e50;
        }
        .navbar-pushed {
            transform: translateX(150px); 
        }
        .navbar-brand {
            font-size: 1.3rem;
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .navbar .btn-primary {
            background-color: #ff6b6b;
            border: none;
            transition: background-color 0.3s ease;
        }

        .navbar .btn-primary:hover {
            background-color: #ff4747;
        }

        /* Button Styling */
        .btn-primary {
            background-color: #ff6b6b;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #ff4747;
        }

        /* Footer styling */
        .footer {
            background-color: #1e3d58;
            color: #ffffff;
            text-align: center;
            padding: 15px;
            font-size: 0.9rem;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            position: relative;
            bottom: 0;
            width: 100%;
            height: 60px;
        }

        .footer-pushed {
            transform: translateX(250px);
        }

        @media (min-width: 1200px) {
            .dashboard-content {
                padding-left: 50px;
                padding-right: 50px;
            }
        }

        /* Fix top-right heading visibility */
        .navbar-brand {
            width: calc(100% - 150px); /* Ensures text doesn't overflow when the menu is pushed */
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Navbar for Off-Canvas Trigger -->
        <nav class="navbar navbar-dark">
            <div class="container-fluid">
                <!-- Flex container for Menu button and brand -->
                <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
                    &#9776; Menu
                </button>
                <a class="navbar-brand ms-auto text-truncate" href="#">UPR Senate Election</a>
            </div>
        </nav>

        <!-- Off-Canvas Sidebar -->
        <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Main Menu</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <a class="nav-link active" aria-current="page" href="#">
                    <span class="icon-large">&#x1F3E0;</span> Dashboard
                </a>
                <a class="nav-link" href="candidate_profile.php">
                    <span class="icon-large">&#x1F464;</span> Profile Management
                </a>
                <a class="nav-link" href="document_submit.php">
                    <span class="icon-large">&#x1F4C3;</span> Document Submission
                </a>
                <a class="nav-link" href="#">
                    <span class="icon-large">&#x1F4CA;</span> View Symbol
                </a>
                <a class="nav-link" href="candidate_feedback.php">
                    <span class="icon-large">&#x1F4AC;</span> Report Issues
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 nav-link">
            <div class="dashboard-content">
                <div class="row">
                    <!-- Profile Management Card -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-header">Profile Management</div>
                            <div class="card-body">
                                <p>Update your personal information and manage your profile.</p>
                                <a href="candidate_profile.php" class="btn btn-primary">Manage Profile</a>
                            </div>
                        </div>
                    </div>

                    <!-- Document Submission Card -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-header">Document Submission</div>
                            <div class="card-body">
                                <p>Submit or update your documents for verification.</p>
                                <a href="document_submit.php" class="btn btn-primary">Submit Documents</a>
                            </div>
                        </div>
                    </div>

                    <!-- View Symbol Card -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-header">View Symbol</div>
                            <div class="card-body">
                                <p>View the symbol assigned to you for the election.</p>
                                <a href="#" class="btn btn-primary">View Symbol</a>
                            </div>
                        </div>
                    </div>

                    <!-- Report Issues Card -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-header">Report Issues</div>
                            <div class="card-body">
                                <p>Report any issues or concerns regarding the election process.</p>
                                <a href="candidate_feedback.php" class="btn btn-primary">Report Issue</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container mt-5">
    <?php if ($upcoming_election): ?>
        <div class="alert alert-info">
            <h4>Upcoming Election</h4>
            <p><strong>Date:</strong> <?php echo $upcoming_election['election_date']; ?></p>
            <p><strong>Time:</strong> <?php echo $upcoming_election['start_time'] . ' to ' . $upcoming_election['end_time']; ?></p>
            <p>Please ensure your documents are submitted and verified before the election date.</p>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <p>No upcoming elections at the moment.</p>
        </div>
    <?php endif; ?>

</div>
        </main>
    </div>
  
</div>

<!-- Footer -->
<div class="footer">
    &copy; 2024 UPR Senate Election - All rights reserved
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const offcanvasSidebar = document.getElementById('offcanvasSidebar');
        const mainContent = document.querySelector('main');
        const footer = document.querySelector('.footer');
        const navbar = document.querySelector('.navbar'); // Select the navbar

        offcanvasSidebar.addEventListener('show.bs.offcanvas', function () {
            mainContent.classList.add('dashboard-pushed');
            footer.classList.add('footer-pushed');
            navbar.classList.add('navbar-pushed'); // Add the class to the navbar when the sidebar is open
        });

        offcanvasSidebar.addEventListener('hide.bs.offcanvas', function () {
            mainContent.classList.remove('dashboard-pushed');
            footer.classList.remove('footer-pushed');
            navbar.classList.remove('navbar-pushed');  // Remove the class when the sidebar is closed
        });
    });
</script>
</body>
</html>
