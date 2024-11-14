<?php
session_start();
include 'config.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>
<body>
<?php
if (!isset($_SESSION['email'])) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
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


$sql = "SELECT cd.id, c.candidate_name, cd.document_path, cd.submission_date, cd.verification_status 
        FROM candidate_documents cd
        JOIN candidates c ON cd.candidate_id = c.candidate_id
        WHERE cd.verification_status = 'pending'";
$result = $conn->query($sql);

?>


   < <?php
  include 'sidebar.php';
  ?>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="javascript:void(0);" id="navbarToggle">☰</a> 
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="welcome.php">Home</a>
                    </li>
                </ul>
                <!-- Search form -->
                <form class="d-flex">
                <input class="form-control me-2" type="text" id="searchInput" placeholder="Search..." aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="content" id="mainContent" >
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
                                    <option value="rejected">Request-additional</option>
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
    <a href="verified_documents.php"> view verified documents</a>
</div>


<a href="verified_documents.php"> view verified documents</a>
<footer>
        <p>&copy; 2024 E-Voting UPR. All rights reserved.</p>
        <p>Designed for University of Poonch Rawalakot Elections</p>
    </footer>
    <script>
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!confirm('Are you sure you want to update the verification status?')) {
                event.preventDefault();
            }
        });
    });
</script>
<script src="scripts.js"></script>
</body>
</html>
