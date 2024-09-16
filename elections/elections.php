<?php
include"config.php";
include"design.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>election process</title>
    <style>
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
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="content">
        <div class="container">
            <h1>Election Day Settings</h1>

            <div class="form-section">
                <h5>Set Election Day</h5>
                <form method="POST" action="election_settings.php">
                    <label for="electionDate" class="form-label">Election Date</label>
                    <input type="date" class="form-control" id="electionDate" name="election_date" required>

                    <label for="electionDay" class="form-label">Election Day</label>
                    <input type="text" class="form-control" id="electionDay" name="election_day" placeholder="e.g., Monday" required>

                    <label for="status" class="form-label">Election Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="upcoming">Upcoming</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                    </select>

                    <label for="notification" class="form-label">Notification Message</label>
                    <textarea class="form-control" id="notification" name="notification" rows="3" placeholder="Enter notification message for voters..." required></textarea>

                    <div class="form-actions mt-4">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
</body>
</html>