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
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style> body {
            margin-top: 20px;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding-top: 56px;
        }
        .navbar-brand {
            font-size: 1.5rem;
            color: #007bff !important;
        }
        .content {
            padding: 20px;
            margin-left: 250px;
            transition: all 0.3s ease;
            min-height: calc(100vh - 112px); /* Adjust for navbar and footer height */
        }
        .content.no-sidebar {
            margin-left: 0;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color:#212529;
            color: white;
            padding-top: 20px;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .sidebar h5 {
            text-align: center;
            margin-bottom: 30px;
            color: white;
        }
        .sidebar a {
            padding: 10px 15px;
            display: block;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
        }
        .sidebar a:hover {
            background-color: #0056b3; /* Red color on hover */
            color: white;
        }
        .sidebar.closed {
            left: -250px;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 250px;
            width: calc(100% - 250px);
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
        .overlay.active {
            display: block;
        }
        .sidebar.closed + .overlay {
            left: 0;
            width: 100%;
        }       
         .card-title {
            font-size: 1.25rem;
            color: #0275d8; /* Red color for the section title */
        }
        .form-label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #d9534f;
            border-color: #d9534f;
        }
        .btn-danger {
            background-color: #0275d8;
            border-color: #0275d8;
        }
        .form-section {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            background-color: #fff;
        }
        .form-section h5 {
            border-bottom: 2px solid #0275d8;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #0275d8;
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
            background-color: #0275d8;
            border: 1px solid #0275d8;
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
    
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 160px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 100;
        }
       
        footer {
            background-color: #212529;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

    <!-- Overlay -->
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
                        <a class="nav-link active" aria-current="page" href="welcome.php">Home</a>
                    </li>
                </ul>
                
        </div>
    </nav>

    <div class="sidebar closed" id="sidebar">
        <h5>Dashboard Menu</h5>
        <!-- <a href="#" class="d-block mb-2" id="sidebarToggle">☰ Toggle Sidebar</a> -->
        <a href="manage%20users/approval_requests.php"> Approval requests</a>
        <a href="manage%20users/manage%20candidates/manage_candidates.php">Candidate Management</a>
        <a href="live_voting.php"> Live voting</a>
        <a href="document_verification.php"> Document Verification</a>
        <a href="manage%20users/symbol_allocation.php"> Symbol Allocation</a>
        <a href="manage_feedback.php"> Feedback Management</a>
        <a href="#"> Settings</a>
        <a href="logout.php">Sign Out</a>
    </div>
    <!-- Main Content -->
    <div class="content">
        <div class="container">
            <h1>Update Profile</h1>

        


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
    <footer>
        <p>&copy; 2024 E-Voting UPR. All rights reserved.</p>
        <p>Designed for University of Poonch Rawalakot Elections</p>
    </footer>
    <script>
         document.addEventListener('DOMContentLoaded', function () {
    // Get elements for sidebar toggle, overlay, main content, and search
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const sidebarToggle = document.getElementById('navbarToggle');
    const mainContent = document.querySelector('.content');
  

    // Function to initialize sidebar state based on screen size
    function initializeSidebar() {
      if (window.innerWidth <= 525) {
        sidebar.classList.add('closed');
        mainContent.classList.add('no-sidebar');
      } else {
        sidebar.classList.remove('closed');
        mainContent.classList.remove('no-sidebar');
      }
    }

    // Call the function on page load to set initial state
    initializeSidebar();

    // Toggle sidebar visibility on button click
    sidebarToggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
      sidebar.classList.toggle('closed');
      mainContent.classList.toggle('no-sidebar');

      // Show overlay on small screens when sidebar is open
      if (sidebar.classList.contains('open') && window.innerWidth <= 525) {
        overlay.classList.add('active');
      } else {
        overlay.classList.remove('active');
      }
    });

    // Close sidebar when clicking on the overlay
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('open');
      sidebar.classList.add('closed');
      mainContent.classList.add('no-sidebar');
      overlay.classList.remove('active');
    });

    // Adjust sidebar on window resize
    window.addEventListener('resize', function () {
      initializeSidebar();
    });



    });

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
        
    </script>

</body>
</html>
