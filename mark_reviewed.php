<?php
session_start();
include 'config.php'; // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['email'])) {
    header("Location: admin.php");
    exit();
}

if (isset($_GET['id'])) {
    $feedback_id = $_GET['id'];

    // Update feedback status to 'Reviewed'
    $sql = "UPDATE feedback SET status = 'Reviewed' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $feedback_id);

    if ($stmt->execute()) {
        header("Location: manage_feedback.php");
        exit();
    } else {
        echo "Error marking feedback as reviewed: " . $stmt->error;
    }
} else {
    header("Location: manage_feedback.php");
}
?>
