<?php
include '../security/ballot_security.php'; // Security file
include "config.php"; // Database connection

secure_ballot_page(); // Secure the ballot page with session checks

if (!isset($_SESSION['candidate_email'])) {
    header("Location: candidate.php");
    exit();
}

$election_id = isset($_GET['election_id']) ? intval($_GET['election_id']) : 0;
if ($election_id == 0) {
    echo "Invalid election.";
    exit();
}

// Check the status of the current election
$election_check = "SELECT status, role FROM elections WHERE election_id = ?";
$stmt = $conn->prepare($election_check);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $election_status = $row['status'];
    $election_role = $row['role'];

    // Check election status
    if ($election_status === 'completed') {
        echo "Election is completed. You cannot vote now.";
        header("refresh:2;url=voter_dashboard.php");
        exit();
    } elseif ($election_status === 'inactive') {
        echo "Elections have not started yet.";
        header("refresh:2;url=voter_dashboard.php");
        exit();
    } elseif ($election_status === 'upcoming') {
        echo "Elections are starting soon. Prepare to vote.";
        header("refresh:2;url=voter_dashboard.php");
        exit();
    }
} else {
    echo "Invalid election.";
    exit();
}

$voter_id = $_SESSION['candidate_id'];
$voter_role_query = "SELECT candidate_role,status, election_id FROM candidates WHERE candidate_id = ?";
$stmt = $conn->prepare($voter_role_query);
$stmt->bind_param("i", $voter_id);
$stmt->execute();
$stmt->bind_result($voter_role,$c_status, $voter_election_id);
$stmt->fetch();
$stmt->close();

// Check if the voter's role matches the election's role and election ID
if ($voter_role !== $election_role || $voter_election_id !== $election_id ||$c_status!=='Approved') {
    echo "You are not eligible to vote in this election.";
    header("refresh:2;url=voter_dashboard.php");
    exit();
}

// Check if the voter has already voted
$sql = "SELECT has_voted FROM candidates WHERE candidate_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $voter_id);
$stmt->execute();
$stmt->bind_result($has_voted);
$stmt->fetch();
$stmt->close();

if ($has_voted) {
    echo "You have already voted and cannot vote again.";
    header("refresh:2;url=voter_dashboard.php");
    exit();
}

// Fetch candidates for the ballot
$query = "SELECT c.candidate_id, c.candidate_name, c.candidate_role, c.department, c.symbol
          FROM candidates c
          INNER JOIN candidate_documents cd ON c.candidate_id = cd.candidate_id
          WHERE c.role = 'candidate' AND c.status = 'approved' 
          AND cd.verification_status = 'verified' 
          AND c.candidate_role = ? AND c.election_id = ?
          ORDER BY c.candidate_name";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $election_role, $election_id);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ballot Paper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .thank-you-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #28a745;
            color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-size: 24px;
            z-index: 9999;
            display: none;
        }
    </style>
</head>
<body>
    <div class="thank-you-message" id="thankYouMessage">Thank you for voting!</div>
<h2 align-item="centre"> Ballot Paper</h2>
    <div class="container mt-5">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Candidate Name</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Symbol</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['candidate_role']); ?></td>
                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                        <td>    <img src="../<?php echo htmlspecialchars($row['symbol']); ?>" alt="Symbol" style="width: 50px; height: 50px;"></td>                            <td>
                                <button type="button" class="btn btn-sm btn-primary" onclick="castVote(<?php echo $row['candidate_id']; ?>)">Vote</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No candidates found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        let hasVoted = false;

        function castVote(candidateId) {
            if (hasVoted) {
                alert("You have already voted!");
                return;
            }

            // Send vote to the server
            fetch('vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?php echo generate_csrf_token(); ?>'
                },
                body: JSON.stringify({ candidate_id: candidateId, election_id: <?php echo $election_id; ?> })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hasVoted = true;
                    displayThankYouMessage();
                } else {
                    alert(data.message || "Failed to record vote. Please try again.");
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
        }

        function displayThankYouMessage() {
            const thankYouDiv = document.getElementById('thankYouMessage');
            thankYouDiv.style.display = 'block';

            // Redirect after 3 seconds
            setTimeout(() => {
                window.location.href = 'voter_dashboard.php'; // Redirect to the voter dashboard
            }, 1500); 
        }
    </script>
</body>
</html>
