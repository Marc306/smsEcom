<?php 
header('Content-Type: application/json');

$servername = "localhost:3307";
$username = "root"; 
$password = ""; 
$dbname = "studentaccount";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

$sql = "SELECT 
            p.productId, p.name, p.image, p.price, p.typeItem, p.productDescription,
            GROUP_CONCAT(pc.productCategories) AS productCategories
        FROM products p
        LEFT JOIN productCategories pc ON p.productId = pc.productId
        GROUP BY p.productId;";

$result = $conn->query($sql);

$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['productCategories'] = explode(",", $row['productCategories']); // Convert CSV string to an array
        
        // Define the image directory
        $imageDirectory = "../uploadIMGProducts/";
        
        // Ensure image path is properly set
        if (!empty($row['image'])) {
            $row['image'] = $imageDirectory . basename($row['image']);
        }
        
        $products[] = $row;
    }
}

// Output JSON response
echo json_encode($products, JSON_PRETTY_PRINT);

$conn->close();
?>


