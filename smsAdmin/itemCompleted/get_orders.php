<?php
header("Content-Type: application/json");
require_once "../config/database.php"; // Include database connection

$query = "
    SELECT 
        o.id AS order_id,
        o.student_id,
        o.total_price,
        o.payment_method,
        o.status AS order_status,
        o.created_at,
        CONCAT(s.first_name, ' ', s.last_name) AS student_name,
        p.status AS payment_status,
        COALESCE(p.receipt_url, '') AS receipt_url,
        GROUP_CONCAT(
            CONCAT(
                pr.productId, '|', pr.name, '|', oi.quantity, '|',
                COALESCE(oi.size, ''), '|', COALESCE(oi.gender, '')
            ) SEPARATOR ';'
        ) AS items
    FROM orders o
    LEFT JOIN students s ON o.student_id = s.student_id
    LEFT JOIN payments p ON o.id = p.order_id
    LEFT JOIN orders_items oi ON o.id = oi.order_id
    LEFT JOIN products pr ON oi.productId = pr.productId
    GROUP BY o.id, o.student_id, o.total_price, o.payment_method, o.status, o.created_at, s.first_name, s.last_name, p.status, p.receipt_url
    ORDER BY o.created_at DESC
";

$result = $conn->query($query);

if ($result) {
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $order = [
            "order_id" => $row["order_id"],
            "student_id" => $row["student_id"],
            "total_price" => $row["total_price"],
            "payment_method" => $row["payment_method"],
            "order_status" => $row["order_status"],
            "created_at" => $row["created_at"],
            "student_name" => $row["student_name"],
            "payment_status" => $row["payment_status"],
            "receipt_url" => $row["receipt_url"],
            "items" => explode(";", $row["items"]) // Convert items into an array
        ];
        $orders[] = $order;
    }
    echo json_encode(["success" => true, "orders" => $orders]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to retrieve orders."]);
}

$conn->close();
?>
