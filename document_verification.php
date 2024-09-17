<?php
session_start();
include 'config.php'; 


if (!isset($_SESSION['email'])) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from the form
    $document_id = intval($_POST['document_id']);
    $verification_status = $_POST['verification_status'];
    $admin_id = $_SESSION['admin_id']; 

    
    $sql = "UPDATE candidate_documents SET verification_status = ?, verified_by = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $verification_status, $admin_id, $document_id);

    if ($stmt->execute()) {
        echo "Document verification status updated successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all pending documents
$sql = "SELECT cd.id, c.candidate_name, cd.document_path, cd.submission_date, cd.verification_status 
        FROM candidate_documents cd
        JOIN candidates c ON cd.candidate_id = c.candidate_id
        WHERE cd.verification_status = 'pending'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <h1 class="mt-4">Admin Document Verification</h1>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Candidate Name</th>
                   
                    <th>Document Path</th>
                    <th>Submission Date</th>
                    <th>Verification Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                 
                        <td><a href="<?php echo htmlspecialchars(str_replace("../", "", $row['document_path'])); ?>" target="_blank">View Document</a></td>
                        <td><?php echo htmlspecialchars($row['submission_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['verification_status']); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="document_id" value="<?php echo $row['id']; ?>">
                                <select name="verification_status" class="form-select" required>
                                    <option value="verified">Verify</option>
                                    <option value="rejected">Reject</option>
                                </select>
                                <button type="submit" class="btn btn-primary mt-2">Update Status</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No documents pending verification.</p>
    <?php endif; ?>
</div>
<script>
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!confirm('Are you sure you want to update the verification status?')) {
                event.preventDefault();
            }
        });
    });
</script>
<?php 

?>
<a href="verified_documents.php"> view verified documennts</a>

</body>
</html>
