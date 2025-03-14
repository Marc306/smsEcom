<?php
header("Content-Type: application/json");
require_once "connection.php"; // Database connection

$API_KEY = "AIzaSyBJtSKSLL800qyF0y92pzlEOa53R1F5f54"; // Replace with your actual AI API key

// Function to get an available time slot with fewer than 100 students
function getAvailableSlot($conn) {
    $query = "SELECT schedule_date, COUNT(*) as total FROM schedules WHERE status = 'Scheduled' GROUP BY schedule_date HAVING total < 100 ORDER BY schedule_date ASC LIMIT 1";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['schedule_date'];
    }
    
    return null; // No available slot
}

// Function to assign a schedule
function createSchedule($conn, $order_id, $student_id) {
    $availableSlot = getAvailableSlot($conn);

    // If no available slot, assign the next available day
    if (!$availableSlot) {
        $availableSlot = date('Y-m-d H:i:s', strtotime("+2 day 09:00:00"));
    }

    // Insert into schedules table
    $stmt = $conn->prepare("INSERT INTO schedules (order_id, student_id, schedule_date) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $order_id, $student_id, $availableSlot);
    $stmt->execute();
    $stmt->close();

    return ["success" => true, "message" => "Pickup scheduled on $availableSlot"];
}

// Handle request
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['order_id']) || !isset($data['student_id']) || !isset($data['payment_method'])) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit();
}

$order_id = $data['order_id'];
$student_id = $data['student_id'];
$payment_method = $data['payment_method'];

// Check payment status for Gcash & Kasunduan
if ($payment_method === "Gcash Payment" || $payment_method === "Kasunduan") {
    $paymentQuery = $conn->prepare("SELECT status FROM payments WHERE order_id = ?");
    $paymentQuery->bind_param("i", $order_id);
    $paymentQuery->execute();
    $paymentResult = $paymentQuery->get_result();
    $paymentRow = $paymentResult->fetch_assoc();
    
    if (!$paymentRow || $paymentRow['status'] !== 'Confirmed') {
        echo json_encode(["success" => false, "message" => "Payment confirmation required"]);
        exit();
    }
}

// Assign a schedule
$response = createSchedule($conn, $order_id, $student_id);
echo json_encode($response);
?>
