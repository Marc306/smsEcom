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
    INSERT INTO completed_orders 
    (order_id, student_id, productId, product_name, product_image, quantity, size, gender, total_price)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
    exit();
}

foreach ($data as $order) {
    foreach ($order["items"] as $item) {
        // Ensure correct order of extracted values
        list($productId, $productName, $productImage, $quantity, $size, $gender, $productCategory) = explode("|", $item);

        // Convert empty values to NULL
        $size = empty(trim($size)) ? NULL : trim($size);
        $gender = empty(trim($gender)) ? NULL : trim($gender);

        // **If the item is a book, force size and gender to NULL**
        if (strtolower($productCategory) === "book") {
            $size = NULL;
            $gender = NULL;
        }

        // **If the item is a uniform, ensure size and gender are provided**
        if (strtolower($productCategory) === "uniform") {
            if ($size === NULL || $gender === NULL) {
                echo json_encode(["success" => false, "message" => "Uniforms require size and gender."]);
                exit();
            }
        }

        // Validate numeric fields
        $orderId = intval($order["order_id"]);
        $studentId = intval($order["student_id"]);
        $productId = intval($productId);
        $quantity = intval($quantity);
        $totalPrice = floatval($order["total_price"]);

        // Prepend image path (if necessary)
        $productImagePath = "../../uploadIMGProducts/" . basename($productImage);

        // Bind parameters
        $stmt->bind_param(
            "issssissd",  // Data types: (int, int, int, string, string, int, string (nullable), string (nullable), decimal)
            $orderId,            // int
            $studentId,          // string
            $productId,          // string
            $productName,        // string
            $productImagePath,   // string (full path)
            $quantity,           // int
            $size,               // string (nullable)
            $gender,             // string (nullable)
            $totalPrice          // decimal
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



