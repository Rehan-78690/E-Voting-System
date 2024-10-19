<?php
include 'config.php';
session_start();

// Check if the candidate is logged in
if (!isset($_SESSION['candidate_id'])) {
    header("Location: candidate_login.php");
    exit();
}

// Fetch the candidate's symbol from the database
$candidate_id = $_SESSION['candidate_id'];
$query = "SELECT candidate_name, symbol FROM candidates WHERE candidate_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $candidate = $result->fetch_assoc();
    $candidate_name = $candidate['candidate_name'];
    $symbol = $candidate['symbol'];
} else {
    echo "<div class='alert alert-danger text-center mt-3'>No symbol found for this candidate.</div>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Allocated Symbol</title>
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
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card img {
            max-width: 200px;
            max-height: 200px;
            margin: 20px auto;
        }
        .modal img {
            max-width: 100%;
            max-height: 80vh;
            margin: auto;
            display: block;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Candidate Dashboard</h1>
    <h2>Welcome, <?php echo htmlspecialchars($candidate_name); ?>!</h2>

    <div class="card mx-auto" style="width: 18rem;">
        <div class="card-body text-center">
            <h5 class="card-title">Your Allocated Symbol</h5>
            <?php if (!empty($symbol)): ?>
                <img src="<?php echo htmlspecialchars($symbol); ?>" alt="Symbol" class="img-fluid" id="symbolImage">
                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#fullScreenModal">View Full Screen</button>
            <?php else: ?>
                <p class="text-muted">No symbol has been allocated to you yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal for Full-Screen View -->
<div class="modal fade" id="fullScreenModal" tabindex="-1" aria-labelledby="fullScreenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fullScreenModalLabel">Full-Screen Symbol View</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($symbol)): ?>
                    <img src="<?php echo htmlspecialchars($symbol); ?>" alt="Full-Screen Symbol">
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
