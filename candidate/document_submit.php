<?php
session_start();
include 'config.php'; 
if (!isset($_SESSION['candidate_email'])) {
    header("Location: candidate.php");
    exit();
}

$alert_message = ""; 
$candidate_id = $_SESSION['candidate_id'];

// Check if the candidate has already submitted documents
$sql = "SELECT id, document_path, withdrawn, status, verification_status FROM candidate_documents WHERE candidate_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Candidate has already submitted documents
    $row = $result->fetch_assoc();
    $document_submitted = true;
    $document_path = $row['document_path'];
    $withdrawn = $row['withdrawn'];
    $status = $row['status'];
    $verification_status = $row['verification_status'];
} else {
    // Candidate has not submitted documents
    $document_submitted = false;
    $document_path = '';
    $withdrawn = 0;
    $status = '';
    $verification_status = '';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['withdraw'])) {    if ($verification_status == 'verified') {
        // If document is verified, do not allow withdrawal
        $alert_message = "Your document has already been verified and cannot be withdrawn.";
    } else {

        if (file_exists($document_path)) {
            unlink($document_path); // Delete the file from the server
        }

        $sql = "DELETE FROM candidate_documents WHERE candidate_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $candidate_id);
        if ($stmt->execute()) {
            $alert_message = "Your documents have been withdrawn and deleted.";
            $document_submitted = false; // Reset the submission status
        } else {
            $alert_message = "Error withdrawing your documents: " . $stmt->error;
        }
    }
    } elseif (isset($_POST['resubmit']) && $document_submitted) {
        // **Prevent resubmission if the document is verified**
        if ($verification_status == 'verified') {
            // If document is verified, do not allow resubmission
            $alert_message = "Your document has already been verified and cannot be resubmitted.";
        } else {
            // Proceed with document resubmission
            if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
                $target_dir = "../uploads/documents/";
                $target_file = $target_dir . basename($_FILES["document"]["name"]);
                $uploadOk = 1;
                $documentFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Check file size
                if ($_FILES["document"]["size"] > 900000) {
                    $alert_message = "Sorry, your file is too large.";
                    $uploadOk = 0;
                }

                // Allow only certain file formats
                if ($documentFileType != "pdf" && $documentFileType != "doc" && $documentFileType != "docx") {
                    $alert_message = "Sorry, only PDF, DOC & DOCX files are allowed.";
                    $uploadOk = 0;
                }

                if ($uploadOk == 1) {
                    // If a previous document exists, delete it
                    if (file_exists($document_path)) {
                        unlink($document_path); // Delete the old file
                    }

                    // Move the new file to the server
                    if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
                        // Update the document information in the database
                        $submission_date = date("Y-m-d H:i:s"); // Current date and time
                        $sql = "UPDATE candidate_documents SET document_path=?, submission_date=?, status='Pending' WHERE id=?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssi", $target_file, $submission_date, $row['id']);
                        if ($stmt->execute()) {
                            $alert_message = "Your document has been resubmitted successfully.";
                            $document_path = $target_file;
                        } else {
                            $alert_message = "Error updating the database: " . $stmt->error;
                        }
                    } else {
                        $alert_message = "Sorry, there was an error uploading your file.";
                    }
                }
            } else {
                $alert_message = "No file selected for resubmission.";
            }
        }
    } elseif (isset($_POST['submit']) && !$document_submitted) {
        // Handle first-time submission or submission after withdrawal
        if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
            $target_dir = "../uploads/documents/";
            $target_file = $target_dir . basename($_FILES["document"]["name"]);
            $uploadOk = 1;
            $documentFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check file size
            if ($_FILES["document"]["size"] > 900000) {
                $alert_message = "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            // Allow only certain file formats
            if ($documentFileType != "pdf" && $documentFileType != "doc" && $documentFileType != "docx") {
                $alert_message = "Sorry, only PDF, DOC & DOCX files are allowed.";
                $uploadOk = 0;
            }

            if ($uploadOk == 1) {
                // Move the file to the server
                if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
                    // Insert the document information into the database
                    $submission_date = date("Y-m-d H:i:s"); // Current date and time
                    $sql = "INSERT INTO candidate_documents (candidate_id, document_path, submission_date, status) 
                            VALUES (?, ?, ?, 'Pending')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iss", $candidate_id, $target_file, $submission_date);
                    if ($stmt->execute()) {
                        $alert_message = "Your document has been submitted successfully.";
                        $document_submitted = true; 
                        $document_path = $target_file; // Mark as submitted
                    } else {
                        $alert_message = "Error updating the database: " . $stmt->error;
                    }
                } else {
                    $alert_message = "Sorry, there was an error uploading your file.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Submission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    <?php if (!empty($alert_message)): ?>
    <div class="alert alert-info" role="alert">
        <?php echo $alert_message; ?>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="content">
        <div class="container">
            <h1>Document Submission</h1>

            <?php if ($document_submitted && !$withdrawn): ?>
                <p>Your document has been submitted.</p>
                <p>Status: <?php echo htmlspecialchars($status); ?></p>
                <p><a href="<?php echo htmlspecialchars($document_path); ?>" target="_blank">View Document</a></p>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="document" class="form-label">Resubmit Document (PDF, DOC, DOCX only)</label>
                        <input type="file" class="form-control" id="document" name="document">
                    </div>
                    <button type="submit" name="resubmit" class="btn btn-primary">Resubmit</button>
                </form>
                <form method="POST" action="">
                    <button type="submit" name="withdraw" class="btn btn-danger mt-3">Withdraw Document</button>
                </form>
            <?php elseif ($withdrawn): ?>
                <p>You have withdrawn your document submission.</p>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="document" class="form-label">Submit Document (PDF, DOC, DOCX only)</label>
                        <input type="file" class="form-control" id="document" name="document" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                </form>
            <?php else: ?>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="document" class="form-label">Submit Document (PDF, DOC, DOCX only)</label>
                        <input type="file" class="form-control" id="document" name="document" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                </form>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>
