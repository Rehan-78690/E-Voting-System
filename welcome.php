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
            margin-top: 20px;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding-top: 56px;
        }
        .navbar-brand {
            font-size: 1.5rem;
            color: #007bff !important;
        }
        .content {
            padding: 20px;
            margin-left: 250px;
            transition: all 0.3s ease;
            min-height: calc(100vh - 112px); /* Adjust for navbar and footer height */
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
            background-color:#212529;
            color: white;
            padding-top: 20px;
            z-index: 1000;
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
            transition: background-color 0.3s, color 0.3s;
        }
        .sidebar a:hover {
            background-color: #0056b3; /* Red color on hover */
            color: white;
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
            background-color: white;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
            height: 180px;
            color: #333;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2); /* Increased shadow on hover */
        }
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 20px;
        }
        .card-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .card p {
            color: #666; /* Darker text color for readability */
        }
        .btn-primary {
            background-color: #007bff; /* Blue button color */
            border: none;
            align-self: flex-start;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .navbar-nav .nav-link {
            color: white !important;
        }
        .row-equal {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .col-md-6 {
            flex: 1;
            max-width: 48%;
            margin-bottom: 20px;
        }
        footer {
            background-color: #212529;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }
        form .form-control {
    border: 2px solid #007bff; /* Change the border color to blue */
    color: #007bff; /* Change the text color to blue */
}

/* Search Button */
form .btn-outline-success {
    border-color: #007bff; /* Change the border color to blue */
    color: #007bff; /* Change the text color to blue */
}

form .btn-outline-success:hover {
    background-color: #007bff; /* Change the background color to blue on hover */
    color: white; /* Change the text color to white on hover */
}
@media (max-width: 525px) {
    .card {
        background-color: white;
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        overflow: hidden;
        height: auto; /* Allow height to adjust based on content */
        color: #333;
        margin-bottom: 15px; /* Add spacing between cards */
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15); /* Subtle shadow on hover */
    }
    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 8px;
        text-overflow: ellipsis;
    }
    .card-title {
        font-size: 16px; /* Reduce title size for better fit */
        font-weight: 600;
        margin-bottom: 5px;
    }
    .card p {
        font-size: 12px; /* Smaller text for better readability */
        color: #666;
    }
    .btn-primary {
        font-size: 12px; /* Reduce font size */
        padding: 4px 8px; /* Reduce padding */
        background-color: #007bff; /* Blue button color */
        border: none;
        align-self: flex-start;
    }
    .sidebar {
        left: -250px; /* Sidebar hidden by default */
    }
    .content {
        margin-left: 0; /* No margin when sidebar is closed */
    }
}


    </style>
</head>
<body>

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
        <a href="election_settings.php"> Settings</a>
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
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
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
                            <h5 class="card-title">User Management</h5>
                            <p>Manage candidates &voters ,approve signup requests and allocate symbols</p>
                            <a href="manage%20users/manage_users.php" class="btn btn-primary">Manage Users</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 searchable-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Profile Management</h5>
                            <p>Update your profile details and change your password.</p>
                            <a href="admin_profile.php" class="btn btn-primary">Manage Profile</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Voting History and Additional Card Row -->
            <div class="row row-equal">
                <div class="col-md-6 searchable-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Voting History</h5>
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
                <div class="col-md-6 searchable-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Announce Polling</h5>
                            <p>Start, end, and manage polling.</p>
                            <a href="election_notify.php" class="btn btn-primary">Notify</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Sections -->
            <div class="row row-equal">
                <div class="col-md-6 searchable-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Document Verification</h5>
                            <p>Verify the documents submitted by the candidates.</p>
                            <a href="document_verification.php" class="btn btn-primary">Verify Documents</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 searchable-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Symbol Allocation</h5>
                            <p>Allocate symbols to the candidates.</p>
                            <a href="manage%20users/symbol_allocation.php" class="btn btn-primary">Allocate Symbols</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-equal">
                <div class="col-md-6 searchable-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Feedback Management</h5>
                            <p>Manage feedback provided by the users.</p>
                            <a href="manage_feedback.php" class="btn btn-primary">Manage Feedback</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 searchable-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">View Live Voting</h5>
                            <p>See how the current election process is progressing.</p>
                            <a href="live_voting.php" class="btn btn-primary">View Voting</a>
                        </div>
                    </div>
                </div>
            </div>
          
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 E-Voting UPR. All rights reserved.</p>
        <p>Designed for University of Poonch Rawalakot Elections</p>
    </footer>
    <script>
  document.addEventListener('DOMContentLoaded', function () {
    // Get elements for sidebar toggle, overlay, main content, and search
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const sidebarToggle = document.getElementById('navbarToggle');
    const mainContent = document.getElementById('mainContent');
    const searchInput = document.getElementById('searchInput');
    const searchableItems = document.querySelectorAll('.searchable-item');

    // Function to initialize sidebar state based on screen size
    function initializeSidebar() {
      if (window.innerWidth <= 525) {
        sidebar.classList.add('closed');
        mainContent.classList.add('no-sidebar');
      } else {
        sidebar.classList.remove('closed');
        mainContent.classList.remove('no-sidebar');
      }
    }

    // Call the function on page load to set initial state
    initializeSidebar();

    // Toggle sidebar visibility on button click
    sidebarToggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
      sidebar.classList.toggle('closed');
      mainContent.classList.toggle('no-sidebar');

      // Show overlay on small screens when sidebar is open
      if (sidebar.classList.contains('open') && window.innerWidth <= 525) {
        overlay.classList.add('active');
      } else {
        overlay.classList.remove('active');
      }
    });

    // Close sidebar when clicking on the overlay
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('open');
      sidebar.classList.add('closed');
      mainContent.classList.add('no-sidebar');
      overlay.classList.remove('active');
    });

    // Adjust sidebar on window resize
    window.addEventListener('resize', function () {
      initializeSidebar();
    });

    // Prevent form submission for live search
    searchInput.closest('form').addEventListener('submit', function(e) {
      e.preventDefault();
    });

    // Live search functionality for dashboard elements
    searchInput.addEventListener('keyup', function () {
      const searchValue = searchInput.value.toLowerCase();

      // Loop through the searchable items
      searchableItems.forEach(function (item) {
        const cardTitleElement = item.querySelector('.card-title');
        const cardTextElement = item.querySelector('p');

        // Get the text content if elements exist
        const cardTitle = cardTitleElement ? cardTitleElement.textContent.toLowerCase() : '';
        const cardText = cardTextElement ? cardTextElement.textContent.toLowerCase() : '';

        // Check if the search value matches the card title or text
        if (cardTitle.includes(searchValue) || cardText.includes(searchValue)) {
          item.style.display = ''; // Show the card
        } else {
          item.style.display = 'none'; // Hide the card
        }
      });
    });
  });
</script>


</html>
