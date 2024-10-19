<?php
include 'config.php';
session_start();

// Fetch all elections to allow the admin to choose one to update
$query = "SELECT election_id, election_day, election_date, end_time, status, role FROM elections";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Fetch all election data
    $elections = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    echo "<div class='alert alert-warning text-center mt-3'>No elections found.</div>";
    $elections = [];
}

// Handle form submission for status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status_update'])) {
    // Get the election ID, new status, and role
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    $election_id_to_update = mysqli_real_escape_string($conn, $_POST['election_id']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Fetch current election status
    $status_check_query = "SELECT status FROM elections WHERE election_id = ?";
    $stmt_check = $conn->prepare($status_check_query);
    $stmt_check->bind_param("i", $election_id_to_update);
    $stmt_check->execute();
    $stmt_check->bind_result($current_status);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($current_status == 'completed') {
        echo "<script>alert('This election is already completed and cannot be updated.');</script>";
    } else {
        // SQL query to update the election status
        $update_query = "UPDATE elections SET status = ?, role = ? WHERE election_id = ?";
        $stmt = $conn->prepare($update_query);

        if ($stmt) {
            $stmt->bind_param("ssi", $new_status, $role, $election_id_to_update);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success text-center mt-3'>Election status updated successfully!</div>";
                
                // If the election is now marked as 'completed', move votes to voting history
                if ($new_status == 'completed') {
                    $reset_query = "UPDATE candidates SET has_voted = 0";
                    if ($conn->query($reset_query)) {
                        echo "<script>alert('Voting status reset successfully for all candidates.');</script>";
                    } else {
                        echo "<script>alert('Failed to reset voting status.');</script>";
                    }
                    moveVotesToVotingHistory($conn, $election_id_to_update);
                }
            } else {
                echo "<div class='alert alert-danger text-center mt-3'>Error updating election status: " . $stmt->error . "</div>";
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "<div class='alert alert-danger text-center mt-3'>Error preparing statement: " . $conn->error . "</div>";
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['election_announcement'])) {
    // Handle election announcement form submission
    $election_day = mysqli_real_escape_string($conn, $_POST['election_day']);
    $election_date = mysqli_real_escape_string($conn, $_POST['election_date']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time_input = mysqli_real_escape_string($conn, $_POST['end_time']);
    $notification = mysqli_real_escape_string($conn, $_POST['notification']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    if (empty($election_date)) {
        echo "<div class='alert alert-danger text-center mt-3'>Error: Election date is missing!</div>";
        exit;
    }

    $start_datetime = $election_date . ' ' . $start_time;
    $end_datetime = $election_date . ' ' . $end_time_input;

    // SQL query to insert election announcement into the elections table
    $insert_query = "INSERT INTO elections (election_day, election_date, start_time, end_time, status, description, role) 
                     VALUES (?, ?, ?, ?, 'inactive', ?, ?)"; // Setting initial status to 'inactive'

    $stmt = $conn->prepare($insert_query);
    if ($stmt) {
        $stmt->bind_param("ssssss", $election_day, $election_date, $start_datetime, $end_datetime, $notification, $role);

        // Execute the query
        if ($stmt->execute()) {
            echo "<div class='alert alert-success text-center mt-3'>Election announcement added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger text-center mt-3'>Error: " . $stmt->error . "</div>";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger text-center mt-3'>Error preparing statement: " . $conn->error . "</div>";
    }
}

// Function to move votes to voting_history
function moveVotesToVotingHistory($conn, $election_id) {
    // Fetch votes for the completed election
    $query = "SELECT * FROM votes WHERE election_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Insert votes into voting_history
    while ($row = $result->fetch_assoc()) {
        // Insert into voting_history
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
    <title>Manage Voting</title>
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
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
    <h1>Add Elections</h1>
    <div class="card">
        <form method="POST" action="">
            <input type="hidden" name="election_announcement" value="true">

            <div class="mb-3">
                <label for="election_day">Election Day:</label>
                <input type="text" id="election_day" name="election_day" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="election_date">Election Date:</label>
                <input type="date" id="election_date" name="election_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="role">Choose a role:</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="professor">Professor</option>
                    <option value="lecturer">Lecturer</option>
                    <option value="assistant_professor">Assistant Professor</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="description">Description:</label>
                <textarea id="description" name="notification" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Add Elections</button>
        </form>
    </div>

    <h2>Manage Election Status</h2>
    <div class="card">
        <form method="POST" action="">
            <div class="mb-3">
                <label for="election_id">Select Election to Update:</label>
                <select name="election_id" id="election_id" class="form-select" required>
                    <?php foreach ($elections as $election): ?>
                        <option value="<?php echo $election['election_id']; ?>" <?php echo $election['status'] == 'completed' ? 'disabled' : ''; ?>>
                            <?php echo $election['role'] . ' (' . ucfirst($election['status']) . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="new_status">Change Status:</label>
                <select name="new_status" id="new_status" class="form-select" required>
                    <option value="upcoming">Upcoming</option>
                    <option value="inactive">Inactive</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="role">Choose a role:</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="professor">Professor</option>
                    <option value="lecturer">Lecturer</option>
                    <option value="assistant_professor">Assistant Professor</option>
                </select>
            </div>

            <input type="hidden" name="status_update" value="true">
            <button type="submit" class="btn btn-primary">Update Status</button>
        </form>
    </div>

    <h2>Activate or Deactivate Voting</h2>
    <div class="card">
        <form method="POST" action="">
            <div class="mb-3">
                <label for="election_id">Activate Voting for:</label>
                <select name="election_id" id="election_id" class="form-select" required>
                    <?php foreach ($elections as $election): ?>
                        <option value="<?php echo $election['election_id']; ?>">
                            <?php echo $election['role'] . ' (' . ucfirst($election['election_date']) . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="activate_voting" class="btn btn-primary">Activate Voting</button>
        </form>
    </div>

    <div class="card">
        <form method="POST" action="">
            <div class="mb-3">
                <label for="election_id">Deactivate Voting for:</label>
                <select name="election_id" id="election_id" class="form-select" required>
                    <?php foreach ($elections as $election): ?>
                        <option value="<?php echo $election['election_id']; ?>">
                            <?php echo $election['role'] . ' (' . ucfirst($election['election_date']) . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="deactivate_voting" class="btn btn-primary">Deactivate Voting</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
