<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'dbconnection.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'invalid_request';
    exit;
}

try {
    $conn = connectDB();
    if (!$conn) {
        error_log("Database connection failed");
        echo 'server_error';
        exit;
    }

    $to = isset($_POST['u_email']) ? trim($_POST['u_email']) : '';
    $password = isset($_POST['u_pass']) ? trim($_POST['u_pass']) : '';

    if (empty($to)) {
        echo 'email_missing';
        exit;
    }

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        echo 'invalid_email';
        exit;
    }

    // Verify email and password
    $stmt = $conn->prepare("SELECT u_pass FROM user WHERE u_email = ?");
    $stmt->bind_param("s", $to);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo 'email_not_found';
        exit;
    }

    $row = $result->fetch_assoc();
    $hashedPassword = $row['u_pass'];

    if (!password_verify($password, $hashedPassword)) {
        echo 'wrong_password';
        exit;
    }
    $stmt->close();

    // Generate OTP
    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_email'] = $to;
    $_SESSION['otp_expires'] = time() + (3 * 60);
    $_SESSION['otp_attempts'] = 0;
    $_SESSION['otp_last_sent'] = time();

    // Send email
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'rstate.noreply@gmail.com';
    $mail->Password   = 'ydajslqmyyschpix';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->Timeout    = 30;

    $mail->setFrom('rstate.noreply@gmail.com', 'Irenic Support');
    $mail->addAddress($to);
    $mail->isHTML(true);
    $mail->Subject = 'Login Verification - Your OTP Code';
    $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #00d4ff 0%, #8b5cf6 100%); padding: 20px; text-align: center;'>
                <h2 style='color: white; margin: 0;'>User Authentication System</h2>
            </div>
            <div style='padding: 30px; background: #f8f9fa;'>
                <h3 style='color: #333;'>Login Verification</h3>
                <p>Hello,</p>
                <p>You are attempting to log in to your account. Your OTP code is:</p>
                <div style='text-align: center; margin: 20px 0;'>
                    <span style='font-size: 32px; font-weight: bold; color: #00d4ff; background: white; padding: 15px 30px; border-radius: 10px; border: 2px solid #00d4ff; display: inline-block;'>$otp</span>
                </div>
                <p><strong style='color: #e74c3c;'>This code will expire in 3 minutes.</strong></p>
                <p>If you didn't attempt to log in, please ignore this email and consider changing your password.</p>
                <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                <p style='color: #666; font-size: 14px;'>Best regards,<br>Team Irenic</p>
            </div>
        </div>";
    $mail->AltBody = "Login Verification\n\nYour OTP code is: $otp\n\nThis code will expire in 3 minutes.\n\nIf you didn't attempt to log in, please ignore this email.";

    $mail->send();
    echo 'otp_sent';

} catch (Exception $e) {
    error_log("Error in send-login-otp.php: " . $e->getMessage());
    echo 'otp_failed';
} finally {
    if (isset($conn) && $conn) {
        $conn->close();
    }
}
?>