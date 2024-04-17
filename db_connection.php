<?php
// Database credentials
$host = 'localhost'; // Standard localhost, should be correct
$username = 'root'; // Default XAMPP username
$password = ''; // Default XAMPP has no password for root
$dbname = 'projecthub'; // Make sure this matches your database name exactly
$port = 3307; // New port number you set in XAMPP

// Create connection
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully"; // This line for testing connection, can be removed later
?>
