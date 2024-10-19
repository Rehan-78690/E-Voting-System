<?php
include 'config.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: admin.php");
    exit();
}

// Fetch completed elections from the database
$sql = "SELECT election_id, election_name FROM elections WHERE status = 'completed'";
$completed_elections = [];
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $completed_elections[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: Poppins, sans-serif;
            background-color: #f8f9fa;
            padding-top: 60px;
        }
        .navbar-dark .navbar-toggler {
            background-color: #6c757d;
        }
        .navbar-brand {
            font-size: 1.5rem;
        }
        .offcanvas-body {
            padding-top: 20px;
        }
        .content {
            padding: 20px;
        }
        .card-title {
            font-weight: bold;
        }
        .card-body {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        @media (max-width: 991px) {
            .navbar-text {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <button id="icon-ham" class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="navbar-text">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></span>
                    </li>
                    <li class="nav-item dropdown ms-3">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="profile-pic.jpg" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px;">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
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
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Dashboard Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link active" href="#">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="manage%20candidates/manage_candidates.php">Candidate Management</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_profile.php">Profile Management</a></li>
                <li class="nav-item"><a class="nav-link" href="document_verification.php">Document Verification</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Symbol Allocation</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_feedback.php">Feedback Management</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Settings</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container">
            <h1 class="text-center mb-5">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?>!</h1>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Candidate Management</h5>
                            <p class="card-text">Manage candidates, add new candidates, and delete existing ones.</p>
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
            <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">send notifications</h5>
                            <p class="card-text">Manage candidates, add new candidates, and delete existing ones.</p>
                            <a href="send_notification.php" class="btn btn-primary">send notifications</a>
                        </div>
                    </div>

            <!-- Completed Elections and Voting History Section -->
            <div class="card">
            <div class="card-body">
                <h5 class="card-title">View Voting History</h5>
                <form method="GET" action="voting_history.php">
                    <div class="mb-3">
                        <label for="election_id" class="form-label">Select Completed Election</label>
                        <select class="form-select" id="election_id" name="election_id" required>
                            <?php foreach ($completed_elections as $election): ?>
                                <option value="<?php echo $election['election_id']; ?>">
                                    <?php echo htmlspecialchars($election['election_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">View Voting History</button>
                </form>
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
                            <a href="symbol_allocation.php" class="btn btn-primary">Allocate Symbols</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Feedback Management</h5>
                            <p class="card-text">Manage feedback provided by the users.</p>
                            <a href="manage_feedback.php" class="btn btn-primary">Manage Feedback</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Announce Polling</h5>
                            <p class="card-text">Start, end, and initiate polling.</p>
                            <a href="election_notify.php" class="btn btn-primary">Notify</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">View Live Voting</h5>
                            <p class="card-text">See how the current election process is progressing.</p>
                            <a href="live_voting.php" class="btn btn-primary">View Voting</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
