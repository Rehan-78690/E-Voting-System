<?php
include "config.php";
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: candidate.php");
    exit();
}

$voter_id = $_SESSION['candidate_id'];

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
    exit();
}

// Prepare and execute the SQL query
$query = "SELECT candidate_id, candidate_name, candidate_role, department, symbol FROM candidates";
$result = $conn->query($query);
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
                    <td>  
                        <form id="voteForm<?php echo $row['candidate_id']; ?>" action="vote.php" method="POST" style="display: none;">
                            <input type="hidden" name="candidate_id" value="<?php echo $row['candidate_id']; ?>">
                            <input type="hidden" name="candidate_name" value="<?php echo htmlspecialchars($row['candidate_name']); ?>">
                            <input type="hidden" name="role" value="<?php echo htmlspecialchars($row['candidate_role']); ?>">
                            <input type="hidden" name="department" value="<?php echo htmlspecialchars($row['department']); ?>">
                        </form>
                        <button class="btn btn-sm btn-secondary" onclick="vote(<?php echo $row['candidate_id']; ?>, this)">Vote</button>
                        <button class="btn btn-sm btn-danger" onclick="cancelVote(<?php echo $row['candidate_id']; ?>, this)">Cancel Vote</button>
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
    
    <script>
        let hasVoted = <?php echo json_encode($has_voted); ?>;

        function vote(candidateId, button) {
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
                    displayThankYouMessage(); // Display the thank you message before redirecting
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

