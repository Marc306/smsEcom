<?php
require_once '../config2/database2.php';

// Get total orders
$sql = "SELECT COUNT(*) as total FROM orders";
$result = $conn->query($sql);
$totalOrders = $result->fetch_assoc()['total'];

// Get pending payments
$sql = "SELECT COUNT(*) as pending FROM orders WHERE status = 'Pending'";
$result = $conn->query($sql);
$pendingPayments = $result->fetch_assoc()['pending'];

// Get today's pickups
$today = date('Y-m-d');
$sql = "SELECT COUNT(*) as pickups FROM orders o
        JOIN schedules s ON o.id = s.order_id
        WHERE s.schedule_date = '$today'";
$result = $conn->query($sql);
$todayPickups = $result->fetch_assoc()['pickups'];

// Get low stock products (less than 10)
$sql = "SELECT COUNT(*) as low_stock FROM products WHERE stock < 10";
$result = $conn->query($sql);
$lowStock = $result->fetch_assoc()['low_stock'];
?>

<div class="container-fluid">
    <h2 class="mb-4">Dashboard</h2>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card-counter bg-primary">
                <i class="fas fa-shopping-cart"></i>
                <span class="count-numbers"><?php echo $totalOrders; ?></span>
                <span class="count-name">Total Orders</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-counter bg-warning">
                <i class="fas fa-clock"></i>
                <span class="count-numbers"><?php echo $pendingPayments; ?></span>
                <span class="count-name">Pending Payments</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-counter bg-success">
                <i class="fas fa-calendar-check"></i>
                <span class="count-numbers"><?php echo $todayPickups; ?></span>
                <span class="count-name">Pickups</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-counter bg-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <span class="count-numbers"><?php echo $lowStock; ?></span>
                <span class="count-name">Low Stock Items</span>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Orders</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Student ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5";
                            $result = $conn->query($sql);
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$row['id']}</td>";
                                echo "<td>{$row['student_id']}</td>";
                                echo "<td>₱{$row['total_price']}</td>";
                                echo "<td>{$row['status']}</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Today's Pickup Schedule</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Student ID</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $today = date('Y-m-d');
                            $sql = "SELECT o.id, o.student_id, o.payment_method, o.status 
                                   FROM orders o
                                   JOIN schedules s ON o.id = s.order_id
                                   WHERE DATE(s.schedule_date) = CURRENT_DATE 
                                   ORDER BY s.schedule_date ASC 
                                   LIMIT 5";
                    
                            $result = $conn->query($sql);
                            
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $status_class = '';
                                    switch($row['status']) {
                                        case 'Completed':
                                            $status_class = 'text-success';
                                            break;
                                        case 'Pending':
                                            $status_class = 'text-warning';
                                            break;
                                        default:
                                            $status_class = 'text-primary';
                                    }
                                    
                                    echo "<tr>";
                                    echo "<td>{$row['id']}</td>";
                                    echo "<td>{$row['student_id']}</td>";
                                    echo "<td>{$row['payment_method']}</td>";
                                    echo "<td class='$status_class'>{$row['status']}</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No pickups scheduled for today</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
