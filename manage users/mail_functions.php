<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Include PHPMailer autoload

// Send approval email
function sendApprovalEmail($email, $name) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'rehankhan.upr@gmail.com'; // Your Gmail address
        $mail->Password = 'ccqu utkq itfm lznb'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('your_email@example.com', 'UPR Senate22');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Candidate Approval';
        $mail->Body = "Dear $name,<br>Your registration has been approved. Welcome to the UPR Senate.";

        $mail->send();
    } catch (Exception $e) {
        error_log("Approval email failed: {$mail->ErrorInfo}");
    }
}

// Send rejection email
function sendRejectionEmail($email, $name) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'rehankhan.upr@gmail.com'; // Your Gmail address
        $mail->Password = 'ccqu utkq itfm lznb'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('your_email@example.com', 'UPR Senate22');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Candidate Rejection';
        $mail->Body = "Dear $name,<br>We regret to inform you that your registration has been rejected.";

        $mail->send();
    } catch (Exception $e) {
        error_log("Rejection email failed: {$mail->ErrorInfo}");
    }
}
?>
