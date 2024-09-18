<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
     <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        #icon-ham{
            display: block;
            background-color:rgb(119, 106, 100);
        }
        .offcanvas-body {
            padding-top: 20px;
        }
        .content {
            padding: 20px;
            margin-top: 60px; /* Adjust to prevent content overlap */
        }
        .navbar-brand {
            padding-left: 10px;
        }
        .navbar-toggler {
            margin-right: 10px;
        }
        @media (max-width: 991px) { 
            .navbar-collapse {
                display: flex;
                justify-content: flex-end;
            }
            .navbar-text {
                display: none; /* Hide the welcome message on smaller screens to fit other elements */
            }
        }
        @media (min-width: 992px) { 
            .navbar-toggler {
                display: none; /* Hide drawer button on larger screens */
            }
            .navbar-text {
                display: block;
            }
        }
       
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <button  id = "icon-ham" class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="navbar-text">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></span>
                    </li>
                    <li class="nav-item dropdown ms-3">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="profle-pic.jpg" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px;"> 
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Edit Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Log Out</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Off-Canvas Sidebar -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Dashboard Menu</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage%20candidates/manage_candidates.php">Candidate Management</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_profile.php">Profile Management</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Document Verification</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Symbol Allocation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_feedback.php">Feedback Management</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Settings</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?>!</h1>
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Candidate Management</h5>
                            <p class="card-text">Manage the list of candidates, add new candidates, and delete existing ones.</p>
                            <a href="manage%20candidates/manage_candidates.php" class="btn btn-primary">Manage Candidates</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Profile Management</h5>
                            <p class="card-text">Update your profile details and change your password.</p>
                            <a href="admin_profile.php" class="btn btn-primary">Manage Profile</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Document Verification</h5>
                            <p class="card-text">Verify the documents submitted by the candidates.</p>
                            <a href="document_verification.php" class="btn btn-primary">Verify Documents</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Symbol Allocation</h5>
                            <p class="card-text">Allocate symbols to the candidates.</p>
                            <a href="#" class="btn btn-primary">Allocate Symbols</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-mt-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Feedback Management</h5>
                            <p class="card-text">Manage the feedback provided by the users.</p>
                            <a href="manage_feedback.php" class="btn btn-primary">Manage Feedback</a>
                        </div>
                    </div>
                  
            </div>
            <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Announce polling </h5>
                            <p class="card-text">start, end, iniitiate polling</p>
                            <a href="election_notify.php" class="btn btn-primary">Notify</a>
                        </div>
                    </div>
                </div>
                </div>
           
        
        </div>
    </div>

</body>
</html>
