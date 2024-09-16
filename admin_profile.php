<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['email'])) {
    header("Location: admin.php");
    exit();
}

if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];

    // Prepare the SQL statement to fetch admin details
    $sql = "SELECT name, email, password FROM users WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $candidate_name = $row['name'];
        $candidate_email = $row['email'];
     
        $current_hashed_password = $row['password'];
    } else {
        echo "admin not found.";
        exit();
    }
    $stmt->close();
} else {
    echo "admin ID is not set in the session.";
    exit();
}

// Handle Profile Update Form Submission
if (isset($_POST['name'], $_POST['email'])) {
    // Retrieve the candidate ID from the hidden form field
    $candidate_name = $_POST['name'];
    $candidate_email = $_POST['email'];
 
    if (!empty($admin_id)) {
        // Update the existing record in the database
        $sql = "UPDATE users SET name = ?, email = ? WHERE candidate_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssi", $candidate_name, $candidate_email, $admin_id);
            if ($stmt->execute()) {
                echo "<script>alert('Records updated!');</script>";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing the statement: " . $conn->error;
        }
    } else {
        echo "No admin ID provided.";
    }
}

// Handle Password Reset Form Submission
if (isset($_POST['current_password'], $_POST['password'], $_POST['cpassword'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['password'];
    $confirm_password = $_POST['cpassword'];

    // Verify the current password
    if (password_verify($current_password, $current_hashed_password)) {
        // Check if the new passwords match and meet the requirements
        if ($new_password === $confirm_password) {
            if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
                // Hash the new password
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the password in the database
                $sql = "UPDATE users SET password = ? WHERE candidate_id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("si", $new_hashed_password, $candidate_id);
                    if ($stmt->execute()) {
                        echo "<script>alert('Password has been updated successfully.');</script>";
                    } else {
                        echo "<script>alert('Error updating password: " . $stmt->error . "');</script>";
                    }
                    $stmt->close();
                } else {
                    echo "<script>alert('Error preparing the statement: " . $conn->error . "');</script>";
                }
            } else {
                echo "<script>alert('New password does not meet the requirements.');</script>";
            }
        } else {
            echo "<script>alert('New passwords do not match.');</script>";
        }
    } else {
        echo "<script>alert('Current password is incorrect.');</script>";
    }
} else {
    echo "<script>alert('Password form not submitted correctly.');</script>";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .offcanvas-body {
            padding-top: 20px;
        }
        .content {
            padding: 20px;
            margin-top: 60px; /* Adjust to prevent content overlap */
        }
        .navbar-brand {
            padding-left: 10px;
        }
        .card-title {
            font-size: 1.25rem;
            color: #d9534f; /* Red color for the section title */
        }
        .form-label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #0275d8;
            border-color: #0275d8;
        }
        .btn-danger {
            background-color: #d9534f;
            border-color: #d9534f;
        }
        .form-section {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            background-color: #fff;
        }
        .form-section h5 {
            border-bottom: 2px solid #d9534f;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #d9534f;
        }
        .form-section .form-control {
            margin-bottom: 15px;
        }
        .form-actions {
            display: flex;
            justify-content: space-between;
        }
        .reset-password-link {
            color: #fff;
            background-color: #d9534f;
            border: 1px solid #d9534f;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        .reset-password-link:hover {
            background-color: #c9302c;
            border-color: #ac2925;
            text-decoration: none;
        }
        .password-reset-form {
            display: none; /* Ensure the form is hidden by default */
        }
        .profile-picture-container {
            position: relative;
            display: inline-block;
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-picture-container img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            transition: 0.3s;
        }
        .profile-picture-container:hover img {
            opacity: 0.5;
        }
        .profile-picture-container:hover .overlay {
            opacity: 1;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: 0.3s;
        }
        .overlay .icon {
            color: white;
            font-size: 30px;
        }
        .profile-picture-container input[type="file"] {
            display: none;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 160px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 100;
        }
        .profile-picture-container:hover.dropdown-menu {
            display: block;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#">Candidate Dashboard</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="navbar-text">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></span>
                    </li>
                    <li class="nav-item dropdown ms-3">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="uprlogo.png" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px;"> Profile
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
                    <a class="nav-link" href="#">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">Profile Management</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Document Submission</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">View Symbol</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Report Issues</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container">
            <h1>Update Profile</h1>

            <!-- Profile Picture Upload -->
            <div class="profile-picture-container">
            <img src="current-profile-pic.png" alt="Current Profile Picture" id="profilePic">
            <div class="overlay">
                <span class="icon">&#128247;</span>
            </div>
            <div class="dropdown-menu">
                <button class="dropdown-item" onclick="viewPhoto()">View Photo</button>
                <label class="dropdown-item" for="profilePicture">Take Photo</label>
                <label class="dropdown-item" for="profilePicture">Upload Photo</label>
                <button class="dropdown-item" onclick="removePhoto()">Remove Photo</button>
            </div>
            <input type="file" name="profilePicture" id="profilePicture" onchange="uploadPhoto()">
        </div>


            <div class="form-section">
                <h5>Update Profile</h5>
                <form method="POST" action="">
                    <input type="hidden" name="candidate_id" value="<?php echo htmlspecialchars($candidate_id); ?>">
                    <label for="Name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="Name" name="name" value="<?php echo htmlspecialchars($candidate_name); ?>" required>

                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($candidate_email); ?>" required>

                 
                    <div class="form-actions">
                        <a href="javascript:void(0);" id="resetPasswordBtn" class="reset-password-link" onclick="togglePasswordReset()">Reset Password <i class="fa fa-edit"></i></a>
                        <div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-secondary">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>

            
         <!-- Password Reset Form -->
<div class="form-section password-reset-form" id="passwordResetForm">
    <h5>Password Reset</h5>
    <form method="POST" action="">
        <input type="hidden" name="admin_id" value="<?php echo htmlspecialchars($admin_id); ?>">

        <label for="current_password" class="form-label">Current Password</label>
        <input type="password" class="form-control" id="current_password" name="current_password" required>

        <label for="password" class="form-label">New Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Minimum 8 characters, with at least one uppercase letter, one lowercase letter, one digit, and one special character" required>

        <label for="confirmPassword" class="form-label">Confirm New Password</label>
        <input type="password" class="form-control" name="cpassword" id="confirmPassword" required>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="button" class="btn btn-secondary" onclick="togglePasswordReset()">Cancel</button>
        </div>
    </form>
</div>

        </div>
    </div>

    <script>
        function togglePasswordReset() {
            var resetForm = document.getElementById("passwordResetForm");
            var resetBtn = document.getElementById("resetPasswordBtn");
            if (resetForm.style.display === "none" || resetForm.style.display === "") {
                resetForm.style.display = "block";
                resetBtn.innerHTML = "Cancel Reset Password <i class='fa fa-edit'></i>";
            } else {
                resetForm.style.display = "none";
                resetBtn.innerHTML = "Reset Password <i class='fa fa-edit'></i>";
            }
        }

        // Ensure the password reset form is hidden when the page loads
        window.onload = function() {
            document.getElementById("passwordResetForm").style.display = "none";
        };
        function viewPhoto() {
            // Logic to view the photo in a larger size
            alert('View Photo clicked');
        }

        function removePhoto() {
            // Logic to remove the current profile picture
            alert('Remove Photo clicked');
        }

        function uploadPhoto() {
            // Logic to handle the photo upload
            alert('Photo Uploaded');
        }

    </script>

</body>
</html>
