<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

$url = 'localhost/bttavm/activate.php?token=$token';

function sendConfirmationEmail($email, $token): bool{
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  // Replace with your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'aslankoylu1071@gmail.com';  // Your email
        $mail->Password   = 'stkyijzdfjvwtbpd';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587; 

        // Recipients
        $mail->setFrom('aslankoylu1071@gmail.com', 'BTTAVM');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Confirm Your Email';
        $mail->Body    = "Click <a href= '" . $url . "'>here</a> to activate your account.";

        $mail->send();

        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>

