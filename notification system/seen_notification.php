<?php
include 'config.php';

function markAsSeen($notification_id) {
    global $conn;
    $sql = "UPDATE notifications SET noti_seen = 'seen' WHERE notification_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $notification_id);
    $stmt->execute();
    $stmt->close();
}

// Example usage
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['notification_id'])) {
    markAsSeen($_POST['notification_id']);
}
?>
