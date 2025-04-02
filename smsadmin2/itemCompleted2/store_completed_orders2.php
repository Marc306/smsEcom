<?php 
header("Content-Type: application/json");
require_once "../config2/database2.php"; // Include database connection

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
        $itemParts = explode("|", $item);

        // Ensure correct number of values in itemParts array
        if (count($itemParts) < 6) { // Now expecting only 6 values
            echo json_encode(["success" => false, "message" => "Invalid item format: " . $item]);
            exit();
        }

        list($productId, $productName, $productImage, $quantity, $size, $gender) = $itemParts;

        // Convert empty values to NULL
        $size = empty(trim($size)) ? NULL : trim($size);
        $gender = empty(trim($gender)) ? NULL : trim($gender);

        // Assumption: If both size and gender are NULL, it's likely a book
        if ($size === NULL && $gender === NULL) {
            // Book: No size or gender
        } elseif ($size !== NULL && $gender !== NULL) {
            // Uniform: Must have both size and gender
        } else {
            echo json_encode(["success" => false, "message" => "Invalid size or gender format for item: " . $productName]);
            exit();
        }

        // Validate numeric fields
        $orderId = intval($order["order_id"]); // order_id is INT
        $studentId = $order["student_id"]; // VARCHAR
        $productId = $productId; // VARCHAR
        $quantity = intval($quantity); // INT
        $totalPrice = floatval($order["total_price"]); // FLOAT

        // Verify that student_id exists in students table
        $checkStudent = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
        $checkStudent->bind_param("s", $studentId);
        $checkStudent->execute();
        $result = $checkStudent->get_result();

        if ($result->num_rows == 0) {
            echo json_encode(["success" => false, "message" => "Error: Student ID " . $studentId . " does not exist."]);
            exit();
        }
        $checkStudent->close();

        // Prepend image path (if necessary)
        $productImagePath = "../../uploadIMGProducts/" . basename($productImage);

        // Bind parameters
        $stmt->bind_param(
            "issssissd",  // Correct data types
            $orderId,            // int
            $studentId,          // string (VARCHAR)
            $productId,          // string (VARCHAR)
            $productName,        // string
            $productImagePath,   // string
            $quantity,           // int
            $size,               // string (nullable)
            $gender,             // string (nullable)
            $totalPrice          // double
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



