<?php
include 'config.php';

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Notifications
function fetchNotifications() {
    global $conn;
    $sql = "SELECT * FROM notifications";
    $result = $conn->query($sql);

    if (!$result) {
        die(json_encode(['status' => 'error', 'message' => 'Query failed: ' . $conn->error]));
    }

    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'data' => $notifications]);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    fetchNotifications();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
