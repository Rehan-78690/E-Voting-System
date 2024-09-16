<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $election_id = $_POST['election_id'];

    $sql = "UPDATE elections SET status = 'completed' WHERE election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Voting deactivated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to deactivate voting']);
    }

    $stmt->close();
}
?>
