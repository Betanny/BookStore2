<?php
require_once '../Shared Components/dbconnection.php';
require '../vendor/autoload.php';
include '../Shared Components/logger.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email2'];

    // Check if the email exists in the database
    $stmt = $db->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 day'));

        // Store the token and expiry time in the database
        $stmt = $db->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        $stmt->execute([$token, $expiry, $email]);

        // Send the reset link to the user's email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->SMTPDebug = 2; // Enable verbose debug output
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'smartcbcinfo@gmail.com'; // SMTP username
            $mail->Password = 'yttf rsgi ccus rngz'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable SSL encryption
            $mail->Port = 465; // TCP port to connect to

            // Recipients
            $mail->setFrom('smartcbcinfo@gmail.com');
            $mail->addAddress($email); // Add a recipient

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click the link below to reset your password:<br><br><a href='http://localhost:3000/Registration/login.php?token=$token'>Reset Password</a>";

            $mail->send();
            writeLog($db, "Password reset email sent to $email", "INFO", $user['user_id']);
            echo "Password reset email sent.";
        } catch (Exception $e) {
            writeLog($db, "Failed to send password reset email to $email. Mailer Error: {$mail->ErrorInfo}", "ERROR", $user['user_id']);
            echo "Failed to send password reset email. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "No user found with that email address.";
    }
}