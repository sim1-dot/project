<?php
include 'db_connection.php';
session_start();

// Redirect if not logged in or not an admin
if (!isset($_SESSION['username']) || $_SESSION['permission'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch pending, approved, and rejected users data
$query_pending = "SELECT student_number, username, email, password FROM users WHERE status = 'pending'";
$query_approved = "SELECT CONCAT(last_name, ' ', first_name) AS Name, student_number, email, username, password, permission, (CASE WHEN activity = 0 THEN 'Inactive' ELSE 'Active' END) AS activity_status FROM users WHERE status = 'approved'";
$query_rejected = "SELECT CONCAT(last_name, ' ', first_name) AS Name, student_number, email, username FROM users WHERE status = 'rejected'";

$result_pending = $conn->query($query_pending);
$result_approved = $conn->query($query_approved);
$result_rejected = $conn->query($query_rejected);

// Handle user status update if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_number = $_POST['student_number'];
    if (isset($_POST['approve'])) {
        $updateQuery = "UPDATE users SET status = 'approved' WHERE student_number = ?";
    } elseif (isset($_POST['terminate'])) {
        $updateQuery = "UPDATE users SET status = 'rejected' WHERE student_number = ?";
    } elseif (isset($_POST['reject'])) {
        $updateQuery = "UPDATE users SET status = 'rejected' WHERE student_number = ?";
    } elseif (isset($_POST['reactivate'])) {
        $updateQuery = "UPDATE users SET status = 'approved' WHERE student_number = ?";
    }

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $student_number);
    $stmt->execute();

    // Refresh page to reflect changes
    header("Location: users.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
</head>
<body>
<h1>User Management</h1>

<h2>Pending Users</h2>
<table border="1">
    <thead>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Password</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result_pending->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['password']) ?></td>
            <td>
                <form action="users.php" method="post">
                    <input type="hidden" name="student_number" value="<?= $row['student_number'] ?>">
                    <button type="submit" name="approve">Accept</button>
                    <button type="submit" name="reject">Reject</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<h2>Approved Users</h2>
<table border="1">
    <thead>
        <tr>
            <th>Name</th>
            <th>Student Number</th>
            <th>Email</th>
            <th>Username</th>
            <th>Password</th>
            <th>Permission</th>
            <th>Activity</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result_approved->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['Name']) ?></td>
            <td><?= htmlspecialchars($row['student_number']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['password']) ?></td>
            <td><?= htmlspecialchars($row['permission']) ?></td>
            <td><?= htmlspecialchars($row['activity_status']) ?></td>
            <td>
                <form action="users.php" method="post">
                    <input type="hidden" name="student_number" value="<?= $row['student_number'] ?>">
                    <button type="submit" name="terminate">Terminate</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<h2>Rejected Users</h2>
<table border="1">
    <thead>
        <tr>
            <th>Name</th>
            <th>Student Number</th>
            <th>Email</th>
            <th>Username</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result_rejected->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['Name']) ?></td>
            <td><?= htmlspecialchars($row['student_number']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td>
                <form action="users.php" method="post">
                    <input type="hidden" name="student_number" value="<?= $row['student_number'] ?>">
                    <button type="submit" name="reactivate">Reactivate</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<a href="interns.php">Interns</a> | <a href="logout.php">Logout</a>
</body>
</html>
