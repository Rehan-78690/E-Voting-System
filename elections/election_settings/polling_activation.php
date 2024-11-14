<?php
include '../../config.php';
session_start();

// Fetch all elections to allow the admin to activate or deactivate polling
$query = "SELECT election_id, election_name, election_date, status, role FROM elections";
$result = mysqli_query($conn, $query);
$elections = $result && mysqli_num_rows($result) > 0 ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

// Handle polling activation/deactivation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $election_id = mysqli_real_escape_string($conn, $_POST['election_id']);
    
    // Fetch role and status of the selected election
    $role_query = "SELECT role, status FROM elections WHERE election_id = ?";
    $stmt_role = $conn->prepare($role_query);
    $stmt_role->bind_param("i", $election_id);
    $stmt_role->execute();
    $stmt_role->bind_result($election_role, $election_status);
    $stmt_role->fetch();
    $stmt_role->close();

    if (isset($_POST['activate_voting'])) {
        // Check if there is already an active election for the same role
        $active_check_query = "SELECT COUNT(*) AS active_count FROM elections WHERE status = 'active' AND role = ?";
        $stmt_active_check = $conn->prepare($active_check_query);
        $stmt_active_check->bind_param("s", $election_role);
        $stmt_active_check->execute();
        $stmt_active_check->bind_result($active_count);
        $stmt_active_check->fetch();
        $stmt_active_check->close();

        if ($active_count > 0) {
            echo "<div class='alert alert-danger text-center mt-3'>Another election for this role is already active. Please deactivate it before activating a new one.</div>";
        } else {
            // Proceed to activate the election
            $update_query = "UPDATE elections SET status = 'active' WHERE election_id = ?";
            $stmt = $conn->prepare($update_query);
            if ($stmt) {
                $stmt->bind_param("i", $election_id);
                if ($stmt->execute()) {
                    echo "<div class='alert alert-success text-center mt-3'>Polling has been activated successfully!</div>";

                    // Assign election_id to candidates matching the role
                    $assign_query = "UPDATE candidates SET election_id = ? WHERE candidate_role = ?";
                    $stmt_assign = $conn->prepare($assign_query);
                    $stmt_assign->bind_param("is", $election_id, $election_role);
                    if (!$stmt_assign->execute()) {
                        echo "<div class='alert alert-danger text-center mt-3'>Error assigning election ID: " . $stmt_assign->error . "</div>";
                    }
                    $stmt_assign->close();
                } else {
                    echo "<div class='alert alert-danger text-center mt-3'>Error: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
        }
    }elseif (isset($_POST['deactivate_voting'])) {
        // Change the election status to inactive without clearing election_id
        $update_query = "UPDATE elections SET status = 'inactive' WHERE election_id = ?";
        $stmt = $conn->prepare($update_query);
        if ($stmt) {
            $stmt->bind_param("i", $election_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success text-center mt-3'>Polling has been deactivated successfully!</div>";
            } else {
                echo "<div class='alert alert-danger text-center mt-3'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activate/Deactivate Voting</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .content {
            margin-top: 10px;
            max-width: 700px; /* Restrict width for the form */
            width: 100%; /* Full width on smaller screens */
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
        }

        .card {
            padding: 20px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Soft shadow */
            background-color: white;
            margin-bottom: 20px;
        }

        label {
            font-weight: 500;
            color: #555;
        }

        select {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        select:focus {
            border-color: #007bff;
            box-shadow: 0 0 4px rgba(0, 123, 255, 0.3); /* Subtle glow */
        }

        button {
            font-size: 16px;
            font-weight: 600;
            padding: 10px;
            border-radius: 8px;
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .tooltip-icon {
            font-size: 1.2rem;
            color: white; /* Different color for tooltip icon */
            cursor: pointer;
            margin-left: 10px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }

            h2 {
                font-size: 1.5rem;
            }

            button {
                font-size: 14px;
            }
        }
        .back-button {
    position: absolute; /* Ensure it stays in place as the user scrolls */
    top: 70px; /* Adjust to appear right below the fixed navbar */
    left: 260px; /* Padding from the left edge of the screen */
    z-index: 1050; /* Ensure it appears above most elements but below the navbar */
}
    </style>
</head>
<body>

<?php
    include 'sidebar.php';
    ?>
    
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
    <div class="content"id="mainContent">
    <div class="back-button">
    <a href="election_settings.php" class="btn btn-secondary">← Back</a>
</div>
    <h2>Activate or Deactivate Voting</h2>
    
    <!-- Activate Voting Card -->
    <div class="card">
        <form method="POST" action="">
            <div class="mb-3">
                <label for="election_id">Activate Voting for:</label>
                <select name="election_id" id="election_id" class="form-select" required>
                    <?php foreach ($elections as $election): ?>
                        <?php if ($election['status'] !== 'active' && $election['status'] !== 'completed'): ?>
                            <option value="<?php echo $election['election_id']; ?>">
                                <?php echo $election['election_name'] . ' (' . date('d-m-Y', strtotime($election['election_date'])) . ')'; ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="activate_voting" class="btn btn-primary w-100">
                Activate Voting
                <i class="bi bi-question-circle-fill tooltip-icon" data-bs-toggle="tooltip" 
                   title="Activating will change the election status to 'active'."></i>
            </button>
        </form>
    </div>
    
    <!-- Deactivate Voting Card -->
    <div class="card">
        <form method="POST" action="">
            <div class="mb-3">
                <label for="election_id">Deactivate Voting for:</label>
                <select name="election_id" id="election_id" class="form-select" required>
                    <?php foreach ($elections as $election): ?>
                        <?php if ($election['status'] === 'active'): ?>
                            <option value="<?php echo $election['election_id']; ?>">
                                <?php echo $election['election_name'] . ' (' . date('d-m-Y', strtotime($election['election_date'])) . ')'; ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="deactivate_voting" class="btn btn-danger w-100">
                Deactivate Voting
                <i class="bi bi-question-circle-fill tooltip-icon" data-bs-toggle="tooltip" 
                   title="Deactivating will change the election status to 'inactive'."></i>
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
<script src="../script.js"></script>
</body>
</html>
