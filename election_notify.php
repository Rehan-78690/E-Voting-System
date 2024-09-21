<?php
include 'config.php'; // Include your database configuration file

// Query to fetch only the election_id from the elections table
$query = "SELECT election_id, end_time FROM elections";

$result = mysqli_query($conn, $query); 

// Check if the query was successful
if ($result && mysqli_num_rows($result) > 0) {
    // Fetch the election_id
    $row = mysqli_fetch_assoc($result);
    $election_id = $row['election_id'];
    $end_time = $row['end_time'];
} else {
    // Handle the case where no election ID is found or an error occurred
    echo "No election found or error in fetching data.";
    $election_id = null; // Set the election_id to null if no record is found
}

// Close the database connection
mysqli_close($conn);
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
<h1>Election Announcement</h1>
<form method="POST" action="">
    <label for="election_day">Election Day:</label>
    <input type="text" id="election_day" name="election_day" required>

    <label for="election_date">Election Date:</label>
    <input type="date" id="election_date" name="election_date" required>

    <label for="start_time">Start Time:</label>
    <input type="time" id="start_time" name="start_time" required>

    <label for="end_time">End Time:</label>
    <input type="time" id="end_time" name="end_time" required>

    <label for="description">Description:</label>
    <textarea id="description" name="notification"></textarea>

    <button type="submit">Announce Date</button>
</form>

<!-- Activation Form -->
<form method="POST" action="activate_voting.php">
    <input type="hidden" name="election_id" value="<?php echo $row ['election_id']?>"> <!-- Pass the correct election ID here -->
    <button type="submit">Activate Voting</button>
</form>

<!-- Deactivation Form -->
<form method="POST" action="deactivate_voting.php">
    <input type="hidden" name="election_id" value="<?php echo $row ['election_id']?>"> <!-- Pass the correct election ID here -->
    <button type="submit">Deactivate Voting</button>
</form>

<script>
    // Assuming `election_end_time` is passed from the backend as a variable
    const electionEndTime = new Date("<?php echo $row['end_time']; ?>");  // Example end time, replace with server-provided time

    // Function to check if current time is past election end time
    function checkAndDeactivateVoting() {
        const currentTime = new Date();

        if (currentTime >= electionEndTime) {
            // Deactivate voting via AJAX when the end time has passed
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

    
    setInterval(checkAndDeactivateVoting, 60000);  // Every minute
</script>

</body>
</html>
