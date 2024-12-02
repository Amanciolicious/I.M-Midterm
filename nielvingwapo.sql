-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2024 at 08:41 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ohaha`
--

-- --------------------------------------------------------

--
-- Table structure for table `cat_tbl`
--

CREATE TABLE `cat_tbl` (
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cat_tbl`
--

INSERT INTO `cat_tbl` (`cat_id`, `cat_name`) VALUES
(1, 'lepaopao'),
(2, 'Drinks'),
(3, 'WAO'),
(4, 'was'),
(5, 'EAT');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `shipping_address` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `created_at`, `shipping_address`, `phone_number`) VALUES
(1, 8, 0.00, 'completed', '2024-11-11 08:28:01', '', ''),
(2, 8, 0.00, 'completed', '2024-11-11 08:31:13', '', ''),
(3, 8, 0.00, 'completed', '2024-11-11 08:31:36', '', ''),
(4, 8, 0.00, 'completed', '2024-11-11 08:32:54', '', ''),
(5, 8, 0.00, 'completed', '2024-11-11 08:34:01', '', ''),
(6, 8, 0.00, 'completed', '2024-11-11 08:37:11', '', ''),
(7, 8, 0.00, 'completed', '2024-11-11 08:38:30', '', ''),
(8, 8, 0.00, 'completed', '2024-11-11 08:38:55', '', ''),
(9, 8, 390.00, 'completed', '2024-11-11 08:53:31', 'Cabancalan', '09123456789'),
(10, 8, 150.00, 'completed', '2024-11-11 08:55:22', 'Cabancalan', '09123456789'),
(11, 8, 250.00, 'completed', '2024-11-13 08:27:16', 'Cabancalan', '09123456789'),
(12, 8, 250.00, 'completed', '2024-11-13 08:37:48', 'Cabancalan', '09123456789'),
(13, 8, 150.00, 'completed', '2024-11-13 08:39:43', 'Cabancalan', '09123456789'),
(14, 8, 500.00, 'completed', '2024-11-13 08:49:11', 'Cabancalan', '09123456789'),
(15, 8, 270.00, '', '2024-11-27 01:04:58', '', ''),
(16, 8, 270.00, '', '2024-11-27 01:07:24', '', ''),
(17, 8, 270.00, '', '2024-11-27 01:09:45', 'Cabancalan', '09123456789'),
(18, 8, 150.00, '', '2024-11-27 01:09:53', 'Cabancalan', '09123456789'),
(19, 8, 1250.00, '', '2024-11-27 01:26:35', 'Cabancalan', '09123456789'),
(20, 8, 615.00, '', '2024-11-27 01:31:45', 'Cabancalan', '09123456789'),
(21, 12, 150.00, '', '2024-11-27 01:42:18', 'Cabancalan', '09123456789'),
(22, 12, 369.00, '', '2024-11-27 01:44:19', 'Cabancalan', '09123456789'),
(23, 12, 120.00, '', '2024-11-27 01:45:28', 'Cabancalan', '09123456789'),
(24, 12, 500.00, '', '2024-11-27 01:55:49', 'Cabancalan', '09123456789'),
(25, 12, 150.00, 'completed', '2024-11-27 02:03:55', 'Cabancalan', '09123456789');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 7, 5, 1, 120.00),
(2, 7, 3, 1, 150.00),
(3, 7, 6, 1, 250.00),
(4, 8, 5, 1, 120.00),
(5, 9, 5, 2, 120.00),
(6, 9, 3, 1, 150.00),
(7, 10, 3, 1, 150.00),
(8, 11, 6, 1, 250.00),
(9, 12, 6, 1, 250.00),
(10, 13, 3, 1, 150.00),
(11, 14, 6, 2, 250.00),
(12, 17, 3, 1, 150.00),
(13, 17, 5, 1, 120.00),
(14, 18, 3, 1, 150.00),
(15, 19, 6, 5, 250.00),
(16, 20, 8, 5, 123.00),
(17, 21, 3, 1, 150.00),
(18, 22, 8, 3, 123.00),
(19, 23, 5, 1, 120.00),
(20, 24, 6, 2, 250.00),
(21, 25, 3, 1, 150.00);

-- --------------------------------------------------------

--
-- Table structure for table `product_tbl`
--

CREATE TABLE `product_tbl` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `product_availability` varchar(255) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_tbl`
--

INSERT INTO `product_tbl` (`id`, `product_name`, `category`, `price`, `quantity`, `product_availability`, `date`) VALUES
(3, 'Bbboy', 1, 150, 1, 'In Stock', 2010),
(5, 'RedHorse', 2, 120, 1, 'In Stock', 2012),
(6, 'Tanduay Select', 2, 250, 50, 'In Stock', 2012),
(8, 'was', 3, 123, 12, 'In Stock', 2001);

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'CUSTOMER',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`user_id`, `first_name`, `last_name`, `address`, `birthdate`, `gender`, `username`, `password`, `role`, `date_created`) VALUES
(8, 'Carl', 'Lepaopao', 'Cabancalan ', '2010-10-10', 'male', 'Carl', '$2y$10$7QmZGmHs1EhOX8k/PimAmOaovBLFx3tK2.vWCnIZ/VR40hCd7is0i', 'user', '2024-11-04 09:49:01'),
(9, 'asdfadsf', 'asdfadfs', 'sadfdsf', '2001-01-01', 'female', 'asdfadsf', '$2y$10$UtgR7W4aT7gDCzp1dBLZgemxMoTvWWP5k.kz8WPG4BspD7XeZ0e4u', 'user', '2024-11-04 09:52:40'),
(10, 'Carl', 'Lepaopao', 'Cabancalan ', '2010-10-10', 'male', 'Carl', '$2y$10$Z7cpfwO0z8KzQ.zWg/7Ph.SUqHf0xhnwb8yj.HBfeI2jWOHC1Ohq2', 'user', '2024-11-06 08:20:45'),
(11, 'Carl', 'Lepaopao', 'Cabancalan ', '2010-10-10', 'male', 'Carl', '$2y$10$mFwSgMnanCgB16A9vYzIJuTLy99gpOt/VnzafUUU0iL95XEfJ6EWa', 'user', '2024-11-06 09:16:10'),
(12, 'James', 'Malto', 'Npa', '2012-12-12', 'male', 'james', '$2y$10$9gOm8javukt4KA6ZB6XDkeyzhvNpb0OJklT76PKEFSpSOWrAcCN82', 'CUSTOMER', '2024-11-27 08:36:42');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$haXvgBwg2HSBb/K5hMtG5uxrib43H5AGKiUUYz67LerCFgU.BJUl.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cat_tbl`
--
ALTER TABLE `cat_tbl`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_tbl`
--
ALTER TABLE `product_tbl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category` (`category`);

--
-- Indexes for table `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `fk_register_username` (`username`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cat_tbl`
--
ALTER TABLE `cat_tbl`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `product_tbl`
--
ALTER TABLE `product_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `register` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product_tbl` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_tbl`
--
ALTER TABLE `product_tbl`
  ADD CONSTRAINT `product_tbl_ibfk_1` FOREIGN KEY (`category`) REFERENCES `cat_tbl` (`cat_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
