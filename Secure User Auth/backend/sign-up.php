<?php
include 'dbconnection.php';
include 'utils.php';

header('Content-Type: application/json');

$u_name = strtolower(trim($_POST['u_name'] ?? ''));
$u_email = trim($_POST['u_email'] ?? '');
$u_phno = trim($_POST['u_phn'] ?? '');
$u_pass = trim($_POST['u_pass'] ?? '');

// Basic validation
if (empty($u_name) || empty($u_email) || empty($u_phno) || empty($u_pass)) {
    echo json_encode(["status" => "error", "message" => "All fields are required!", "code" => "missing_fields"]);
    exit;
}

if (!preg_match("/^[a-zA-Z\s]{2,}$/", $u_name)) {
    echo json_encode(["status" => "error", "message" => "Invalid name.", "code" => "invalid_name"]);
    exit;
}

if (!filter_var($u_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email.", "code" => "invalid_email"]);
    exit;
}

if (!preg_match("/^[6-9]\d{9}$/", $u_phno)) {
    echo json_encode(["status" => "error", "message" => "Invalid phone number.", "code" => "invalid_phone"]);
    exit;
}

if (strlen($u_pass) < 8) {
    echo json_encode(["status" => "error", "message" => "Password too short.", "code" => "weak_password"]);
    exit;
}

$hashedPassword = password_hash($u_pass, PASSWORD_DEFAULT);
$conn = connectDB();

// Check if user exists
$stmt = $conn->prepare("SELECT u_email, u_phno FROM user WHERE u_email = ? OR u_phno = ?");
$stmt->bind_param("ss", $u_email, $u_phno);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($u_email === $row['u_email']) {
        echo json_encode(["status" => "error", "message" => "Email already registered!", "code" => "email_exists"]);
    } elseif ($u_phno === $row['u_phno']) {
        echo json_encode(["status" => "error", "message" => "Phone number already registered!", "code" => "phone_exists"]);
    }
} else {
    // Insert new user
    $uid = generateUserID($conn);
    $stmt = $conn->prepare("INSERT INTO user (u_id, u_name, u_email, u_phno, u_pass) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $uid, $u_name, $u_email, $u_phno, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Account created successfully!", "code" => "account_created"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Something went wrong!", "code" => "server_error"]);
    }
}

$stmt->close();
$conn->close();
?>
