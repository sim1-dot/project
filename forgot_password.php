<?php
include 'db_connection.php';
session_start();
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $student_number = $_POST['student_number'];
    $new_password = $_POST['new_password'];

    // Check if email and student_number match a record in the database
    $query = "SELECT * FROM users WHERE email = ? AND student_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $email, $student_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If the user is found, allow password update
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE users SET password = ? WHERE email = ? AND student_number = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssi", $hashed_password, $email, $student_number);
        if ($updateStmt->execute()) {
            $message = "Password updated successfully.";
        } else {
            $message = "Failed to update password.";
        }
    } else {
        $message = "No matching user found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
</head>
<body>
<h2>Forgot Password</h2>
<form method="post" action="forgot_password.php">
    <!-- Assume the user will provide their email and perhaps another identifier -->
    Email: <input type="email" name="email" required><br>
    Student Number (for verification): <input type="number" name="studentNumber" required><br>
    <input type="submit" value="Reset Password">
</form>

<!-- Add a button to go back to the login page -->
<p><a href="login.php"><button type="button">Go Back to Login</button></a></p>
</body>
</html>
