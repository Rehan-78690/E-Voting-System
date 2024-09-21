<?php
include 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Function to send email notifications
function sendEmailNotification($conn, $election_id) {
    $email_query = "SELECT candidate_email FROM candidates";
    $email_result = mysqli_query($conn, $email_query);

    if ($email_result && mysqli_num_rows($email_result) > 0) {
        while ($email_row = mysqli_fetch_assoc($email_result)) {
            $user_email = $email_row['candidate_email'];

            if (!empty($user_email)) {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'rehankhan.upr@gmail.com';
                    $mail->Password = 'ccqu utkq itfm lznb'; // App password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('UPRSenate@upr.edu.pk', 'UPR Senate');
                    $mail->addAddress($user_email); // Send to each candidate's email

                    // Email content
                    $mail->isHTML(true);
                    $mail->Subject = 'Election Notification';
                    $mail->Body = "Elections have started. Please vote as soon as possible.";
                    $mail->AltBody = 'Elections have started, please vote.';

                    $mail->send();
                    $mail->clearAddresses(); // Clear addresses for the next iteration
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
        }
    } else {
        echo "No candidates found to notify.";
    }
}

// Main logic for activating/deactivating voting
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['election_id'])) {
    $election_id = $_POST['election_id'];

    // Fetch the election start and end times
    $status_query = "SELECT start_time, end_time, status FROM elections WHERE election_id = ?";
    $stmt = $conn->prepare($status_query);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    $status_result = $stmt->get_result();

    if ($status_result && mysqli_num_rows($status_result) > 0) {
        $row = $status_result->fetch_assoc();
        $election_start_time = $row['start_time'];
        $election_end_time = $row['end_time'];
        $current_status = $row['status'];
        
        $current_time = date('H:i:s');
        
        // Check if the election is already active or inactive
        if ($current_status == 'completed') {
            echo "The elections have completed and voting has been deactivated.";
        } else if ($current_time >= $election_start_time && $current_time <= $election_end_time) {
            // Activate voting
            $update_query = "UPDATE elections SET status = 'active' WHERE election_id = ?";
            $stmt_update = $conn->prepare($update_query);
            $stmt_update->bind_param("i", $election_id);
            if ($stmt_update->execute()) {
                echo "Voting has been activated.";
                sendEmailNotification($conn, $election_id); // Send notification emails
            } else {
                echo "Failed to activate voting.";
            }
        } else if ($current_time > $election_end_time) {
            // Deactivate voting
            $update_query = "UPDATE elections SET status = 'completed' WHERE election_id = ?";
            $stmt_update = $conn->prepare($update_query);
            $stmt_update->bind_param("i", $election_id);
            if ($stmt_update->execute()) {
                echo "Voting has been deactivated.";
            } else {
                echo "Failed to deactivate voting.";
            }
        } else {
            echo "The election is either not yet started or invalid time for voting.";
        }
    } else {
        echo "Invalid election ID or election not found.";
    }
    $stmt->close();
} else {
    echo "Invalid request. Election ID missing.";
}
?>
