-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2026 at 10:09 PM
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
-- Database: `tnvs`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer_home`
--

CREATE TABLE `customer_home` (
  `customer_id` int(11) NOT NULL,
  `pick_up` char(255) NOT NULL,
  `drop_off` char(255) NOT NULL,
  `fare` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_tbl`
--

CREATE TABLE `customer_tbl` (
  `customer_id` int(100) NOT NULL,
  `name` char(255) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `username` char(255) NOT NULL,
  `password` char(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_tbl`
--

INSERT INTO `customer_tbl` (`customer_id`, `name`, `contact_no`, `username`, `password`) VALUES
(1, 'testdummy', '09471548975', 'test', 'test'),
(2, 'test1', '1234', 'test1', 'test1');

-- --------------------------------------------------------

--
-- Table structure for table `driver_tbl`
--

CREATE TABLE `driver_tbl` (
  `driver_id` int(11) NOT NULL,
  `name` char(255) NOT NULL,
  `contact_no` varchar(12) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` char(255) NOT NULL,
  `plate_no` char(255) NOT NULL,
  `brand` char(255) NOT NULL,
  `model` char(255) NOT NULL,
  `color` char(255) NOT NULL,
  `driver_license` varchar(50) DEFAULT NULL,
  `license_expiry` date DEFAULT NULL,
  `gcash` varchar(20) DEFAULT NULL,
  `status` enum('on','off') DEFAULT 'off',
  `total_rides` int(11) DEFAULT 0,
  `total_earnings` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver_tbl`
--

INSERT INTO `driver_tbl` (`driver_id`, `name`, `contact_no`, `username`, `password`, `plate_no`, `brand`, `model`, `color`, `driver_license`, `license_expiry`, `gcash`, `status`, `total_rides`, `total_earnings`) VALUES
(1, 'testdummy0.2', '09471548974', 'test', 'test', 'ABC-1433', 'Kotsi', 'Lambingini', 'Itim', NULL, NULL, NULL, 'on', 0, 0.00),
(2, 'test1', '1234', 'test1', 'test1', 'qwer', 'qwer', 'qwer', 'wer', NULL, NULL, NULL, 'on', 6, 990.00);

-- --------------------------------------------------------

--
-- Table structure for table `ride_requests`
--

CREATE TABLE `ride_requests` (
  `id` int(11) NOT NULL,
  `rider_name` varchar(100) DEFAULT NULL,
  `pickup` varchar(255) DEFAULT NULL,
  `dropoff` varchar(255) DEFAULT NULL,
  `distance` float DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','accepted','started','completed','cancelled') DEFAULT 'pending',
  `driver_id` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ride_requests`
--

INSERT INTO `ride_requests` (`id`, `rider_name`, `pickup`, `dropoff`, `distance`, `price`, `status`, `driver_id`, `start_time`, `end_time`, `created_at`) VALUES
(1, 'testdummy', 'Cubao', 'Marikina', 6, 100.00, 'completed', 1, '2026-04-20 00:23:10', '2026-04-20 00:23:14', '2026-04-19 16:22:51'),
(2, 'testdummy', 'Cubao', 'Marikina', 6, 100.00, 'completed', 1, '2026-04-20 00:24:34', '2026-04-20 00:24:53', '2026-04-19 16:24:11'),
(3, 'testdummy', 'Cubao', 'Marikina', 6, 100.00, 'completed', 1, '2026-04-20 00:26:40', '2026-04-20 00:27:02', '2026-04-19 16:26:00'),
(4, 'testdummy', 'Marikina', 'Manila', 12, 160.00, 'completed', 1, '2026-04-20 00:28:26', '2026-04-20 00:28:41', '2026-04-19 16:27:37'),
(5, 'testdummy', 'Cubao', 'Marikina', 6, 100.00, 'completed', 1, '2026-04-20 00:31:09', '2026-04-20 00:31:21', '2026-04-19 16:30:55'),
(6, 'testdummy', 'Cubao', 'Marikina', 6, 100.00, 'completed', 1, '2026-04-20 00:33:07', '2026-04-20 00:33:18', '2026-04-19 16:32:53'),
(7, 'testdummy', 'Cubao', 'Marikina', 6, 100.00, 'completed', 1, '2026-04-20 00:34:12', '2026-04-20 00:34:18', '2026-04-19 16:34:02'),
(8, 'testdummy', 'Cubao', 'Marikina', 6, 100.00, 'completed', 1, '2026-04-20 00:36:52', '2026-04-20 00:37:00', '2026-04-19 16:36:32'),
(9, 'test1', 'Cubao', 'Marikina', 6, 100.00, 'completed', 2, '2026-04-21 20:04:37', '2026-04-21 20:59:21', '2026-04-21 12:04:13'),
(10, 'test1', 'Cubao', 'Manila', 10, 150.00, 'completed', 2, '2026-04-21 21:17:54', '2026-04-21 21:18:00', '2026-04-21 13:17:30'),
(11, 'test1', 'Cubao', 'Manila', 10, 150.00, 'completed', 2, '2026-04-22 01:04:37', '2026-04-22 01:04:39', '2026-04-21 17:00:33'),
(12, 'test1', 'Manila', 'Taguig', 12, 170.00, 'completed', 2, '2026-04-22 01:13:17', '2026-04-22 01:13:19', '2026-04-21 17:13:04'),
(13, 'test1', 'Cubao', 'Caloocan', 14, 190.00, 'completed', 2, '2026-04-22 01:23:23', '2026-04-22 01:23:25', '2026-04-21 17:23:09'),
(14, 'test1', 'Manila', 'Taguig', 12, 170.00, 'completed', 2, '2026-04-22 03:52:29', NULL, '2026-04-21 19:52:21'),
(15, 'test1', 'Marikina', 'Makati', 14, 190.00, 'completed', 2, '2026-04-22 03:54:43', NULL, '2026-04-21 19:54:36'),
(16, 'test1', 'Marikina', 'Taguig', 18, 230.00, 'completed', 2, '2026-04-22 04:07:25', '2026-04-22 04:07:27', '2026-04-21 20:07:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer_home`
--
ALTER TABLE `customer_home`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `customer_tbl`
--
ALTER TABLE `customer_tbl`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `contact_no` (`contact_no`),
  ADD UNIQUE KEY `email` (`username`),
  ADD UNIQUE KEY `name` (`name`,`contact_no`,`username`,`password`) USING HASH;

--
-- Indexes for table `driver_tbl`
--
ALTER TABLE `driver_tbl`
  ADD PRIMARY KEY (`driver_id`),
  ADD UNIQUE KEY `email` (`username`),
  ADD UNIQUE KEY `username` (`name`,`contact_no`,`plate_no`,`username`) USING HASH;

--
-- Indexes for table `ride_requests`
--
ALTER TABLE `ride_requests`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer_tbl`
--
ALTER TABLE `customer_tbl`
  MODIFY `customer_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `driver_tbl`
--
ALTER TABLE `driver_tbl`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ride_requests`
--
ALTER TABLE customer_tbl ADD COLUMN wallet_balance DECIMAL(10,2) NOT NULL DEFAULT 0.00;
ALTER TABLE `ride_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
