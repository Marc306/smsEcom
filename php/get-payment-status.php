<?php
header("Content-Type: application/json");
require_once "connection.php"; // Database connection

if (!isset($_GET['order_id'])) {
    echo json_encode(["error" => "Order ID is required"]);
    exit();
}

$order_id = intval($_GET['order_id']);

$query = $conn->prepare("SELECT status FROM payments WHERE order_id = ?");
$query->bind_param("i", $order_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["status" => $row['status']]);
} else {
    echo json_encode(["status" => "Not Found"]);
}
?>
