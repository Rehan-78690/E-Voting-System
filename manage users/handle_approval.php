<?php
// Include database configuration and mail functions
include '../config.php';
include 'mail_functions.php';

session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../admin.php");
    exit();
}


// Get candidate details
$candidate_id = $_POST['candidate_id'];
$candidate_email = $_POST['candidate_email'];
$candidate_name = $_POST['candidate_name'];

// Approve or reject based on button clicked
if (isset($_POST['approve'])) {
    // Update status to 'Approved'
    $sql = "UPDATE candidates SET status = 'Approved' WHERE candidate_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $candidate_id);
    $stmt->execute();

    // Send approval email
    sendApprovalEmail($candidate_email, $candidate_name);

    $message = "Candidate approved and notified via email.";
} elseif (isset($_POST['reject'])) {
    // Update status to 'Rejected'
    $sql = "DELETE FROM candidates WHERE candidate_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $candidate_id);
    $stmt->execute();
    // Send rejection email
    sendRejectionEmail($candidate_email, $candidate_name);

    $message = "Candidate rejected and notified via email.";
}

$stmt->close();
$conn->close();

echo "<script>
    alert('$message');
    window.location.href = 'approval_requests.php';
</script>";
exit();
?>
