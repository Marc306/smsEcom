<?php
require_once '../config2/database2.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    $response = ['success' => false];
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        $response['error'] = 'Invalid order ID';
        echo json_encode($response);
        exit;
    }

    try {
        switch ($_POST['action']) {
            case 'mark_to_receive':
                $conn->begin_transaction();
                
                // Update order status to 'To Receive'
                $stmt = $conn->prepare("UPDATE orders SET status = 'To Receive' WHERE id = ? AND status = 'To Pay'");
                if (!$stmt) {
                    throw new Exception($conn->error);
                }
                
                $stmt->bind_param("i", $id);
                if (!$stmt->execute()) {
                    throw new Exception($stmt->error);
                }

                if ($stmt->affected_rows > 0) {
                    $conn->commit();
                    $response = ['success' => true, 'message' => 'Order marked as To Receive'];
                } else {
                    throw new Exception("Order not found or cannot be marked as To Receive");
                }
                $stmt->close();
                break;

            case 'confirm_payment':
                $conn->begin_transaction();
                
                // Update order status to 'Completed'
                $stmt = $conn->prepare("UPDATE orders SET status = 'Completed' WHERE id = ?");
                if (!$stmt) {
                    throw new Exception($conn->error);
                }
                
                $stmt->bind_param("i", $id);
                if (!$stmt->execute()) {
                    throw new Exception($stmt->error);
                }
                
                // Update payment status to 'Confirmed'
                $stmt = $conn->prepare("UPDATE payments SET status = 'Confirmed' WHERE order_id = ?");
                if (!$stmt) {
                    throw new Exception($conn->error);
                }
                
                $stmt->bind_param("i", $id);
                if (!$stmt->execute()) {
                    throw new Exception($stmt->error);
                }

                if ($stmt->affected_rows >= 0) {
                    $conn->commit();
                    $response = ['success' => true, 'message' => 'Payment confirmed and order completed'];
                } else {
                    throw new Exception("Order not found or cannot be marked as completed");
                }
                $stmt->close();
                break;

            case 'assign_pickup':
                $date = $conn->real_escape_string($_POST['pickup_date']);

                $conn->begin_transaction();

                try {
                    // Get student_id from the order
                    $stmt = $conn->prepare("SELECT student_id FROM orders WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $student_id = $row['student_id'];

                        // Check if a schedule already exists
                        $stmt = $conn->prepare("SELECT id FROM schedules WHERE order_id = ? AND student_id = ?");
                        $stmt->bind_param("is", $id, $student_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            // Update existing schedule
                            $stmt = $conn->prepare("UPDATE schedules SET schedule_date = ?, status = 'Scheduled' WHERE order_id = ? AND student_id = ?");
                            $stmt->bind_param("sis", $date, $id, $student_id);
                        } else {
                            // Insert new schedule
                            $stmt = $conn->prepare("INSERT INTO schedules (schedule_date, order_id, student_id, status) VALUES (?, ?, ?, 'Scheduled')");
                            $stmt->bind_param("sis", $date, $id, $student_id);
                        }

                        if (!$stmt->execute()) {
                            throw new Exception("Error updating/inserting schedule: " . $stmt->error);
                        }

                        // Update order status to 'To Receive'
                        $stmt = $conn->prepare("UPDATE orders SET status = 'To Receive' WHERE id = ? AND status = 'To Pay'");
                        $stmt->bind_param("i", $id);
                        if (!$stmt->execute()) {
                            throw new Exception("Error updating order status: " . $stmt->error);
                        }

                        // Count booked slots
                        $stmt = $conn->prepare("SELECT COUNT(*) AS booked FROM schedules WHERE schedule_date = ?");
                        $stmt->bind_param("s", $date);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        $booked_slots = $row['booked'];

                        if ($booked_slots <= 100) {
                            $conn->commit();
                            $response = ['success' => true, 'slot' => $booked_slots];
                        } else {
                            throw new Exception('Date is fully booked');
                        }
                    } else {
                        throw new Exception('Order not found');
                    }
                } catch (Exception $e) {
                    $conn->rollback();
                    $response = ['success' => false, 'message' => $e->getMessage()];
                }
                break;

            case 'suggest_dates':
                $dates = [];

                for ($i = 1; $i <= 7; $i++) {
                    $date = date('Y-m-d', strtotime("+$i day"));

                    // Get the current number of bookings for the date
                    $stmt = $conn->prepare("SELECT COUNT(*) AS booked FROM schedules WHERE schedule_date = ?");
                    $stmt->bind_param("s", $date);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $booked = $result->fetch_assoc()['booked'];

                    // Calculate available slots
                    $available = 100 - $booked;
                    if ($available > 0) {
                        $dates[] = [
                            'date' => $date,
                            'available' => $available,
                            'score' => $available / 100
                        ];
                    }
                }

                // Sort by score (higher availability is prioritized)
                usort($dates, function($a, $b) {
                    return $b['score'] - $a['score'];
                });

                $response = ['success' => true, 'dates' => $dates];
                break;
        }
    } catch (Exception $e) {
        if (isset($conn) && $conn->connect_errno == 0) {
            $conn->rollback();
        }
        $response = ['success' => false, 'error' => $e->getMessage()];
    }

    echo json_encode($response);
    exit;
}

// Get all orders with student details and payment info
$query = "
    SELECT 
        o.id,
        o.student_id,
        o.total_price,
        o.payment_method,
        o.status as order_status,
        o.created_at,
        CONCAT(s.first_name, ' ', s.last_name) as student_name,
        p.status as payment_status,
        COALESCE(p.receipt_url, '') as receipt_url,
        GROUP_CONCAT(
            CONCAT(
                pr.name, 
                ' (', oi.quantity, ')', 
                CASE 
                    WHEN oi.size IS NOT NULL THEN CONCAT(' Size: ', oi.size)
                    ELSE ''
                END,
                CASE 
                    WHEN oi.gender IS NOT NULL THEN CONCAT(' Gender: ', oi.gender)
                    ELSE ''
                END
            ) SEPARATOR ', '
        ) as items
    FROM orders o
    LEFT JOIN students s ON o.student_id = s.student_id
    LEFT JOIN payments p ON o.id = p.order_id
    LEFT JOIN orders_items oi ON o.id = oi.order_id
    LEFT JOIN products pr ON oi.productId = pr.productId
    GROUP BY o.id, o.student_id, o.total_price, o.payment_method, o.status, o.created_at, s.first_name, s.last_name, p.status, p.receipt_url
    ORDER BY o.created_at DESC
";

$result = $conn->query($query);
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-shopping-cart me-2"></i>Order Management</h2>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control" id="orderSearch" placeholder="Search orders...">
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Order ID</th>
                            <th>Student Code</th>
                            <th>Student Name</th>
                            <th>Items</th>
                            <th>Total Price</th>
                            <th>Payment Method</th>
                            <th>Order Status</th>
                            <th>Payment Status</th>
                            <th>Created At</th>
                            <th>Receipt</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        // Determine status badge colors
                        $orderStatusClass = '';
                        switch($row['order_status']) {
                            case 'To Pay':
                                $orderStatusClass = 'bg-warning';
                                break;
                            case 'To Receive':
                                $orderStatusClass = 'bg-info';
                                break;
                            case 'Completed':
                                $orderStatusClass = 'bg-success';
                                break;
                        }
                        
                        echo "<tr>";
                        echo "<td class='text-center'>{$row['id']}</td>";
                        echo "<td>{$row['student_id']}</td>";
                        echo "<td>{$row['student_name']}</td>";
                        echo "<td>{$row['items']}</td>";
                        echo "<td>₱" . number_format($row['total_price'], 2) . "</td>";
                        echo "<td>{$row['payment_method']}</td>";
                        echo "<td><span class='badge {$orderStatusClass}'>{$row['order_status']}</span></td>";
                        if ($row['payment_status']) {
                            $paymentStatusClass = '';
                            switch($row['payment_status']) {
                                case 'Pending':
                                    $paymentStatusClass = 'bg-warning';
                                    break;
                                case 'Confirmed':
                                    $paymentStatusClass = 'bg-success';
                                    break;
                                default:
                                    $paymentStatusClass = 'bg-secondary';
                            }
                            echo "<td><span class='badge {$paymentStatusClass}'>{$row['payment_status']}</span></td>";
                        } else {
                            echo "<td>-</td>";
                        }
                        echo "<td>" . date('Y-m-d H:i:s', strtotime($row['created_at'])) . "</td>";
                        
                        // Receipt column
                        echo "<td>";
                        
                        if ($row['payment_method'] === 'Gcash Payment') {
                            if (!empty($row['receipt_url'])) {
                                echo '<a href="https://ecommerce.schoolmanagementsystem2.com/smsadmin2/download-receipt2.php?order_id=' . $row['id'] . '" class="btn btn-sm btn-primary">Download Receipt</a>';
                            } else {
                                echo '<span class="text-muted">No receipt uploaded</span>';
                            }
                        } else {
                            echo "<span class='text-muted'>-</span>";
                        }
                        echo "</td>";
                        
                        echo "<td>";
                        
                        // Show Confirm Payment button if payment is pending
                        if ($row['payment_status'] === 'Pending') {
                            echo "<button class='btn btn-sm btn-success me-2' onclick='confirmPayment({$row['id']})'>Confirm Payment</button>";
                        }
                        
                        // Show Assign Pickup button for To Pay and To Receive orders
                        if ($row['order_status'] === 'To Pay' || $row['order_status'] === 'To Receive') {
                            echo "<button class='btn btn-sm btn-info' onclick='showPickupModal({$row['id']})'>Assign Pickup</button>";
                        }
                        
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Pickup Date Modal -->
<div class="modal fade" id="pickupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Pickup Date</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="pickupDateAlert" class="alert alert-info d-none">
                    Loading suggested dates...
                </div>
                <div class="mb-3">
                    <label for="pickupDate" class="form-label">Select Pickup Date</label>
                    <input type="date" class="form-control" id="pickupDate" min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                </div>
                <div id="slotInfo" class="alert alert-info d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="assignPickup()">Assign Date</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add table search functionality
    $("#orderSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});

let currentOrderId = null;

function showPickupModal(orderId) {
    currentOrderId = orderId;
    const modal = new bootstrap.Modal(document.getElementById('pickupModal'));
    modal.show();
    
    // Show suggested dates
    const alertDiv = document.getElementById('pickupDateAlert');
    alertDiv.classList.remove('d-none');
    
    // fetch('http://localhost/smsEcommerce/smsAdmin/pages/orders.php', {
    //     method: 'POST',
    //     headers: {
    //         'Content-Type': 'application/x-www-form-urlencoded',
    //     },
    //     body: `action=suggest_dates&id=${orderId}`
    // })
    fetch('https://ecommerce.schoolmanagementsystem2.com/smsadmin2/pages2/orders2.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=suggest_dates&id=${orderId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.dates.length > 0) {
            let message = 'Suggested pickup dates:\n';
            data.dates.forEach(date => {
                message += `${date.date} (${date.available} slots available)\n`;
            });
            alertDiv.textContent = message;
        } else {
            alertDiv.textContent = 'No available dates found';
        }
    })
    .catch(error => {
        alertDiv.classList.add('alert-danger');
        alertDiv.textContent = 'Error loading suggested dates';
    });
}

// function assignPickup() {
//     const date = document.getElementById('pickupDate').value;
//     if (!date) {
//         alert('Please select a date');
//         return;
//     }
    
//     fetch('/smsEcommerce/smsAdmin/pages/orders.php', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/x-www-form-urlencoded',
//         },
//         body: `action=assign_pickup&id=${currentOrderId}&pickup_date=${date}`
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             const slotInfo = document.getElementById('slotInfo');
//             slotInfo.textContent = `Pickup scheduled! Slot #${data.slot}`;
//             slotInfo.classList.remove('d-none');
//             setTimeout(() => {
//                 const modal = bootstrap.Modal.getInstance(document.getElementById('pickupModal'));
//                 modal.hide();
//                 location.reload();
//             }, 2000);
//         } else {
//             alert(data.message || 'Error assigning pickup date');
//         }
//     })
//     .catch(error => {
//         alert('Error assigning pickup date');
//     });
// }
function assignPickup() {
    const date = document.getElementById('pickupDate').value;
    if (!date) {
        alert('Please select a date');
        return;
    }

    // fetch('http://localhost/smsEcommerce/smsAdmin/pages/orders.php', {
    //     method: 'POST',
    //     headers: {
    //         'Content-Type': 'application/x-www-form-urlencoded',
    //     },
    //     body: `action=assign_pickup&id=${currentOrderId}&pickup_date=${date}`
    // })
    fetch('https://ecommerce.schoolmanagementsystem2.com/smsadmin2/pages2/orders2.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=assign_pickup&id=${currentOrderId}&pickup_date=${date}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const slotInfo = document.getElementById('slotInfo');
            slotInfo.textContent = `Pickup scheduled!`;
            slotInfo.classList.remove('d-none');
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('pickupModal'));
                modal.hide();
                location.reload();
            }, 2000);
        } else {
            alert(data.error || 'Error assigning pickup date');
        }
    })
    .catch(error => {
        alert('Error assigning pickup date');
    });
}

function markToReceive(orderId) {
    if (!confirm('Are you sure you want to mark this order as received?')) {
        return;
    }
    
    // fetch('http://localhost/smsEcommerce/smsAdmin/pages/orders.php', {
    //     method: 'POST',
    //     headers: {
    //         'Content-Type': 'application/x-www-form-urlencoded',
    //     },
    //     body: `action=mark_to_receive&id=${orderId}`
    // })
    fetch('https://ecommerce.schoolmanagementsystem2.com/smsadmin2/pages2/orders2.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=mark_to_receive&id=${orderId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error marking order as received');
        }
    })
    .catch(error => {
        alert('Error marking order as received');
    });
}

function confirmPayment(orderId) {
    if (!confirm('Are you sure you want to confirm payment for this order?')) {
        return;
    }
    
    // fetch('http://localhost/smsEcommerce/smsAdmin/pages/orders.php', {
    //     method: 'POST',
    //     headers: {
    //         'Content-Type': 'application/x-www-form-urlencoded',
    //     },
    //     body: `action=confirm_payment&id=${orderId}`
    // })
    fetch('https://ecommerce.schoolmanagementsystem2.com/smsadmin2/pages2/orders2.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=confirm_payment&id=${orderId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Find the row
            const row = document.querySelector(`tr:has(button[onclick="confirmPayment(${orderId})"])`);
            if (row) {
                // Update order status
                const orderStatusCell = row.querySelector('td:nth-child(7)');
                if (orderStatusCell) {
                    orderStatusCell.innerHTML = '<span class="badge bg-success">Completed</span>';
                }
                
                // Update payment status
                const paymentStatusCell = row.querySelector('td:nth-child(8)');
                if (paymentStatusCell) {
                    paymentStatusCell.innerHTML = '<span class="badge bg-success">Confirmed</span>';
                }
                
                // Remove action buttons
                const actionCell = row.querySelector('td:last-child');
                if (actionCell) {
                    actionCell.innerHTML = '<span class="text-success">Order Completed</span>';
                }

                fetchAndStoreCompletedOrders();
            }
        } else {
            alert(data.error || 'Error confirming payment');
        }
    })
    .catch(error => {
        alert('Error confirming payment');
    });
}

function fetchAndStoreCompletedOrders() { 
    // if (!confirm('Are you sure you want to fetch and store completed orders?')) {
    //     return;
    // }

    fetch('https://ecommerce.schoolmanagementsystem2.com/smsadmin2/itemCompleted2/get_orders2.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || 'Error fetching orders.');
            return;
        }

        return fetch('https://ecommerce.schoolmanagementsystem2.com/smsadmin2/itemCompleted2/store_completed_orders2.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data.orders)
        });
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Completed orders successfully stored.');
        } else {
            alert(result.message || 'Error storing completed orders.');
        }
    })
    .catch(error => {
        alert('Error processing request.');
    });
}
</script>