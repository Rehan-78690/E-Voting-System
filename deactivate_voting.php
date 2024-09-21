<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['election_id'])) {
    $election_id = $_POST['election_id'];

    // Update the election status to 'inactive'
    $update_query = "UPDATE elections SET status = 'inactive' WHERE election_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $election_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Voting has been deactivated.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to deactivate voting.']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
