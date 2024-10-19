<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit();
}

// Prepare statement to fetch completed election
$sql = "SELECT * FROM elections WHERE election_id = ? AND status = 'completed'";
if ($stmt = $conn->prepare($sql)) {
    // Assuming election ID is passed via GET
    $election_id = $_GET['election_id'] ?? null;
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if the election is completed
    if ($result->num_rows > 0) {
        // Prepare query to fetch voting history and related details
        $query = "
            SELECT vh.history_id, v.id, v.total_votes, vh.election_id, vh.candidate_id, vh.voter_hash, vh.election_date,
                   c.candidate_name, c.candidate_role, c.department, e.election_name
            FROM voting_history vh
            JOIN candidates c ON vh.candidate_id = c.candidate_id
            JOIN elections e ON vh.election_id = e.election_id
            JOIN votes v ON vh.id = v.id
            WHERE vh.election_id = ?
            ORDER BY vh.election_date DESC";
        
        if ($stmt2 = $conn->prepare($query)) {
            $stmt2->bind_param("i", $election_id);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            
            // Fetch the voting history
            $voting_history = [];
            if ($result2->num_rows > 0) {
                while ($row = $result2->fetch_assoc()) {
                    $voting_history[] = $row;
                }
            }
        } else {
            echo "Error in preparing voting history query.";
        }
    } else {
        echo "No completed election found with the provided ID.";
    }
} else {
    echo "Error in preparing election query.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Voting History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
            font-family: 'Poppins', sans-serif;
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

        .table td {
            padding: 15px;
        }

        .table .election-name {
            font-weight: bold;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Voting History</h2>

    <?php if (!empty($voting_history)): ?>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Election Name</th>
                <th>Candidate Name</th>
                <th>Role</th>
                <th>Department</th>
                <th>Voter (Hashed)</th>
                <th>Total Votes</th>
                <th>Election Date</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($voting_history as $row): ?>
            <tr>
                <td class="election-name"><?php echo htmlspecialchars($row['election_name']); ?></td>
                <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                <td><?php echo htmlspecialchars($row['candidate_role']); ?></td>
                <td><?php echo htmlspecialchars($row['department']); ?></td>
                <td><?php echo htmlspecialchars($row['voter_hash']); ?></td>
                <td><?php echo htmlspecialchars($row['total_votes']); ?></td>
                <td><?php echo date('d M Y, H:i', strtotime($row['election_date'])); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="alert alert-warning text-center" role="alert">
            No voting history found for this election.
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
