<?php 
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost"; 
$username = "ecom_Marc306";        
$password = "OG*ED2e^2P%Atv0g";            
$dbname = "ecom_studentaccount";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Make sure to clean any previous output before sending JSON
ob_start();

// Fix SQL query
$sql = "SELECT 
            p.productId, p.name, p.image, p.price, p.typeItem, p.productDescription,
            GROUP_CONCAT(pc.productcategories) AS productcategories
        FROM products p
        LEFT JOIN productcategories pc ON p.productId = pc.productId
        GROUP BY p.productId";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    exit();
}

$products = [];

while ($row = $result->fetch_assoc()) {
    $row['productcategories'] = explode(",", $row['productcategories']); // Convert to array

    // Define the correct image directory
    $imageDirectory = "../uploadIMGProducts/";

    // Ensure image path is properly set
    if (!empty($row['image'])) {
        $row['image'] = $imageDirectory . basename($row['image']);
    }

    $products[] = $row;
}

$conn->close();

ob_end_clean(); // Clean any unexpected output
echo json_encode($products, JSON_PRETTY_PRINT);
?>



