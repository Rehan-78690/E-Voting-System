<?php
include 'config.php';

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected to the database.<br>";
}

function fetchNotifications() {
    global $conn;
    $sql = "SELECT * FROM notifications"; 
    $result = $conn->query($sql);
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    if ($result->num_rows > 0) {
        echo "Number of notifications fetched: " . $result->num_rows . "<br>";
    } else {
        echo "No notifications found.<br>";
    }
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    // Output the array for debugging purposes
    echo '<pre>';
    print_r($notifications);
    echo '</pre>';

    echo json_encode($notifications); 
}

// Example usage:
if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {

    ob_start();  // Start output buffering
    fetchNotifications(); 

    ob_end_clean();  // End output buffering and send content to the client
    ob_flush();  // Flush output buffer
} else {
    echo "Invalid request method. Please use POST to fetch notifications.<br>";
}
?>
