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

try {
    // Create notifications table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(50) NOT NULL,
        notification_type VARCHAR(50) NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(student_id)
    )";

    if (!$conn->query($sql)) {
        throw new Exception("Error creating table: " . $conn->error);
    }

    // Insert some test notifications if none exist
    $check_sql = "SELECT COUNT(*) as count FROM notifications";
    $result = $conn->query($check_sql);
    if (!$result) {
        throw new Exception("Error checking notifications: " . $conn->error);
    }
    
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        // Get a random student ID
        $student_sql = "SELECT student_id FROM students LIMIT 1";
        $student_result = $conn->query($student_sql);
        
        if (!$student_result) {
            throw new Exception("Error getting student: " . $conn->error);
        }
        
        if ($student_row = $student_result->fetch_assoc()) {
            $student_id = $student_row['student_id'];
            
            // Insert test notifications
            $notifications = [
                [
                    'type' => 'product_update',
                    'message' => 'New uniform items are now available in the store!'
                ],
                [
                    'type' => 'order_status',
                    'message' => 'Your order has been confirmed and is being processed.'
                ],
                [
                    'type' => 'reminder',
                    'message' => 'Don\'t forget to complete your purchase of items in your cart.'
                ]
            ];
            
            $insert_sql = "INSERT INTO notifications (student_id, notification_type, message) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            
            if (!$stmt) {
                throw new Exception("Error preparing insert: " . $conn->error);
            }
            
            foreach ($notifications as $notification) {
                $stmt->bind_param('sss', $student_id, $notification['type'], $notification['message']);
                if (!$stmt->execute()) {
                    throw new Exception("Error inserting notification: " . $stmt->error);
                }
            }
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Notifications table created and test data added successfully'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'success',
            'message' => 'Notifications table exists and contains data'
        ]);
    }

} catch (Exception $e) {
    error_log("Error in create_notifications_table.php: " . $e->getMessage());
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
