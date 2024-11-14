<?php
include 'config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['candidate_email'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$voter_id = $_SESSION['candidate_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $candidate_id = intval($data['candidate_id']);
    $election_id = intval($data['election_id']);

    if ($candidate_id <= 0 || $election_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
        exit();
    }

    // Check if voter has already voted
    $check_voted_sql = "SELECT has_voted FROM candidates WHERE candidate_id = ?";
    $stmt = $conn->prepare($check_voted_sql);
    $stmt->bind_param("i", $voter_id);
    $stmt->execute();
    $stmt->bind_result($has_voted);
    $stmt->fetch();
    $stmt->close();

    if ($has_voted) {
        echo json_encode(['success' => false, 'message' => 'You have already voted.']);
        exit();
    }

    // Update or insert vote record
    $check_sql = "SELECT total_votes FROM votes WHERE candidate_id = ? AND election_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $candidate_id, $election_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $check_stmt->bind_result($total_votes);
        $check_stmt->fetch();
        $new_votes = $total_votes + 1;

        $update_sql = "UPDATE votes SET total_votes = ? WHERE candidate_id = ? AND election_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iii", $new_votes, $candidate_id, $election_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        $insert_sql = "INSERT INTO votes (candidate_id, election_id, total_votes) VALUES (?, ?, 1)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $candidate_id, $election_id);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    // Update voter's status to indicate voting
    $update_voter_sql = "UPDATE candidates SET has_voted = 1 WHERE candidate_id = ?";
    $update_voter_stmt = $conn->prepare($update_voter_sql);
    $update_voter_stmt->bind_param("i", $voter_id);
    $update_voter_stmt->execute();
    $update_voter_stmt->close();
        // Insert voting history in the voters table
        $history_sql = "INSERT INTO voters (voter_id, candidate_id, election_id, date, voted_for) VALUES (?, ?, ?, NOW(), ?)";
        $history_stmt = $conn->prepare($history_sql);
    
        // Retrieve the candidate name for history
        $candidate_name = get_candidate_name($conn, $candidate_id);
        $history_stmt->bind_param("iiis", $voter_id, $candidate_id, $election_id, $candidate_name);
        $history_stmt->execute();
        $history_stmt->close();

    echo json_encode(['success' => true, 'message' => 'Vote recorded successfully.']);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
function get_candidate_name($conn, $candidate_id) {
    $query = "SELECT candidate_name FROM candidates WHERE candidate_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $candidate_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['candidate_name'] ?? 'Unknown';
}
?>
