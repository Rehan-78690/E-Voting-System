<?php
session_start();
include 'config.php'; // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['email'])) {
    header("Location: admin.php");
    exit();
}

// Fetch all feedback from the database
$sql = "SELECT f.id, c.candidate_name, f.feedback_text, f.date, f.status 
        FROM feedback f 
        JOIN candidates c ON f.candidate_id = c.candidate_id
        ORDER BY f.date DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Candidate Feedback</h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Candidate Name</th>
                    <th>Feedback</th>
                    <th>Submission Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['feedback_text']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <a href="mark_reviewed.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Mark as Reviewed</a>
                            <a href="delete_feedback.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No feedback found.</p>
    <?php endif; ?>
</div>

</body>
</html>
