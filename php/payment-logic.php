<?php  
session_start();
require "connection.php";

header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug Log File
$debug_log_file = "debug_log.txt";
file_put_contents($debug_log_file, "=== CHECKOUT DEBUG LOG ===\n", FILE_APPEND);

// Capture session data
$student_id = $_SESSION["student_id"] ?? null;
file_put_contents($debug_log_file, "🟢 Session student_id: " . json_encode($_SESSION) . "\n", FILE_APPEND);

if (!$student_id) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

// Read JSON input
$raw_input = file_get_contents("php://input");
$data = json_decode($raw_input, true);

// Debug raw request
file_put_contents($debug_log_file, "🔵 Raw JSON Input: " . $raw_input . "\n", FILE_APPEND);
file_put_contents($debug_log_file, "🟡 Decoded Request: " . print_r($data, true) . "\n", FILE_APPEND);

// Extract payment method
$payment_method = trim($data["payment_method"] ?? ($data["product"]["payment_method"] ?? ""));

// Debug extracted payment method
file_put_contents($debug_log_file, "🟣 Extracted Payment Method: " . $payment_method . "\n", FILE_APPEND);

// Allowed payment methods
$valid_methods = ["Kasunduan", "Walk-In Payment", "Gcash Payment"];

// Validate payment method
if (!in_array($payment_method, $valid_methods, true)) {
    file_put_contents($debug_log_file, "❌ Invalid Payment Method Detected: " . $payment_method . "\n", FILE_APPEND);
    echo json_encode(["success" => false, "error" => "Invalid payment method."]);
    exit;
}

// Verify student exists in database
$sql_verify_student = "SELECT student_id FROM students WHERE student_id = ?";
$stmt_verify_student = $conn->prepare($sql_verify_student);
$stmt_verify_student->bind_param("s", $student_id);
$stmt_verify_student->execute();
$result_verify_student = $stmt_verify_student->get_result();

if ($result_verify_student->num_rows === 0) {
    echo json_encode(["success" => false, "error" => "Invalid student ID."]);
    exit;
}

// Extract additional data
$buy_now = $data["type"] === "buy_now";
$product_id = $data["product"]["productId"] ?? null;
$quantity = $data["product"]["quantity"] ?? 1;
$size = isset($data["product"]["size"]) ? $data["product"]["size"] : null;
$gender = isset($data["product"]["gender"]) ? $data["product"]["gender"] : null;

// Fetch product details and category
if ($buy_now && $product_id) {
    $sql_product = "SELECT productId, name, image, price, typeItem FROM products WHERE productId = ?";
    $stmt_product = $conn->prepare($sql_product);
    $stmt_product->bind_param("s", $product_id);
    $stmt_product->execute();
    $result_product = $stmt_product->get_result();

    if ($result_product->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "Product not found."]);
        exit;
    }

    $product = $result_product->fetch_assoc();
    $total_price = $product["price"] * $quantity;

    // Ensure size and gender are NULL if the item is a book
    if (strtolower($product["typeItem"]) === "book") {
        $size = null;
        $gender = null;
    }

    $cart_items = [[
        "productId" => $product_id,
        "name" => $product["name"],
        "image" => $product["image"],
        "price" => $product["price"],
        "quantity" => $quantity,
        "size" => $size,
        "gender" => $gender
    ]];
} else {
    // Fetch cart items if not buy now
    $sql_cart = "SELECT c.productId, p.name, p.image, p.price, c.quantity, 
                        NULLIF(c.size, '') AS size, 
                        NULLIF(c.gender, '') AS gender, 
                        p.typeItem 
                 FROM cart c 
                 JOIN products p ON c.productId = p.productId 
                 WHERE c.student_id = ?";
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->bind_param("s", $student_id);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();

    $cart_items = [];
    $total_price = 0;
    while ($row = $result_cart->fetch_assoc()) {
        // Ensure size and gender are NULL if the item is a book
        if (strtolower($row["typeItem"]) === "book") {
            $row["size"] = null;
            $row["gender"] = null;
        }

        $cart_items[] = $row;
        $total_price += $row["price"] * $row["quantity"];
    }

    if (empty($cart_items)) {
        echo json_encode(["success" => false, "error" => "Your cart is empty."]);
        exit;
    }
}

// Start Transaction
$conn->begin_transaction();

try {
    // Insert order into orders table
    $sql_order = "INSERT INTO orders (student_id, total_price, payment_method, status) VALUES (?, ?, ?, 'To Pay')";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("sds", $student_id, $total_price, $payment_method);
    $stmt_order->execute();
    $order_id = $stmt_order->insert_id;

    // Insert items into orders_items table
    $sql_item = "INSERT INTO orders_items (order_id, productId, name, image, quantity, size, gender) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);

    foreach ($cart_items as $item) {
        $stmt_item->bind_param("isssiss", $order_id, $item["productId"], $item["name"], $item["image"], $item["quantity"], $item["size"], $item["gender"]);
        $stmt_item->execute();
    }

    // Insert payment record
    $sql_payment = "INSERT INTO payments (order_id, amount, status) VALUES (?, ?, 'Pending')";
    $stmt_payment = $conn->prepare($sql_payment);
    $stmt_payment->bind_param("id", $order_id, $total_price);
    $stmt_payment->execute();

    // Insert schedule based on payment method
    if ($payment_method === "Kasunduan" || $payment_method === "Gcash Payment") {
        $sql_schedule = "INSERT INTO schedules (order_id, student_id, schedule_date, status) VALUES (?, ?, NULL, 'Pending')";
    } else {
        $pickup_date = date("Y-m-d");
        $sql_schedule = "INSERT INTO schedules (order_id, student_id, schedule_date, status) VALUES (?, ?, ?, 'Scheduled')";
    }

    $stmt_schedule = $conn->prepare($sql_schedule);
    $stmt_schedule->bind_param($payment_method === "Walk-In Payment" ? "iss" : "is", $order_id, $student_id, $pickup_date);
    $stmt_schedule->execute();

    // Clear cart if checkout was from cart
    if (!$buy_now) {
        $sql_clear_cart = "DELETE FROM cart WHERE student_id = ?";
        $stmt_clear_cart = $conn->prepare($sql_clear_cart);
        $stmt_clear_cart->bind_param("s", $student_id);
        $stmt_clear_cart->execute();
    }

    // Commit transaction
    $conn->commit();

    file_put_contents($debug_log_file, "✅ Order Placed Successfully - Order ID: $order_id\n", FILE_APPEND);
    echo json_encode(["success" => true, "message" => "Order placed successfully!", "order_id" => $order_id]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "error" => "Transaction failed."]);
    file_put_contents($debug_log_file, "❌ Transaction Failed: " . $e->getMessage() . "\n", FILE_APPEND);
}
?>














