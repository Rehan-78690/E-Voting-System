<?php
include "config.php";
session_start();
if (!isset($_SESSION['candidate_email'])) {
    header("Location: voter.php");
    exit();
}
$voter_id = $_SESSION['candidate_id'];
$sql= "Select candidate_name, address, status from candidates where candidate_id =?";
$stmt=$conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param('i', $voter_id);

$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$sql = "SELECT election_date, start_time, end_time, status FROM elections WHERE status IN ('upcoming', 'active') ORDER BY election_date ASC LIMIT 1";
$result = $conn->query($sql);
$upcoming_election = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Dashboard</title>
    <!-- Bootstrap CSS -->
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
            /* flex-direction: column; */
            min-height: 100%;
            position: relative;
            padding-bottom: 60px; /* Make space for the footer */
        }

        /* Offcanvas menu specific styling */
        .offcanvas {
            width: 250px; /* Set the width of the offcanvas */
        }

        .dashboard-content {
            padding: 20px;
            
            background-color: #f9fafb;
            border-radius: 15px;
            margin-top: 20px;
            transition: transform 0.3s ease; /* Smooth transition for push-in effect */
            min-height: calc(100vh - 60px); /* Ensure it fills at least the screen height minus footer */
        }

        .dashboard-pushed {
            transform: translateX(250px); /* Push content to the right by 250px */
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

        .icon-large {
            font-size: 2.5rem;
            color: #2b3e50;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            color: #0056b3;
            text-decoration: underline;
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

        .navbar-brand {
            font-size: 1.3rem;
            color: #fff;
            font-weight: 600;
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
            height: 60px; /* Height of the footer */
        }

        .footer-pushed {
            transform: translateX(250px); /* Push footer to the right when sidebar is open */
        }

        /* Mobile-first styles */
        @media (max-width: 768px) {
            .dashboard-content,
            .footer {
                transform: translateX(0); /* Default on mobile: no push-in effect */
            }
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
        <a class="navbar-brand ms-auto" href="#">UPR Senate Election</a>
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
                <a class="nav-link" href="#">
                    <span class="icon-large">&#x1F464;</span> View Profile
                </a>
                <a class="nav-link" href="#">
                    <span class="icon-large">&#x1F514;</span> Notifications
                </a>
                <a class="nav-link" href="#">
                    <span class="icon-large">&#x1F4CA;</span> View Results
                </a>
                <a class="nav-link" href="#">
                    <span class="icon-large">&#x1F4C3;</span> Vote Confirmation
                </a>
               
                <a class="nav-link" href="#">
                    <span class="icon-large">&#x1F511;</span> Sign Out
                </a>
            </div>
        </div>

   
        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 nav-link">
            <div class="dashboard-content">
                <div class="row">
                    <!-- Election Information -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Election Information
                            </div>
                            <div class="card-body">
                                <p>Upcoming Elections: <strong>2024 Senate Election</strong></p>
                                <p>Candidate Information: <a href="#">View Candidates</a></p>
                                <p>Ballot Measures: <a href="ballot.php">cast your vote</a></p>
                            </div>
                        </div>
                    </div>

                    <!-- Voter Profile -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Voter Profile
                            </div>
                            <div class="card-body">
                            <p>Name: <strong><?php echo htmlspecialchars($row['candidate_name']); ?></strong></p>
                            <p>Address: <strong><?php echo htmlspecialchars($row['address']); ?></strong></p>
                                <p>Status: <strong><?php echo htmlspecialchars($row['status']); ?></p>
                                <a href="voter_profile.php" class="btn btn-primary">Update Profile</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Voting History -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Voting History
                            </div>
                            <div class="card-body">
                                <p>Last Election: <strong>2022 Presidential Election</strong></p>
                                <p>Vote Confirmation: <a href="#">View Receipt</a></p>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Notifications
                            </div>
                            <div class="card-body">
                                <p>Upcoming Election: <strong>2024 Senate Election</strong></p>
                                <p>System Alerts: <a href="#">View Alerts</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Educational Resources -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Educational Resources
                            </div>
                            <div class="card-body">
                                <p><a href="#">How to Vote</a></p>
                                <p><a href="#">Voter Rights</a></p>
                                <p><a href="#">FAQs</a></p>
                            </div>
                        </div>
                    </div>

                    <!-- Support and Help -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Support and Help
                            </div>
                            <div class="card-body">
                                <p>Contact Information: <a href="#">Election Office</a></p>
                                <p>Live Chat: <a href="#">Chat Now</a></p>
                                <p>Report Issues: <a href="#">Report a Problem</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Poll Locations and Hours -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Poll Locations and Hours
                            </div>
                            <div class="card-body">
                                <p><a href="#">Find Your Polling Station</a></p>
                                <p>Hours: <strong>7:00 AM - 7:00 PM</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Sample Ballots -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Sample Ballots
                            </div>
                            <div class="card-body">
                                <p><a href="#">Preview Ballot</a></p>
                                <p><a href="#">Practice Voting</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Feedback and Reporting -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Feedback and Reporting
                            </div>
                            <div class="card-body">
                                <p><a href="#">Provide Feedback</a></p>
                                <p><a href="#">Report an Issue</a></p>
                            </div>
                        </div>
                    </div>

                    <!-- News and Updates -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                News and Updates
                            </div>
                            <div class="card-body">
                                <p><a href="#">Election News</a></p>
                                <p><a href="#">Live Results</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container mt-5">
    <?php if ($upcoming_election): ?>
        <div class="alert alert-info">
            <?php if ($upcoming_election['status'] == 'upcoming'): ?>
                <h4>Upcoming Election</h4>
                <p><strong>Date:</strong> <?php echo $upcoming_election['election_date']; ?></p>
                <p><strong>Time:</strong> <?php echo $upcoming_election['start_time'] . ' to ' . $upcoming_election['end_time']; ?></p>
                <p>Please prepare to cast your vote in the upcoming election.</p>
            <?php elseif ($upcoming_election['status'] == 'active'): ?>
                <h4>Voting Active</h4>
                <p>The election is currently active! Please cast your vote as soon as possible.</p>
                <p><strong>Voting ends at:</strong> <?php echo $upcoming_election['end_time']; ?></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <p>No upcoming or active elections at the moment.</p>
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
<div id="voting-status"></div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const offcanvasSidebar = document.getElementById('offcanvasSidebar');
        const mainContent = document.querySelector('main');
        const footer = document.querySelector('.footer');

        offcanvasSidebar.addEventListener('show.bs.offcanvas', function () {
            mainContent.classList.add('dashboard-pushed');
            footer.classList.add('footer-pushed');
        });

        offcanvasSidebar.addEventListener('hide.bs.offcanvas', function () {
            mainContent.classList.remove('dashboard-pushed');
            footer.classList.remove('footer-pushed');
        });
    });

    function checkVotingStatus() {
        $.ajax({
            url: 'get_voting_status.php', // URL to your backend file
            type: 'GET',
            success: function(response) {
                var result = JSON.parse(response);
                var statusMessage = '';

                if (result.status === 'upcoming') {
                    statusMessage = 'Voting will begin soon.';
                } else if (result.status === 'active') {
                    statusMessage = 'Voting is currently active. Please cast your vote.';
                } else if (result.status === 'completed') {
                    statusMessage = 'Voting has ended. Thank you for participating!';
                } else {
                    statusMessage = 'No election is currently active.';
                }

                $('#voting-status').text(statusMessage);
            },
            error: function() {
                $('#voting-status').text('Failed to fetch voting status.');
            }
        });
    }

    // Call the function every 30 seconds to check for updates
    setInterval(checkVotingStatus, 30000);

    // Check voting status immediately when the page loads
    checkVotingStatus();


</script>

</body>
</html>
