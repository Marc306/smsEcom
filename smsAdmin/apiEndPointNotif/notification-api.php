<?php
//AIzaSyCDi_pimz_P7z_HsEgv36A7OsL-ggNVEvI
// Set the content type for JSON responses
header('Content-Type: application/json');

// Get the API key from an environment variable (don't hardcode in production)
//$apiKey = "AIzaSyCDi_pimz_P7z_HsEgv36A7OsL-ggNVEvI";  // Fetch the API key from environment variables
$apiKey = "AIzaSyCDi_pimz_P7z_HsEgv36A7OsL-ggNVEvI";

// Get the headers from the request
$headers = getallheaders();
$apiKeyHeader = isset($headers['API_KEY']) ? $headers['API_KEY'] : null;  // Retrieve the API key from request header

echo json_encode([
    'received_api_key' => $apiKeyHeader,
    'expected_api_key' => $apiKey
]);

// Check if the correct API key is provided in the header
if ($apiKeyHeader !== $apiKey) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}


$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['notification_type']) && isset($data['student_id']) && isset($data['message'])) {
    $notificationType = $data['notification_type'];
    $studentId = $data['student_id'];
    $message = $data['message'];

    switch ($notificationType) {
        case 'product_update':
            $returnMessage = sendProductUpdateNotification($studentId, $message);
            break;
        case 'order_status':
            $returnMessage = sendOrderStatusNotification($studentId, $message);
            break;
        case 'reminder':
            $returnMessage = sendReminderNotification($studentId, $message);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Unknown notification type']);
            exit;
    }

    echo json_encode(['status' => 'success', 'message' => $returnMessage]); //returns message from function.
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
}

function sendProductUpdateNotification($studentId, $message) {
    error_log("Sending product update to student ID $studentId: $message");
    return "Product Update Sent"; // example message.
}

function sendOrderStatusNotification($studentId, $message) {
    error_log("Sending order status to student ID $studentId: $message");
    return "Order Status Sent"; // example message.
}

function sendReminderNotification($studentId, $message) {
    error_log("Sending reminder to student ID $studentId: $message");
    return "Reminder Sent"; // example message.
}

?>

