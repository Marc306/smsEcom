<?php
session_start();

// Ensure the student is logged in
if (!isset($_SESSION['student_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$student_id = $_SESSION['student_id'];

// Database connection
// $servername = "localhost:3307"; // Change if your MySQL port is different
// $username = "root";
// $password = "";
// $dbname = "studentaccount";
$servername = "localhost"; // Change if your database is hosted elsewhere
$username = "ecom_Marc306";        // Default XAMPP username is 'root'
$password = "OG*ED2e^2P%Atv0g";            // Default XAMPP password is empty
$dbname = "ecom_studentaccount"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Prepare the query
$stmt = $conn->prepare("
    SELECT p.order_id, p.receipt_url 
    FROM payments p
    JOIN orders o ON p.order_id = o.id
    WHERE o.student_id = ? 
    AND o.payment_method = 'Gcash Payment' 
    AND p.status = 'Pending'
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$receipts = [];
while ($row = $result->fetch_assoc()) {
    if (empty($row['receipt_url'])) {
        error_log("Warning: Receipt URL is empty for order_id " . $row['order_id']);
    }
    $receipts[] = $row;
}

$stmt->close();
$conn->close();

// Send JSON response
header('Content-Type: application/json');
echo json_encode(["receipts" => $receipts]);

?>


