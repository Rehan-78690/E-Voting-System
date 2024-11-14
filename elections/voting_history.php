<?php
include 'config.php';
session_start();

// Check if the candidate (voter) is logged in
if (!isset($_SESSION['candidate_id'])) {
    header("Location:voter.php");
    exit();
}

// Fetch the voting history for the logged-in candidate (voter)
$voter_id = $_SESSION['candidate_id'];
$query = "
    SELECT 
        elections.election_name AS election,
        candidates.candidate_name AS voted_for,
        candidates.candidate_role AS candidate_role,
        voters.date AS election_date,
        YEAR(voters.date) AS year
    FROM 
        voters
    JOIN 
        candidates ON voters.candidate_id = candidates.candidate_id
    JOIN 
        elections ON voters.election_id = elections.election_id
    WHERE 
        voters.voter_id = ?
    ORDER BY 
        voters.date DESC;
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $voter_id);  // Bind only voter_id
$stmt->execute();
$result = $stmt->get_result();

// Fetch results
$voting_history = [];
if ($result->num_rows > 0) {
    $voting_history = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            padding-top: 60px;
        }
        .container {
            margin-top: 50px;
        }
        .table {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #343a40;
            color: #fff;
        }
        .table td, .table th {
            text-align: center;
            vertical-align: middle;
        }
        h1 {
            margin-bottom: 30px;
            text-align: center;
        }
        .alert {
            margin-top: 20px;
        }
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
        }
    </style>
</head>
<body>
<div class="back-button">
    <a href="voter_dashboard.php" class="btn btn-secondary">← Back</a>
</div>
<div class="container">
    <h1>Your Voting History</h1>

    <?php if (!empty($voting_history)): ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Election Name</th>
                    <th>Date</th>
                    <th>Voted Candidate</th>
                    <th>Designation</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($voting_history as $history): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($history['election']); ?></td>
                        <td><?php echo date('d M Y', strtotime($history['election_date'])); ?></td>
                        <td><?php echo htmlspecialchars($history['voted_for']); ?></td>
                        <td><?php echo htmlspecialchars($history['candidate_role']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning text-center" role="alert">
            No voting history found.
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
