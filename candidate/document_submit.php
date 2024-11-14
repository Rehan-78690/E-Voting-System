<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['candidate_email'])) {
    header("Location: candidate.php");
    exit();
}

$candidate_id = $_SESSION['candidate_id'];
$election_check = "SELECT election_id, election_name, status, last_date_documents FROM elections WHERE status = 'upcoming' LIMIT 1";
$result = $conn->query($election_check);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $election_id = $row['election_id'];
    $election_name = $row['election_name'];
    $last_date_documents = $row['last_date_documents'];
    $current_date = date("Y-m-d");
    if ($row['status'] !== 'upcoming' || $current_date > $last_date_documents) {
        if ($row['status'] == 'active') {
            echo "Document submission is not allowed for active elections.";
        } else {
            // If the last date for document submission has passed
            echo "The document submission period has ended for this upcoming election.";
        }
        $delay = 2;
        header("refresh:$delay;url=candidate_dashboard.php");
        exit();
    }

    // Election is upcoming and within the document submission period
} else {
    echo "No upcoming election found for document submission.";
    $delay = 2;
    header("refresh:$delay;url=candidate_dashboard.php");
    exit();
}

//  already submitted documents for election
$sql = "SELECT id, document_path, withdrawn, status, verification_status FROM candidate_documents WHERE candidate_id = ? AND election_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $candidate_id, $election_id);
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
    $document_submitted = false;
    $document_path = '';
    $withdrawn = 0;
    $status = '';
    $verification_status = '';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['withdraw'])) {    
        // if ($verification_status == 'verified') {
        //     $alert_message = "Your document has already been verified and cannot be withdrawn.";}
         
            if (file_exists($document_path)) {
                unlink($document_path); // Delete the file from the server
            }

            $sql = "DELETE FROM candidate_documents WHERE candidate_id = ? AND election_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $candidate_id, $election_id);
            if ($stmt->execute()) {
                $alert_message = "Your documents have been withdrawn and deleted.";
                $document_submitted = false; // Reset the submission status
            } else {
                $alert_message = "Error withdrawing your documents: " . $stmt->error;
            }
        
    } elseif (isset($_POST['resubmit']) && $document_submitted) {
        if ($verification_status == 'verified') {
            $alert_message = "Your document has already been verified and cannot be resubmitted.";
        } else {
            handleDocumentUpload($candidate_id, $election_id, $row['id']);
        }
    } elseif (isset($_POST['submit']) && !$document_submitted) {
        // Handle first-time submission or submission after withdrawal
        handleDocumentUpload($candidate_id, $election_id);
    }
}

// document upload
function handleDocumentUpload($candidate_id, $election_id, $document_id = null) {
    global $conn, $alert_message, $document_path, $document_submitted;

    if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
        $target_dir = "../uploads/documents/";
        $target_file = $target_dir . basename($_FILES["document"]["name"]);
        $uploadOk = 1;
        $documentFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($_FILES["document"]["size"] > 800000) {
            $alert_message = "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        if ($documentFileType != "pdf" && $documentFileType != "doc" && $documentFileType != "docx") {
            $alert_message = "Sorry, only PDF, DOC & DOCX files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
                $submission_date = date("Y-m-d");
                if (file_exists($document_path)) {
                    unlink($document_path); // Delete the file from the server
                }
                if ($document_id) {
                    // Update 
                    $sql = "UPDATE candidate_documents SET document_path=?, submission_date=?, verification_status='Pending' WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssi", $target_file, $submission_date, $document_id);
                } else {
                    // Insert new document record
                    $sql = "INSERT INTO candidate_documents (candidate_id, election_id, document_path, submission_date, verification_status) 
                            VALUES (?, ?, ?, ?, 'Pending')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iiss", $candidate_id, $election_id, $target_file, $submission_date);
                }

                if ($stmt->execute()) {
                    $alert_message = "Your document has been submitted successfully.";
                    $document_path = $target_file;
                    $document_submitted = true;
                } else {
                    $alert_message = "Error updating the database: " . $stmt->error;
                }
            } else {
                $alert_message = "Sorry, there was an error uploading your file.";
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

    <div class="content">
        <div class="container">
            <h1>Document Submission</h1>

            <?php if ($document_submitted && !$withdrawn): ?>
                <p>Your document has been submitted.</p>
                <p>Status: <?php echo htmlspecialchars($verification_status); ?></p>
                <p><a href="<?php echo htmlspecialchars($document_path); ?>" target="_blank">View Document</a></p>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="document" class="form-label">Resubmit Document (PDF, DOC, DOCX only)</label>
                        <input type="file" class="form-control" id="document" name="document">
                    </div>
                    <button type="submit" name="resubmit" class="btn btn-primary"  onclick="return confirm('Are you sure you want to replace your existing documents with new ones? This action cannot be undone.');">Resubmit</button>
                </form>
                <form method="POST" action="">
                    <button type="submit" name="withdraw" class="btn btn-danger mt-3" onclick="return confirm('Are you sure you want to withdraw your documents? This action cannot be undone.You will not be able to contest in elections.');">Withdraw Document</button>
                </form>
            <?php elseif ($withdrawn): ?>
                <p>You have withdrawn your document submission.</p>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="document" class="form-label">Submit Document (PDF, DOC, DOCX only)</label>
                        <input type="file" class="form-control" id="document" name="document" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to submit these documents? Carefully review before submission.');">Submit</button>
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
