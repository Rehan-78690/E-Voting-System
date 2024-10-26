<?php
include "config.php";
session_start();
if (!isset($_SESSION['candidate_email'])) {
    header("Location: candidate.php");
    exit();
}

// Fetch upcoming elections from the database
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f0f4f8;
            font-family: "Open Sans", sans-serif;
            margin: 0;
            padding-top: 56px;
        }
        .navbar {
            background-color: #2b3e50;
        }
        .navbar-brand {
            font-size: 1.5rem;
            color: white !important;
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
            background-color: #1e3d58;
            color: white;
            padding-top: 20px;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .sidebar h5 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        .sidebar a {
            padding: 10px 15px;
            display: block;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar a:hover {
            background-color: #3b637f;
        }
        .sidebar.closed {
            left: -250px;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
        .overlay.active {
            display: block;
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
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
        .card-header {
            background-color: #2b3e50;
            color: white;
            padding: 15px;
            font-size: 1.2rem;
        }
        .btn-primary {
            background-color: #ff6b6b;
            border: none;
        }
        .btn-primary:hover {
            background-color: #ff4747;
        }
        .footer {
            background-color: #1e3d58;
            color: white;
            text-align: center;
            padding: 15px;
        }
        /* Notification styling */
        .notification-area {
            position: relative;
            margin-right: 20px;
            cursor: pointer;
        }
        .notification-icon {
            font-size: 24px;
            color: white;
        }
        .badge {
            background-color: red;
            color: white;
            border-radius: 50%;
            position: absolute;
            top: -5px;
            right: -10px;
            font-size: 12px;
            padding: 5px;
            display: none;
        }
        .dropdown-menu {
            position: absolute;
            top: 30px;
            right: 0;
            max-width: 300px;
            display: none;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1001;
        }
        .dropdown-menu.show {
            display: block;
        }
        .dropdown-item {
            padding: 10px;
            color: #333;
            border-bottom: 1px solid #eaeaea;
        }
        .dropdown-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar open" id="sidebar">
    <h5>Candidate Menu</h5>
    <a href="#">Dashboard</a>
    <a href="candidate_profile.php">Profile Management</a>
    <a href="document_submit.php">Document Submission</a>
    <a href="view_symbol.php">View Symbol</a>
    <a href="view_voting_history.php">Voting History</a>
    <a href="candidate_feedback.php">Report Issues</a>
    <a href="logout.php">Sign Out</a>
</div>

<!-- Overlay -->
<div class="overlay" id="overlay"></div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="javascript:void(0);" id="navbarToggle">☰</a>
        <a class="navbar-brand ms-auto" href="#">UPR Senate Election</a>
        <!-- Notification Bell -->
        <div class="notification-area">
            <i class="fas fa-bell notification-icon"></i>
            <span id="notificationCount" class="badge">0</span>
            <ul id="notifications" class="dropdown-menu">
                <li class="dropdown-item">No notifications</li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="content" id="mainContent">
    <div class="container mt-5">
        <div class="row">
            <!-- Cards for Different Actions -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Profile Management</div>
                    <div class="card-body">
                        <p>Update your profile and manage your information.</p>
                        <a href="candidate_profile.php" class="btn btn-primary">Manage Profile</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Document Submission</div>
                    <div class="card-body">
                        <p>Submit or update your documents for verification.</p>
                        <a href="document_submit.php" class="btn btn-primary">Submit Documents</a>
                    </div>
                </div>
            </div>
            <!-- Additional Cards -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">View Symbol</div>
                    <div class="card-body">
                        <p>View your assigned symbol for the election.</p>
                        <a href="view_symbol.php" class="btn btn-primary">View Symbol</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Voting History</div>
                    <div class="card-body">
                        <p>Review your voting history in past elections.</p>
                        <a href="voting_history.php" class="btn btn-primary">View History</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Report Issues</div>
                    <div class="card-body">
                        <p>Report any issues related to the election process.</p>
                        <a href="candidate_feedback.php" class="btn btn-primary">Report Issue</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Voting REsult</div>
                    <div class="card-body">
                        <p>see voting results.</p>
                        <a href="../voting_result.php" class="btn btn-primary">view results</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Election Information -->
        <?php if ($upcoming_election): ?>
            <div class="alert alert-info mt-5">
                <h4>Upcoming Election</h4>
                <p><strong>Date:</strong> <?php echo $upcoming_election['election_date']; ?></p>
                <p><strong>Time:</strong> <?php echo $upcoming_election['start_time'] . ' to ' . $upcoming_election['end_time']; ?></p>
                <p>Ensure that your documents are submitted and verified before the election date.</p>
            </div>
        <?php else: ?>
            <div class="alert alert-warning mt-5">
                <p>No upcoming elections at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    &copy; 2024 UPR Senate Election - All rights reserved
</footer>

<!-- JavaScript for Sidebar & Notifications -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const sidebarToggle = document.getElementById('navbarToggle');
        const notificationArea = document.querySelector('.notification-area');
        const notifications = document.getElementById('notifications');
        const notificationCount = document.getElementById('notificationCount');

        // Sidebar toggle
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            sidebar.classList.toggle('closed');

            // Only show overlay on smaller screens
            if (window.innerWidth <= 768) {
                overlay.classList.toggle('active');
            }
        });

        overlay.addEventListener('click', function () {
            sidebar.classList.remove('open');
            sidebar.classList.add('closed');
            overlay.classList.remove('active');
        });

        // Show/Hide notifications
        notificationArea.addEventListener('click', function () {
            notifications.classList.toggle('show');
        });

        // Fetch notifications
        fetchNotifications();

        function fetchNotifications() {
            $.ajax({
                type: 'GET',
                url: '../notification system/fetch_notification.php',
                dataType: 'json',
                success: function (response) {
                    updateNotificationList(response);
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching notifications:', error);
                }
            });
        }

        function updateNotificationList(response) {
            notifications.innerHTML = '';
            if (response.data && response.data.length > 0) {
                notificationCount.textContent = response.data.length;
                notificationCount.style.display = 'inline';

                response.data.forEach(notification => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('dropdown-item');
                    listItem.innerHTML = `
                        <a href="${notification.noti_url || '#'}">
                            ${notification.noti_message} - <small>${new Date(notification.noti_date).toLocaleString()}</small>
                        </a>
                    `;
                    notifications.appendChild(listItem);
                });
            } else {
                notificationCount.textContent = 0;
                notifications.innerHTML = '<li class="dropdown-item">No notifications</li>';
                notificationCount.style.display = 'none';
            }
        }
    });
</script>
</body>
</html>
