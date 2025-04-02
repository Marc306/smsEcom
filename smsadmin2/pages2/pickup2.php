<?php
require_once '../config2/database2.php';

// Get date range for calendar
$start_date = date('Y-m-d');
$end_date = date('Y-m-d', strtotime('+30 days'));

// Get all pickup schedules grouped by date
$sql = "SELECT s.schedule_date as date, COUNT(*) as booked_slots
        FROM schedules s
        JOIN orders o ON s.order_id = o.id
        WHERE s.schedule_date BETWEEN '$start_date' AND '$end_date'
        GROUP BY s.schedule_date
        ORDER BY s.schedule_date";
$result = $conn->query($sql);

$schedule_data = [];
while($row = $result->fetch_assoc()) {
    $schedule_data[$row['date']] = $row['booked_slots'];
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Pickup Schedule Management</h2>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Calendar View</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Booked Slots</th>
                                    <th>Available Slots</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $current = $start_date;
                                while (strtotime($current) <= strtotime($end_date)) {
                                    $booked = $schedule_data[$current] ?? 0;
                                    $available = 100 - $booked;
                                    $status_class = '';
                                    $status_text = '';
                                    
                                    if ($booked >= 100) {
                                        $status_class = 'bg-danger text-white';
                                        $status_text = 'Full';
                                    } elseif ($booked >= 80) {
                                        $status_class = 'bg-warning';
                                        $status_text = 'Almost Full';
                                    } else {
                                        $status_class = 'bg-success text-white';
                                        $status_text = 'Available';
                                    }
                                    
                                    echo "<tr>";
                                    echo "<td>" . date('Y-m-d (D)', strtotime($current)) . "</td>";
                                    echo "<td>$booked</td>";
                                    echo "<td>$available</td>";
                                    echo "<td class='$status_class'>$status_text</td>";
                                    echo "</tr>";
                                    
                                    $current = date('Y-m-d', strtotime($current . ' +1 day'));
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Today's Pickup List</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Student ID</th>
                                <th>Payment</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $today = date('Y-m-d'); 

                        $sql = "SELECT o.id, o.student_id, o.payment_method, o.status, s.schedule_date 
                                FROM orders o
                                JOIN schedules s ON o.id = s.order_id
                                WHERE s.schedule_date = '$today'
                                ORDER BY s.schedule_date";

                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
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
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Statistics</h5>
                </div>
                <div class="card-body">
                <?php
                // Get today's date
                $today = date('Y-m-d'); 

                // Get today's pickup count
                $sql = "SELECT COUNT(*) as total 
                        FROM orders o
                        JOIN schedules s ON o.id = s.order_id
                        WHERE s.schedule_date = '$today'";

                $result = $conn->query($sql);
                $today_total = $result->fetch_assoc()['total'];

                // Get tomorrow's date
                $tomorrow = date('Y-m-d', strtotime('+1 day'));

                // Get tomorrow's pickup count
                $sql = "SELECT COUNT(*) as total 
                        FROM orders o
                        JOIN schedules s ON o.id = s.order_id
                        WHERE s.schedule_date = '$tomorrow'";

                $result = $conn->query($sql);
                $tomorrow_total = $result->fetch_assoc()['total'];

                // Get completed pickups for today
                $sql = "SELECT COUNT(*) as completed 
                        FROM orders o
                        JOIN schedules s ON o.id = s.order_id
                        WHERE s.schedule_date = '$today' AND o.status = 'Completed'";

                $result = $conn->query($sql);
                $completed_total = $result->fetch_assoc()['completed'];
                ?>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Today's Pickups:</span>
                        <span class="badge bg-primary"><?php echo $today_total; ?>/100</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tomorrow's Bookings:</span>
                        <span class="badge bg-info"><?php echo $tomorrow_total; ?>/100</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Completed Today:</span>
                        <span class="badge bg-success"><?php echo $completed_total; ?>/<?php echo $today_total; ?></span>
                    </div>
                    
                    <div class="progress mt-3">
                        <div class="progress-bar" role="progressbar" 
                             style="width: <?php echo $today_total; ?>%" 
                             aria-valuenow="<?php echo $today_total; ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <?php echo $today_total; ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
