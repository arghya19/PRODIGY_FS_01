<?php
session_start();

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'dbconnection.php';              // ✅ Include DB connection
$conn = connectDB();                     // ✅ Get connection object

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ✅ Sanitize email input
    $to = isset($_POST['u_email']) ? trim($_POST['u_email']) : '';

    if (empty($to)) {
        echo 'email_missing';
        exit;
    }

    // ✅ Check if email exists in DB
    $stmt = $conn->prepare("SELECT * FROM user WHERE u_email = ?");
    $stmt->bind_param("s", $to);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo 'email_not_found';
        exit;
    }

    // ✅ Generate OTP and store in session
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_email'] = $to; 
    $_SESSION['otp_expiry'] = time() + (3 * 60); // OTP expires in 3 minutes

    $mail = new PHPMailer(true);

    try {
        // SMTP config
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'rstate.noreply@gmail.com'; // your Gmail
        $mail->Password   = 'ydajslqmyyschpix';         // your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender and recipient
        $mail->setFrom('rstate.noreply@gmail.com', 'Irenic Support');
        $mail->addAddress($to);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset - Your OTP Code';
        $mail->Body    = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 20px; text-align: center;'>
                    <h2 style='color: white; margin: 0;'>Private Chat Room</h2>
                </div>
                <div style='padding: 30px; background: #f8f9fa;'>
                    <h3 style='color: #333;'>Reset your password</h3>
                    <p>Hello,</p>
                    <p>You are attempting to log in to your account. Your OTP code is:</p>
                    <div style='text-align: center; margin: 20px 0;'>
                        <span style='font-size: 32px; font-weight: bold; color: #4facfe; background: white; padding: 15px 30px; border-radius: 10px; border: 2px solid #4facfe; display: inline-block;'>$otp</span>
                    </div>
                    <p><strong style='color: #e74c3c;'>This code will expire in 3 minutes.</strong></p>
                    <p>If you didn't attempt to reset your password, please ignore this email and consider changing your password.</p>
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                    <p style='color: #666; font-size: 14px;'>
                        Best regards,<br>
                        Team Irenic
                    </p>
                </div>
            </div>";

        $mail->send();
        echo 'otp_sent';
    } catch (Exception $e) {
        echo "otp_failed: " . $mail->ErrorInfo;
    }
} else {
    echo 'invalid_request';
}
