<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

// Database connection
require_once "connection.php";
session_start(); // Start session to get student_id

// Read JSON input from JavaScript fetch request
$data = json_decode(file_get_contents("php://input"), true);

// Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit;
}

// Get student ID from session (string)
$student_id = $_SESSION["student_id"] ?? null;
if (!$student_id) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit;
}

// Validate JSON data
if (!isset($data["action"], $data["productId"]) || $data["action"] !== "delete") {
    echo json_encode(["success" => false, "message" => "Invalid request data."]);
    exit;
}

// Sanitize and extract the product ID (string)
$productId = trim($data["productId"]);
if (empty($productId)) {
    echo json_encode(["success" => false, "message" => "Product ID is required."]);
    exit;
}

// Find the order item and check if it belongs to the logged-in student
$sqlFind = "SELECT 
                oi.id AS order_item_id, 
                oi.order_id, 
                o.student_id, 
                COALESCE(p.status, 'Pending') AS payment_status 
            FROM orders_items oi
            JOIN orders o ON oi.order_id = o.id
            LEFT JOIN payments p ON o.id = p.order_id 
            WHERE oi.productId = ? AND o.student_id = ?";

$stmtFind = $conn->prepare($sqlFind);
$stmtFind->bind_param("ss", $productId, $student_id); // Product ID = string, Student ID = string
$stmtFind->execute();
$resultFind = $stmtFind->get_result();
$orderItem = $resultFind->fetch_assoc();

// Debugging: Log query results
error_log("Query Result: " . json_encode($orderItem));

// Check if order item exists and belongs to the student
if (!$orderItem) {
    echo json_encode(["success" => false, "message" => "Order item not found or unauthorized action."]);
    exit;
}

$orderItemId = (int) $orderItem["order_item_id"]; // Convert order item ID to integer
$orderId = (int) $orderItem["order_id"]; // Convert order ID to integer
$paymentStatus = $orderItem["payment_status"];

// Double-check that the logged-in student is the same as the one in the orders table
$sqlCheckStudent = "SELECT student_id FROM orders WHERE id = ?";
$stmtCheckStudent = $conn->prepare($sqlCheckStudent);
$stmtCheckStudent->bind_param("i", $orderId); // Order ID = integer
$stmtCheckStudent->execute();
$resultCheckStudent = $stmtCheckStudent->get_result();
$orderStudent = $resultCheckStudent->fetch_assoc();

if (!$orderStudent || $orderStudent["student_id"] !== $student_id) {
    echo json_encode(["success" => false, "message" => "Unauthorized action: Order does not belong to you."]);
    exit;
}

// Debugging: Log student check
error_log("Order Student ID: " . $orderStudent["student_id"] . " | Logged-in Student ID: " . $student_id);

// Start transaction
$conn->begin_transaction();

try {
    // Delete the order item
    $sqlDeleteItem = "DELETE FROM orders_items WHERE id = ?";
    $stmtDeleteItem = $conn->prepare($sqlDeleteItem);
    $stmtDeleteItem->bind_param("i", $orderItemId); // Order item ID = integer
    $stmtDeleteItem->execute();

    // Check if any items remain for the order
    $sqlCheckOrder = "SELECT COUNT(*) AS remaining FROM orders_items WHERE order_id = ?";
    $stmtCheckOrder = $conn->prepare($sqlCheckOrder);
    $stmtCheckOrder->bind_param("i", $orderId); // Order ID = integer
    $stmtCheckOrder->execute();
    $resultCheckOrder = $stmtCheckOrder->get_result();
    $rowCheckOrder = $resultCheckOrder->fetch_assoc();

    if ($rowCheckOrder["remaining"] == 0) {
        // Delete associated payment record first
        $sqlDeletePayment = "DELETE FROM payments WHERE order_id = ? AND EXISTS (
            SELECT 1 FROM orders WHERE id = ? AND student_id = ?
        )";
        $stmtDeletePayment = $conn->prepare($sqlDeletePayment);
        $stmtDeletePayment->bind_param("iis", $orderId, $orderId, $student_id); // Order ID = int, Student ID = string
        $stmtDeletePayment->execute();

        // If no more items, delete the order itself
        $sqlDeleteOrder = "DELETE FROM orders WHERE id = ? AND student_id = ?";
        $stmtDeleteOrder = $conn->prepare($sqlDeleteOrder);
        $stmtDeleteOrder->bind_param("is", $orderId, $student_id); // Order ID = int, Student ID = string
        $stmtDeleteOrder->execute();
    }

    // Commit transaction
    $conn->commit();

    echo json_encode(["success" => true, "message" => "Order successfully canceled."]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Error canceling order: " . $e->getMessage()]);
}

exit;
?>









