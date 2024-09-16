<?php
include "../config.php";
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../admin.php");
    exit();
}

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $candidate_name = htmlspecialchars($_POST['candidate_name']);
    $candidate_role = htmlspecialchars($_POST['candidate_role']);
    $candidate_department = htmlspecialchars($_POST['candidate_department']);
    $candidate_email = htmlspecialchars($_POST['candidate_email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    // Handle file upload for profile picture and documents
    $target_dir = "../uploads/";
    
    // symbol
    $symbol = $target_dir . basename($_FILES["symbol"]["name"]);
    $symbol_pic_file_type = strtolower(pathinfo($symbol, PATHINFO_EXTENSION));

    // Check file type
    if (in_array($symbol_pic_file_type, ['jpg', 'png', 'jpeg']) && $_FILES["symbol"]["size"] < 5000000) {
        move_uploaded_file($_FILES["symbol"]["tmp_name"], $symbol);
    } else {
        echo "Invalid symbol file type or size.";
        exit();
    }

    // Insert data into the database
    $sql = "INSERT INTO candidates (candidate_name, candidate_email, password,  department, candidate_role,  symbol, status, created_at, updated_at)
            VALUES ( ?, ?, ?, ?, ?, ?, 'Pending', NOW(), NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $candidate_name, $candidate_email, $password, $candidate_department, $candidate_role, $symbol);

    if ($stmt->execute()) {
        echo "New candidate added successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all candidates from the database and display them in a table
$query = "SELECT candidate_id, candidate_name, candidate_email, candidate_number, address, manifesto, socialmedia_links, candidate_role, department, symbol FROM candidates";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
   <link rel="stylesheet" href="style.css">
   <script src="script.js"></script>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <button id="icon-ham" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="navbar-text">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></span>
                    </li>
                    <li class="nav-item dropdown ms-3">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="../uprlogo.png" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px;"> Muhammad
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Edit Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Log Out</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Off-Canvas Sidebar -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Dashboard Menu</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="../admin.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Candidate Management</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Document Verification</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Symbol Allocation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Feedback Management</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../elections/election_settings.php">Settings</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
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
                            <th>Status</th>
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
                            <td>Pending</td>
                            <td><?php echo htmlspecialchars($row['symbol']); ?></td>
                            <td>
         <button class="btn btn-sm btn-secondary view-btn" data-bs-toggle="modal" data-bs-target="#viewCandidateModal" data-candidate-id="<?php echo $row['candidate_id']; ?>">View</button>

         <form id="editCandidateForm<?php echo $row['candidate_id']; ?>" method="POST" enctype="multipart/form-data" action="edit_candidate.php">
    <input type="hidden" name="candidate_id" value="<?php echo $row['candidate_id']; ?>"> 
    <button class="btn btn-sm btn-secondary edit-btn" data-bs-toggle="modal" data-bs-target="#editCandidateModal" data-candidate-id="<?php echo $row['candidate_id']; ?>">Edit</button>

</form>
<form method="POST" action="delete_candidate.php">
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
                            <label for="symbol" class="form-label">symbol</label>
                            <input type="file" class="form-control" id="symbol" name="symbol">
                            <small>leave this empty if you don't want to add symbol</small>
                        </div>
                        
                        
                        <button type="submit" class="btn btn-primary">Add Candidate</button>
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
                <form id="editCandidateForm" method="POST">
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
                        <img id="editSymbolPreview" src="<?php echo htmlspecialchars($row['symbol']); ?>" alt="Current Symbol" style="max-width: 100%; height: auto; margin-top: 5px;">
                        <small>Leave blank to keep the current symbol.</small>

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

</body>
</html>
