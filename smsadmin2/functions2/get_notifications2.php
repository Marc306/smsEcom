<?php
require_once __DIR__ . '/../config/database2.php';

header('Content-Type: application/json');

try {
    // Get notifications with student names
    $query = "SELECT n.*, CONCAT(s.first_name, ' ', s.last_name) as student_name
           FROM notifications n
           JOIN students s ON n.student_id = s.student_id 
           ORDER BY n.created_at DESC";
           
    $result = $conn->query($query);
    
    if ($result) {
        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            // Format student display
            $row['student_id'] = htmlspecialchars($row['student_id'] . ' - ' . $row['student_name']);
            $row['message'] = htmlspecialchars($row['message']);
            
            // Format notification type to be more readable
            $type_map = [
                'product_update' => 'Product Update',
                'order_status' => 'Order Status',
                'reminder' => 'Reminder'
            ];
            $row['notification_type'] = $type_map[$row['notification_type']] ?? ucfirst(str_replace('_', ' ', $row['notification_type']));
            
            $notifications[] = $row;
        }
        echo json_encode(['data' => $notifications]);
    } else {
        throw new Exception("Failed to fetch notifications");
    }
} catch (Exception $e) {
    // Log error for debugging
    error_log("Error in get_notifications.php: " . $e->getMessage());
    
    // Return empty data array with error message
    echo json_encode([
        'data' => [],
        'error' => 'Failed to load notifications. Please try again.'
    ]);
}
?>
