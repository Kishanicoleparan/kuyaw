-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 22, 2026 at 12:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `data`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(10) UNSIGNED NOT NULL,
  `id` int(10) UNSIGNED NOT NULL,
  `car_id` int(10) UNSIGNED NOT NULL,
  `booking_date` date NOT NULL,
  `return_date` date NOT NULL,
  `status` enum('Pending','Approved','Cancelled','Completed') NOT NULL DEFAULT 'Pending',
  `total_price` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `id`, `car_id`, `booking_date`, `return_date`, `status`, `total_price`, `payment_status`, `payment_method`, `transaction_id`, `payment_date`) VALUES
(1, 13, 4, '2026-02-04', '2026-03-03', 'Completed', 135000.00, 'pending', NULL, NULL, NULL),
(2, 14, 2, '2026-02-26', '2026-02-23', 'Pending', 40000.00, 'pending', NULL, NULL, NULL),
(3, 14, 2, '2026-03-11', '2026-06-20', 'Pending', 4038333.33, 'paid', 'Maya', NULL, '2026-02-22 06:43:39'),
(4, 14, 2, '2026-03-07', '2026-03-04', 'Pending', 40000.00, 'paid', NULL, NULL, '2026-02-22 07:07:03'),
(5, 15, 4, '2026-02-19', '2026-02-28', 'Pending', 45000.00, 'pending', NULL, NULL, NULL),
(6, 15, 2, '2026-02-27', '2026-02-09', 'Pending', 40000.00, 'paid', NULL, NULL, '2026-02-22 07:08:12'),
(7, 14, 3, '2026-03-26', '2026-04-01', 'Pending', 4700457.67, 'paid', 'Maya', NULL, '2026-02-22 06:44:12');

-- --------------------------------------------------------

--
-- Table structure for table `booking_settings`
--

CREATE TABLE `booking_settings` (
  `id` int(11) NOT NULL,
  `min_days` int(11) DEFAULT 1,
  `max_days` int(11) DEFAULT 30,
  `allow_same_day` enum('Yes','No') DEFAULT 'Yes',
  `auto_approve` enum('Yes','No') DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_settings`
--

INSERT INTO `booking_settings` (`id`, `min_days`, `max_days`, `allow_same_day`, `auto_approve`) VALUES
(1, 1, 30, 'Yes', 'No');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `car_id` int(11) UNSIGNED NOT NULL,
  `car_name` varchar(255) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `status` enum('Available','Rented','Maintenance') NOT NULL DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `car_image` varchar(255) DEFAULT NULL,
  `availability` enum('available','rented','maintenance') DEFAULT 'available',
  `model` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`car_id`, `car_name`, `brand`, `price_per_day`, `status`, `created_at`, `car_image`, `availability`, `model`) VALUES
(2, 'love', 'kia', 40000.00, 'Rented', '2026-02-05 09:22:21', '1770283341_reserved.png', 'available', ''),
(3, 'jgygy', 'honda', 788888.00, 'Rented', '2026-02-05 09:42:59', '1770284579_menu (2).png', 'available', ''),
(4, 'veinasas', 'toyota', 5000.00, 'Rented', '2026-02-05 14:09:19', '1770300559_choose (1).png', 'available', '');

-- --------------------------------------------------------

--
-- Table structure for table `company_settings`
--

CREATE TABLE `company_settings` (
  `id` int(11) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `contact_email` varchar(150) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_settings`
--

INSERT INTO `company_settings` (`id`, `company_name`, `contact_email`, `contact_phone`, `address`, `logo`) VALUES
(1, 'UrbanDrive Car Rental', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_history`
--

CREATE TABLE `payment_history` (
  `payment_id` int(11) UNSIGNED NOT NULL,
  `booking_id` int(10) UNSIGNED NOT NULL,
  `id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `status` enum('pending','success','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_settings`
--

CREATE TABLE `payment_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_settings`
--

INSERT INTO `payment_settings` (`id`, `setting_key`, `setting_value`, `created_at`) VALUES
(1, 'currency', 'PHP', '2026-02-21 14:05:25'),
(2, 'service_fee', '50', '2026-02-21 14:05:25'),
(3, 'paypal_client_id', '', '2026-02-21 14:05:25'),
(4, 'paypal_secret', '', '2026-02-21 14:05:25'),
(5, 'gcash_merchant_id', '', '2026-02-21 14:05:25'),
(6, 'gcash_api_key', '', '2026-02-21 14:05:25');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `company_email` varchar(100) DEFAULT NULL,
  `company_phone` varchar(20) DEFAULT NULL,
  `max_booking_days` int(11) DEFAULT NULL,
  `cancellation_policy` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `company_name`, `company_email`, `company_phone`, `max_booking_days`, `cancellation_policy`) VALUES
(1, NULL, NULL, NULL, 0, 'Not Allowed');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) NOT NULL,
  `default_price` decimal(10,2) NOT NULL,
  `site_logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `site_name`, `default_price`, `site_logo`) VALUES
(1, 'My Car Rental', 100.00, '1770293489_catering (1).png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `address` varchar(100) NOT NULL,
  `profile_pic` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`, `address`, `profile_pic`) VALUES
(10, 'Admin', 'admin@gmail.com', NULL, '$2y$10$ufh7isApZPS8Sm3AN3thkO1QRKAJR8Oww053577AJOkLcQGJQe9WK', 'admin', '2026-02-05 12:28:38.231379', '', ''),
(11, 'jingkie', 'jingkie@gmail.com', '5678', NULL, 'customer', '2026-02-05 12:28:38.231379', '', ''),
(12, 'jeza', 'jeza@gmail.com', NULL, '$2y$10$MANFsDY4MilKKsOupA0VYe9nsThJska.XdfNHP0/YJOt73lnISUfG', 'customer', '2026-02-05 13:15:50.729943', '', ''),
(13, 'xian', 'xian@gmail.com', NULL, '$2y$10$bC0Y1EI4Ttq0gQB85WNZ0uRcK2CdaG.q7EtBea/9W1uljSmOwLK9C', 'customer', '2026-02-15 07:10:12.456699', '', ''),
(14, 'libe', 'libe@gmail.com', '0923723674', '$2y$10$4jL0wKI9njT6yXUof5pr8eO7yaIIJ2.gmqNjFdKgHd0zZ1gFqd6Te', 'customer', '2026-02-21 13:53:45.564091', 'naga', '1771748369_Screenshot 2026-02-22 151539.png'),
(15, 'mama', 'mama@gmail.com', '09736438238', '$2y$10$8BOWY1/GDGmsG51wiofJieOTjkN8MBdpHTH1Q5g2Z/JgixB7l4myS', 'customer', '2026-02-22 04:18:53.083802', 'naga', '1771747103_Screenshot 2026-02-22 144057.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `id` (`id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `booking_settings`
--
ALTER TABLE `booking_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`car_id`);

--
-- Indexes for table `company_settings`
--
ALTER TABLE `company_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `payment_settings`
--
ALTER TABLE `payment_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `booking_settings`
--
ALTER TABLE `booking_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `car_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `company_settings`
--
ALTER TABLE `company_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_settings`
--
ALTER TABLE `payment_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `cars` (`car_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
