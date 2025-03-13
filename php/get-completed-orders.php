<?php
header('Content-Type: application/json');
include "connection.php";

try {
    $conn = new PDO("mysql:host=$servername;dbname=studentaccount", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch completed orders with product details
    $stmt = $conn->prepare("
        SELECT co.id, co.order_id, co.student_id, s.name AS student_name, 
               co.productId, p.product_name, p.product_image, 
               co.quantity, co.total_price, co.completed_at
        FROM completed_orders co
        JOIN students s ON co.student_id = s.student_id
        JOIN products p ON co.productId = p.productId
        WHERE co.student_id = :student_id
        ORDER BY co.completed_at DESC
    ");
    
    session_start();
    $student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : 0;
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'orders' => $orders]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn = null;
?>

