<?php
// election_result.php
session_start();
include 'config.php';
//error_reporting(0);// Include your database configuration file

// Include PHPMailer for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Function to send email notifications to users
function sendEmailNotifications($conn) {
    // Fetch all user emails
    $email_query = "SELECT email FROM users";
    $email_result = mysqli_query($conn, $email_query);

    if ($email_result && mysqli_num_rows($email_result) > 0) {
        while ($email_row = mysqli_fetch_assoc($email_result)) {
            $user_email = $email_row['email'];

            if (!empty($user_email)) {
                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'your_email@example.com'; // Replace with your email
                    $mail->Password = 'your_email_password';    // Replace with your email password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('noreply@yourelectionsite.com', 'Election Results');
                    $mail->addAddress($user_email);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Election Results are Now Available!';
                    $mail->Body    = 'Dear voter,<br><br>The election results are now available. Please visit our site to view the results.<br><br>Best regards,<br>Election Committee';

                    $mail->send();
                } catch (Exception $e) {
                    // Handle errors here
                }
            }
        }
    }
}

// Check if results are already sent
if (!isset($_SESSION['results_sent'])) {
    sendEmailNotifications($conn);
    $_SESSION['results_sent'] = true;
}

// Fetch election summary data
$total_votes_query = "SELECT SUM(total_votes) as total_votes FROM votes";
$total_votes_result = mysqli_query($conn, $total_votes_query);

// Error handling for query execution
if (!$total_votes_result) {
    die("Query failed: " . mysqli_error($conn));
}

$total_votes_row = mysqli_fetch_assoc($total_votes_result);

if ($total_votes_row) {
    $total_votes = $total_votes_row['total_votes'];
} else {
    $total_votes = 0; // No votes found
}

// Debugging output
echo "Total Votes: " . $total_votes;

// Fetch voter turnout (assuming total registered voters is known)
$total_voters = 1000; // Replace with actual number
$voter_turnout = ($total_votes / $total_voters) * 100;
// Fetch candidates and their vote counts
$candidates_query = "
   SELECT c.candidate_id, c.candidate_name, c.profile_pic, c.candidate_role, c.department,SUM(v.total_votes) AS vote_count
FROM candidates c
LEFT JOIN votes v ON c.candidate_id = v.candidate_id
GROUP BY c.candidate_id
ORDER BY vote_count DESC";

$candidates_result = mysqli_query($conn, $candidates_query);

$candidates = [];
while ($row = mysqli_fetch_assoc($candidates_result)) {
    $candidates[] = $row;
}

// Get top three candidates for badges
$top_candidates = array_slice($candidates, 0, 3);

// Prepare data for charts
$chart_labels = [];
$chart_votes = [];
$chart_colors = [];

foreach ($candidates as $candidate) {
    $chart_labels[] = $candidate['candidate_name'];
    $chart_votes[] = $candidate['vote_count'];
    // Generate random color for each candidate
    $chart_colors[] = 'rgba('.rand(0,255).', '.rand(0,255).', '.rand(0,255).', 0.7)';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Election Results</title>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include jsPDF for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- Include confetti.js for celebration effect -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    <!-- Include CountUp.js for animated counters -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.0.7/countUp.min.js"></script>
    <style>
        body {
            font-family: poppins;
            background-color: #f7f7f7;
            margin: 0;
            padding: 20px;
        }
        .winner-highlight {
            background-color: #fff;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        .winner-highlight img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
        }
        .winner-highlight h2 {
            margin: 10px 0;
        }
        .badge {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 50px;
        }
        .infographic {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .stat {
            background-color: #fff;
            padding: 20px;
            text-align: center;
            flex: 1;
            margin: 0 10px;
        }
        .stat .value {
            font-size: 40px;
            color: #333;
        }
        .leaderboard {
            background-color: #fff;
            padding: 20px;
        }
        .candidate {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .candidate img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }
        .candidate .name {
            flex: 1;
            margin-left: 10px;
        }
        .candidate .votes {
            font-weight: bold;
        }
        .badge-gold {
            color: gold;
        }
        .badge-silver {
            color: silver;
        }
        .badge-bronze {
            color: #cd7f32;
        }
        .download-btn {
            margin: 20px 0;
            text-align: center;
        }
        .download-btn button {
            padding: 10px 20px;
            font-size: 16px;
        }
        .chart-container {
            width: 60%;
            margin: auto;
        }
    </style>
</head>
<body>

<!-- Winner Highlight Section -->
<div class="winner-highlight">
    <div class="badge badge-gold">🏆</div>
    <img src="<?php echo $top_candidates[0]['profile_pic']; ?>" alt="Winner's Photo">
    <h2>Congratulations to <?php echo $top_candidates[0]['candidate_name']; ?></h2>
    <p>Total Votes: <span id="winner-votes"><?php echo $total_votes;?></span></p>
    <p>Winning Percentage: <?php echo number_format(($top_candidates[0]['vote_count'] / $total_votes) * 100, 2); ?>%</p>
</div>

<!-- Trigger confetti on page load -->
<script>
    window.onload = function() {
        confetti();
        const winnerVotes = new CountUp('winner-votes', <?php echo $top_candidates[0]['vote_count']; ?>);
        winnerVotes.start();
    };
</script>

<!-- Infographic Section -->
<div class="infographic">
    <div class="stat">
        <div class="icon">🗳️</div>
        <div class="value" id="total-votes"><?php echo $total_votes_row['total_votes']; ?></div>
        <div class="label">Total Votes Cast</div>
    </div>
    <div class="stat">
        <div class="icon">📈</div>
        <div class="value"><?php echo number_format($voter_turnout, 2); ?>%</div>
        <div class="label">Voter Turnout</div>
    </div>
    <div class="stat">
        <div class="icon">👥</div>
        <div class="value"><?php echo count($candidates); ?></div>
        <div class="label">Candidates</div>
    </div>
</div>

<script>
    const totalVotes = new CountUp('total-votes', <?php echo $total_votes; ?>);
    totalVotes.start();
</script>

<!-- Leaderboard Section -->
<div class="leaderboard">
    <h2>Election Leaderboard</h2>
    <?php foreach ($candidates as $index => $candidate): ?>
        <div class="candidate">
            <?php if ($index == 0): ?>
                <div class="badge badge-gold">🥇</div>
            <?php elseif ($index == 1): ?>
                <div class="badge badge-silver">🥈</div>
            <?php elseif ($index == 2): ?>
                <div class="badge badge-bronze">🥉</div>
            <?php else: ?>
                <div class="rank"><?php echo $index + 1; ?></div>
            <?php endif; ?>
            <img src="<?php echo $candidate['profile_pic']; ?>" alt="Candidate Photo">
            <div class="name"><?php echo $candidate['candidate_name']; ?></div>
            <div class="votes" id="vote-count-<?php echo $candidate['candidate_id']; ?>"><?php echo $candidate['vote_count']??0; ?></div>
        </div>
        <script>
            const voteCount<?php echo $candidate['candidate_id']; ?> = new CountUp('vote-count-<?php echo $candidate['candidate_id']; ?>', <?php echo $candidate['vote_count']?? 0; ?>);
            voteCount<?php echo $candidate['candidate_id']; ?>.start();
        </script>
    <?php endforeach; ?>
</div>

<!-- Chart Section -->
<div class="chart-container">
    <canvas id="resultsChart"></canvas>
</div>

<script>
    const ctx = document.getElementById('resultsChart').getContext('2d');
    const resultsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Votes',
                data: <?php echo json_encode($chart_votes); ?>,
                backgroundColor: <?php echo json_encode($chart_colors); ?>,
                borderColor: <?php echo json_encode($chart_colors); ?>,
                borderWidth: 1
            }]
        },
        options: {
            animation: {
                duration: 2000
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<!-- Download PDF Button -->
<div class="download-btn">
    <button onclick="generatePDF()">Download Results as PDF</button>
</div>

<script>
    async function generatePDF() {
        const { jsPDF } = window.jspdf;

        const doc = new jsPDF();

        doc.setFontSize(18);
        doc.text('Election Results', 14, 22);

        doc.setFontSize(12);
        doc.text('Winner: <?php echo $top_candidates[0]['candidate_name']; ?>', 14, 32);
        doc.text('Total Votes Cast: <?php echo $total_votes; ?>', 14, 40);
        doc.text('Voter Turnout: <?php echo number_format($voter_turnout, 2); ?>%', 14, 48);

        // Add leaderboard
        doc.setFontSize(16);
        doc.text('Leaderboard', 14, 60);

        let yOffset = 70;
        <?php foreach ($candidates as $index => $candidate): ?>
            doc.setFontSize(12);
            doc.text('<?php echo ($index + 1) . '. ' . $candidate['candidate_name'] . ' - ' . $candidate['vote_count'] . ' votes'; ?>', 14, yOffset);
            yOffset += 8;
        <?php endforeach; ?>

        // Save the PDF
        doc.save('election_results.pdf');
    }
</script>

</body>
</html>
