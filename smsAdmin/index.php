<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit(); // Stop further execution
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
        }
        .nav-link {
            color: #fff;
        }
        .nav-link:hover {
            background: #495057;
        }
        .main-content {
            padding: 20px;
        }
        .card-counter {
            padding: 20px;
            border-radius: 5px;
            color: #fff;
        }
        .card-counter i {
            font-size: 4em;
        }
        .count-numbers {
            font-size: 32px;
        }
        .count-name {
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="d-flex flex-column flex-shrink-0 p-3 text-white">
                    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="fs-4">SMS Admin</span>
                    </a>
                    <hr>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="#" class="nav-link active" data-page="dashboard">
                                <i class="fas fa-home me-2"></i>Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link text-white" data-page="orders">
                                <i class="fas fa-shopping-cart me-2"></i>Orders
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link text-white" data-page="products">
                                <i class="fas fa-box me-2"></i>Products
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link text-white" data-page="pickup">
                                <i class="fas fa-calendar me-2"></i>Pickup Schedule
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link text-white" data-page="message-chat">
                                <i class="fas fa-comment-alt"></i> Messages
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link text-white" data-page="notifications">
                                <i class="fas fa-bell me-2"></i>Notifications
                            </a>
                        </li>
                        <li>
                            <a href="logout.php" class="nav-link text-white">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div id="content-area">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Load dashboard by default
            loadPage('dashboard');

            // Handle navigation
            $('.nav-link').click(function(e) {
                // Skip the logout link
                if ($(this).attr('href') === 'logout.php') {
                    return; // Do not trigger page load for logout
                }

                e.preventDefault();
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
                loadPage($(this).data('page'));
            });

            function loadPage(page) {
                $.get('pages/' + page + '.php', function(data) {
                    $('#content-area').html(data);
                });
            }
        });
    </script>
</body>
</html>
