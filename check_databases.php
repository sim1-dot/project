<?php
// Database credentials
$host = 'localhost'; // Hostname
$username = 'root'; // Username for MySQL
$password = ''; // Password for MySQL, empty by default in XAMPP
$port = 3307; // The port you configured MySQL to use

// Create connection using MySQLi
$conn = new mysqli($host, $username, $password, '', $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get all database names
$query = "SHOW DATABASES;";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<h1>List of databases:</h1>";
    while ($row = $result->fetch_assoc()) {
        echo "Database: " . $row["Database"] . "<br>";
    }
} else {
    echo "No databases found.";
}

$conn->close();
?>
