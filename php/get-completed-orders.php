<?php
header('Content-Type: application/json');
include "connection.php";

try {
    $conn = new PDO("mysql:host=$servername;dbname=completeOrders", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Join with products table to get product details
    $stmt = $conn->prepare("
        SELECT co.*, p.product_name, p.product_image 
        FROM completed_orders co
        LEFT JOIN products p ON co.product_id = p.id
        WHERE co.student_id = :student_id
        ORDER BY co.completed_at DESC
    ");
    
    $student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : 0;
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'orders' => $orders]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn = null;
?>
