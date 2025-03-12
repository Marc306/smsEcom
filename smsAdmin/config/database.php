<?php
define('DB_HOST', 'localhost:3307');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'studentaccount');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === FALSE) {
    die("Error creating database: " . $conn->error);
}

$conn->select_db(DB_NAME);

// Create necessary tables
$tables = [
    "CREATE TABLE IF NOT EXISTS products (
        productId VARCHAR(255) NOT NULL PRIMARY KEY,
        image VARCHAR(255) NOT NULL,
        productDescription VARCHAR(255) NOT NULL,
        sizeChart VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        typeItem ENUM('uniform', 'books', 'others') NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        quantity INT(10) UNSIGNED NOT NULL DEFAULT 1,
        stock INT(255) NOT NULL
    )",
    
    "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(255) NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        payment_method ENUM('Gcash Payment', 'Kasunduan', 'Walk-In Payment') NOT NULL,
        status ENUM('To Pay', 'To Receive', 'Completed') NOT NULL DEFAULT 'To Pay',
        receipt_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS orders_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        productId VARCHAR(255) NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        size VARCHAR(10),
        gender ENUM('Male', 'Female'),
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (productId) REFERENCES products(productId) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        student_id VARCHAR(255) NOT NULL,
        schedule_date DATE NOT NULL,
        status ENUM('Scheduled', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Scheduled',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        student_id VARCHAR(255) NOT NULL, 
        amount DECIMAL(10,2) NOT NULL,
        status VARCHAR(50) NOT NULL DEFAULT 'Pending',
        receipt_url VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE ON UPDATE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS notifications (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        notification_type ENUM('product_update', 'order_status', 'reminder') NOT NULL,
        is_read TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(student_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
    )"
];

foreach ($tables as $sql) {
    if ($conn->query($sql) === FALSE) {
        die("Error creating table: " . $conn->error);
    }
}
?>
