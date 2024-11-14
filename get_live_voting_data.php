<?php
include 'config.php';
$quer = "SELECT election_id FROM elections WHERE status = 'active'";
$stmt = $conn->prepare($quer);
$stmt->execute();
$result = $stmt->get_result();
$election_id = 0;

if ($result->num_rows > 0) {
    $election = $result->fetch_assoc();
    $election_id = $election['election_id'];
}

if ($election_id == 0) {
    echo json_encode(["error" => "No active election found"]);
    exit();
}
$sql = "
    SELECT candidates.candidate_name, votes.total_votes
    FROM votes
    JOIN candidates ON votes.candidate_id = candidates.candidate_id
    WHERE votes.election_id = ?
    ORDER BY votes.total_votes DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result = $stmt->get_result();

$liveVotingData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $liveVotingData[] = $row;
    }
}
$sql_total_voters = "
    SELECT COUNT(*) AS total_voters 
    FROM candidates 
    WHERE status = 'approved' 
      AND election_id = ?
";
$stmt_total_voters = $conn->prepare($sql_total_voters);
$stmt_total_voters->bind_param("i", $election_id);
$stmt_total_voters->execute();
$result_total_voters = $stmt_total_voters->get_result();
$total_voters = $result_total_voters->fetch_assoc()['total_voters'];

// Get number of voters who have already voted in the specific election
$sql_voted_voters = "
    SELECT COUNT(*) AS voted_voters 
    FROM candidates 
    WHERE status = 'approved' 
      AND has_voted = 1 
      AND election_id = ?
";
$stmt_voted_voters = $conn->prepare($sql_voted_voters);
$stmt_voted_voters->bind_param("i", $election_id);
$stmt_voted_voters->execute();
$result_voted_voters = $stmt_voted_voters->get_result();
$voted_voters = $result_voted_voters->fetch_assoc()['voted_voters'];

// Calculate remaining voters
$remaining_voters = $total_voters - $voted_voters;
$response = [
    'liveVotingData' => $liveVotingData,
    'voterTurnout' => [
        'totalVoters' => $total_voters,
        'votedVoters' => $voted_voters,
        'remainingVoters' => $remaining_voters
    ]
];
echo json_encode($response);

$stmt->close();
$conn->close();

?>
