<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the message and URL
    $message = htmlspecialchars($_POST['message']);
    $url = !empty($_POST['url']) ? htmlspecialchars($_POST['url']) : null;

    // Fetch all candidates
    $sql = "SELECT candidate_id FROM candidates WHERE status = 'approved'";
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
    $stmt->bind_param('ssssssis', $message, $status, $seen, $date, $type, $url, $user_id, $user_role);

    $stmt->execute();
}
?>
