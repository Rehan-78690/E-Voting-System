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

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding-top: 60px;
        }
        .navbar-brand {
            font-size: 1.5rem;
        }
        .content {
            padding: 20px;
            margin-left: 250px;
            transition: all 0.3s ease;
        }
        .content.no-sidebar {
            margin-left: 0;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            z-index: 1;
            transition: all 0.3s ease;
        }
        .sidebar h5 {
            text-align: center;
            margin-bottom: 30px;
            color: white;
        }
        .sidebar a {
            padding: 10px 15px;
            display: block;
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar.closed {
            left: -250px;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 250px;
            width: calc(100% - 250px);
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
        .overlay.active {
            display: block;
        }
        .sidebar.closed + .overlay {
            left: 0;
            width: 100%;
        }
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }
            .sidebar.open {
                left: 0;
            }
            .content {
                margin-left: 0;
            }
            .overlay.active {
                display: block;
            }
        }
        .card {
            margin-bottom: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
        .card-title {
            font-weight: 600;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar open" id="sidebar">
        <h5>Dashboard Menu</h5>
        <a href="#" class="d-block mb-2" id="sidebarToggle">☰ Toggle Sidebar</a>
        <a href="#">🏠 Dashboard</a>
        <a href="manage%20candidates/manage_candidates.php">👤 Candidate Management</a>
        <a href="admin_profile.php">🔧 Profile Management</a>
        <a href="document_verification.php">📄 Document Verification</a>
        <a href="symbol_allocation.php">📊 Symbol Allocation</a>
        <a href="manage_feedback.php">💬 Feedback Management</a>
        <a href="#">⚙️ Settings</a>
        <a href="logout.php">🔑 Sign Out</a>
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
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Link
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled">Link</a>
                    </li>
                </ul>
                <!-- Search form -->
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content" id="mainContent">
        <div class="container">
            <h1 class="text-center mb-5">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?>!</h1>

            <!-- Dynamic Cards -->
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

            <!-- Completed Elections and Voting History Section -->
            <div class="card mt-4">
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
        </div>
    </div>

    <!-- JavaScript for sidebar toggle and overlay -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const sidebarToggle = document.getElementById('navbarToggle');
        const mainContent = document.getElementById('mainContent');

        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            sidebar.classList.toggle('closed');
            mainContent.classList.toggle('no-sidebar');

            if (sidebar.classList.contains('open') && window.innerWidth <= 768) {
                overlay.classList.add('active');
            } else {
                overlay.classList.remove('active');
            }
        });

        overlay.addEventListener('click', function () {
            sidebar.classList.remove('open');
            sidebar.classList.add('closed');
            mainContent.classList.add('no-sidebar');
            overlay.classList.remove('active');
        });
    </script>
</body>
</html>
