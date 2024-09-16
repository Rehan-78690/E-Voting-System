<?php

include 'config.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: candidate.php");
    exit();
}

$voter_id = $_SESSION['candidate_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $candidate_name = $_POST['candidate_name'];
    $department = $_POST['department'];
    $role = $_POST['role'];

    // First, check if the candidate already exists in the votes table
    $check_sql = "SELECT total_votes FROM votes WHERE candidate_name = ? AND department = ? AND role = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("sss", $candidate_name, $department, $role);
    $check_stmt->execute();
    $check_stmt->store_result();

    header('Location: voter.php');

    if ($check_stmt->num_rows > 0) {
        // Candidate exists, update their vote count
        $check_stmt->bind_result($total_votes);
        $check_stmt->fetch();
        $new_votes = intval($total_votes) + 1;

        $update_sql = "UPDATE votes SET total_votes = ? WHERE candidate_name = ? AND department = ? AND role = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("isss", $new_votes, $candidate_name, $department, $role);

        if ($update_stmt->execute()) {
            echo "Vote recorded successfully!";
        } else {
            echo "Error updating vote count: " . $update_stmt->error;
        }
                // Update the voter's status to indicate they have voted
$update_sql = "UPDATE candidates SET has_voted = 1 WHERE candidate_id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("i", $voter_id);
$update_stmt->execute();
$update_stmt->close();


echo "Thank you for voting!";
header('Location: voter.php');

       // $update_stmt->close();
    } else {
        // Candidate doesn't exist, insert a new record with the initial vote count of 1
        $insert_sql = "INSERT INTO votes (candidate_name, department, role, total_votes) VALUES (?, ?, ?, 1)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sss", $candidate_name, $department, $role);

        $update_sql = "UPDATE candidates SET has_voted = 1 WHERE candidate_id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("i", $voter_id);
$update_stmt->execute();
$update_stmt->close();

        if ($insert_stmt->execute()) {
            echo "Vote recorded successfully!";
        } else {
            echo "Error recording vote: " . $insert_stmt->error;
        }

        $insert_stmt->close();
    }

    $check_stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
