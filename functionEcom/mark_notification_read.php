<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost:3307";
$username = "root"; 
$password = ""; 
$dbname = "studentaccount";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => "Database connection failed: " . $conn->connect_error]));
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    http_response_code(401);
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['notification_id']) || !is_numeric($input['notification_id'])) {
    http_response_code(400);
    die(json_encode(['status' => 'error', 'message' => 'Invalid notification ID']));
}

$notification_id = intval($input['notification_id']);
$student_id = $_SESSION['student_id'];

try {
    // Debugging: Log values to PHP error log
    error_log("Student ID: " . $student_id);
    error_log("Notification ID: " . $notification_id);

    // Check if the notification exists
    $check_query = "SELECT is_read FROM notifications WHERE id = ? AND student_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param('is', $notification_id, $student_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        die(json_encode(['status' => 'error', 'message' => 'Notification not found']));
    }

    $notification = $result->fetch_assoc();
    if ($notification['is_read'] == 1) {
        echo json_encode(['status' => 'success', 'message' => 'Notification was already marked as read']);
        exit;
    }

    // Update notification status
    $update_query = "UPDATE notifications SET is_read = 1 WHERE id = ? AND student_id = ?";
    $update_stmt = $conn->prepare($update_query);
    if (!$update_stmt) {
        throw new Exception("Database error: " . $conn->error);
    }

    $update_stmt->bind_param('is', $notification_id, $student_id);
    $update_stmt->execute();

    if ($update_stmt->affected_rows === 0) {
        http_response_code(500);
        die(json_encode(['status' => 'error', 'message' => 'Failed to update notification']));
    }

    // Get updated unread count
    $count_query = "SELECT COUNT(*) as count FROM notifications WHERE student_id = ? AND is_read = 0";
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->bind_param('s', $student_id);
    $count_stmt->execute();
    $unread_count = $count_stmt->get_result()->fetch_assoc()['count'];

    echo json_encode([
        'status' => 'success',
        'message' => 'Notification marked as read',
        'unread_count' => $unread_count
    ]);

} catch (Exception $e) {
    error_log("Error in mark_notification_read.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    if (isset($check_stmt)) $check_stmt->close();
    if (isset($update_stmt)) $update_stmt->close();
    if (isset($count_stmt)) $count_stmt->close();
    if (isset($conn)) $conn->close();
}
?>




