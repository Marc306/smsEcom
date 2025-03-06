<?php
require_once 'connection.php';
header('Content-Type: application/json');

if (!isset($_FILES['receipt_url']) || !isset($_POST['order_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Missing required data',
        'debug' => ['files' => $_FILES, 'post' => $_POST]
    ]);
    exit;
}

$order_id = $_POST['order_id'];
$file = $_FILES['receipt_url'];

try {
    // Generate a unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'receipt_' . time() . '_' . $order_id . '.' . $extension;
    $upload_path = '../uploads/receipts/' . $filename;
    
    // Create directory if it doesn't exist
    if (!file_exists('../uploads/receipts/')) {
        mkdir('../uploads/receipts/', 0777, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // Store the relative path in database
        $relative_path = 'uploads/receipts/' . $filename;
        
        $stmt = $conn->prepare("UPDATE payments SET receipt_url = ? WHERE order_id = ?");
        $stmt->bind_param("si", $relative_path, $order_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Receipt uploaded successfully']);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to update database',
                'error' => $stmt->error
            ]);
        }
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to upload file',
            'error' => error_get_last()
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error uploading receipt',
        'error' => $e->getMessage()
    ]);
}
