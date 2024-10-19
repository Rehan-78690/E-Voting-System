<?php
include "config.php";

// Check if election_id is provided
$election_id = isset($_POST['election_id']) ? intval($_POST['election_id']) : 0;

if ($election_id > 0) {
    // Reset 'has_voted' to 0 for all candidates who participated in the completed election
    $reset_query = "UPDATE candidates c
                    JOIN votes v ON c.candidate_id = v.candidate_id
                    SET c.has_voted = 0
                    WHERE v.election_id = ?";
    $stmt = $conn->prepare($reset_query);
    $stmt->bind_param("i", $election_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Voting status reset successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to reset voting status."]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid election ID."]);
}
?>
