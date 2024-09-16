<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $candidate_name = $_POST['candidate_name'];
    $department = $_POST['department'];
    $role = $_POST['role'];
    $voter_id = $_SESSION['candidate_id']; // Assuming this is the voter ID

    // Check if the voter has already voted
    $check_voter_sql = "SELECT has_voted FROM candidates WHERE candidate_id = ?";
    $check_voter_stmt = $conn->prepare($check_voter_sql);
    $check_voter_stmt->bind_param("i", $voter_id);
    $check_voter_stmt->execute();
    $check_voter_stmt->bind_result($has_voted);
    $check_voter_stmt->fetch();
    $check_voter_stmt->close();
}
    if ($has_voted) {
        echo json_encode(['success' => false, 'message' => 'You have already voted!']);
        exit();
    }

    // Proceed with voting
    $check_sql = "SELECT total_votes FROM votes WHERE candidate_name = ? AND department = ? AND role = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("sss", $candidate_name, $department, $role);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Candidate exists, update their vote count
        $check_stmt->bind_result($total_votes);
        $check_stmt->fetch();
        $new_votes = intval($total_votes) + 1;

        $update_sql = "UPDATE votes SET total_votes = ? WHERE candidate_name = ? AND department = ? AND role = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("isss", $new_votes, $candidate_name, $department, $role);
    }
        if ($update_stmt->execute()) {
            // Mark voter as having voted
            $update_voter_sql = "UPDATE candidates SET has_voted = 1 WHERE candidate_id = ?";
            $update_voter_stmt = $conn->prepare($update_voter_sql);
            $update_voter_stmt->bind_param("i", $voter_id);
            $update_voter_stmt->execute();

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating vote count.']);
        }

        $update_stmt->close();
   
?>