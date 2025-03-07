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
                    $productDescription = $_POST['productDescription'];
                    $price = floatval($_POST['price']);
                    $stock = intval($_POST['stock']);
                    $typeItem = $conn->real_escape_string($_POST['typeItem']);
                    $image = $_POST['image']; // Store image data
                    
                    // Begin transaction
                    $conn->begin_transaction();
                    
                    try {
                        // Insert into products table
                        $sql = "INSERT INTO products (productId, name, productDescription, price, stock, image) 
                                VALUES (?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sssdds", $productId, $name, $productDescription, $price, $stock, $image);
                        
                        if (!$stmt->execute()) {
                            throw new Exception("Failed to insert product: " . $stmt->error);
                        }
                        
                        // Insert into productcategories table
                        $sql = "INSERT INTO productcategories (productId, productcategories) VALUES (?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ss", $productId, $typeItem);
                        
                        if (!$stmt->execute()) {
                            throw new Exception("Failed to insert product category: " . $stmt->error);
                        }
                        
                        $conn->commit();
                        $response = ['success' => true, 'message' => 'Product added successfully'];
                    } catch (Exception $e) {
                        $conn->rollback();
                        throw $e;
                    }
                    break;

                case 'delete':
                    $productId = $conn->real_escape_string($_POST['productId']);
                    
                    // Begin transaction
                    $conn->begin_transaction();
                    
                    try {
                        // Delete from productcategories first (due to foreign key)
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
                        <th>Type Item</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT p.productId, p.image, p.name, pc.productcategories, p.price, p.stock 
                        FROM products p
                        LEFT JOIN productcategories pc ON p.productId = pc.productId
                        ORDER BY pc.productcategories, p.name";
            
                $result = $conn->query($sql);
            
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['productId']}</td>";
                        echo "<td><img src='../assets/images/{$row['image']}' alt='Product Image' width='50'></td>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td>{$row['productcategories']}</td>";
                        echo "<td>₱{$row['price']}</td>";
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
                    echo "<tr><td colspan='7' class='text-center'>No products found</td></tr>";
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
                <form id="addProductForm">
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
                    <div class="mb-3" id="textDescription">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="productDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3" id="sizeChartUpload" style="display:none;">
                        <label class="form-label">Size Chart</label>
                        <input type="file" class="form-control" name="sizeChart" accept="image/*">
                        <div class="mt-2">
                            <img id="sizeChartPreview" src="" style="max-width:100px; max-height:100px; display:none;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" class="form-control" name="productImage" accept="image/*" required>
                        <div class="mt-2">
                            <img id="productImagePreview" src="" style="max-width:100px; max-height:100px; display:none;">
                        </div>
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
                url: '/smsEcommerce/smsAdmin/pages/products.php',
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
            url: '/smsEcommerce/smsAdmin/pages/products.php',
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

    // Toggle description/size chart based on product type
    $('#typeItem').change(function() {
        const type = $(this).val();
        if (type === 'uniform') {
            $('#textDescription').hide();
            $('#sizeChartUpload').show();
            $('textarea[name="productDescription"]').prop('required', false);
            $('input[name="sizeChart"]').prop('required', true);
        } else {
            $('#textDescription').show();
            $('#sizeChartUpload').hide();
            $('textarea[name="productDescription"]').prop('required', true);
            $('input[name="sizeChart"]').prop('required', false);
        }
    });

    // Handle image uploads
    $('input[name="productImage"]').change(function() {
        handleImageUpload(this, document.getElementById('productImagePreview'));
    });

    $('input[name="sizeChart"]').change(function() {
        handleImageUpload(this, document.getElementById('sizeChartPreview'));
    });

    // Handle form submission
    $('#saveProduct').click(function() {
        const form = $('#addProductForm')[0];
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const type = $('#typeItem').val();
        
        // Handle description based on type
        if (type === 'uniform') {
            const sizeChartInput = $('input[name="sizeChart"]')[0];
            if (sizeChartInput.dataset.base64) {
                formData.set('productDescription', sizeChartInput.dataset.base64);
            }
        }

        // Add product image
        const productImageInput = $('input[name="productImage"]')[0];
        if (productImageInput.dataset.base64) {
            formData.set('image', productImageInput.dataset.base64);
        }

        $.ajax({
            url: '/smsEcommerce/smsAdmin/pages/products.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Product added successfully');
                    location.reload();
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function() {
                alert('Error adding product');
            }
        });
    });
});
</script>
