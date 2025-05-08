<?php
session_start();

// Database connection
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
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['student_id'];

// Get unread notification count
$query = "SELECT COUNT(*) as count FROM notifications WHERE student_id = ? AND is_read = 0";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$unread_count = $result->fetch_assoc()['count'];
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notifications</title>
    
    <!-- Your existing CSS -->
    <link rel="stylesheet" href="css/home-page-navBar.css">
    <link rel="stylesheet" href="css/profile-sideBar.css">
    <link rel="stylesheet" href="css/user-profile.css">
    <link rel="stylesheet" href="css/footer.css">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
    .notification-card {
        transition: all 0.3s ease;
        cursor: pointer;
        border-radius: 8px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,.05);
    }
    .notification-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
    }
    .notification-unread {
        border-left: 4px solid #0d6efd;
        background-color: rgba(13, 110, 253, 0.05);
    }
    .notification-read {
        border-left: 4px solid #6c757d;
    }
    .notification-time {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .empty-notifications {
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: #6c757d;
    }
    .empty-notifications i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    .profile-text {
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    #notificationsContainer {
        padding: 15px;
    }
    .unread-badge {
        background-color: #0d6efd;
        color: white;
        padding: 0.35em 0.65em;
        font-size: 0.85em;
        border-radius: 50rem;
        display: inline-block;
        margin-left: 10px;
    }
    .refresh-btn {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.2rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .refresh-btn:disabled {
        cursor: not-allowed;
    }
    .notification-type-product {
        background-color: #0dcaf0 !important;
    }
    .notification-type-order {
        background-color: #0d6efd !important;
    }
    .notification-type-reminder {
        background-color: #ffc107 !important;
        color: #000 !important;
    }
    </style>
</head>
<body>
    <div class="home-page2">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand color-dark" href="#">BCP E-COMMERCE</a>
                <button class="navbar-toggler border-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <div class="notif-burger"></div>      
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="home-page.php">HOME</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="product-page.php">PRODUCTS</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about-us.php" tabindex="-">ABOUT US</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php" tabindex="-1">CONTACTS</a>
                        </li>
                        <li class="nav-item dropdown">
                            <div class="notif"></div>
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            MY ACCOUNT
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="user-profile.php">PROFILE <span class="notif-profile"></span></a></li>
                            <li><a class="dropdown-item" href="cart-page.php">CART <span class="cart-count"></span></a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav> 

        <section class="info-home-page">
            <div class="bcp-text">
                <div>
                    <h1 class="first-text">MAGANDANG BUHAY!</h1>
                    <p class="second-text">#Profile Account</p>
                </div>
            </div>
        </section>

        <div class="body-page">
            <!-- Sidebar Toggle Button -->
            <button id="sidebarToggle" class="sidebar-toggle" aria-label="Toggle Sidebar">
                <svg class="hamburger-icon" viewBox="0 0 24 24" width="24" height="24">
                    <path d="M3 12h18M3 6h18M3 18h18" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <!-- Overlay -->
            <div class="sidebar-overlay"></div>

            <!-- Enhanced Sidebar -->
            <nav class="side-bar2" id="sidebar">
                <!-- Close button -->
                <button class="sidebar-close" id="sidebarClose" aria-label="Close Sidebar"></button>

                <div class="image-side-bar">
                    <div class="user-profile-div">
                        <img id="preview2" class="side-bar-profile" alt="">
                        <div class="name-id">
                            <span class="first-span"></span>
                            <span class="second-span"></span>
                        </div>
                    </div>         
                </div>
        
                <div class="sidebar-links">
                    <a class="sidebar-link" href="user-profile.php">
                        <img class="icon-sideBar" src="image/profile-image.png" alt="">
                        <span class="a-tag">My Account</span>
                    </a>
                    
                    <a class="sidebar-link" href="user-toPay.php">
                        <img class="icon-sideBar" src="image/items.png" alt="">
                        <span class="a-tag">My Purchase</span>
                        <span class="purchase-notif"></span>
                    </a>
                    
                    <a class="sidebar-link" href="notifications.php">
                        <img class="icon-sideBar" src="image/icon/notification.png" alt=""> 
                        <span class="a-tag">Notification</span>
                        <span class="notif-number"><?php echo $unread_count; ?></span>
                    </a>
                    
                    <a class="sidebar-link" href="php/logout.php?logout=true">
                        <img class="icon-sideBar" src="image/icon/logout.png" alt=""> 
                        <span class="a-tag">Logout</span>
                    </a>
                </div>
            </nav>

            <div class="profile-content">
                <main class="main-div">
                    <div class="profile-account-container">
                        <div class="profile-text">
                            <div>
                                <i class="fas fa-bell text-primary me-2"></i>
                                My Notifications
                                <?php if ($unread_count > 0): ?>
                                <span class="unread-badge"><?php echo $unread_count; ?> unread</span>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-outline-primary refresh-btn" id="refreshBtn">
                                <i class="fas fa-sync-alt"></i>
                                <span>Refresh</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" id="refreshSpinner">
                                    <span class="visually-hidden">Loading...</span>
                                </span>
                            </button>
                        </div>
                        <div class="profile-section">
                            <div id="notificationsContainer">
                                <!-- Notifications will be loaded here -->
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="script/cart-counter.js" type="module"></script>
    <script src="script/sidebarActive2.js"></script>
    <script src="script/sideBar-info.js" type="module"></script>

    <script>
    $(document).ready(function() {
        const studentId = <?php echo json_encode($student_id); ?>;
        let isLoading = false;
        
        function loadNotifications() {
            if (isLoading) return;
            
            isLoading = true;
            const $btn = $('#refreshBtn');
            const $spinner = $('#refreshSpinner');
            
            $btn.prop('disabled', true);
            $spinner.removeClass('d-none');
            
            $.ajax({
                url: 'functionEcom/get_user_notifications.php',
                type: 'GET',
                data: { student_id: studentId },
                success: function(response) {
                    if (response.status === 'success') {
                        displayNotifications(response.data);
                        updateUnreadCount(response.data);
                        // Update sidebar notification count
                        $('.notif-number').text(response.unread_count);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to load notifications'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load notifications. Please try again.'
                    });
                },
                complete: function() {
                    isLoading = false;
                    $btn.prop('disabled', false);
                    $spinner.addClass('d-none');
                }
            });
        }

        function updateUnreadCount(notifications) {
            const unreadCount = notifications.filter(n => n.is_read == 0).length;
            const $badge = $('.unread-badge');
            
            if (unreadCount > 0) {
                if ($badge.length) {
                    $badge.text(unreadCount + ' unread');
                } else {
                    $('.profile-text > div').append(`<span class="unread-badge">${unreadCount} unread</span>`);
                }
            } else {
                $badge.remove();
            }
        }

        function displayNotifications(notifications) {
            const container = $('#notificationsContainer');
            
            if (!notifications || notifications.length === 0) {
                container.html(`
                    <div class="empty-notifications">
                        <i class="fas fa-bell-slash"></i>
                        <p class="mb-0">No notifications yet</p>
                    </div>
                `);
                return;
            }

            let html = '';
            notifications.forEach(function(notification) {
                const readClass = notification.is_read == 1 ? 'notification-read' : 'notification-unread';
                const typeClass = getTypeClass(notification.notification_type);
                const timeAgo = moment(notification.created_at).fromNow();
                
                html += `
                    <div class="card notification-card ${readClass}" data-id="${notification.id}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge ${typeClass}">${notification.notification_type}</span>
                                <small class="notification-time">${timeAgo}</small>
                            </div>
                            <p class="card-text mb-0">${notification.message}</p>
                        </div>
                    </div>
                `;
            });
            
            container.html(html);
        }

        function getTypeClass(type) {
            const typeClasses = {
                'Product Update': 'notification-type-product',
                'Order Status': 'notification-type-order',
                'Reminder': 'notification-type-reminder'
            };
            return typeClasses[type] || 'bg-secondary';
        }

        // function markAsRead(notificationId) {
        //     if (isLoading) return;
            
        //     isLoading = true;
        //     $.ajax({
        //         url: 'functions/mark_notification_read.php',
        //         type: 'POST',
        //         data: { notification_id: notificationId },
        //         success: function(response) {
        //             if (response.status === 'success') {
        //                 loadNotifications();
        //                 // Update sidebar notification count
        //                 if (response.unread_count > 0) {
        //                     $('.notif-number').text(response.unread_count);
        //                 } else {
        //                     $('.notif-number').hide();
        //                 }
        //             }
        //         },
        //         error: function() {
        //             console.error('Failed to mark notification as read');
        //         },
        //         complete: function() {
        //             isLoading = false;
        //         }
        //     });
        // }
        function markAsRead(notificationId) {
            if (!notificationId) {
                console.error("Invalid notification ID");
                return;
            }

            $.ajax({
                url: 'functionEcom/mark_notification_read.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ notification_id: notificationId }),
                success: function(response) {
                    console.log(response);
                    if (response.status === 'success') {
                        // Refresh notifications list after marking as read
                        loadNotifications();

                        // Update sidebar notification count
                        if (response.unread_count > 0) {
                            $('.notif-number').text(response.unread_count);
                        } else {
                            $('.notif-number').hide();
                        }
                    } else {
                        console.warn("Notification not updated:", response.message);
                    }
                },
                error: function(xhr) {
                    console.error('Failed to mark notification as read:', xhr.responseText);
                }
            });
        }



        // Click handler for notifications
        $(document).on('click', '.notification-card', function() {
            const notificationId = $(this).data('id');
            if (!$(this).hasClass('notification-read')) {
                markAsRead(notificationId);
            }
        });

        // Refresh button handler
        $('#refreshBtn').on('click', function() {
            loadNotifications();
        });

        // Initial load
        loadNotifications();

        // Auto refresh every 5 minutes
        setInterval(loadNotifications, 300000);
    });
    </script>
</body>
</html>
