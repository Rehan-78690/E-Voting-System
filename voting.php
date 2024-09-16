<?php
session_start();
require 'config.php';

// Simulating a logged-in user
$voter_id = 1; // This should be retrieved from the session or login

// Fetch voter information
$sql = "SELECT name, has_voted, voted_for FROM voters WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $voter_id);
$stmt->execute();
$stmt->bind_result($voter_name, $has_voted, $voted_for);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$has_voted) {
    $candidate_id = $_POST['candidate_id'];
    
    // Update voter's record
    $sql = "UPDATE voters SET has_voted = 1, voted_for = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $candidate_id, $voter_id);
    
    if ($stmt->execute()) {
        // Debugging: Check if the update was successful
        echo "Vote recorded successfully.<br>";
    } else {
        // Debugging: Print error message
        echo "Error updating record: " . $stmt->error . "<br>";
    }
    $stmt->close();

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Voting System</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($voter_name); ?></h2>

    <?php if ($has_voted): ?>
        <h3>Profile</h3>
        <p>Name: <?php echo htmlspecialchars($voter_name); ?></p>
        <?php
        $sql = "SELECT name FROM candidates WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $voted_for);
        $stmt->execute();
        $stmt->bind_result($candidate_name);
        $stmt->fetch();
        $stmt->close();
        ?>
        <p>You voted for: <?php echo htmlspecialchars($candidate_name); ?></p>
    <?php else: ?>
        <h3>Ballot Paper</h3>
        <form method="POST" action="">
            <?php
            $sql = "SELECT id, name FROM candidates";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()): ?>
                <input type="radio" name="candidate_id" value="<?php echo $row['id']; ?>" required> <?php echo htmlspecialchars($row['name']); ?><br>
            <?php endwhile; ?>
            <input type="submit" value="Vote">
        </form>
    <?php endif; ?>
</div>
</body>
</html>

<?php
$conn->close();
?>
