<?php
// Include the database connection file
include 'db_connection.php';

// Initialize variables to hold form data and errors
$firstName = $lastName = $studentNumber = $email = "";
$errors = [];
$successMessage = ""; // Initialize success message

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $studentNumber = $_POST['studentNumber'];
    $email = $_POST['email'];

    // Validate if the student is enrolled
    $query = "SELECT * FROM enrolled WHERE student_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $studentNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Student is enrolled, insert into interns table
        $insertQuery = "INSERT INTO interns (first_name, last_name, student_number, email) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ssis", $firstName, $lastName, $studentNumber, $email);
        if ($insertStmt->execute()) {
            $successMessage = "You've successfully submitted your application.";
        } else {
            $errors[] = "Failed to submit your application.";
        }
    } else {
        $errors[] = "You are not enrolled.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Internship Application</title>
</head>
<body>
<h2>Internship Application Form</h2>
<?php
if (!empty($successMessage)) {
    echo "<div style='color: green;'>$successMessage</div>";
}

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<div style='color: red;'>$error</div>";
    }
}
?>
<form method="post" action="index.php">
    First Name: <input type="text" name="firstName" required><br>
    Last Name: <input type="text" name="lastName" required><br>
    Student Number: <input type="number" name="studentNumber" required><br>
    Email: <input type="email" name="email" required><br>
    <input type="submit" value="Submit">
</form>

<div>
    <a href="login.php">Login</a> | <a href="register.php">Register</a>
</div>
</body>
</html>
