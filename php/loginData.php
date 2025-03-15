<?php
session_start();
header('Content-Type: application/json'); // Set JSON response header

// Database connection
// $servername = "localhost:3307";
// $username = "root";
// $password = "";
// $dbname = "studentaccount";

$servername = "localhost"; // Change if your database is hosted elsewhere
$username = "ecom_Marc306";        // Default XAMPP username is 'root'
$password = "OG*ED2e^2P%Atv0g";            // Default XAMPP password is empty
$dbname = "ecom_studentaccount"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed."]);
    exit();
}

// Debugging
file_put_contents("debug.log", json_encode($_POST) . PHP_EOL, FILE_APPEND);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = trim($_POST['student_id']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $stored_password_hash = $student['password'];

        // Verify password (no rehashing)
        if (password_verify($password, $stored_password_hash)) {
            $_SESSION['student_id'] = $student['student_id'];
            echo json_encode(["success" => true, "redirect" => "home-page.php"]);
            exit();
        } else {
            echo json_encode(["success" => false, "error" => "Invalid password."]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "No account found with that student ID."]);
    }

    $stmt->close();
}

$conn->close();
?>

