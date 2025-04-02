<?php
require_once __DIR__ . '/../config/database.php';

// Fetch students list with proper name formatting
try {
    $query = "SELECT student_id, CONCAT(first_name, ' ', last_name) as fullname 
              FROM students 
              ORDER BY first_name, last_name";
    $students = [];
    if ($result = $conn->query($query)) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        $result->free();
    } else {
        throw new Exception("Failed to fetch students");
    }
} catch (Exception $e) {
    error_log("Error in notifications.php: " . $e->getMessage());
    $students = [];
}

// Close database connection
if (isset($conn)) {
    $conn->close();
}
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body py-3">
                    <h2 class="card-title m-0">
                        <i class="fas fa-bell me-2 text-primary"></i>Student Notifications
                        <?php if (empty($students)): ?>
                        <small class="text-danger ms-2">
                            <i class="fas fa-exclamation-circle"></i> Error loading students
                        </small>
                        <?php endif; ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Notification Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h3 class="card-title m-0 font-weight-bold">
                <i class="fas fa-plus-circle me-2 text-success"></i>Add New Notification
            </h3>
        </div>
        <div class="card-body">
            <form id="addNotificationForm" class="needs-validation" novalidate>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="form-group mb-3">
                            <label for="student_id" class="form-label">Student ID</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <select class="form-select" id="student_id" name="student_id" required <?php echo empty($students) ? 'disabled' : ''; ?>>
                                    <option value="">Select Student</option>
                                    <?php foreach ($students as $student): 
                                        echo '<option value="'.htmlspecialchars($student['student_id']).'">'.htmlspecialchars($student['student_id'].' - '.$student['fullname']).'</option>';
                                    endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a student.</div>
                            </div>
                            <?php if (empty($students)): ?>
                            <small class="text-danger">Unable to load student list. Please refresh the page.</small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group mb-3">
                            <label for="notification_type" class="form-label">Notification Type</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">
                                    <i class="fas fa-tag"></i>
                                </span>
                                <select class="form-select" id="notification_type" name="notification_type" required <?php echo empty($students) ? 'disabled' : ''; ?>>
                                    <option value="">Select Type</option>
                                    <option value="product_update">Product Update</option>
                                    <option value="order_status">Order Status</option>
                                    <option value="reminder">Reminder</option>
                                </select>
                                <div class="invalid-feedback">Please select a notification type.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group mb-3">
                            <label for="message" class="form-label">Message</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">
                                    <i class="fas fa-comment"></i>
                                </span>
                                <textarea class="form-control" id="message" name="message" rows="4" 
                                        required minlength="10" placeholder="Enter your message (minimum 10 characters)"
                                        <?php echo empty($students) ? 'disabled' : ''; ?>></textarea>
                                <div class="invalid-feedback">Message must be at least 10 characters long.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" id="submitBtn" <?php echo empty($students) ? 'disabled' : ''; ?>>
                            <i class="fas fa-paper-plane me-2"></i>Send Notification
                            <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" id="submitSpinner">
                                <span class="visually-hidden">Loading...</span>
                            </span>
                        </button>
                        <?php if (empty($students)): ?>
                        <button type="button" class="btn btn-outline-secondary ms-2" onclick="window.location.reload()">
                            <i class="fas fa-sync-alt me-2"></i>Refresh Page
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title m-0 font-weight-bold">
                <i class="fas fa-list me-2 text-info"></i>Recent Notifications
            </h3>
            <button type="button" class="btn btn-sm btn-outline-primary" id="refreshBtn">
                <i class="fas fa-sync-alt me-1"></i>Refresh
                <span class="spinner-border spinner-border-sm ms-1 d-none" role="status" id="refreshSpinner">
                    <span class="visually-hidden">Loading...</span>
                </span>
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="notificationsTable">
                    <thead class="bg-light">
                        <tr>
                            <th>Student ID</th>
                            <th>Message</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    // Enable form validation
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Automated message generation based on notification type
    $('#notification_type').on('change', function() {
        var notificationType = $(this).val();
        var studentId = $('#student_id').val();
        var studentName = $('#student_id option:selected').text().split(" - ")[1]; // Extract the student name

        if (notificationType && studentId) {
            generateMessage(notificationType, studentName)
                .then(generatedMessage => {
                    $('#message').val(generatedMessage);  // Set the generated message in the message field
                })
                .catch(error => {
                    $('#message').val('Error: ' + error);  // Show error if something goes wrong
                });
        } else {
            $('#message').val('');  // Clear message if no notification type or student is selected
        }
    });

    // Function to generate message based on notification type using an API
    function generateMessage(type, studentName) {
        const apiKey = "AIzaSyCDi_pimz_P7z_HsEgv36A7OsL-ggNVEvI"; // Store your API key securely, avoid hardcoding it in production.

        return new Promise((resolve, reject) => {
            // Determine the endpoint and data to send based on notification type
            let apiUrl = '';
            switch (type) {
                case 'product_update':
                    apiUrl = 'https://ecommerce.schoolmanagementsystem2.com/smsAdmin/apiEndPointNotif/notification-api.php'; // Replace with your actual API endpoint
                    break;
                case 'order_status':
                    apiUrl = 'https://ecommerce.schoolmanagementsystem2.com/smsAdmin/apiEndPointNotif/notification-api.php'; // Replace with your actual API endpoint
                    break;
                case 'reminder':
                    apiUrl = 'https://ecommerce.schoolmanagementsystem2.com/smsAdmin/apiEndPointNotif/notification-api.php'; // Replace with your actual API endpoint
                    break;
                default:
                    resolve(`Hello ${studentName}, we couldn't find a specific message.`);
                    return;
            }

            // Fetch the message from the API
            fetch(apiUrl, {
                method: 'POST',  // Use POST as you're sending data (you can use GET depending on your API)
                headers: {
                    'API_KEY': "AIzaSyCDi_pimz_P7z_HsEgv36A7OsL-ggNVEvI",
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    student_name: studentName,
                    notification_type: type
                })
            })
            .then(response => response.json())
            .then(data => {
                // Assuming the API sends back a message field that contains the message body
                resolve(`Hello ${studentName}, ${data.message}`);
            })
            .catch(error => {
                console.error('Error fetching the message:', error);
                reject(`Hello ${studentName}, we encountered an error while fetching the message.`);
            });
        });
    }

    // Initialize DataTable with responsive features
    var notificationsTable = $('#notificationsTable').DataTable({
        "ajax": {
            "url": 'https://ecommerce.schoolmanagementsystem2.com/smsAdmin/functions/get_notifications.php',
            "type": "GET",
            "error": function(xhr, error, thrown) {
                console.error('DataTables Ajax error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load notifications. Please try again.'
                });
            }
        },
        "columns": [
            { "data": "student_id" },
            { "data": "message", "render": function(data) { return '<div class="text-break">' + data + '</div>'; } },
            { "data": "notification_type", "render": function(data) { return '<span class="badge bg-secondary">' + data + '</span>'; } },
            { "data": "is_read", "render": function(data) { return data == 1 ? '<span class="badge bg-success">Read</span>' : '<span class="badge bg-warning text-dark">Unread</span>'; } },
            { "data": "created_at", "render": function(data) { return new Date(data).toLocaleString(); } }
        ],
        "order": [[4, "desc"]],
        "responsive": true,
        "pageLength": 10,
        "language": {
            "emptyTable": "No notifications found",
            "zeroRecords": "No matching notifications found",
            "info": "Showing _START_ to _END_ of _TOTAL_ notifications",
            "infoEmpty": "No notifications available",
            "infoFiltered": "(filtered from _MAX_ total notifications)",
            "search": "Search notifications:",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });

    // Refresh button handler
    $('#refreshBtn').on('click', function() {
        const $btn = $(this);
        const $spinner = $('#refreshSpinner');
        
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        
        notificationsTable.ajax.reload(function() {
            $btn.prop('disabled', false);
            $spinner.addClass('d-none');
        });
    });

    // Handle form submission
    $('#addNotificationForm').on('submit', function(e) { 
        e.preventDefault();

        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        const $form = $(this);
        const formData = $form.serialize();
        console.log('Submitting Data:', formData); // Debugging

        $.ajax({
            url: 'https://ecommerce.schoolmanagementsystem2.com/smsAdmin/functions/add_notification.php',
            type: 'POST',
            data: formData,
            dataType: 'json',  
            success: function(response) {
                console.log('Server Response:', response); // Debugging log
                if (response.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Success', text: 'Notification sent successfully!' });
                    $form[0].reset();
                    $form.removeClass('was-validated');
                    notificationsTable.ajax.reload();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Failed to send notification.' });
                }
            },
            error: function(xhr) {
                console.error('AJAX error:', xhr.responseText);
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while sending the notification.' });
            }
        });
    });
});
</script>

<!-- <script>
$(document).ready(function() {
    // Enable form validation
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Automated message generation based on notification type
    $('#notification_type').on('change', function() {
        var notificationType = $(this).val();
        var studentId = $('#student_id').val();
        var studentName = $('#student_id option:selected').text().split(" - ")[1]; // Extract the student name

        if (notificationType && studentId) {
            var generatedMessage = generateMessage(notificationType, studentName);
            $('#message').val(generatedMessage);
        } else {
            $('#message').val('');
        }
    });

    // Function to generate message based on notification type
    function generateMessage(type, studentName) {
        switch (type) {
            case 'product_update':
                return `Hello ${studentName}, a new update is available for the product you were interested in! Check out the latest features.`;
            case 'order_status':
                return `Hello ${studentName}, your order has been processed and shipped! You can track it using your tracking number.`;
            case 'reminder':
                return `Hello ${studentName}, this is a reminder that your assignment is due in 2 days. Please ensure you submit it on time.`;
            default:
                return '';
        }
    }

    // Initialize DataTable with responsive features
    var notificationsTable = $('#notificationsTable').DataTable({
        "ajax": {
            "url": 'https://ecommerce.schoolmanagementsystem2.com/smsAdmin/functions/get_notifications.php',
            "type": "GET",
            "error": function(xhr, error, thrown) {
                console.error('DataTables Ajax error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load notifications. Please try again.'
                });
            }
        },
        "columns": [
            { "data": "student_id" },
            { "data": "message", "render": function(data) { return '<div class="text-break">' + data + '</div>'; } },
            { "data": "notification_type", "render": function(data) { return '<span class="badge bg-secondary">' + data + '</span>'; } },
            { "data": "is_read", "render": function(data) { return data == 1 ? '<span class="badge bg-success">Read</span>' : '<span class="badge bg-warning text-dark">Unread</span>'; } },
            { "data": "created_at", "render": function(data) { return new Date(data).toLocaleString(); } }
        ],
        "order": [[4, "desc"]],
        "responsive": true,
        "pageLength": 10,
        "language": {
            "emptyTable": "No notifications found",
            "zeroRecords": "No matching notifications found",
            "info": "Showing _START_ to _END_ of _TOTAL_ notifications",
            "infoEmpty": "No notifications available",
            "infoFiltered": "(filtered from _MAX_ total notifications)",
            "search": "Search notifications:",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });

    // Refresh button handler
    $('#refreshBtn').on('click', function() {
        const $btn = $(this);
        const $spinner = $('#refreshSpinner');
        
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        
        notificationsTable.ajax.reload(function() {
            $btn.prop('disabled', false);
            $spinner.addClass('d-none');
        });
    });

    // Handle form submission
    $('#addNotificationForm').on('submit', function(e) { 
        e.preventDefault();

        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        const $form = $(this);
        const formData = $form.serialize();
        console.log('Submitting Data:', formData); // Debugging

        $.ajax({
            url: 'https://ecommerce.schoolmanagementsystem2.com/smsAdmin/functions/add_notification.php',
            type: 'POST',
            data: formData,
            dataType: 'json',  
            success: function(response) {
                console.log('Server Response:', response); // Debugging log
                if (response.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Success', text: 'Notification sent successfully!' });
                    $form[0].reset();
                    $form.removeClass('was-validated');
                    notificationsTable.ajax.reload();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Failed to send notification.' });
                }
            },
            error: function(xhr) {
                console.error('AJAX error:', xhr.responseText);
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while sending the notification.' });
            }
        });
    });
});
</script> -->

<!-- <script>
$(document).ready(function() {
    // Enable form validation
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Initialize DataTable with responsive features
    var notificationsTable = $('#notificationsTable').DataTable({
        "ajax": {
            //"url": "http://localhost/smsEcommerce/smsAdmin/functions/get_notifications.php",
            "url": 'https://ecommerce.schoolmanagementsystem2.com/smsAdmin/functions/get_notifications.php',
            "type": "GET",
            "error": function(xhr, error, thrown) {
                console.error('DataTables Ajax error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load notifications. Please try again.'
                });
            }
        },
        "columns": [
            { "data": "student_id" },
            { 
                "data": "message",
                "render": function(data) {
                    return '<div class="text-break">' + data + '</div>';
                }
            },
            { 
                "data": "notification_type",
                "render": function(data) {
                    var typeClasses = {
                        'Product Update': 'bg-info text-white',
                        'Order Status': 'bg-primary text-white',
                        'Reminder': 'bg-warning text-dark'
                    };
                    return '<span class="badge ' + (typeClasses[data] || 'bg-secondary') + '">' + data + '</span>';
                }
            },
            { 
                "data": "is_read",
                "render": function(data) {
                    return data == 1 ? 
                        '<span class="badge bg-success">Read</span>' : 
                        '<span class="badge bg-warning text-dark">Unread</span>';
                }
            },
            { 
                "data": "created_at",
                "render": function(data) {
                    return new Date(data).toLocaleString();
                }
            }
        ],
        "order": [[4, "desc"]],
        "responsive": true,
        "pageLength": 10,
        "language": {
            "emptyTable": "No notifications found",
            "zeroRecords": "No matching notifications found",
            "info": "Showing _START_ to _END_ of _TOTAL_ notifications",
            "infoEmpty": "No notifications available",
            "infoFiltered": "(filtered from _MAX_ total notifications)",
            "search": "Search notifications:",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });

    // Refresh button handler
    $('#refreshBtn').on('click', function() {
        const $btn = $(this);
        const $spinner = $('#refreshSpinner');
        
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        
        notificationsTable.ajax.reload(function() {
            $btn.prop('disabled', false);
            $spinner.addClass('d-none');
        });
    });

    // Handle form submission
    $('#addNotificationForm').on('submit', function(e) { 
        e.preventDefault();

        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        const $form = $(this);
        const formData = $form.serialize();
        console.log('Submitting Data:', formData); // Debugging

        $.ajax({
            // url: 'http://localhost/smsEcommerce/smsAdmin/functions/add_notification.php',
            url: 'https://ecommerce.schoolmanagementsystem2.com/smsAdmin/functions/add_notification.php',
            type: 'POST',
            data: formData,
            dataType: 'json',  
            success: function(response) {
                console.log('Server Response:', response); // Debugging log
                if (response.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Success', text: 'Notification sent successfully!' });
                    $form[0].reset();
                    $form.removeClass('was-validated');
                    notificationsTable.ajax.reload();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Failed to send notification.' });
                }
            },
            error: function(xhr) {
                console.error('AJAX error:', xhr.responseText);
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while sending the notification.' });
            }
        });
    });
});

</script> -->