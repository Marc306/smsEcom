<?php
$servername = "localhost:3307"; // Change if your database is hosted elsewhere
$username = "root";        // Default XAMPP username is 'root'
$password = "";            // Default XAMPP password is empty
$database = "studentaccount"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
