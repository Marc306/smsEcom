<?php
session_start();
require "connection.php"; // Ensure this connects to your database

header("Content-Type: application/json");

// Debug log file path
$debug_log_file = "debug_log.txt";

// Get student ID from session
$student_id = $_SESSION["student_id"] ?? null;

// Log session info
file_put_contents($debug_log_file, "Fetching orders for student_id: " . json_encode($_SESSION) . "\n", FILE_APPEND);

if (!$student_id) {
    file_put_contents($debug_log_file, "ERROR: User not logged in.\n", FILE_APPEND);
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

// 🔍 Verify that the student has orders
$sql_verify = "SELECT COUNT(*) AS order_count FROM orders WHERE student_id = ?";
$stmt_verify = $conn->prepare($sql_verify);
$stmt_verify->bind_param("s", $student_id);
$stmt_verify->execute();
$result_verify = $stmt_verify->get_result();
$order_check = $result_verify->fetch_assoc();

if ($order_check["order_count"] == 0) {
    file_put_contents($debug_log_file, "ERROR: No orders found for student_id: $student_id\n", FILE_APPEND);
    echo json_encode(["success" => false, "error" => "No orders found"]);
    exit;
}

// 🔍 Fetch orders
$sql_orders = "SELECT o.id, o.total_price, o.payment_method, o.status, o.created_at, 
                      COALESCE(s.schedule_date, 'Not scheduled') AS schedule_date
               FROM orders o 
               LEFT JOIN schedules s ON o.id = s.order_id 
               WHERE o.student_id = ? 
               ORDER BY o.created_at DESC";
$stmt_orders = $conn->prepare($sql_orders);
$stmt_orders->bind_param("s", $student_id);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();

$orders = [];
while ($order = $result_orders->fetch_assoc()) {
    $order_id = $order["id"];

    // 🔍 Fetch items for this order
    $sql_items = "SELECT productId, name, image, quantity, size, gender FROM orders_items WHERE order_id = ?";
    $stmt_items = $conn->prepare($sql_items);
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $result_items = $stmt_items->get_result();

    $items = [];
    while ($item = $result_items->fetch_assoc()) {
        $items[] = $item;
    }

    // 🔍 Fetch payment details
    $sql_payment = "SELECT amount, status FROM payments WHERE order_id = ?";
    $stmt_payment = $conn->prepare($sql_payment);
    $stmt_payment->bind_param("i", $order_id);
    $stmt_payment->execute();
    $result_payment = $stmt_payment->get_result();
    $payment = $result_payment->fetch_assoc() ?? ["amount" => "0.00", "status" => "Pending"];

    // Combine data
    $order["items"] = $items;
    $order["payment"] = $payment;

    $orders[] = $order;
}

// ✅ Return orders in JSON format
echo json_encode(["success" => true, "orders" => $orders]);
exit;
?>


