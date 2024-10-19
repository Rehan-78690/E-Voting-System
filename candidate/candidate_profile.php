<?php
session_start();
include 'config.php'; // Ensure this includes the $conn variable

if (!isset($_SESSION['candidate_email'])) {
    header("Location: candidate.php");
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$candidate_id = $_SESSION['candidate_id'];

if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
    $file = $_FILES['profilePicture'];
    
    // Define a directory to store uploaded images
    $uploadDir = 'uploads/profile_pics/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Create the directory if it doesn't exist
    }

    // Get file extension and ensure it's an image
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (in_array($fileExtension, $allowedExtensions)) {
        // Generate a unique file name
        $fileName = $uploadDir . 'candidate_' . $candidate_id . '.' . $fileExtension;
        
        // Move the file to the upload directory
        if (move_uploaded_file($file['tmp_name'], $fileName)) {
            // Update the candidate's profile picture in the database
            $sql = "UPDATE candidates SET profile_pic = ? WHERE candidate_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $fileName, $candidate_id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'filePath' => $fileName]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error uploading file']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
}

$sql = "SELECT candidate_name, candidate_email, candidate_number, address, profile_pic FROM candidates WHERE candidate_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $candidate_name = $row['candidate_name'];
    $candidate_email = $row['candidate_email'];
    $candidate_number = $row['candidate_number'];
    $address = $row['address'];
    $profile_pic = $row['profile_pic'];
} else {
    echo "Candidate not found.";
    exit();
}
$stmt->close();



// Handle Profile Update Form Submission
if (isset($_POST['name'], $_POST['email'], $_POST['number'], $_POST['address'])) {
    // Retrieve the candidate ID from the hidden form field
    $candidate_name = $_POST['name'];
    $candidate_email = $_POST['email'];
    $candidate_number = $_POST['number'];
    $address = $_POST['address'];

    if (!empty($candidate_id)) {
        // Update the existing record in the database
        $sql = "UPDATE candidates SET candidate_name = ?, candidate_email = ?, candidate_number = ?, address = ? WHERE candidate_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssssi", $candidate_name, $candidate_email, $candidate_number, $address, $candidate_id);
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
        echo "No candidate ID provided.";
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
                $sql = "UPDATE candidates SET password = ? WHERE candidate_id = ?";
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
        .profile-picture-container input[type="file"] {
    display: block;
    margin-top: 10px;
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
            opacity: 0.7;
        }
        .upload-label {
            display: block;
            margin-top: 10px;
            background-color: #0275d8;
            color: white;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .upload-label:hover {
            background-color: #025aa5;
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
                        <span class="navbar-text">Welcome, <?php echo htmlspecialchars($_SESSION['candidate_email']); ?></span>
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
    <img src="<?php echo !empty($profile_pic) ? htmlspecialchars($profile_pic) : 'default_profile_pic.jpeg'; ?>" alt="Current Profile Picture" id="profilePic">
    <div class="overlay">
        <span class="icon">&#128247;</span>
    </div>
    <div class="dropdown-menu">
        <button class="dropdown-item" onclick="viewPhoto()">View Photo</button>
        <label class="dropdown-item" for="profilePicture">Upload Photo</label>
        <button class="dropdown-item" onclick="removePhoto()">Remove Photo</button>
    </div>
    <!-- Updated to call the upload function properly -->
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

                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" required>

                    <label for="phoneNumber" class="form-label">Personal Cell</label>
                    <input type="text" class="form-control" id="phoneNumber" name="number" value="<?php echo htmlspecialchars($candidate_number); ?>" required>

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
         <!-- Password Reset Form -->
<div class="form-section password-reset-form" id="passwordResetForm">
    <h5>Password Reset</h5>
    <form method="POST" action="">
        <input type="hidden" name="candidate_id" value="<?php echo htmlspecialchars($candidate_id); ?>">

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
        if (confirm('Are you sure you want to remove your profile picture?')) {
            // Call a server-side script to remove the profile picture
            alert('Profile picture removed!');
            document.getElementById('profilePic').src = 'default-profile-pic.jpeg';
        }
    }

    function uploadPhoto() {
    var fileInput = document.getElementById('profilePicture');
    var file = fileInput.files[0];

    if (file) {
        var formData = new FormData();
        formData.append('profilePicture', file);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '', true); // Send the request to the same PHP file
        xhr.onload = function () {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    document.getElementById('profilePic').src = response.filePath; // Update profile picture
                    alert('Profile picture updated successfully!');
                } else {
                    alert('Error: ' + response.message);
                }
            }
        };
        xhr.send(formData);
    } else {
        alert('No file selected!');
    }
}

    </script>

</body>
</html>
