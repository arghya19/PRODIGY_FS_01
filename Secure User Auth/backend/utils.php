<?php
function generateUserID($conn) {
    $year = date("Y"); // Get current year, e.g., 2025
    $prefix = "U" . $year;

    // Query to get the last u_id for the current year
    $query = "SELECT u_id FROM user WHERE u_id LIKE '{$prefix}%' ORDER BY u_id DESC LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $lastID = $row['u_id']; // e.g., U2025003

        // Extract the numeric part (e.g., 003)
        $num = (int) substr($lastID, 5); // Skip first 5 characters (U + year)
        $newNum = $num + 1;
    } else {
        // If no record exists for the year, start from 001
        $newNum = 1;
    }

    // Format new ID (e.g., U2025001)
    $newID = $prefix . str_pad($newNum, 3, '0', STR_PAD_LEFT);

    return $newID;
}
?>