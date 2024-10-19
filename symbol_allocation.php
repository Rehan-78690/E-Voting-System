<?php
include 'config.php'; // Include your database configuration file

// Fetch upcoming elections
$election_query = "SELECT election_id, election_name, election_date FROM elections WHERE status = 'upcoming'";
$election_result = mysqli_query($conn, $election_query);

$elections = [];
if ($election_result && mysqli_num_rows($election_result) > 0) {
    $elections = mysqli_fetch_all($election_result, MYSQLI_ASSOC);
} else {
    echo "<div class='alert alert-warning text-center mt-3'>No upcoming elections found.</div>";
}

// Fetch approved candidates without symbols for the selected election
$candidates = [];
if (isset($_GET['election_id'])) {
    $election_id = mysqli_real_escape_string($conn, $_GET['election_id']);
    
    $candidate_query = "
        SELECT c.candidate_id, c.candidate_name, c.symbol 
        FROM candidates c
        WHERE c.status = 'approved' 
        AND c.election_id = ? 
        AND (c.symbol IS NULL OR c.symbol = '')";
    
    $stmt = $conn->prepare($candidate_query);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && mysqli_num_rows($result) > 0) {
        $candidates = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        echo "<div class='alert alert-warning text-center mt-3'>No approved candidates found for this upcoming election.</div>";
    }
}

// Handle symbol (image) allocation form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['allocate_symbol'])) {
    $election_id = mysqli_real_escape_string($conn, $_POST['election_id']);
    $candidate_id = mysqli_real_escape_string($conn, $_POST['candidate_id']);

    // File upload logic for symbol (image)
    if (isset($_FILES['symbol_image']) && $_FILES['symbol_image']['error'] == 0) {
        $image_name = $_FILES['symbol_image']['name'];
        $image_tmp_name = $_FILES['symbol_image']['tmp_name'];
        $upload_dir = "uploads/symbols/"; // Directory to store symbols

        // Create the directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $target_file = $upload_dir . basename($image_name);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($image_tmp_name, $target_file)) {
            // Update candidate's symbol with the file path
            $update_query = "UPDATE candidates SET symbol = ? WHERE candidate_id = ? AND election_id = ?";
            $stmt_update = $conn->prepare($update_query);
            $stmt_update->bind_param("sii", $target_file, $candidate_id, $election_id);

            if ($stmt_update->execute()) {
                echo "<div class='alert alert-success text-center mt-3'>Symbol image allocated successfully!</div>";
            } else {
                echo "<div class='alert alert-danger text-center mt-3'>Error allocating symbol: " . $stmt_update->error . "</div>";
            }

            $stmt_update->close();
        } else {
            echo "<div class='alert alert-danger text-center mt-3'>Error uploading symbol image.</div>";
        }
    } else {
        echo "<div class='alert alert-warning text-center mt-3'>No symbol image uploaded or error during upload.</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allocate Symbols to Approved Candidates</title>
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
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .btn-primary {
            margin-top: 10px;
            width: 100%;
        }
        h1, h2 {
            margin-bottom: 30px;
            text-align: center;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Allocate Symbols to Approved Candidates</h1>

    <!-- Form to select an upcoming election -->
    <div class="card">
        <form method="GET" action="">
            <div class="mb-3">
                <label for="election_id" class="form-label">Select Upcoming Election:</label>
                <select name="election_id" id="election_id" class="form-select" required>
                    <option value="">Select an election</option>
                    <?php foreach ($elections as $election): ?>
                        <option value="<?php echo $election['election_id']; ?>">
                            <?php echo htmlspecialchars($election['election_name']) . ' - ' . htmlspecialchars($election['election_date']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">View Candidates</button>
        </form>
    </div>

    <!-- Display approved candidates and symbol (image) allocation form -->
    <?php if (!empty($candidates)): ?>
        <h2>Approved Candidates for Election</h2>
        <div class="card">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="election_id" value="<?php echo htmlspecialchars($_GET['election_id']); ?>">
                
                <div class="mb-3">
                    <label for="candidate_id" class="form-label">Select Candidate:</label>
                    <select name="candidate_id" id="candidate_id" class="form-select" required>
                        <?php foreach ($candidates as $candidate): ?>
                            <option value="<?php echo $candidate['candidate_id']; ?>">
                                <?php echo htmlspecialchars($candidate['candidate_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="symbol_image" class="form-label">Upload Symbol Image:</label>
                    <input type="file" name="symbol_image" id="symbol_image" class="form-control" accept="image/*" required>
                </div>

                <button type="submit" name="allocate_symbol" class="btn btn-primary">Allocate Symbol</button>
            </form>
        </div>
    <?php elseif (isset($_GET['election_id'])): ?>
        <div class="alert alert-warning text-center" role="alert">
            No approved candidates found for this upcoming election.
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
