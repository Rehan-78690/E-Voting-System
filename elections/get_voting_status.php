<?php
include 'config.php';

// Fetch the current election status
$sql = "SELECT status FROM elections ORDER BY election_id DESC LIMIT 1"; // Adjust if necessary
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['status' => $row['status']]);
} else {
    echo json_encode(['status' => 'No election found']);
}

$conn->close();
?>
