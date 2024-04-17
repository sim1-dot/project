<?php
include 'db_connection.php';

// Initialize a message variable
$message = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Retrieve the token from the database
    $query = "SELECT student_number, expiration FROM verification_tokens WHERE token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $studentNumber = $data['student_number'];
        $expiration = $data['expiration'];

        // Check if the token has expired
        if (new DateTime($expiration) > new DateTime()) {
            // Token is valid, activate the user's account
            $updateUser = "UPDATE users SET status = 'pending' WHERE student_number = ?";
            $updateStmt = $conn->prepare($updateUser);
            $updateStmt->bind_param("i", $studentNumber);
            if ($updateStmt->execute()) {
                $message = "Your account has been successfully verified.";
            } else {
                $message = "Failed to activate account.";
            }

            // Delete the token
            $deleteToken = "DELETE FROM verification_tokens WHERE token = ?";
            $deleteStmt = $conn->prepare($deleteToken);
            $deleteStmt->bind_param("s", $token);
            $deleteStmt->execute();
        } else {
            $message = "The verification link has expired. Please register again.";
        }
    } else {
        $message = "Invalid token provided.";
    }
} else {
    $message = "No token provided.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Email</title>
</head>
<body>
    <h1>Email Verification</h1>
    <p><?php echo $message; ?></p>
</body>
</html>
