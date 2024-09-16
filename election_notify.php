<?php
include 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['election_date'], $_POST['start_time'], $_POST['end_time'], $_POST['election_day'])) {
    $election_date = $_POST['election_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $election_day = $_POST['election_day'];
    $notification_message = isset($_POST['notification']) ? $_POST['notification'] : '';

    $sql = "INSERT INTO elections (election_date, start_time, end_time, election_day, status) VALUES (?, ?, ?,?,'upcoming')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $election_date, $start_time, $end_time, $election_day);
    if ($stmt->execute()) {
        echo "<script>alert('Election date announced!');</script>";

        // Fetch all users' emails to send notifications
        $email_query = "SELECT candidate_email FROM candidates";
        $email_result = mysqli_query($conn, $email_query);

        if ($email_result) {
            while ($row = mysqli_fetch_assoc($email_result)) {
                $user_email = $row['candidate_email'];

                // Use PHPMailer to send the notification email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'rehankhan.upr@gmail.com'; // Your Gmail address
                    $mail->Password = 'ccqu utkq itfm lznb'; // App Password for Gmail
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('UPRSenate@upr.edu.pk', 'UPR Senate');
                    $mail->addAddress($row['candidate_email']); 

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Election Notification';
                    $mail->Body = $notification_message . $election_date.$start_time; 
                    $mail->AltBody = 'Elections are going to be held soon.';

                    $mail->send();
                    $mail->clearAddresses();
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
        } else {
            echo "Error fetching user emails.";
        }

    } else {
        echo "<script>alert('Error announcing election date: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Voting</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Announcement</title>
</head>
<body>
<form method="POST" action="">
    <label for="election_day">Election Day:</label>
    <input type="text" id="election_day" name="election_day" required>

    <label for="election_date">Election Date:</label>
    <input type="date" id="election_date" name="election_date" required>

    <label for="start_time">Start Time:</label>
    <input type="time" id="start_time" name="start_time" required>

    <label for="end_time">End Time:</label>
    <input type="time" id="end_time" name="end_time" required>

    <label for="description">Description:</label>
    <textarea id="description" name="notification"></textarea>

    <button type="submit">Announce Date</button>
</form>
    <button id="activate-voting" data-election-id="1">Activate Voting</button>
    <button id="deactivate-voting" data-election-id="1">Deactivate Voting</button>

    <script>
        $(document).ready(function() {
            $('#activate-voting').click(function() {
                var election_id = $(this).data('election-id');

                $.ajax({
                    url: 'activate_voting.php',
                    type: 'POST',
                    data: { election_id: election_id },
                    success: function(response) {
                        var result = JSON.parse(response);
                        alert(result.message);
                    },
                    error: function() {
                        alert('Error activating voting.');
                    }
                });
            });

            $('#deactivate-voting').click(function() {
                var election_id = $(this).data('election-id');

                $.ajax({
                    url: 'deactivate_voting.php',
                    type: 'POST',
                    data: { election_id: election_id },
                    success: function(response) {
                        var result = JSON.parse(response);
                        alert(result.message);
                    },
                    error: function() {
                        alert('Error deactivating voting.');
                    }
                });
            });
        });
    </script>

</body>
</html>
