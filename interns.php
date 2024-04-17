<?php
include 'db_connection.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle assignment update if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['intern_id'])) {
    foreach ($_POST['intern_id'] as $i => $intern_id) {
        $new_assignment = $_POST['assignment'][$i];

        $updateQuery = "UPDATE interns SET assignment = ? WHERE student_number = ?";
        $stmt = $conn->prepare($updateQuery);
        if ($stmt) {
            $stmt->bind_param("ss", $new_assignment, $intern_id);
            $stmt->execute();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }

    // Refresh page to reflect changes
    header("Location: interns.php");
    exit();
}

// Fetch interns data for different assignment statuses
$query_pending = "SELECT student_number AS intern_id, CONCAT(last_name, ', ', first_name) AS Name, student_number, email, assignment FROM interns WHERE assignment = 'Pending'";
$query_assigned = "SELECT student_number AS intern_id, CONCAT(last_name, ', ', first_name) AS Name, student_number, email, assignment FROM interns WHERE assignment NOT IN ('Pending', 'Reject')";
$query_rejected = "SELECT student_number AS intern_id, CONCAT(last_name, ', ', first_name) AS Name, student_number, email, assignment FROM interns WHERE assignment = 'Reject'";

$result_pending = $conn->query($query_pending);
$result_assigned = $conn->query($query_assigned);
$result_rejected = $conn->query($query_rejected);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Intern Management</title>
</head>
<body>
<h1>Intern Management</h1>
<form method="post" action="interns.php">
    <h2>Pending Interns</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Student Number</th>
                <th>Email</th>
                <th>Assignment</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_pending->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['Name']) ?></td>
                <td><?= htmlspecialchars($row['student_number']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td>
                    <input type="hidden" name="intern_id[]" value="<?= $row['intern_id'] ?>">
                    <select name="assignment[]">
                        <option <?= $row['assignment'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Project 1">Project 1</option>
                        <option value="Project 2">Project 2</option>
                        <option value="Project 3">Project 3</option>
                        <option value="Project 4">Project 4</option>
                        <option value="Reject">Reject</option>
                    </select>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Assigned Interns</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Student Number</th>
                <th>Email</th>
                <th>Assignment</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_assigned->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['Name']) ?></td>
                <td><?= htmlspecialchars($row['student_number']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td>
                    <input type="hidden" name="intern_id[]" value="<?= $row['intern_id'] ?>">
                    <select name="assignment[]">
                        <option value="Project 1" <?= $row['assignment'] == 'Project 1' ? 'selected' : '' ?>>Project 1</option>
                        <option value="Project 2" <?= $row['assignment'] == 'Project 2' ? 'selected' : '' ?>>Project 2</option>
                        <option value="Project 3" <?= $row['assignment'] == 'Project 3' ? 'selected' : '' ?>>Project 3</option>
                        <option value="Project 4" <?= $row['assignment'] == 'Project 4' ? 'selected' : '' ?>>Project 4</option>
                        <option value="Reject" <?= $row['assignment'] == 'Reject' ? 'selected' : '' ?>>Reject</option>
                    </select>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Intern Archive (Rejected)</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Student Number</th>
                <th>Email</th>
                <th>Assignment</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_rejected->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['Name']) ?></td>
                <td><?= htmlspecialchars($row['student_number']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['assignment']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <button type="submit">Save Changes</button>
</form>

<a href="logout.php">Logout</a>
</body>
</html>
