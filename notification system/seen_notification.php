<?php
session_start();
include 'config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if candidate ID is set in the session
if (!isset($_SESSION['candidate_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Candidate ID not set in session.']);
    exit();
}

// Check if notification ID is provided in the POST request
if (!isset($_POST['notification_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing notification ID']);
    exit();
}

$candidate_id = $_SESSION['candidate_id'];
$notification_id = $_POST['notification_id'];
file_put_contents('debug_log.txt', "Candidate ID: $candidate_id, Notification ID: $notification_id\n", FILE_APPEND);

// Verify that the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Prepare the SQL statement
    $sql = "UPDATE notifications SET noti_seen = 'seen' WHERE candidate_id = ? AND notification_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind the parameters and execute the statement
        $stmt->bind_param('ii', $candidate_id, $notification_id);

        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Notification marked as seen']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No rows were updated. Possible issue with candidate ID or notification ID.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to execute statement: ' . $stmt->error]);
        }

        $stmt->close(); // Close the statement
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close(); // Close the connection
?>
