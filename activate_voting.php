<?php
include 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $election_id = $_POST['election_id'];

    // Update the election status to active
    $sql = "UPDATE elections SET status = 'active' WHERE election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);

    if ($stmt->execute()) {
        // Fetch emails of all candidates to notify them about voting activation
        $email_query = "SELECT candidate_email FROM candidates";
        $email_result = $conn->query($email_query);

        if ($email_result->num_rows > 0) {
            // Send notifications to all candidates
            while ($row = $email_result->fetch_assoc()) {
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
                    $mail->addAddress($user_email); // Send to each candidate's email

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Election Notification';
                    $mail->Body = "Elections have started, vote as soon as possible.";
                    $mail->AltBody = 'Elections have started, please vote.';

                    $mail->send();
                    $mail->clearAddresses();
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
            echo json_encode(['status' => 'success', 'message' => 'Voting activated successfully and notifications sent']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No candidate emails found']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to activate voting']);
    }
    $stmt->close();
}
?>
