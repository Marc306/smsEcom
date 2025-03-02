<?php
header('Content-Type: application/json');

include "connection.php"; 

try {
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        throw new Exception('No data received');
    }

    // Validate required fields
    $required_fields = ['name', 'email', 'subject', 'message'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Prepare and execute the SQL query using mysqli
    $stmt = $conn->prepare("
        INSERT INTO message_chat (name, email, subject, message)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("ssss", $data['name'], $data['email'], $data['subject'], $data['message']);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Message sent successfully!'
        ]);
    } else {
        throw new Exception('Failed to send message');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
