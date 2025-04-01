<?php
session_start();
header("Content-Type: application/json");
include "connection.php";

// Retrieve the product ID from the request body
$data = json_decode(file_get_contents("php://input"), true);
$productId = $data["productId"];
$studentId = $_SESSION["student_id"] ?? null;

// Check if the student is logged in
if (!$studentId) {
    echo json_encode(["error" => "Student not logged in", "session" => $_SESSION]);
    exit;
}

// Debugging: Log the product and student ID being checked
error_log("🔹 Checking duplicate for Product ID: $productId, Student ID: $studentId");

// Query the database to count how many times the student has bought this product
$query = "SELECT COUNT(*) as total FROM orders WHERE student_id = ? AND productId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $studentId, $productId);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

// Debugging: Log the total count of purchases
error_log("🔹 Purchase count for Product ID: $productId, Student ID: $studentId is: " . $result["total"]);

// Respond with whether the student has purchased the product more than twice
$response = ["duplicate" => $result["total"] >= 1]; // Prevent more than 2 purchases
echo json_encode($response);
?>

