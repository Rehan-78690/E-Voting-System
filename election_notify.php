<?php
include 'config.php'; // Include your database configuration file

// Fetch all elections to allow the admin to choose one to update
$query = "SELECT election_id, election_day, election_date, end_time, status ,role FROM elections";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Fetch all election data
    $elections = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    echo "No elections found.";
    $elections = [];
}

// Handle form submission for status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status_update'])) {
    // Get the election ID and new status
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    $election_id_to_update = mysqli_real_escape_string($conn, $_POST['election_id']);

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
        $update_query = "UPDATE elections SET status = ? WHERE election_id = ?";
        $stmt = $conn->prepare($update_query);

        if ($stmt) {
            $stmt->bind_param("si", $new_status, $election_id_to_update);

            if ($stmt->execute()) {
                echo "Election status updated successfully!";
            } else {
                echo "Error updating election status: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle election announcement form submission
    $election_day = mysqli_real_escape_string($conn, $_POST['election_day']);
    $election_date = mysqli_real_escape_string($conn, $_POST['election_date']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time_input = mysqli_real_escape_string($conn, $_POST['end_time']);
    $notification = mysqli_real_escape_string($conn, $_POST['notification']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    if (empty($election_date)) {
        echo "Error: Election date is missing!";
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
            echo "Election announcement added successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Voting</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>

<h1>Add Elections</h1>
<form method="POST" action="">
    <label for="election_day">Election Day:</label>
    <input type="text" id="election_day" name="election_day" required>

    <label for="election_date">Election Date:</label>
    <input type="date" id="election_date" name="election_date" required>

    <label for="start_time">Start Time:</label>
    <input type="time" id="start_time" name="start_time" required>

    <label for="end_time">End Time:</label>
    <input type="time" id="end_time" name="end_time" required>

    <label for="role">Choose a role:</label>
    <select id="role" name="role" required>
        <option value="professor">Professor</option>
        <option value="lecturer">Lecturer</option>
        <option value="assistant_professor">Assistant Professor</option>
    </select>

    <label for="description">Description:</label>
    <textarea id="description" name="notification"></textarea>

    <button type="submit">Add Elections</button>
</form>

<h2>Manage Election Status</h2>

<!-- Form to select and update the status of an election -->
<form method="POST" action="">
    <label for="election_id">Select Election to Update:</label>
    <select name="election_id" id="election_id" required>
        <?php foreach ($elections as $election): ?>
            <option value="<?php echo $election['election_id']; ?>" <?php echo $election['status'] == 'completed' ? 'disabled' : ''; ?>>
                <?php echo $election['role'] . ' (' . ucfirst($election['status']) . ')'; ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label for="new_status">Change Status:</label>
    <select name="new_status" id="new_status" required>
        <option value="upcoming">Upcoming</option>
        <option value="inactive">Inactive</option>
        <option value="active">Active</option>
        <option value="completed">Completed</option>
    </select><br>

    <input type="hidden" name="status_update" value="true">
    <button type="submit">Update Status</button>
</form>

<!-- Activation Form -->
<form method="POST" action="activate_voting.php">
    <label for="election_id">Activate Voting for:</label>
    <select name="election_id" id="election_id" required>
        <?php foreach ($elections as $election): ?>
            <option value="<?php echo $election['election_id']; ?>">
            <?php echo $election['role'] . ' (' . ucfirst($election['election_date']) . ')'; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Activate Voting</button>
</form>

<!-- Deactivation Form -->
<form method="POST" action="deactivate_voting.php">
    <label for="election_id">Deactivate Voting for:</label>
    <select name="election_id" id="election_id" required>
        <?php foreach ($elections as $election): ?>
            <option value="<?php echo $election['election_id']; ?>">
            <?php echo $election['role'] . ' (' . ucfirst($election['election_date']) . ')'; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Deactivate Voting</button>
</form>

<script>
    const electionEndTime = new Date("<?php echo isset($row['end_time']) ? $row['end_time'] : ''; ?>");

    function checkAndDeactivateVoting() {
        const currentTime = new Date();

        if (currentTime >= electionEndTime) {
            $.ajax({
                url: 'deactivate_voting.php',  
                type: 'POST',
                data: { election_id: $('#election_id').val() },
                success: function(response) {
                    const result = JSON.parse(response);
                    alert(result.message);  
                },
                error: function() {
                    alert('Error deactivating voting.');
                }
            });
        }
    }

    setInterval(checkAndDeactivateVoting, 60000);
</script>

</body>
</html>
