<?php
include "config.php";
session_start();
if (!isset($_SESSION['candidate_email'])) {
    header("Location: candidate.php");
    exit();
}
$election_id = isset($_GET['election_id']) ? intval($_GET['election_id']) : 0;

if ($election_id == 0) {
    echo "Invalid election.";
    exit();
}
echo($election_id);
// Check the status of the current election
$election_check = "SELECT status FROM elections WHERE election_id = ?";
$stmt = $conn->prepare($election_check);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $election_status = $row['status'];
    if ($election_status === 'completed') {
        echo "Election is completed. You cannot vote now.";
        header("refresh:2;url=voter_dashboard.php");
        exit();
    }
    if ($election_status === 'inactive') {
        echo "Elections have not started yet.";
        header("refresh:2;url=voter_dashboard.php");
        exit();
    }
    if ($election_status === 'upcoming') {
        echo "Elections are starting soon. Prepare to vote.";
        header("refresh:2;url=voter_dashboard.php");
        exit();
    }
}


$voter_id = $_SESSION['candidate_id'];

// Check if the voter has already voted
$sql = "SELECT has_voted FROM candidates WHERE candidate_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $voter_id,);
$stmt->execute();
$stmt->bind_result($has_voted);
$stmt->fetch();
$stmt->close();

if ($has_voted) {
    echo "You have already voted and cannot vote again.";
    $redirect_url = "voter_dashboard.php";
$delay = 2;
header("Refresh: $delay; url=$redirect_url");
    exit();
}

$query = "SELECT c.candidate_id, c.candidate_name, c.candidate_role, c.department, c.symbol, IFNULL(v.total_votes, 0) AS total_votes
FROM candidates c
LEFT JOIN votes v ON c.candidate_id = v.candidate_id AND v.election_id = ?
INNER JOIN candidate_documents cd ON c.candidate_id = cd.candidate_id
WHERE c.role = 'candidate' AND c.status = 'approved' AND cd.verification_status = 'verified'
ORDER BY c.candidate_name";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $election_id);
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
</head>
<body>
    <div class="table-container">
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
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['candidate_role']); ?></td>
                    <td><?php echo htmlspecialchars($row['department']); ?></td>
                    <td><?php echo htmlspecialchars($row['symbol']); ?></td>
                    <td>  <form id="voteForm<?php echo $row['candidate_id']; ?>" action="vote.php" method="POST" style="display: none;">
                            <input type="hidden" name="candidate_id" value="<?php echo $row['candidate_id']; ?>">
                            <input type="hidden" name="candidate_name" value="<?php echo htmlspecialchars($row['candidate_name']); ?>">
                            <input type="hidden" name="role" value="<?php echo htmlspecialchars($row['candidate_role']); ?>">
                            <input type="hidden" name="department" value="<?php echo htmlspecialchars($row['department']); ?>">
                            <input type="hidden" name="election_id" value="<?php echo $election_id; ?>">
                        <td><button class="btn btn-sm btn-secondary" onclick="vote(<?php echo $row['candidate_id']; ?>, this)">Vote</button>
                         </form>
                    </td>
                </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='5'>No candidates found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
    if($election_status === 'completed'){
        $update_voter_sql = "UPDATE candidates SET has_voted = 0 WHERE candidate_id = ?";
        $update_voter_stmt = $conn->prepare($update_voter_sql);
        $update_voter_stmt->bind_param("i", $voter_id);
        $update_voter_stmt->execute();
        $update_voter_stmt->close();
    }
    ?>
    <script>
        let hasVoted = false;

        function vote(candidateId, button) {
            displayThankYouMessage();
            if (hasVoted) {
                alert("You have already voted!");
                return;
            }

            // Send vote to the server
            fetch('vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ candidateId: candidateId }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Vote recorded successfully!");
                    hasVoted = true;
                    // button.disabled = true; // Disable the vote button after voting
                } else {
                    alert("Failed to record vote. Please try again.");
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
        }

        function cancelVote(candidateId, button) {
            if (!hasVoted) {
                alert("You haven't voted yet!");
                return;
            }

            // Send cancel vote request to the server
            fetch('cancel_vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ candidateId: candidateId }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Vote canceled successfully!");
                    hasVoted = false;
                    button.disabled = true; // Disable the cancel button after canceling the vote
                } else {
                    alert("Failed to cancel vote. Please try again.");
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
        }
        function displayThankYouMessage() {
            // Create a div element to show the thank you message
            const thankYouDiv = document.createElement('div');
            thankYouDiv.textContent = 'Thank you for voting!';
            thankYouDiv.style.position = 'fixed';
            thankYouDiv.style.top = '50%';
            thankYouDiv.style.left = '50%';
            thankYouDiv.style.transform = 'translate(-50%, -50%)';
            thankYouDiv.style.backgroundColor = '#28a745';
            thankYouDiv.style.color = '#fff';
            thankYouDiv.style.padding = '20px';
            thankYouDiv.style.borderRadius = '10px';
            thankYouDiv.style.textAlign = 'center';
            thankYouDiv.style.fontSize = '24px';

            document.body.appendChild(thankYouDiv);

            // Redirect after 3 seconds
            setTimeout(() => {
                window.location.href = 'ballot.php'; // Redirect to the ballot page
            }, 3000); // 3 seconds delay
        }
    </script>
</body>
</html>
