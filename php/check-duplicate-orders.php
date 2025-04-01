<?php
session_start();
header("Content-Type: application/json");
include "connection.php";

$data = json_decode(file_get_contents("php://input"), true);
$productId = $data["productId"];
$studentId = $_SESSION["student_id"] ?? null;

if (!$studentId) {
    echo json_encode(["error" => "Student not logged in"]);
    exit;
}

// Check how many times the student has bought this product
$query = "SELECT COUNT(*) as total FROM orders WHERE student_id = ? AND productId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $studentId, $productId);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

$response = ["duplicate" => $result["total"] == 1]; // ✅ Prevent more than 2 purchases
echo json_encode($response);
?>

