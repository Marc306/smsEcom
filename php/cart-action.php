<?php
include "connection.php"; 
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['student_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$student_id = $_SESSION['student_id'];
$data = json_decode(file_get_contents("php://input"), true);

// Debugging
error_log("Received JSON Data: " . json_encode($data));

if (isset($data['action'])) {
    if (!isset($data['productId']) || empty($data['productId'])) {
        echo json_encode(["error" => "Invalid product ID"]);
        exit;
    }

    $product_id = strval($data['productId']); // Convert to string

    if ($data['action'] === 'delete') {
        $stmt = $conn->prepare("DELETE FROM cart WHERE student_id = ? AND productId = ?");
        $stmt->bind_param("ss", $student_id, $product_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["error" => "Failed to delete item"]);
        }
        $stmt->close();
    } 
    elseif ($data['action'] === 'update_size') {
        $new_size = trim($data['newSize']);
        $allowed_sizes = ["Small", "Medium", "Large", "XL", "2XL", "3XL", "4XL", "5XL"];
        
        if (!in_array($new_size, $allowed_sizes)) {
            echo json_encode(["error" => "Invalid size"]);
            exit;
        }

        $stmt = $conn->prepare("UPDATE cart SET size = ? WHERE student_id = ? AND productId = ?");
        $stmt->bind_param("sss", $new_size, $student_id, $product_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["error" => "Failed to update size"]);
        }
        $stmt->close();
    }
}

$conn->close();
?>





