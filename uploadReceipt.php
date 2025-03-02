<?php 
session_start();

// Ensure student is logged in
if (!isset($_SESSION['student_id'])) {
    die("<script>alert('Session expired. Please log in again.'); window.location.href='login.php';</script>");
}

$student_id = $_SESSION['student_id'];
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "studentaccount";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the request is a POST request and a file is uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['receipt_image'])) {
    $file = $_FILES['receipt_image'];
    $target_dir = "uploadReceiptPic/";

    // Ensure the directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = time() . "_" . basename($file["name"]);
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate the uploaded file
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        echo "<script>alert('File is not an image.'); window.history.back();</script>";
        $uploadOk = 0;
    }

    // Check file size (limit: 5MB)
    if ($file["size"] > 5000000) {
        echo "<script>alert('File size too large (Max: 5MB).'); window.history.back();</script>";
        $uploadOk = 0;
    }

    // Allow only JPG, JPEG, PNG
    if (!in_array($imageFileType, ["jpg", "jpeg", "png"])) {
        echo "<script>alert('Only JPG, JPEG & PNG files allowed.'); window.history.back();</script>";
        $uploadOk = 0;
    }

    // Proceed with upload if validation passes
    if ($uploadOk == 1) {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            // Find the latest unpaid Gcash payment order for the student
            $stmt = $conn->prepare("
                SELECT p.id 
                FROM payments p
                JOIN orders o ON p.order_id = o.id
                WHERE o.student_id = ? 
                AND o.payment_method = 'Gcash Payment' 
                AND p.status = 'Pending'
                ORDER BY p.id DESC 
                LIMIT 1
            ");
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $stmt->bind_result($payment_id);
            $stmt->fetch();
            $stmt->close();

            // Check if a pending payment exists
            if ($payment_id) {
                // Update the payments table with the receipt URL
                $stmt = $conn->prepare("UPDATE payments SET receipt_url = ? WHERE id = ?");
                $stmt->bind_param("si", $target_file, $payment_id);
                $stmt->execute();

                // Check if the update was successful
                if ($stmt->affected_rows > 0) {
                    error_log("Receipt successfully saved for payment ID: $payment_id, URL: $target_file");
                    echo "<script>alert('Receipt uploaded successfully!'); window.location.href='user-profile.php';</script>";
                } else {
                    error_log("Error: Receipt upload did not update any rows for payment ID: $payment_id");
                    echo "<script>alert('No record updated. Ensure order exists and is pending.'); window.history.back();</script>";
                }                
                $stmt->close();
            } else {
                echo "<script>alert('No pending Gcash payment found.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Error uploading file.'); window.history.back();</script>";
        }
    }
}

$conn->close();
?>




