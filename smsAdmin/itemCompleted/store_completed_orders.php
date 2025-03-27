<?php 
header("Content-Type: application/json");
require_once "../config/database.php"; // Include database connection

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid JSON data."]);
    exit();
}

// Prepare SQL statement
$stmt = $conn->prepare("
    INSERT INTO completed_orders (order_id, student_id, productId, product_name, product_image, quantity, size, gender, total_price)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
    exit();
}

foreach ($data as $order) {
    foreach ($order["items"] as $item) {
        // Ensure correct order of extracted values
        list($productId, $productName, $productImage, $quantity, $size, $gender) = explode("|", $item);

        // Prepend image path (if necessary)
        $productImagePath = "../../uploadIMGProducts/" . $productImage;

        // Bind parameters
        $stmt->bind_param(
            "issssissd",  // Data types: (int, string, string, string, string, int, string, string, decimal)
            $order["order_id"],      // int
            $order["student_id"],    // string
            $productId,              // string
            $productName,            // string
            $productImagePath,       // string (full path)
            $quantity,               // int
            $size,                   // string (nullable)
            $gender,                 // string (nullable)
            $order["total_price"]     // decimal
        );

        // Execute statement
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


