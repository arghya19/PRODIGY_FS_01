<?php 
session_start(); 
include 'dbconnection.php';  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {     
    $email = trim($_POST['u_email']);
    $newPassword = trim($_POST['u_pass']);     
    $enteredOtp = trim($_POST['otp']);      

    // Validate inputs
    if (empty($email) || empty($newPassword) || empty($enteredOtp)) {
        echo "missing_fields";
        exit;
    }

    // Check if OTP and email are stored in session
    if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_email'])) {
        echo "otp_missing";
        exit;     
    }      

    // Validate OTP and match it with email
    if ($enteredOtp != $_SESSION['otp'] || $email != $_SESSION['otp_email']) {         
        echo "otp_invalid";
        exit;     
    }      

    // Hash new password     
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);      

    // Update in DB
    $conn = connectDB();     
    $stmt = $conn->prepare("UPDATE user SET u_pass = ? WHERE u_email = ?");     
    $stmt->bind_param("ss", $hashedPassword, $email);     
    $stmt->execute();      

    if ($stmt->affected_rows > 0) {         
        // Clear OTP and email session
        unset($_SESSION['otp']);         
        unset($_SESSION['otp_email']);         
        echo "password_updated";
    } else {         
        echo "update_failed"; // No matching user or password was same as before
    }      

    $stmt->close();     
    $conn->close(); 
} else {
    echo "invalid_request";
}
?>
