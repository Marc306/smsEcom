<?php

// Set the content type for JSON responses
header('Content-Type: application/json');

// Sample authentication (use environment variables in production instead of hardcoding API keys)
$apiKey = getenv('AIzaSyCDi_pimz_P7z_HsEgv36A7OsL-ggNVEvI');  // Use environment variable for storing API key

// Get the headers from the request
$headers = getallheaders();
$apiKeyHeader = isset($headers['API_KEY']) ? $headers['API_KEY'] : null;

// Check if the correct API key is provided in the header
if ($apiKeyHeader !== $apiKey) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Get the raw POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the data is valid
if (isset($data['notification_type']) && isset($data['student_id']) && isset($data['message'])) {
    $notificationType = $data['notification_type'];
    $studentId = $data['student_id'];
    $message = $data['message'];

    // Depending on the notification type, perform the appropriate action
    switch ($notificationType) {
        case 'product_update':
            sendProductUpdateNotification($studentId, $message);
            break;
        case 'order_status':
            sendOrderStatusNotification($studentId, $message);
            break;
        case 'reminder':
            sendReminderNotification($studentId, $message);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Unknown notification type']);
            exit;
    }

    // Return success response
    echo json_encode(['status' => 'success', 'message' => 'Notification sent successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
}

// Functions to handle each notification type
function sendProductUpdateNotification($studentId, $message) {
    // Logic to send product update notification, e.g., save to DB or trigger external service
    error_log("Sending product update to student ID $studentId: $message");
}

function sendOrderStatusNotification($studentId, $message) {
    // Logic to send order status notification, e.g., save to DB or trigger external service
    error_log("Sending order status to student ID $studentId: $message");
}

function sendReminderNotification($studentId, $message) {
    // Logic to send reminder notification, e.g., save to DB or trigger external service
    error_log("Sending reminder to student ID $studentId: $message");
}

?>

