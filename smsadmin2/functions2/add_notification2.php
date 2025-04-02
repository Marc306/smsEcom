<?php
require_once __DIR__ . '/../config/database2.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    // Ensure the database connection is valid
    if (!$conn) {
        throw new Exception('Database connection failed.');
    }

    // Validate and sanitize inputs
    if (!isset($_POST['student_id'], $_POST['message'], $_POST['notification_type'])) {
        throw new Exception('All fields are required.');
    }

    $student_id = trim($_POST['student_id']);
    $message = trim($_POST['message']);
    $notification_type = trim($_POST['notification_type']);

    // Validate required fields
    if (empty($student_id) || empty($message) || empty($notification_type)) {
        throw new Exception('All fields are required.');
    }

    // Validate message length
    if (strlen($message) < 10) {
        throw new Exception('Message must be at least 10 characters long.');
    }

    // Validate notification type
    $valid_types = ['product_update', 'order_status', 'reminder'];
    if (!in_array($notification_type, $valid_types)) {
        throw new Exception('Please select a valid notification type.');
    }

    // Validate student existence using prepared statement
    $stmt = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }

    $stmt->bind_param('s', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Invalid student ID selected.');
    }
    $stmt->close();

    // Insert notification using prepared statement
    $stmt = $conn->prepare("INSERT INTO notifications (student_id, message, notification_type) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }

    $stmt->bind_param('sss', $student_id, $message, $notification_type);

    if (!$stmt->execute()) {
        throw new Exception('Failed to send notification: ' . $stmt->error);
    }

    // Ensure no previous output before JSON response
    ob_clean();

    echo json_encode([
        'status' => 'success',
        'message' => 'Notification sent successfully.'
    ]);

} catch (Exception $e) {
    // Ensure no previous output before JSON response
    ob_clean();
    
    // Log error for debugging
    error_log("Error in add_notification.php: " . $e->getMessage());

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

