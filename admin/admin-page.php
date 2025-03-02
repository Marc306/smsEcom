<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/admin-page.css">
    <title>Admin Panel</title>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="#dashboard">Dashboard</a></li>
            <li><a href="#orders">Orders</a></li>
            <li><a href="#schedule">Pickup Schedule</a></li>
            <li><a href="#products">Products</a></li>
            <li><a href="#students">Students</a></li>
            <li><a href="#notifications">Notifications</a></li>
        </ul>
    </div>
    <div class="main-content">
        <header>
            <h1>Admin Dashboard</h1>
        </header>

        <section id="orders" class="section hidden">
            <h2>Orders Management</h2>
            <div class="table-container">
                <table>
                    <tr>
                        <th>Order ID</th>
                        <th>Student Name & ID</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                        <td>1001</td>
                        <td>John Doe (S12345)</td>
                        <td>2 Items</td>
                        <td>To Pay</td>
                        <td><button class="view-order" data-details='{"items": [{"product": "Uniform", "size": "M", "gender": "Male", "price": 25}, {"product": "Shoes", "size": "10", "gender": "Male", "price": 50}]}'>View</button></td>
                    </tr>
                </table>
            </div>
        </section>
        <div class="modal-overlay"></div>
        <div class="modal">
            <h2>Order Details</h2>
            <div id="order-details"></div>
            <button onclick="document.querySelector('.modal').style.display='none'; document.querySelector('.modal-overlay').style.display='none';">Close</button>
        </div>

        <section id="schedule" class="section hidden">
            <h2>Pickup Schedule</h2>
            <div class="table-container">
                <table>
                    <tr>
                        <th>Schedule ID</th>
                        <th>Student Name & ID</th>
                        <th>Pickup Date</th>
                        <th>Items</th>
                    </tr>
                    <tr>
                        <td>5001</td>
                        <td>John Doe (S12345)</td>
                        <td>March 5, 2025</td>
                        <td>Uniform (Size: M), Shoes (Size: 10)</td>
                    </tr>
                </table>
            </div>
        </section>
        
        <section id="products" class="section hidden">
            <h2>Product Management</h2>
            <button>Add Product</button>
            <div class="table-container">
                <table>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                        <td>3001</td>
                        <td>Uniform</td>
                        <td>50</td>
                        <td><button>Edit</button></td>
                    </tr>
                </table>
            </div>
        </section>
    </div>

    <script src="script/admin-sidebar.js"></script>
</body>
</html>


