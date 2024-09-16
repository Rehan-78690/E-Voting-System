<?php
session_start();
include 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer library

$mail = new PHPMailer(true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $election_date = $_POST['election_date'];
    $election_day = $_POST['election_day'];
    $status = $_POST['status'];
    $notification_message = $_POST['notification'];

    // Save election settings in the database
    $sql = "INSERT INTO elections (election_date, election_day, status, dscription) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $election_date, $election_day, $status, $notification_message);

    if ($stmt->execute()) {
        // Send notification email to all candidates
        $sql = "SELECT candidate_email FROM candidates";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
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
                    $mail->Body = $notification_message . $election_date; 
                    $mail->AltBody = 'Elections are going to be held soon.';

                    $mail->send();
                    $mail->clearAddresses();
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
            echo "Election settings saved and notifications sent!";
        } else {
            echo "No candidates found to notify.";
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
