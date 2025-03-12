<?php
header('Content-Type: application/json');

$servername = "localhost:3307";
$username = "root"; 
$password = ""; 
$dbname = "studentaccount";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'status' => 'error',
        'message' => "Database connection failed: " . $conn->connect_error
    ]));
}

// Validate session
if (!session_id()) {
    session_start();
}
if (!isset($_SESSION['student_id'])) {
    http_response_code(401);
    die(json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]));
}

// Ensure student_id is provided and matches session
if (!isset($_GET['student_id']) || $_GET['student_id'] !== $_SESSION['student_id']) {
    http_response_code(400);
    die(json_encode([
        'status' => 'error',
        'message' => 'Invalid student ID'
    ]));
}

$student_id = $_GET['student_id'];

try {
    // Get notifications for the specific student
    $query = "SELECT * FROM notifications 
              WHERE student_id = ? 
              ORDER BY created_at DESC 
              LIMIT 50"; // Limit to prevent excessive data transfer
              
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }

    $stmt->bind_param('s', $student_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to fetch notifications: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $notifications = [];

    while ($row = $result->fetch_assoc()) {
        // Format notification type to be more readable
        $type_map = [
            'product_update' => 'Product Update',
            'order_status' => 'Order Status',
            'reminder' => 'Reminder'
        ];
        $row['notification_type'] = $type_map[$row['notification_type']] ?? ucfirst(str_replace('_', ' ', $row['notification_type']));
        $row['message'] = htmlspecialchars($row['message']);
        $notifications[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'data' => $notifications,
        'unread_count' => count(array_filter($notifications, function($n) { return $n['is_read'] == 0; }))
    ]);

} catch (Exception $e) {
    error_log("Error in get_user_notifications.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>
