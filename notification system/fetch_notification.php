<?php
include 'config.php';
session_start();

if (!isset($_SESSION['candidate_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Candidate ID not set in session']);
    exit();
}

$candidate_id = $_SESSION['candidate_id'];

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

function fetchNotifications($candidate_id) {
    global $conn;

    $sql = "SELECT * FROM notifications WHERE candidate_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param('i', $candidate_id);

    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to execute statement: ' . $stmt->error]);
        exit();
    }

    $result = $stmt->get_result();
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch result: ' . $conn->error]);
        exit();
    }

    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }

    $stmt->close();

    if (empty($notifications)) {
        echo json_encode(['status' => 'success', 'data' => [], 'message' => 'No notifications found']);
    } else {
        echo json_encode(['status' => 'success', 'data' => $notifications]);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    fetchNotifications($candidate_id);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
