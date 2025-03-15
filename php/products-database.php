<?php 
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// $servername = "localhost:3307";
// $username = "root"; 
// $password = ""; 
// $dbname = "studentaccount";

$servername = "localhost"; // Change if your database is hosted elsewhere
$username = "ecom_Marc306";        // Default XAMPP username is 'root'
$password = "OG*ED2e^2P%Atv0g";            // Default XAMPP password is empty
$dbname = "ecom_studentaccount"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

$sql = "SELECT 
            p.productId, p.name, p.image, p.price, p.typeItem, p.productDescription,
            GROUP_CONCAT(pc.productcategories) AS productcategories
        FROM products p
        LEFT JOIN productcategories pc ON p.productId = pc.productId
        GROUP BY p.productId;";

$result = $conn->query($sql);

$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['productcategories'] = explode(",", $row['productcategories']); // Convert CSV string to an array
        
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


