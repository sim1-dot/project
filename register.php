<?php
include 'db_connection.php';

$firstName = $lastName = $email = $username = $password = $confirm_password = "";
$errors = [];

function sendVerificationEmail($email, $token) {
    $subject = "Verify Your Email";
    $message = "Thanks for signing up! Please click on this link to verify your email: http://localhost:8080/my_project/verify.php?token=$token";
    $headers = "From: no-reply@yourdomain.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    mail($email, $subject, $message, $headers);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $permission = $_POST['permission'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if the user is in the enrolled table and retrieve student number
    $stmtEnrolled = $conn->prepare("SELECT student_number FROM enrolled WHERE email = ?");
    $stmtEnrolled->bind_param("s", $email);
    $stmtEnrolled->execute();
    $resultEnrolled = $stmtEnrolled->get_result();

    if ($resultEnrolled->num_rows === 0) {
        $errors[] = "You must be enrolled to register.";
    } else {
        $enrolledData = $resultEnrolled->fetch_assoc();
        $studentNumber = $enrolledData['student_number'];

        // Check if username is already taken
        $stmtUser = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmtUser->bind_param("s", $username);
        $stmtUser->execute();
        $resultUser = $stmtUser->get_result();

        if ($resultUser->num_rows > 0) {
            $errors[] = "Username is already taken.";
        } else {
            // Insert new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            if (empty($errors)) {
                $insertStmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, username, password, student_number, permission, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
                $insertStmt->bind_param("sssssis", $firstName, $lastName, $email, $username, $hashedPassword, $studentNumber, $permission);
                if ($insertStmt->execute()) {
                    // Generate and store verification token
                    $token = bin2hex(random_bytes(16));  // More appropriate size
                    $expires = date('Y-m-d H:i:s', strtotime('+1 day'));  // 24 hours from now
                    $tokenStmt = $conn->prepare("INSERT INTO verification_tokens (student_number, token, expiration) VALUES (?, ?, ?)");
                    $tokenStmt->bind_param("iss", $studentNumber, $token, $expires);
                    if ($tokenStmt->execute()) {
                        // Send verification email
                        sendVerificationEmail($email, $token);

                        echo "<script>alert('Registration successful. Please check your email to verify your account.');</script>";
                        header("Refresh:2; url=login.php");
                    } else {
                        $errors[] = "Error storing verification token.";
                    }
                } else {
                    $errors[] = "Error while registering.";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
<h2>Register</h2>
<?php
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<div style='color: red;'>$error</div>";
    }
}
?>
<form method="post" action="register.php">
    First Name: <input type="text" name="firstName" required><br>
    Last Name: <input type="text" name="lastName" required><br>
    Email: <input type="email" name="email" required><br>
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    Confirm Password: <input type="password" name="confirm_password" required><br>
    Permission: <select name="permission">
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select><br>
    <input type="submit" value="Register">
</form>
<div>
    <a href="login.php">Login</a>
</div>
</body>
</html>
