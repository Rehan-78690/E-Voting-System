<?php
session_start();
include 'config.php'; // Database connection

// Check if the candidate is logged in
if (!isset($_SESSION['candidate_email'])) {
    header("Location: candidate.php");
    exit();
}

$alert_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback'])) {
    $candidate_id = $_SESSION['candidate_id']; // Assuming candidate_id is stored in session
    $feedback_text = $_POST['feedback_text'];
    $submission_date = date('Y-m-d H:i:s'); // Get the current date and time

    // Insert feedback into the database
    $sql = "INSERT INTO feedback (candidate_id, feedback_text, date, status) VALUES (?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iss', $candidate_id, $feedback_text, $submission_date);

    if ($stmt->execute()) {
        $alert_message = "Your feedback has been submitted successfully!";
        header("location:candidate_feedback.php");
    } else {
        $alert_message = "Error submitting feedback: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Submit Feedback</h2>

    <?php if (!empty($alert_message)): ?>
        <div class="alert alert-info">
            <?php echo $alert_message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="feedback_text" class="form-label">Your Feedback</label>
            <textarea class="form-control" id="feedback_text" name="feedback_text" rows="5" placeholder="Enter your feedback here" required></textarea>
        </div>
        <button type="submit" name="submit_feedback" class="btn btn-primary">Submit Feedback</button>
    </form>
</div>

</body>
</html>
