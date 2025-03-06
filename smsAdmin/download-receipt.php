<?php
require_once 'config/database.php';

if (!isset($_GET['order_id'])) {
    die('Order ID not provided');
}

$order_id = intval($_GET['order_id']);

// Get the receipt URL from the database
$stmt = $conn->prepare("SELECT receipt_url FROM payments WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Receipt not found');
}

$row = $result->fetch_assoc();
$receipt_path = dirname(__DIR__) . '/' . $row['receipt_url'];

// Debug output
error_log('Receipt path: ' . $receipt_path);

// Check if file exists
if (!file_exists($receipt_path)) {
    die('Receipt file not found: ' . $receipt_path);
}

// Get file information
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $receipt_path);
finfo_close($finfo);

// Set headers for download
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="receipt_' . $order_id . '.' . pathinfo($receipt_path, PATHINFO_EXTENSION) . '"');
header('Content-Length: ' . filesize($receipt_path));

// Output file
readfile($receipt_path);
exit;
