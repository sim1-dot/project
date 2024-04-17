<?php
session_start();
include 'db_connection.php';

if (isset($_SESSION['student_number'])) {
    $student_number = $_SESSION['student_number'];

    // Update activity status to 0
    $updateQuery = "UPDATE users SET activity = 0 WHERE student_number = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $student_number);
    $stmt->execute();
}

// Destroy the session and redirect to login page
session_destroy();
header("Location: index.php");
exit();
?>
