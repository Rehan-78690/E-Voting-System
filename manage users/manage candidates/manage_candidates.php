<?php
include "../../config.php";
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../../admin.php");
    exit();
}

// Handling form submission for adding a new candidate
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_candidate'])) {
    // Get form data
    $candidate_name = htmlspecialchars($_POST['candidate_name']);
    $candidate_role = htmlspecialchars($_POST['candidate_role']);
    $candidate_department = htmlspecialchars($_POST['candidate_department']);
    $candidate_email = htmlspecialchars($_POST['candidate_email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    // Handle file upload for the symbol
 // Handle file upload for the symbol
$symbol = null;
if (!empty($_FILES["symbol"]["name"])) {
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/EVotingSystem/uploads/symbols/"; // Set the server path
    $original_file_name = $_FILES["symbol"]["name"];
    
    // Sanitize the file name: replace spaces with underscores and remove special characters
    $clean_file_name = preg_replace('/[^a-zA-Z0-9-_\.]/', '', str_replace(' ', '_', $original_file_name));
    $clean_file_name = strtolower($clean_file_name); // Convert to lowercase

    // Add a unique identifier to prevent overwriting
    $unique_file_name = uniqid() . '_' . $clean_file_name;

    // Set the full file path
    $symbol_path = $target_dir . $unique_file_name;

    // Get the file extension
    $symbol_file_type = strtolower(pathinfo($symbol_path, PATHINFO_EXTENSION));

    // Check file type and size
    if (in_array($symbol_file_type, ['jpg', 'png', 'jpeg']) && $_FILES["symbol"]["size"] < 5000000) {
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["symbol"]["tmp_name"], $symbol_path)) {
            // Set the web-accessible path for database storage
            $symbol = "/uploads/symbols/" . $unique_file_name;
        } else {
            echo "Failed to move uploaded file.";
            exit();
        }
    } else {
        echo "Invalid symbol file type or size.";
        exit();
    }
}



    // Insert data into the database
    $sql = "INSERT INTO candidates (candidate_name, candidate_email, password, department, candidate_role, symbol, status,role, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, 'approved','candidate',NOW(), NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $candidate_name, $candidate_email, $password, $candidate_department, $candidate_role, $symbol);

    if ($stmt->execute()) {
        echo "New candidate added successfully";
        header("Location: manage_candidates.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all candidates from the database and display them in a table
$query = "SELECT candidate_id, candidate_name, candidate_email, candidate_number, address, manifesto, socialmedia_links, candidate_role, department, symbol FROM candidates where role ='candidate'AND status='approved'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Management</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body {
font-family: 'Poppins', sans-serif;
background-color: #f8f9fa;
}
</style>
</head>
<body>
     <!-- Sidebar -->
 <div class="sidebar closed" id="sidebar">
    <h5>Dashboard Menu</h5>
    <a href="../../welcome.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'welcome.php' ? 'active-link' : ''; ?>">Dashboard</a>
    <a href="../approval_requests.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'approval_requests.php' ? 'active-link' : ''; ?>">Approval Requests</a>
    <a href="manage_candidates.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_candidates.php' ? 'active-link' : ''; ?>">Candidate Management</a>
    <a href="../../admin_profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_profile.php' ? 'active-link' : ''; ?>">Profile Management</a>
    <a href="../../document_verification.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'document_verification.php' ? 'active-link' : ''; ?>">Document Verification</a>
    <a href="../symbol_allocation.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'symbol_allocation.php' ? 'active-link' : ''; ?>">Symbol Allocation</a>
    <a href="../../manage_feedback.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_feedback.php' ? 'active-link' : ''; ?>">Feedback Management</a>
    <a href="../../elections/election_settings/election_settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'election_settings.php' ? 'active-link' : ''; ?>">Settings</a>
    <a href="../../logout.php">Sign Out</a>
</div>
<div class="overlay" id="overlay"></div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="javascript:void(0);" id="navbarToggle">☰</a> <!-- Sidebar toggle button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="../../welcome.php">Home</a>
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
<div class="content" id="mainContent">
        <div class="container">
            <h1>Candidate Management</h1>

            <!-- Add New Candidate Button -->
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCandidateModal">Add New Candidate</button>
            </div>

            <script>
                const candidatesData = {};
            </script>

            <!-- Candidates Table -->
            <div class="table-container">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Candidate Name</th>
                            <th>Role</th>
                            <th>Department</th>
                           
                            <th>Symbol</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<script>
                            candidatesData['{$row['candidate_id']}'] = {
                                candidate_name: '".htmlspecialchars($row['candidate_name'])."',
                                candidate_role: '".htmlspecialchars($row['candidate_role'])."',
                                department: '".htmlspecialchars($row['department'])."',
                                candidate_email: '".htmlspecialchars($row['candidate_email'])."',
                                candidate_number: '".htmlspecialchars($row['candidate_number'])."',
                                address: '".htmlspecialchars($row['address'])."',
                                manifesto: '".htmlspecialchars($row['manifesto'])."',
                                socialmedia_links: '".htmlspecialchars($row['socialmedia_links'])."'
                            };
                        </script>";
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['candidate_role']); ?></td>
                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                            
                            <td>
            <?php if (!empty($row['symbol'])): ?>
                <img src="<?php echo "../../uploads/symbols/" . htmlspecialchars(basename($row['symbol'])); ?>" alt="Candidate Symbol" style="width: 50px; height: 30px;">
            <?php else: ?>
                No symbol uploaded
            <?php endif; ?>
        </td>
                            <td>
                                <button class="btn btn-sm btn-primary view-btn" data-bs-toggle="modal" data-bs-target="#viewCandidateModal" data-candidate-id="<?php echo $row['candidate_id']; ?>">View</button>

                                <button class="btn btn-sm btn-secondary edit-btn" data-bs-toggle="modal" data-bs-target="#editCandidateModal" data-candidate-id="<?php echo $row['candidate_id']; ?>">Edit</button>

                                <form method="POST" action="delete_candidate.php" style="display:inline;"enctype="multipart/form-data"onsubmit="return confirmDelete();">
                                    <input type="hidden" name="candidate_id" value="<?php echo $row['candidate_id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='6'>No candidates found.</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Candidate Modal -->
    <div class="modal fade" id="addCandidateModal" tabindex="-1" aria-labelledby="addCandidateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCandidateModalLabel">Add New Candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data" action="">
                        <div class="mb-3">
                            <label for="candidateName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="candidateName" name="candidate_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="candidateRole" class="form-label">Role</label>
                            <select class="form-select" id="candidateRole" name="candidate_role" required>
                                <option value="Lecturer">Lecturer</option>
                                <option value="Professor">Professor</option>
                                <option value="Assistant_Professor">Assistant Professor</option>
                                <option value="Associate_Professor">Associate Professor</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="candidateDepartment" class="form-label">Department</label>
                            <input type="text" class="form-control" id="candidateDepartment" name="candidate_department" required>
                        </div>
                        <div class="mb-3">
                            <label for="candidateEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="candidateEmail" name="candidate_email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="symbol" class="form-label">Symbol</label>
                            <input type="file" class="form-control" id="symbol" name="symbol" >
                        </div>
                        <button type="submit" name="submit_candidate" class="btn btn-primary">Add Candidate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Candidate Modal -->
    <div class="modal fade" id="editCandidateModal" tabindex="-1" aria-labelledby="editCandidateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCandidateModalLabel">Edit Candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCandidateForm" method="POST" enctype="multipart/form-data" action="edit_candidate.php">
                        <input type="hidden" name="candidate_id" id="editCandidateId"> 
                        <div class="mb-3">
                            <label for="editCandidateName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editCandidateName" name="candidate_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCandidateRole" class="form-label">Role</label>
                            <select class="form-select" id="editCandidateRole" name="candidate_role" required>
                                <option value="Lecturer">Lecturer</option>
                                <option value="Professor">Professor</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editCandidateDepartment" class="form-label">Department</label>
                            <input type="text" class="form-control" id="editCandidateDepartment" name="candidate_department" required>
                        </div>
                        <div class="mb-3">
                        <label for="editSymbol" class="form-label">Symbol</label>
                        <input type="file" class="form-control" id="editSymbol" name="symbol">
                    </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Candidate Modal -->
    <div class="modal fade" id="viewCandidateModal" tabindex="-1" aria-labelledby="viewCandidateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewCandidateModalLabel">View Candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-4"><strong>Name:</strong></div>
                        <div class="col-8"><span id="viewCandidateName"></span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Role:</strong></div>
                        <div class="col-8"><span id="viewCandidateRole"></span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Department:</strong></div>
                        <div class="col-8"><span id="viewCandidateDepartment"></span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Email:</strong></div>
                        <div class="col-8"><span id="viewCandidateEmail"></span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Number:</strong></div>
                        <div class="col-8"><span id="viewCandidateNumber"></span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Address:</strong></div>
                        <div class="col-8"><span id="viewAddress"></span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Manifesto:</strong></div>
                        <div class="col-8"><span id="viewManifesto"></span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Social Media Links:</strong></div>
                        <div class="col-8"><span id="viewSocialmediaLinks"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to Populate Modal with Data -->
    <script>
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const candidateId = this.getAttribute('data-candidate-id');
                const candidate = candidatesData[candidateId];

                // Populate the form fields for editing
                document.getElementById('editCandidateName').value = candidate.candidate_name;
                document.getElementById('editCandidateRole').value = candidate.candidate_role;
                document.getElementById('editCandidateDepartment').value = candidate.department;
                document.getElementById('editCandidateId').value = candidateId;
            });
        });

        document.querySelectorAll('.view-btn').forEach(button => {
            button.addEventListener('click', function() {
                const candidateId = this.getAttribute('data-candidate-id');
                const candidate = candidatesData[candidateId];

                // Populate the modal for viewing
                document.getElementById('viewCandidateName').textContent = candidate.candidate_name;
                document.getElementById('viewCandidateRole').textContent = candidate.candidate_role;
                document.getElementById('viewCandidateDepartment').textContent = candidate.department;
                document.getElementById('viewCandidateEmail').textContent = candidate.candidate_email;
                document.getElementById('viewCandidateNumber').textContent = candidate.candidate_number;
                document.getElementById('viewAddress').textContent = candidate.address;
                document.getElementById('viewManifesto').textContent = candidate.manifesto;
                document.getElementById('viewSocialmediaLinks').textContent = candidate.socialmedia_links;
            });
        });
        function confirmDelete() {
        return confirm('Are you sure you want to remove this candidate? This action cannot be undone.');
    }
    </script>
<script src="../script.js"></script>
</body>
</html>
