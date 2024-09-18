<?php
include 'config.php';


$sql = "SELECT candidate_name, total_votes FROM votes ORDER BY total_votes DESC";
$result = $conn->query($sql);

$liveVotingData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $liveVotingData[] = $row;
    }
}


echo json_encode($liveVotingData);

$conn->close();
?>
