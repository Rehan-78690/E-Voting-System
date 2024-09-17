<?php
session_start();
include 'config.php'; 

// Check if the admin is logged in
if (!isset($_SESSION['email'])) {
    header("Location: admin.php");
    exit();
}

try {
    // Query to fetch verified candidates and their documents
    $sql = "SELECT c.candidate_name, cd.document_path, cd.submission_date
            FROM candidate_documents cd
            JOIN candidates c ON cd.candidate_id = c.candidate_id
            WHERE cd.verification_status = 'verified'";
    $result = $conn->query($sql);

    if (!$result) {
        // Throw an exception if the query fails
        throw new Exception("Error fetching verified candidates: " . $conn->error);
    }
} catch (Exception $e) {
    // If an error occurs, show an error message
    echo '<div class="alert alert-danger" role="alert">' . $e->getMessage() . '</div>';
    exit(); // Stop further execution
}
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
                    <th>View Document</th>
                    <th>Submission Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                        <td>
                            <?php
                            try {
                                // Check if the document path is set and valid
                                if (!isset($row['document_path']) || empty($row['document_path'])) {
                                    throw new Exception("Document path is missing for candidate " . $row['candidate_name']);
                                }

                                $file_path = str_replace("../", "", $row['document_path']);
                               
                                // Optionally check if the file exists
                                if (!file_exists($file_path)) {
                                    throw new Exception("Document not found at the specified path for candidate " . $row['candidate_name']);
                                }

                                // Display the document link if everything is okay
                                echo '<a href="' . htmlspecialchars($file_path) . '" target="_blank">View Document</a>';
                            } catch (Exception $e) {
                                // Show the error if any issue occurs
                                echo '<span style="color: red;">Error: ' . $e->getMessage() . '</span>';
                            }
                            ?>
                        </td>
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
