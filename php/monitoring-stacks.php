<?php
include 'connection.php'; // ✅ Ensure connection.php is correct

header('Content-Type: application/json');

// ✅ Check if the database connection exists
if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// ✅ Fetch product stock data
$query = "SELECT productId, name, stock FROM products";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . mysqli_error($conn)]);
    exit();
}

// ✅ Process results
$products = [];
$aiWarnings = [];

while ($row = mysqli_fetch_assoc($result)) {
    $stock = intval($row['stock']);
    
    $products[] = [
        "productId" => intval($row['productId']), // ✅ Ensure numeric
        "name" => $row['name'],
        "stock" => $stock
    ];

    // ✅ AI Low Stock Warning
    if ($stock <= 5) {
        $aiWarnings[] = [
            "type" => "Warning",
            "message" => "⚠️ Low stock alert for '{$row['name']}' (Stock: $stock)."
        ];
    }
}

// ✅ Return JSON response
echo json_encode([
    "products" => $products,
    "ai" => $aiWarnings
], JSON_UNESCAPED_UNICODE);

?>


