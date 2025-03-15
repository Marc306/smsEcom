<?php
session_start();
header("Content-Type: application/json");

// Enable error reporting (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
// $conn = new mysqli("localhost:3307", "root", "", "studentaccount");
$conn = new mysqli("localhost", "ecom_Marc306", "OG*ED2e^2P%Atv0g", "ecom_studentaccount");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Validate session
if (!isset($_SESSION['student_id']) || empty($_SESSION['student_id'])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized access - No session found"]);
    exit;
}

$student_id = $_SESSION['student_id'];
$data = json_decode(file_get_contents("php://input"), true);
$action = $_GET['action'] ?? ($data['action'] ?? '');

// Get Cart Items
if ($action === 'getCart') {
    $stmt = $conn->prepare("SELECT * FROM cart WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $cart = [];
    while ($row = $result->fetch_assoc()) {
        $cart[] = $row;
    }
    $stmt->close();
    
    echo json_encode(["success" => true, "cartItems" => $cart]);
    exit;
}

// Add to Cart
if ($action === 'add') {
    $productId = $data['productId'] ?? null;
    $quantity = $data['quantity'] ?? 1;
    $gender = $data['gender'] ?? null;
    $size = $data['size'] ?? null;

    if (!$productId) {
        echo json_encode(["success" => false, "message" => "Invalid product ID"]);
        exit;
    }

    // Check if product exists
    $productCheck = $conn->prepare("SELECT productId FROM products WHERE productId = ?");
    $productCheck->bind_param("s", $productId);
    $productCheck->execute();
    $result = $productCheck->get_result();

    if (!$result->fetch_assoc()) {
        echo json_encode(["success" => false, "message" => "Product does not exist"]);
        exit;
    }
    $productCheck->close();

    // Check if product already exists in the cart
    $cartCheck = $conn->prepare("SELECT * FROM cart WHERE student_id = ? AND productId = ? AND (gender = ? OR gender IS NULL) AND (size = ? OR size IS NULL)");
    $cartCheck->bind_param("ssss", $student_id, $productId, $gender, $size);
    $cartCheck->execute();
    $cartResult = $cartCheck->get_result();

    if ($cartResult->fetch_assoc()) {
        echo json_encode(["success" => false, "message" => "Product is already in the cart"]);
        exit;
    }
    $cartCheck->close();

    // Insert into cart (New Product)
    $stmt = $conn->prepare("INSERT INTO cart (student_id, productId, gender, size, quantity) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $student_id, $productId, $gender, $size, $quantity);
    $success = $stmt->execute();

    if (!$success) {
        echo json_encode(["success" => false, "message" => "Error adding to cart: " . $stmt->error]);
    } else {
        echo json_encode(["success" => true, "message" => "Item added to cart"]);
    }
    
    $stmt->close();
}

$conn->close();
?>




