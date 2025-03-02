<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

// Database connection
require_once "connection.php";

// Read JSON input from JavaScript fetch request
$data = json_decode(file_get_contents("php://input"), true);

// Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit;
}

// Validate JSON data
if (!isset($data["action"], $data["productId"]) || $data["action"] !== "delete") {
    echo json_encode(["success" => false, "message" => "Invalid request data."]);
    exit;
}

// Sanitize and extract the product ID
$productId = trim($data["productId"]);
if (empty($productId)) {
    echo json_encode(["success" => false, "message" => "Product ID is required."]);
    exit;
}

// Find the order item and check its payment status
$sqlFind = "SELECT 
                oi.id AS order_item_id, 
                oi.order_id, 
                COALESCE(p.status, 'Pending') AS payment_status 
            FROM orders_items oi
            JOIN orders o ON oi.order_id = o.id
            LEFT JOIN payments p ON o.id = p.order_id 
            WHERE oi.productId = ?";

$stmtFind = $conn->prepare($sqlFind);
$stmtFind->bind_param("s", $productId);
$stmtFind->execute();
$resultFind = $stmtFind->get_result();
$orderItem = $resultFind->fetch_assoc();

// Debugging: Log query results
error_log("Query Result: " . json_encode($orderItem));

// Check if order item exists
if (!$orderItem) {
    echo json_encode(["success" => false, "message" => "Order item not found."]);
    exit;
}

$orderItemId = $orderItem["order_item_id"];
$orderId = $orderItem["order_id"];
$paymentStatus = $orderItem["payment_status"];

// Debugging: Log payment status
error_log("Payment Status: " . $paymentStatus);

// Prevent cancellation if payment is not pending
// if ($paymentStatus !== "Pending") {
//     echo json_encode(["success" => false, "message" => "Order cannot be canceled as payment has been processed."]);
//     exit;
// }

// Start transaction
$conn->begin_transaction();

try {
    // Delete the order item
    $sqlDeleteItem = "DELETE FROM orders_items WHERE id = ?";
    $stmtDeleteItem = $conn->prepare($sqlDeleteItem);
    $stmtDeleteItem->bind_param("i", $orderItemId);
    $stmtDeleteItem->execute();

    // Check if any items remain for the order
    $sqlCheckOrder = "SELECT COUNT(*) AS remaining FROM orders_items WHERE order_id = ?";
    $stmtCheckOrder = $conn->prepare($sqlCheckOrder);
    $stmtCheckOrder->bind_param("i", $orderId);
    $stmtCheckOrder->execute();
    $resultCheckOrder = $stmtCheckOrder->get_result();
    $rowCheckOrder = $resultCheckOrder->fetch_assoc();

    if ($rowCheckOrder["remaining"] == 0) {
        // Delete associated payment record first
        $sqlDeletePayment = "DELETE FROM payments WHERE order_id = ?";
        $stmtDeletePayment = $conn->prepare($sqlDeletePayment);
        $stmtDeletePayment->bind_param("i", $orderId);
        $stmtDeletePayment->execute();

        // If no more items, delete the order itself
        $sqlDeleteOrder = "DELETE FROM orders WHERE id = ?";
        $stmtDeleteOrder = $conn->prepare($sqlDeleteOrder);
        $stmtDeleteOrder->bind_param("i", $orderId);
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






