<?php
include 'config.php';
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
    <title>Settings</title>
     <!-- Google Fonts -->
     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="style.css">
</head>
<body>
<link rel="stylesheet" href="style.css">
    <!-- Sidebar -->
    <div class="sidebar closed" id="sidebar">
        <h5>Dashboard Menu</h5>
        <!-- <a href="#" class="d-block mb-2" id="sidebarToggle">☰ Toggle Sidebar</a> -->
        <a href="manage%20users/approval_requests.php"> Approval requests</a>
        <a href="manage%20users/manage%20candidates/manage_candidates.php">Candidate Management</a>
        <a href="admin_profile.php"> Profile Management</a>
        <a href="document_verification.php"> Document Verification</a>
        <a href="manage%20users/symbol_allocation.php"> Symbol Allocation</a>
        <a href="manage_feedback.php"> Feedback Management</a>
        <a href="#"> Settings</a>
        <a href="logout.php">Sign Out</a>
    </div>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="javascript:void(0);" id="navbarToggle">☰</a> <!-- Sidebar toggle button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="welcome.php">Home</a>
                    </li>
                </ul>
                <!-- Search form -->
                <form class="d-flex">
                <input class="form-control me-2" type="text" id="searchInput" placeholder="Search..." aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content" id="mainContent">
    <div class="container">
            <!-- Dynamic Cards -->
            <div class="row row-equal">
                <div class="col-md-6 searchable-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Add new election</h5>
                            <p>Start a new election</p>
                            <a href="manage%20users/manage_users.php" class="btn btn-primary">Add election</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 searchable-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage elections</h5>
                            <p>Update status of existing elections</p>
                            <a href="admin_profile.php" class="btn btn-primary">Manage Status</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-equal">
                <div class="col-md-6 searchable-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage Polling </h5>
                            <p>Activate or deactivate elections in one click</p>
                            <a href="manage%20users/manage_users.php" class="btn btn-primary">Polling</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 searchable-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Announce Results </h5>
                            <p>Announce results to Everyone </p>
                            <a href="manage%20users/manage_users.php" class="btn btn-primary">Announce</a>
                        </div>
                    </div>
                </div>
</div>
    </div>
</div>
    <footer>
        <p>&copy; 2024 E-Voting UPR. All rights reserved.</p>
        <p>Designed for University of Poonch Rawalakot Elections</p>
    </footer>
<script src="scripts.js"></script>
</body>
</html>