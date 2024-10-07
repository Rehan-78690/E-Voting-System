<?php
include 'config.php';
session_start();

if (!isset($_SESSION['candidate_email'])) {
    header("Location: candidate.php");
    exit();
}

$voter_id = $_SESSION['candidate_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve candidate ID from the form data
    $candidate_id = $_POST['candidate_id'];
    $candidate_name=$_POST['candidate_name'];
    $candidate_role=$_POST['role'];
    $candidate_department=$_POST['department'];

    // First, check if the candidate already has votes in the votes table
    $check_sql = "SELECT total_votes FROM votes WHERE candidate_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $candidate_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Candidate exists, update their vote count
        $check_stmt->bind_result($total_votes);
        $check_stmt->fetch();
        $new_votes = intval($total_votes) + 1;

        $update_sql = "UPDATE votes SET total_votes = ? WHERE candidate_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $new_votes, $candidate_id);

        if ($update_stmt->execute()) {
            echo "Vote recorded successfully!";
        } else {
            echo "Error updating vote count: " . $update_stmt->error;
        }
        
        $update_stmt->close();
    } else {
        // Candidate doesn't exist in votes table, insert a new record with the initial vote count of 1
        $insert_sql = "INSERT INTO votes (candidate_id,candidate_name,role,department, total_votes) VALUES (?,?,?,?, 1)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("isss", $candidate_id,$candidate_name,$candidate_role,$candidate_department);

        if ($insert_stmt->execute()) {
            echo "Vote recorded successfully!";
        } else {
            echo "Error recording vote: " . $insert_stmt->error;
        }

        $insert_stmt->close();
    }

    // Update the voter's status to indicate they have voted
    $update_voter_sql = "UPDATE candidates SET has_voted = 1 WHERE candidate_id = ?";
    $update_voter_stmt = $conn->prepare($update_voter_sql);
    $update_voter_stmt->bind_param("i", $voter_id);
    $update_voter_stmt->execute();
    $update_voter_stmt->close();

    // Redirect to voter dashboard or another page
    header('Location: voter_dashboard.php');
    exit();

    $check_stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
