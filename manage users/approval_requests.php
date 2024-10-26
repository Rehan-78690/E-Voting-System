<?php
// Include database and mail configuration
include '../config.php';
include 'mail_functions.php';

session_start();

// Check if admin is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../admin.php");
    exit();
}

// Fetch all pending candidates
$sql = "SELECT * FROM candidates WHERE status = 'Pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Requests</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Candidate Approval Requests</h1>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['candidate_email']); ?></td>
                                <td class="text-center">
                                    <form method="POST" action="handle_approval.php" class="d-inline">
                                        <input type="hidden" name="candidate_id" value="<?php echo $row['candidate_id']; ?>">
                                        <input type="hidden" name="candidate_email" value="<?php echo htmlspecialchars($row['candidate_email']); ?>">
                                        <input type="hidden" name="candidate_name" value="<?php echo htmlspecialchars($row['candidate_name']); ?>">
                                        <button type="submit" name="approve" class="btn btn-success btn-sm">Approve</button>
                                        <button type="submit" name="reject" class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center" role="alert">
                No pending approval requests.
            </div>
        <?php endif; ?>

        <?php $conn->close(); ?>
    </div>

    <!-- Add Bootstrap JS (Optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
