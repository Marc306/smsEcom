-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Mar 02, 2025 at 04:29 PM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `studentaccount`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `productId` varchar(255) NOT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `completed_orders`
--

CREATE TABLE `completed_orders` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `productId` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_price` decimal(10,2) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `message_chat`
--

CREATE TABLE `message_chat` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `sent` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `message_chat`
--

INSERT INTO `message_chat` (`id`, `name`, `email`, `subject`, `message`, `sent`) VALUES
(1, 'andrew', 'lics@gmail.com', 'qwertyu', 'wqertyuioijknbvcxzsdf', '2025-03-02 12:46:10');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_method` enum('Kasunduan','Walk-In Payment','Gcash Payment') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('To Pay','To Receive','Completed') NOT NULL DEFAULT 'To Pay'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `orders_items`
--

CREATE TABLE `orders_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `productId` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `size` varchar(10) NOT NULL,
  `gender` enum('Male','Female') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Confirmed') NOT NULL DEFAULT 'Pending',
  `receipt_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pickup_schedule`
--

CREATE TABLE `pickup_schedule` (
  `date` date NOT NULL,
  `total_slots` int(11) DEFAULT 100,
  `booked_slots` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `productcategories`
--

CREATE TABLE `productcategories` (
  `id` int(11) NOT NULL,
  `productId` varchar(255) NOT NULL,
  `productcategories` enum('freshman','sophomore','junior','senior') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `productcategories`
--

INSERT INTO `productcategories` (`id`, `productId`, `productcategories`) VALUES
(1, '123qwe', 'freshman'),
(2, '123qwe', 'sophomore'),
(3, '123qwe', 'junior'),
(4, '123qwe', 'senior'),
(5, '456rty', 'freshman'),
(6, 'ewq321\r\n', 'freshman'),
(7, 'ewq321\r\n', 'sophomore'),
(8, 'ewq321\r\n', 'junior'),
(9, 'ewq321\r\n', 'senior'),
(10, 'fghj987', 'junior'),
(11, 'fghj987', 'senior'),
(12, 'a7s5d4f', 'junior'),
(13, 'a7s5d4f', 'senior'),
(14, 'fghj543', 'junior'),
(15, 'fghj543', 'senior'),
(16, 'po3i445', 'freshman'),
(17, 'po3i445', 'sophomore'),
(18, 'mnjk567', 'junior'),
(19, 'mnjk567', 'senior'),
(20, 'jjk340', 'freshman'),
(21, 'jjk340', 'sophomore'),
(22, 'nvgh056', 'junior'),
(23, 'nvgh056', 'senior');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `productId` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `productDescription` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `typeItem` enum('uniform','books','others') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `stock` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`productId`, `image`, `productDescription`, `name`, `typeItem`, `price`, `quantity`, `stock`) VALUES
('123qwe', 'image/products/crim1stTo4thYear.jpg', 'image/products/IMG_20240919_151403.jpg', 'Bachelor of Science in Criminology School Uniform', 'uniform', '1070.00', 1, 0),
('456rty', 'image/products/istockphoto.jpg', '', 'National Service Training Program(NSTP2) ', 'books', '250.00', 1, 50),
('a7s5d4f', 'image/products/teacherUnif3rdTo4thYear.jpg', 'image/products/IMG_20240919_151403.jpg', 'Bachelor of Science in Education School Uniform', 'uniform', '1060.00', 1, 40),
('cvbn678', 'image/products/comEng3rdTo4thYear.jpg', 'image/products/IMG_20240919_151403.jpg', 'Bachelor of Science in Computer Engineering School Uniform', 'uniform', '1087.00', 1, 70),
('ewq321\r\n', 'image/products/psych1stTo4thYear.jpg', 'image/products/IMG_20240919_151403.jpg', 'Bachelor of Science Psychology School Uniforms', 'uniform', '1050.00', 1, 100),
('fghj543', 'image/products/hm3rdTo4thYear.jpg', 'image/products/IMG_20240919_151403.jpg', 'Bachelor of Science in Hospitality Management School Uniform', 'uniform', '1080.00', 1, 60),
('fghj987', 'image/products/IT3rdTo4thYear.jpg', 'image/products/IMG_20240919_151403.jpg', 'Bachelor of Science in Information Technology School Uniform', 'uniform', '1065.00', 1, 55),
('jjk340', 'image/products/tourism1stTo2ndYear.jpg', 'image/products/IMG_20240919_151403.jpg', 'Bachelor of Science in Tourism Management School Uniform', 'uniform', '1095.00', 1, 30),
('mnjk567', 'image/products/business3rdTo4thYear.jpg', 'image/products/IMG_20240919_151403.jpg\r\n', 'Bachelor of Science in Business Administrator School Uniform', 'uniform', '1077.00', 1, 43),
('nvgh056', 'image/products/tourism3rdTo4thYear.jpg', 'image/products/IMG_20240919_151403.jpg', 'Bachelor of Science in Tourism Management School Uniform', 'uniform', '1100.00', 1, 8),
('po3i445', 'image/products/comonUnif1stTo2ndYear.jpg', 'image/products/IMG_20240919_151403.jpg', 'Bestlink Regular School Uniform', 'uniform', '1090.00', 1, 27);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `schedule_date` date DEFAULT NULL,
  `status` enum('Scheduled','Completed') NOT NULL DEFAULT 'Scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) UNSIGNED NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `school_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `birthday` date NOT NULL,
  `civil_status` enum('Single') NOT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `password`, `first_name`, `middle_name`, `last_name`, `school_name`, `email`, `contact_number`, `birthday`, `civil_status`, `image_url`) VALUES
(1, 's21013867', '$2y$10$LVC9M40XB2649/OlrYgyAeGDHEaNKzazVFdwEGZhFgYbcwXp6ECES', 'Joshua', 'B', 'Quitoriano', 'Bestlink College', 'joshpogi18@gmail.com', '12345456789', '2003-02-18', 'Single', 'image/user-default-profile.jpg'),
(2, 's21019965', '$2y$10$LOqcw3HuJHz66d3cLjk/qe.4vq3t.NfydQJRMwaIn25mEQoCRs98K', 'Marc Andrew', 'Serbo', 'Licuan', 'Bestlink College', 'Andrew990@gmail.com', '1234568911', '2001-10-30', 'Single', 'uploadProfilePic/Premium Vector _ Man profile cartoon.jpg'),
(3, 's21018274', '#Ab8080', 'Angelo', 'Antroco', 'Abaigar', 'Bestlink College', 'abaigar12@gail.com', '4586543219', '2002-03-06', 'Single', 'image/user-default-profile.jpg'),
(4, 's21018458', '#Al8080', 'Karl Aaron', 'Pampag', 'Alvendo', 'Bestlink College', 'Alvendo@gmail.com', '65789543560', '2002-04-15', 'Single', 'image/user-default-profile.jpg'),
(5, 's19018471', '#Ro8080', 'Onemig', 'Luces', 'Robenta', 'Bestlink College', 'robentaOne@gmail.com', '8456783291', '2000-07-01', 'Single', 'image/user-default-profile.jpg'),
(6, 's21121295', '$2y$10$IXOfeHufsBBOmtCZ3QMQ6e5Z9lFDLftaf/qd37jm95/QySm2ITr5K', 'Cylenn Blezz', NULL, 'Centillias', 'Bestlink College', 'Centillias@gmail.com', '34456879629', '2003-05-12', 'Single', 'image/user-default-profile.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cart_student_id` (`student_id`);

--
-- Indexes for table `completed_orders`
--
ALTER TABLE `completed_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message_chat`
--
ALTER TABLE `message_chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_student_order` (`student_id`);

--
-- Indexes for table `orders_items`
--
ALTER TABLE `orders_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order` (`order_id`),
  ADD KEY `fk_product` (`productId`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_paymet` (`order_id`);

--
-- Indexes for table `pickup_schedule`
--
ALTER TABLE `pickup_schedule`
  ADD PRIMARY KEY (`date`);

--
-- Indexes for table `productcategories`
--
ALTER TABLE `productcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `productId` (`productId`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`productId`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `completed_orders`
--
ALTER TABLE `completed_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message_chat`
--
ALTER TABLE `message_chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `orders_items`
--
ALTER TABLE `orders_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `productcategories`
--
ALTER TABLE `productcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_student_order` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `orders_items`
--
ALTER TABLE `orders_items`
  ADD CONSTRAINT `fk_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `fk_product` FOREIGN KEY (`productId`) REFERENCES `products` (`productId`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_paymet` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `productcategories`
--
ALTER TABLE `productcategories`
  ADD CONSTRAINT `productcategories_ibfk_1` FOREIGN KEY (`productId`) REFERENCES `products` (`productId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
