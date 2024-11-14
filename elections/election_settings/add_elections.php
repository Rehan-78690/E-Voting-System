<?php
include '../../config.php';
session_start();

// Handle election announcement form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['election_announcement'])) {
    $election_name=mysqli_real_escape_string($conn,$_POST['election_name']);
    $last_date = mysqli_real_escape_string($conn, $_POST['last_date_documents']);
    $last_date_symbols = mysqli_real_escape_string($conn, $_POST['last_date_symbols']);
    $election_date = mysqli_real_escape_string($conn, $_POST['election_date']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time_input = mysqli_real_escape_string($conn, $_POST['end_time']);
    $notification = mysqli_real_escape_string($conn, $_POST['notification']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    if (empty($election_date)) {
        echo "<div class='alert alert-danger text-center mt-3'>Error: Election date is missing!</div>";
        exit;
    }

    $start_datetime = $election_date . ' ' . $start_time;
    $end_datetime = $election_date . ' ' . $end_time_input;

    // SQL query to insert election announcement into the elections table
    $insert_query = "INSERT INTO elections (election_name,last_date_documents,last_date_symbols, election_date, start_time, end_time, status, description, role) 
                     VALUES (?,?, ?,?, ?, ?, 'upcoming', ?, ?)";
    $stmt = $conn->prepare($insert_query);
    if ($stmt) {
        $stmt->bind_param("ssssssss", $election_name,$last_date,$last_date_symbols, $election_date, $start_datetime, $end_datetime, $notification, $role);

        // Execute the query
        if ($stmt->execute()) {
            echo "<div class='alert alert-success text-center mt-3'>Election announcement added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger text-center mt-3'>Error: " . $stmt->error . "</div>";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger text-center mt-3'>Error preparing statement: " . $conn->error . "</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Elections</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa; /* Light background */
            display: flex;
            justify-content: center;
            align-items: center; /* Center vertically */
            min-height: 100vh;
        }
        .content {
            /* justify-content: center; */
            /* margin-left: 330px; */
            max-width: 800px;
            width:100%;
            margin-top: 50px;
            /* margin-right: 50px; */
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .card {
            padding: 20px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Soft shadow for the card */
            background-color: white;
        }
        label {
            font-weight: 500;
            color: #333;
        }
        input, select, textarea {
            border: 1px solid #ddd; /* Subtle border for inputs */
            border-radius: 8px;
            padding: 10px;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #007bff; /* Highlight border on focus */
            box-shadow: 0 0 4px rgba(0, 123, 255, 0.3); /* Subtle glow on focus */
        }
        
        .back-button {
    position: fixed; /* Ensure it stays in place as the user scrolls */
    top: 70px; /* Adjust to appear right below the fixed navbar */
    left: 260px; /* Padding from the left edge of the screen */
    z-index: 1050; /* Ensure it appears above most elements but below the navbar */
}

    </style>
</head>
<body>
<div class="back-button">
    <a href="election_settings.php" class="btn btn-secondary">← Back</a>
</div>

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
   
        <h1>Add Election</h1>
        <div class="card">
            <form method="POST" action="">
                <input type="hidden" name="election_announcement" value="true">

                <div class="mb-3">
                    <label for="election_name">Election Name:</label>
                    <input type="text" id="election_name" name="election_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="last_date_documents">Last Date for Submission of Documents:</label>
                    <input type="date" id="last_date_documents" name="last_date_documents" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="last_date_symbols">Last Date for Assigning Symbols:</label>
                    <input type="date" id="last_date_symbols" name="last_date_symbols" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="election_date">Election Date:</label>
                    <input type="date" id="election_date" name="election_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="start_time">Start Time:</label>
                    <input type="time" id="start_time" name="start_time" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="end_time">End Time:</label>
                    <input type="time" id="end_time" name="end_time" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="role">Choose a Role:</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="Professor">Professor</option>
                        <option value="Lecturer">Lecturer</option>
                        <option value="Assistant_professor">Assistant Professor</option>
                        <option value="Associate_professor">Associate Professor</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="notification">Description:</label>
                    <textarea id="notification" name="notification" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Add Election</button>
            </form>
        </div>
    </div>
    <script src="../script.js"></script>
</body>
</html>
