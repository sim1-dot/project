<?php
include 'db_connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Example query, adjust fields and table name as necessary
    $query = "SELECT student_number, username, password, permission FROM users WHERE username = ? AND status = 'approved'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Password verification can be done here if passwords are hashed
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['student_number'] = $row['student_number'];
            $_SESSION['permission'] = $row['permission'];

            // Update activity status to 1
            $updateQuery = "UPDATE users SET activity = 1 WHERE student_number = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("i", $row['student_number']);
            $updateStmt->execute();

            // Redirect user based on permission
            if ($row['permission'] == 'admin') {
                header("Location: users.php");
            } else {
                header("Location: interns.php");
            }
            exit();
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "No user found with that username.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
<h2>Login</h2>
<?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
<form method="post" action="login.php">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Login">
</form>
<p><a href="forgot_password.php">Forgot Password?</a></p>
</body>
</html>
