<?php
include 'config.php';
session_start();

// Check if the candidate (voter) is logged in
if (!isset($_SESSION['candidate_id'])) {
    header("Location: candidate_login.php");
    exit();
}

// Fetch the voting history for the logged-in candidate
$candidate_id = $_SESSION['candidate_id'];
$query = "
    SELECT e.election_name, e.election_date, c.candidate_name, c.candidate_role, v.total_votes,
           IF(c.candidate_id = vh.candidate_id, 'won', 'lost') AS result_status
    FROM voting_history vh
    JOIN elections e ON vh.election_id = e.election_id
    JOIN candidates c ON vh.candidate_id = c.candidate_id
    JOIN votes v ON vh.id = v.id
    WHERE vh.voter_hash = ? OR vh.candidate_id = ?
    ORDER BY e.election_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $candidate_id, $candidate_id);  // Bind candidate ID as both voter and candidate
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
    </style>
</head>
<body>

<div class="container">
    <h1>Your Voting History</h1>

    <?php if (!empty($voting_history)): ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Election Name</th>
                    <th>Date</th>
                    <th>Voted Candidate</th>
                    <th>Role</th>
                    <th>Result Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($voting_history as $history): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($history['election_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($history['election_date'])); ?></td>
                        <td><?php echo htmlspecialchars($history['candidate_name']); ?></td>
                        <td><?php echo htmlspecialchars($history['candidate_role']); ?></td>

                        <td>
                            <?php if ($history['result_status'] == 'won'): ?>
                                <span class="badge bg-success">Won</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Lost</span>
                            <?php endif; ?>
                        </td>
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
