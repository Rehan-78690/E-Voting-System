<?php
include '../../config.php';
session_start();
$roles = ["professor", "lecturer", "assistant professor", "associate professor"];
$query = "SELECT election_id, election_name, status, role FROM elections";
$result = mysqli_query($conn, $query);
$elections = $result && mysqli_num_rows($result) > 0 ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status_update'])) {
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    $election_id_to_update = mysqli_real_escape_string($conn, $_POST['election_id']);
    $status_check_query = "SELECT status, role FROM elections WHERE election_id = ?";
    $stmt_check = $conn->prepare($status_check_query);
    $stmt_check->bind_param("i", $election_id_to_update);
    $stmt_check->execute();
    $stmt_check->bind_result($current_status, $current_role);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($current_status == 'completed') {
        echo "<script>alert('This election is already completed and cannot be updated.');</script>";
    } else {
        if ($new_status == 'active') {
            // Check if another election with the same role is already active
            $active_check_query = "SELECT COUNT(*) AS active_count FROM elections WHERE status = 'active' AND role = ?";
            $stmt_active_check = $conn->prepare($active_check_query);
            $stmt_active_check->bind_param("s", $current_role);
            $stmt_active_check->execute();
            $stmt_active_check->bind_result($active_count);
            $stmt_active_check->fetch();
            $stmt_active_check->close();

            if ($active_count > 0) {
                echo "<div class='alert alert-danger text-center mt-3'>Another election for this role is already active. Please deactivate it before activating a new one.</div>";
            } else {
                // Activate election and assign election_id to candidates
                $update_query = "UPDATE elections SET status = ? WHERE election_id = ?";
                $stmt = $conn->prepare($update_query);
                if ($stmt) {
                    $stmt->bind_param("si", $new_status, $election_id_to_update);
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success text-center mt-3'>Election status updated successfully!</div>";

                        // Assign election_id to candidates for the activated election
                        $assign_query = "UPDATE candidates SET election_id = ? WHERE candidate_role = ?";
                        $stmt_assign = $conn->prepare($assign_query);
                        $stmt_assign->bind_param("is", $election_id_to_update, $current_role);
                        if (!$stmt_assign->execute()) {
                            echo "<div class='alert alert-danger text-center mt-3'>Error assigning election ID: " . $stmt_assign->error . "</div>";
                        }
                        $stmt_assign->close();
                    } else {
                        echo "<div class='alert alert-danger text-center mt-3'>Error updating election status: " . $stmt->error . "</div>";
                    }
                    $stmt->close();
                }
            }
        } elseif ($new_status == 'completed') {
            // Complete election and reset election_id for candidates
            $update_query = "UPDATE elections SET status = ? WHERE election_id = ?";
            $stmt = $conn->prepare($update_query);
            if ($stmt) {
                $stmt->bind_param("si", $new_status, $election_id_to_update);
                if ($stmt->execute()) {
                    echo "<div class='alert alert-success text-center mt-3'>Election status updated successfully!</div>";

                    // Reset election_id and has_voted status for candidates after election completion
                    $reset_query = "UPDATE candidates SET election_id = NULL, has_voted = 0 WHERE candidate_role = ?";
                    $stmt_reset = $conn->prepare($reset_query);
                    $stmt_reset->bind_param("s", $current_role);
                    if (!$stmt_reset->execute()) {
                        echo "<div class='alert alert-danger text-center mt-3'>Failed to reset candidate election ID: " . $stmt_reset->error . "</div>";
                    } else {
                        echo "<div class='alert alert-info text-center mt-3'>Election completed and candidate records have been reset.</div>";
                    }
                    moveVotesToVotingHistory($conn, $election_id_to_update);
                    $stmt_reset->close();
                } else {
                    echo "<div class='alert alert-danger text-center mt-3'>Error updating election status: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
        }
    }
}
function moveVotesToVotingHistory($conn, $election_id) {
   
    $query = "SELECT * FROM votes WHERE election_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $insert_query = "INSERT INTO voting_history (election_id, candidate_id, id) 
                         VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_query);
        $stmt_insert->bind_param("iii", $row['election_id'], $row['candidate_id'], $row['id']);
        $stmt_insert->execute();
    }

    echo "<script>alert('Votes have been successfully moved to voting history for the completed election.');</script>";
}


$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Election Status</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 600px; /* Restrict form width */
            width: 100%;
            padding: 15px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
        }

        .card {
            padding: 20px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            background-color: white;
        }

        label {
            font-weight: 500;
            color: #555;
            margin-bottom: 5px;
        }

        select {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        select:focus {
            border-color: #007bff;
            box-shadow: 0 0 4px rgba(0, 123, 255, 0.3); /* Highlight on focus */
        }

        button {
            font-size: 16px;
            font-weight: 600;
            padding: 10px;
            border-radius: 8px;
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
        }

        button:hover {
            background-color: #0056b3;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Hover shadow */
        }

        .form-select option[disabled] {
            color: #999; /* Gray out disabled options */
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            h2 {
                font-size: 1.5rem;
            }

            button {
                font-size: 14px;
            }
        }
        .back-button {
    position: fixed; /* Ensure it stays in place as the user scrolls */
    top: 70px; /* Adjust to appear right below the fixed navbar */
    left: 260px; /* Padding from the left edge of the screen */
    z-index: 1050; /* Ensure it appears above most elements but below the navbar */
}
    </style>
</head>
<body>
<div class="back-button">
    <a href="election_settings.php" class="btn btn-secondary">← Back</a>
</div>
<?php
    include 'sidebar.php';
    ?>
    
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
                        <a class="nav-link active" aria-current="page" href="../../welcome.php">Home</a>
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
    <div class="content"id="mainContent">
    <h2>Manage Election Status</h2>
    
    <div class="card">
        <form method="POST" action="">
            <!-- Election Selection -->
            <div class="mb-3">
                <label for="election_id">Select Election to Update:</label>
                <select name="election_id" id="election_id" class="form-select" required>
                    <?php foreach ($elections as $election): ?>
                        <option value="<?php echo $election['election_id']; ?>" 
                                <?php echo $election['status'] == 'completed' ? 'disabled' : ''; ?>>
                            <?php echo $election['election_name'] . ' (' . ucfirst($election['status']) . ') - Role: ' . ucfirst($election['role']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Status Update Selection -->
            <div class="mb-3">
                <label for="new_status">Change Status:</label>
                <select name="new_status" id="new_status" class="form-select" required>
                    <option value="upcoming">Upcoming</option>
                    <option value="inactive">Inactive</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <!-- Submit Button -->
            <input type="hidden" name="status_update" value="true">
            <button type="submit" class="btn btn-primary">Update Status</button>
        </form>
    </div>
</div>
<script src="../script.js"></script>
</body>
</html>
