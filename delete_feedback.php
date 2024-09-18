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

    // Delete feedback from the database
    $sql = "DELETE FROM feedback WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $feedback_id);

    if ($stmt->execute()) {
        header("Location: manage_feedback.php");
        exit();
    } else {
        echo "Error deleting feedback: " . $stmt->error;
    }
} else {
    header("Location: manage_feedback.php");
}
?>
