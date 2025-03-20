<?php 
require_once '../config/database.php';

// Handle product operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $response = ['success' => false];

    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add':
                    $productId = $conn->real_escape_string($_POST['productId']);
                    $name = $conn->real_escape_string($_POST['name']);
                    $typeItem = $conn->real_escape_string($_POST['typeItem']);
                    $price = floatval($_POST['price']);
                    $stock = intval($_POST['stock']);
                    $productDescription = isset($_POST['productDescription']) ? $conn->real_escape_string($_POST['productDescription']) : '';
                
                    // Define the correct upload directory
                    $productImageDir = "../../uploadIMGProducts/";
                
                    // Ensure the directory exists
                    if (!is_dir($productImageDir)) {
                        mkdir($productImageDir, 0777, true);
                    }
                
                    // Process image upload
                    $newImagePath = "";
                    if (!empty($_FILES['productImage']['name'])) {
                        $imageFileName = basename($_FILES['productImage']['name']);
                        $targetFilePath = $productImageDir . $imageFileName;
                        $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
                
                        // Validate file type (Allow only JPG, PNG, JPEG)
                        $allowedTypes = ['jpg', 'jpeg', 'png'];
                        if (!in_array($imageFileType, $allowedTypes)) {
                            throw new Exception("Invalid image file type. Only JPG, JPEG, and PNG allowed.");
                        }
                
                        // Move uploaded file to the upload directory
                        if (move_uploaded_file($_FILES['productImage']['tmp_name'], $targetFilePath)) {
                            $newImagePath = $targetFilePath; // Save the new image path
                        } else {
                            throw new Exception("Error uploading product image.");
                        }
                    } else {
                        throw new Exception("No image uploaded.");
                    }
                
                    // Start transaction
                    $conn->begin_transaction();
                    try {
                        // Insert product details into products table
                        $sql = "INSERT INTO ecom_studentaccount.products (productId, name, productDescription, price, stock, image, typeItem) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sssddss", $productId, $name, $productDescription, $price, $stock, $newImagePath, $typeItem);
                
                        if (!$stmt->execute()) {
                            throw new Exception("Failed to insert product: " . $stmt->error);
                        }
                
                        // Insert selected categories into productCategories table
                        if (!empty($_POST['categories']) && is_array($_POST['categories'])) {
                            $sql = "INSERT INTO ecom_studentaccount.productcategories (productId, productcategories) VALUES (?, ?)";
                            $stmt = $conn->prepare($sql);
                
                            foreach ($_POST['categories'] as $category) {
                                $stmt->bind_param("ss", $productId, $category);
                                if (!$stmt->execute()) {
                                    throw new Exception("Failed to insert product category: " . $stmt->error);
                                }
                            }
                        } else {
                            throw new Exception("Please select at least one year level category.");
                        }
                
                        // Commit the transaction
                        $conn->commit();
                        $response = ['success' => true, 'message' => 'Product added successfully with image uploaded.'];
                    } catch (Exception $e) {
                        $conn->rollback();
                        $response['error'] = $e->getMessage();
                    }
                    break;                                                              

                case 'delete':
                    $productId = $conn->real_escape_string($_POST['productId']);
                    
                    // Begin transaction
                    $conn->begin_transaction();
                    
                    try {
                        // Delete from productCategories first (due to foreign key)
                        $sql = "DELETE FROM productcategories WHERE productId = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $productId);
                        
                        if (!$stmt->execute()) {
                            throw new Exception("Failed to delete product category: " . $stmt->error);
                        }
                        
                        // Then delete from products
                        $sql = "DELETE FROM products WHERE productId = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $productId);
                        
                        if (!$stmt->execute()) {
                            throw new Exception("Failed to delete product: " . $stmt->error);
                        }
                        
                        $conn->commit();
                        $response = ['success' => true, 'message' => 'Product deleted successfully'];
                    } catch (Exception $e) {
                        $conn->rollback();
                        throw $e;
                    }
                    break;

                case 'update_stock':
                    $productId = $conn->real_escape_string($_POST['productId']);
                    $stock = intval($_POST['stock']);
                    
                    $sql = "UPDATE products SET stock = ? WHERE productId = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $stock, $productId);
                    
                    if ($stmt->execute()) {
                        $response = ['success' => true, 'message' => 'Stock updated successfully'];
                    } else {
                        throw new Exception("Failed to update stock: " . $stmt->error);
                    }
                    break;
            }
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'error' => $e->getMessage()];
    }
    
    echo json_encode($response);
    exit;
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Product Management</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="fas fa-plus"></i> Add New Product
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Categories</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT p.productId, p.image, p.name, p.typeItem, p.productDescription,
                            GROUP_CONCAT(pc.productcategories SEPARATOR ', ') as categories, 
                            p.price, p.stock 
                            FROM ecom_studentaccount.products p
                            LEFT JOIN ecom_studentaccount.productcategories pc ON p.productId = pc.productId
                            GROUP BY p.productId
                            ORDER BY p.typeItem, p.name";

                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $imagePath = "../../uploadIMGProducts/" . $row['image']; // Corrected path

                            echo "<tr>";
                            echo "<td>{$row['productId']}</td>";
                            echo "<td><img src='{$imagePath}' alt='Product Image' width='50' height='50' style='object-fit:cover;'></td>";
                            echo "<td>{$row['name']}</td>";
                            echo "<td>{$row['typeItem']}</td>";
                            echo "<td>{$row['categories']}</td>";
                            echo "<td>" . htmlspecialchars($row['productDescription']) . "</td>";
                            echo "<td>₱" . number_format($row['price'], 2) . "</td>";
                            echo "<td>
                                    <input type='number' class='form-control form-control-sm stock-input' 
                                        value='{$row['stock']}' data-id='{$row['productId']}' style='width: 80px'>
                                </td>";
                            echo "<td>
                                    <button class='btn btn-danger btn-sm delete-product' data-id='{$row['productId']}'>
                                        <i class='fas fa-trash'></i>
                                    </button>
                                </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center'>No products found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Product ID</label>
                        <input type="text" class="form-control" name="productId" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type Item</label>
                        <select class="form-control" name="typeItem" id="typeItem" required>
                            <option value="uniform">Uniform</option>
                            <option value="books">Books</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Categories (Year Levels)</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="categories[]" value="freshman" id="freshman">
                            <label class="form-check-label" for="freshman">Freshman</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="categories[]" value="sophomore" id="sophomore">
                            <label class="form-check-label" for="sophomore">Sophomore</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="categories[]" value="junior" id="junior">
                            <label class="form-check-label" for="junior">Junior</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="categories[]" value="senior" id="senior">
                            <label class="form-check-label" for="senior">Senior</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="productDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" class="form-control" name="productImage" accept="image/*" required>
                        <img id="productImagePreview" style="max-width:100px; max-height:100px; display:none;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" class="form-control" name="price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" class="form-control" name="stock" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveProduct">Save Product</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle product deletion
    $('.delete-product').click(function() {
        const productId = $(this).data('id');
        if (confirm('Are you sure you want to delete this product?')) {
            $.ajax({
                // url: 'http://localhost/smsEcommerce/smsAdmin/pages/products.php',
                url: 'https://ecommerce.schoolmanagementsystem2.com/smsAdmin/pages/products.php',
                type: 'POST',
                data: {
                    action: 'delete',
                    productId: productId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.error || 'Failed to delete product');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('Failed to delete product. Please try again.');
                }
            });
        }
    });

    // Handle stock updates
    $('.stock-input').change(function() {
        const productId = $(this).data('id');
        const stock = $(this).val();
        
        $.ajax({
            // url: 'http://localhost/smsEcommerce/smsAdmin/pages/products.php',
            url: 'https://ecommerce.schoolmanagementsystem2.com/smsAdmin/pages/products.php',
            type: 'POST',
            data: {
                action: 'update_stock',
                productId: productId,
                stock: stock
            },
            dataType: 'json',
            success: function(response) {
                if (!response.success) {
                    alert(response.error || 'Failed to update stock');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Failed to update stock. Please try again.');
            }
        });
    });

    // Handle image preview and processing
    function handleImageUpload(input, previewElement) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    // Create canvas for resizing
                    const canvas = document.createElement('canvas');
                    canvas.width = 100;
                    canvas.height = 100;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, 100, 100);
                    
                    // Convert to JPEG with 75% quality
                    const resizedImage = canvas.toDataURL('image/jpeg', 0.75);
                    previewElement.src = resizedImage;
                    previewElement.style.display = 'block';
                    input.dataset.base64 = resizedImage;
                }
                img.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }

    // Handle image uploads
    $('input[name="productImage"]').change(function() {
        handleImageUpload(this, document.getElementById('productImagePreview'));
    });

    $('#saveProduct').click(function() {
        const form = $('#addProductForm')[0];

        // Check if form is valid
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Check if at least one category is selected
        if ($('input[name="categories[]"]:checked').length === 0) {
            alert('Please select at least one year level category');
            return;
        }

        // Create FormData object
        const formData = new FormData(form);
        formData.append("action", "add");

        // Validate product image upload
        const productImageInput = $('input[name="productImage"]')[0];
        if (!productImageInput.files.length) {
            alert('Please select a product image');
            return;
        }

        // Submit the form using AJAX
        $.ajax({
            // url: 'http://localhost/smsEcommerce/smsAdmin/pages/products.php', // Change to actual upload script
            url: 'https://ecommerce.schoolmanagementsystem2.com/smsAdmin/pages/products.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,  // Important for file uploads
            contentType: false,  // Important for file uploads
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.error || 'Failed to add product');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                alert('Failed to add product. Check console for details.');
            }
        });
    });

});
</script>

