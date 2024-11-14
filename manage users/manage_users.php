<?php
// Include database configuration and mail functions
include '../config.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>manage users</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="styles.css">
</head>
<body>
   < <?php
  include 'sidebar.php';
  ?>

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
                        <a class="nav-link active" aria-current="page" href="../welcome.php">Home</a>
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

<div class="content" id="mainContent">
<div class="container">
<div class="row row-equal">
                <div class="col-md-6 searchable-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Candidate Management</h5>
                            <p>Manage candidates, add new candidates, and delete existing ones.</p>
                            <a href="../manage%20users/manage%20candidates/manage_candidates.php" class="btn btn-primary">Manage Candidates</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 searchable-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Voter Management</h5>
                            <p>Manage voters, add new voters, and delete existing ones.</p>
                            <a href="../manage%20users/manage%20voters/manage_voters.php" class="btn btn-primary">Manage voters</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-equal">
                <div class="col-md-6 searchable-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Approval Requests</h5>
                            <p>Approve candidates and voters.</p>
                            <a href="../manage%20users/approval_requests.php" class="btn btn-primary">view requests</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 searchable-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Symbol Allocation</h5>
                            <p>Allocate symbols to the candidates.</p>
                            <a href="../manage%20users/symbol_allocation.php" class="btn btn-primary">Allocate Symbols</a>
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
<script src="script.js"></script>
</body>
</html>