<?php
session_start();
include 'dbconnection.php';

// Debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['u_email'] ?? '');
    $password = trim($_POST['u_pass'] ?? '');
    $enteredOtp = trim($_POST['otp'] ?? ''); // ✅ Get OTP from form

    // Validate inputs
    if (empty($email) || empty($password) || empty($enteredOtp)) {
        echo "missing_fields";
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "invalid_email";
        exit;
    }

    // ✅ OTP comparison
    if (!isset($_SESSION['otp']) || $enteredOtp !== $_SESSION['otp']) {
        echo "invalid_otp";
        exit;
    }

    try {
        // DB connection
        $conn = connectDB();

        // Get user
        $stmt = $conn->prepare("SELECT u_id, u_name, u_email, u_pass FROM user WHERE u_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['u_pass'])) {
                session_regenerate_id(true);

                // Create session vars
                $_SESSION['user_id'] = $user['u_id'];
                $_SESSION['user_name'] = $user['u_name'];
                $_SESSION['user_email'] = $user['u_email'];
                $_SESSION['logged_in'] = true;

                echo "login_success";
            } else {
                echo "invalid_credentials";
            }
        } else {
            echo "invalid_credentials";
        }

        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        echo "server_error";
    }

} else {
    header("Location: ../index.html");
    exit;
}
?>
