<?php
include '../config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../admin.php");
    exit();
}

// Check if the form was submitted with a candidate ID to delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['candidate_id'])) {
    // Get the candidate ID from the POST data
    $candidate_id = intval($_POST['candidate_id']);

    // Prepare the SQL statement to delete the candidate
    $sql = "DELETE FROM candidates WHERE candidate_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        echo "Error preparing the statement: " . $conn->error;
        exit();
    }

    // Bind the candidate ID parameter to the SQL statement
    $stmt->bind_param("i", $candidate_id);

    // Execute the SQL statement
    if ($stmt->execute()) {
        echo "Candidate deleted successfully.";
        // Optionally redirect back to the manage candidates page
        header("Location: manage_candidates.php");
        exit();
    } else {
        echo "Error deleting candidate: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
