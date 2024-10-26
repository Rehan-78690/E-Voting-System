<?php
session_start();
include 'config.php'; // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['email'])) {
    header("Location: admin.php");
    exit();
}

// Fetch all feedback from the database
$sql = "SELECT f.id, c.candidate_name, f.feedback_text, f.date, f.status 
        FROM feedback f 
        JOIN candidates c ON f.candidate_id = c.candidate_id
        ORDER BY f.date DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Manage Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
    margin-top: 20px;
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
    padding-top: 56px;
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

    <div class="content" id="mainContent">
    <h2>Candidate Feedback</h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Candidate Name</th>
                    <th>Feedback</th>
                    <th>Submission Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['feedback_text']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                        <a href="mark_reviewed.php?id=<?php echo $row['id']; ?>" 
   class="btn btn-success btn-sm"
   onclick="return confirm('Are you sure you want to mark this as reviewed?');"> Reviewed</a>

<a href="delete_feedback.php?id=<?php echo $row['id']; ?>" 
   class="btn btn-danger btn-sm"
   onclick="return confirm('Are you sure you want to delete this feedback? This action cannot be undone.');">Delete</a>

                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No feedback found.</p>
    <?php endif; ?>
</div>
<footer>
        <p>&copy; 2024 E-Voting UPR. All rights reserved.</p>
        <p>Designed for University of Poonch Rawalakot Elections</p>
    </footer>
<script src="scripts.js"></script>
</body>
</html>
