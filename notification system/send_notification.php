<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the message and URL
    $message = htmlspecialchars($_POST['message']);
    $url = !empty($_POST['url']) ? htmlspecialchars($_POST['url']) : null;

    // Fetch all candidates
    $sql = "SELECT candidate_id FROM candidates";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $candidate_id = $row['candidate_id'];

            // Call the function to send notification for each candidate
            postNotification($message, $candidate_id, 'candidate', $url);
        }
        echo "Notifications sent successfully!";
    } else {
        echo "No candidates found!";
    }
}

// Function to send notification to each candidate
function postNotification($message, $user_id, $user_role, $url = null) {
    global $conn;

    $status = 'active';
    $date = date('Y-m-d H:i:s');
    $seen = 'unseen';
    $type = 'manual';

    $sql = "INSERT INTO notifications (noti_message, noti_status, noti_seen, noti_date, noti_type, noti_url, candidate_id, user_role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param('ssssssis', $message, $status, $seen, $date, $type, $url, $user_id, $user_role);

    if (!$stmt->execute()) {
        error_log("Failed to insert notification for user $user_id: " . $stmt->error);
    } else {
        error_log("Notification successfully sent to user $user_id");
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h1>Send Notification to All Candidates</h1>

    <form method="POST" action="post_notification.php">
        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="url" class="form-label">Optional URL</label>
            <input type="text" class="form-control" id="url" name="url" placeholder="Enter URL (optional)">
        </div>

        <button type="submit" class="btn btn-primary">Send Notification</button>
    </form>
</div>

</body>
</html>
