<?php 
header("Content-Type: application/json");
require_once "../config/database.php"; // Include database connection

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid JSON data."]);
    exit();
}

// Prepare SQL statement (Remove `completed_at`)
$stmt = $conn->prepare("
    INSERT INTO completed_orders (order_id, student_id, productId, quantity, total_price)
    VALUES (?, ?, ?, ?, ?)
");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
    exit();
}

foreach ($data as $order) {
    foreach ($order["items"] as $item) {
        list($productId, $productName, $quantity, $size, $gender) = explode("|", $item);

        $stmt->bind_param(
            "issid", // Fixed parameter types
            $order["order_id"],
            $order["student_id"],
            $productId,
            $quantity,
            $order["total_price"]
        );

        if (!$stmt->execute()) {
            echo json_encode(["success" => false, "message" => "Insert failed: " . $stmt->error]);
            exit();
        }
    }
}

echo json_encode(["success" => true, "message" => "Orders successfully stored."]);
$stmt->close();
$conn->close();
?>

