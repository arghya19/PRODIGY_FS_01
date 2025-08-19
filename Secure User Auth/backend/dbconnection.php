<?php
function connectDB() {
    // Connection details
    $servername = "localhost";
    $username = "root";           
    $password = "";               
    $dbname = "user_auth";           

    // Step 1: Connect to MySQL server (no DB yet)
    $conn = new mysqli($servername, $username, $password);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Step 2: Create the database if it doesn't exist
    $createDB = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if (!$conn->query($createDB)) {
        die("Error creating database: " . $conn->error);
    }

    // Step 3: Select the database
    $conn->select_db($dbname);

    // Step 4: Check if `user` table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'user'");
    if ($tableCheck->num_rows === 0) {
        // Step 5: Create the `user` table
        $createTableSQL = "
            CREATE TABLE `user` (
                `u_id` VARCHAR(100) NOT NULL,
                `u_name` VARCHAR(50) NOT NULL,
                `u_email` VARCHAR(100) NOT NULL,
                `u_phno` VARCHAR(10) NOT NULL,
                `u_pass` VARCHAR(100) NOT NULL,
                PRIMARY KEY (`u_id`),
                UNIQUE KEY `u_email` (`u_email`),
                UNIQUE KEY `u_phno` (`u_phno`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        if (!$conn->query($createTableSQL)) {
            die("Error creating table: " . $conn->error);
        }
    }

    return $conn;
}
?>
