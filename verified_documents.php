<?php
session_start();
include 'config.php'; 

// Check if the admin is logged in
if (!isset($_SESSION['email'])) {
    header("Location: admin.php");
    exit();
}

// Fetch all candidates with verified documents
$sql = "SELECT c.candidate_name, cd.document_path, cd.submission_date
        FROM candidate_documents cd
        JOIN candidates c ON cd.candidate_id = c.candidate_id
        WHERE cd.verification_status = 'verified'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verified Candidates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <h1 class="mt-4">Verified Candidates</h1>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Candidate Name</th>
                    <th>View documents</th>
                    <th>Submission Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                        <td><a href="<?php echo htmlspecialchars(str_replace("../", "", $row['document_path'])); ?>" target="_blank">View Document</a></td>
                        <td><?php echo htmlspecialchars($row['submission_date']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No candidates with verified documents found.</p>
    <?php endif; ?>
</div>

</body>
</html>
