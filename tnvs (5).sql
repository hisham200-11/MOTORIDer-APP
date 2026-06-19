-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2026 at 06:54 PM
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
-- Table structure for table `customer_tbl`
--

CREATE TABLE `customer_tbl` (
  `customer_id` int(100) NOT NULL,
  `name` char(255) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `username` char(255) NOT NULL,
  `password` char(255) NOT NULL,
  `gcash` varchar(15) DEFAULT NULL,
  `gcash_balance` decimal(10,2) DEFAULT 0.00,
  `wallet_balance` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_tbl`
--

INSERT INTO `customer_tbl` (`customer_id`, `name`, `contact_no`, `username`, `password`, `gcash`, `gcash_balance`, `wallet_balance`) VALUES
(1, 'testtest', '1111111111111111', 'test1', 'test', '09471548971', 218.50, 0.00),
(2, 'Hisham H. Muctar', '09059557661', 'hisham123', '12345', '09165431255', 0.00, 0.00),
(3, 'john yu', '09165431255', 'JYUK', '123', '09165431255', 0.00, 0.00),
(4, 'JHON PIERRE the VIII of Britain', '0987654266', 'JPD4', '123', '0987654266', 1200.00, 0.00),
(5, 'johan', '09887654', 'johan12', '123', '09887654', 0.00, 0.00),
(6, 'Airo Pakadyot III', '0988888831', 'Airo', '123', '0988888831', 0.00, 0.00);

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
  `total_earnings` decimal(10,2) DEFAULT 0.00,
  `driver_lat` double DEFAULT NULL,
  `driver_lng` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver_tbl`
--

INSERT INTO `driver_tbl` (`driver_id`, `name`, `contact_no`, `username`, `password`, `plate_no`, `brand`, `model`, `color`, `driver_license`, `license_expiry`, `gcash`, `status`, `total_rides`, `total_earnings`, `driver_lat`, `driver_lng`) VALUES
(1, 'test', '09471548974', 'test2', 'test', 'ABC-1234', 'vroom vroom', 'zoom', 'itim', '111111111111111', '2030-07-31', '09471548975', 'on', 110, 7817.68, 14.579628891789, 121.00463867529),
(2, 'test3', '09455286799', 'test3', '123', 'TPK-098', 'Motorcycle', 'Honda ADV 150', 'Gray', '1342567', '2030-06-01', '09153775687', 'on', 0, 0.00, NULL, NULL),
(3, 'Mark Rebeta Jr. III', '091543572', 'Denmark', '123', '7LOB-991', 'Motorcycle', 'Yamaha Mio', 'Prismatic', 'ASDFAS23234234', '2029-09-01', '091543572', 'on', 0, 0.00, NULL, NULL),
(4, 'airo gay', '0967676767', 'airo', '123', 'ARTC-O099', 'E-bike', 'NWOW', 'red', '23423456', '2040-01-01', '0967676767', 'off', 0, 0.00, NULL, NULL),
(5, 'AIRO 3', '09762348', 'ART', '123', '123', '123', '123', '123', '123', '2040-01-01', '123', 'off', 0, 0.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ride_requests`
--

CREATE TABLE `ride_requests` (
  `id` int(11) NOT NULL,
  `rider_name` varchar(100) DEFAULT NULL,
  `pickup` varchar(255) DEFAULT NULL,
  `pickup_lat` decimal(10,8) DEFAULT NULL,
  `pickup_lng` decimal(11,8) DEFAULT NULL,
  `dropoff` varchar(225) DEFAULT 'Unknown Location',
  `dropoff_lat` decimal(10,8) DEFAULT NULL,
  `dropoff_lng` decimal(11,8) DEFAULT NULL,
  `distance` float DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','accepted','started','completed','cancelled') DEFAULT 'pending',
  `driver_id` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `passenger_dismissed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(10) DEFAULT 'Cash',
  `tax` decimal(10,2) DEFAULT 0.00,
  `driver_earnings` decimal(10,2) DEFAULT 0.00,
  `driver_lat` decimal(10,8) DEFAULT NULL,
  `driver_lng` decimal(11,8) DEFAULT NULL,
  `passenger_lat` decimal(10,8) DEFAULT NULL,
  `passenger_lng` decimal(11,8) DEFAULT NULL,
  `gcash_deducted` int(11) DEFAULT 0,
  `tracking_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ride_requests`
--

INSERT INTO `ride_requests` (`id`, `rider_name`, `pickup`, `pickup_lat`, `pickup_lng`, `dropoff`, `dropoff_lat`, `dropoff_lng`, `distance`, `price`, `status`, `driver_id`, `start_time`, `end_time`, `passenger_dismissed`, `created_at`, `payment_method`, `tax`, `driver_earnings`, `driver_lat`, `driver_lng`, `passenger_lat`, `passenger_lng`, `gcash_deducted`, `tracking_token`) VALUES
(1, 'testtest', 'Pedro Gil Street, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.57914950, 121.00365961, '0', 14.58550777, 120.98795647, 2, 45.80, 'completed', 1, '2026-06-08 00:12:44', '2026-06-08 00:12:46', 0, '2026-06-07 16:12:01', 'GCash', 5.50, 40.30, NULL, NULL, NULL, NULL, 1, NULL),
(2, 'testtest', 'Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.57953760, 121.00422394, '0', 14.58514783, 120.99128816, 2, 41.40, '', NULL, NULL, NULL, 0, '2026-06-07 16:17:22', 'GCash', 0.00, 0.00, NULL, NULL, NULL, NULL, 0, NULL),
(3, 'testtest', 'Asociacion de Damas de Filipinas, Inc. Settlement House, 1451, Quirino Avenue Extension, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.58125761, 120.99852562, '2154', 14.57901481, 121.00564957, 1, 40.00, '', NULL, NULL, NULL, 0, '2026-06-07 16:20:43', 'GCash', 0.00, 0.00, NULL, NULL, NULL, NULL, 0, NULL),
(4, 'testtest', 'Barrio Kapampanga, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57964736, 121.00464852, '0', 14.58679331, 120.97885491, 4, 60.80, '', NULL, NULL, NULL, 0, '2026-06-07 16:21:33', 'GCash', 0.00, 0.00, NULL, NULL, NULL, NULL, 0, NULL),
(5, 'testtest', 'Medical Center Manila, 850, United Nations Avenue, Ermita, Fifth District, Manila, Capital District, Metro Manila, 1000, Philippines', 14.58233490, 120.98524377, '0', 14.57826225, 121.00455091, 2, 47.10, '', NULL, NULL, NULL, 0, '2026-06-07 16:26:58', 'GCash', 0.00, 0.00, NULL, NULL, NULL, NULL, 0, NULL),
(6, 'testtest', 'Road 13, Fabie Estate, San Andres Bukid, Fifth District, Manila, Capital District, Metro Manila, 1017, Philippines', 14.57856505, 121.00430272, '8065', 14.56455955, 121.00958000, 2, 46.00, '', NULL, NULL, NULL, 0, '2026-06-07 16:42:38', 'GCash', 0.00, 0.00, NULL, NULL, NULL, NULL, 0, NULL),
(7, 'testtest', 'Barrio Kapampanga, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57964610, 121.00463198, '0', 14.57931667, 121.00842458, 3, 57.90, '', NULL, NULL, NULL, 0, '2026-06-07 16:45:25', 'Cash', 0.00, 0.00, NULL, NULL, NULL, NULL, 0, NULL),
(8, 'testtest', '1156, G. Apacible Street, 734 Zone 80, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.58077025, 120.99173209, '0', 14.57877548, 121.00520418, 1, 40.00, '', NULL, NULL, NULL, 0, '2026-06-07 16:55:00', 'GCash', 0.00, 0.00, NULL, NULL, NULL, NULL, 0, NULL),
(9, 'testtest', 'Road 6, Fabie Estate, San Andres Bukid, Fifth District, Manila, Capital District, Metro Manila, 1017, Philippines', 14.57732837, 121.00304820, '0', 14.58912317, 120.98799937, 3, 51.20, '', NULL, NULL, NULL, 0, '2026-06-07 17:04:55', 'GCash', 0.00, 0.00, NULL, NULL, NULL, NULL, 0, NULL),
(10, 'testtest', 'Paz Street, Barangay 831, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.58367927, 120.99456380, '0', 14.57956508, 121.00584775, 1, 40.00, '', NULL, NULL, NULL, 0, '2026-06-07 17:12:28', 'GCash', 0.00, 0.00, NULL, NULL, NULL, NULL, 0, NULL),
(11, 'testtest', 'Barrio Kapampanga, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57965035, 121.00464735, '0', 14.58425835, 120.99442934, 1, 40.00, '', NULL, NULL, NULL, 0, '2026-06-07 17:17:35', 'GCash', 0.00, 0.00, NULL, NULL, NULL, NULL, 0, NULL),
(12, 'testtest', 'P. Correa Street, 734 Zone 80, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.58480130, 120.98988718, 'Dayang Street, San Andres Bukid, Fifth District, Manila, Capital District, Metro Manila, 1017, Philippines', 14.57944040, 121.00649132, 2.47, 44.70, 'completed', 1, '2026-06-08 01:31:28', '2026-06-08 01:31:30', 0, '2026-06-07 17:30:13', 'GCash', 5.36, 39.34, NULL, NULL, NULL, NULL, 1, NULL),
(13, 'testtest', 'Padre Faura Street, Barangay 670, Ermita, Fifth District, Manila, Capital District, Metro Manila, 1000, Philippines', 14.57998061, 120.98533533, 'Revellín de Parian, Muralla Street, Barangay 658, Intramuros, Fifth District, Manila, Capital District, Metro Manila, 1002, Philippines', 14.59294625, 120.97881381, 2.48, 44.80, 'completed', 1, '2026-06-08 01:32:54', '2026-06-08 01:32:56', 0, '2026-06-07 17:32:35', 'GCash', 5.38, 39.42, NULL, NULL, NULL, NULL, 1, NULL),
(14, 'testtest', 'Green 2 Optical Clinic, Gonzalo Puyat Street, Barangay 383, Barangay 308, Quiapo, Third District, Manila, Capital District, Metro Manila, 1001, Philippines', 14.60072291, 120.98261333, '1st Street, Fabie Estate, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.57753464, 120.99805905, 10.66, 126.60, 'completed', 1, '2026-06-08 01:37:22', '2026-06-08 01:37:24', 0, '2026-06-07 17:37:11', 'GCash', 15.19, 111.41, NULL, NULL, NULL, NULL, 1, NULL),
(15, 'testtest', 'Barrio Kapampanga, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57964264, 121.00464660, '7th Street, Fabie Estate, 734 Zone 80, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.57652862, 120.99979243, 2.22, 42.20, 'completed', 1, '2026-06-08 01:42:39', '2026-06-08 01:42:41', 0, '2026-06-07 17:42:30', 'Cash', 5.06, 37.14, NULL, NULL, NULL, NULL, 0, NULL),
(16, 'testtest', 'Akasya Street, Kahilum Subdivision, 872, Pandacan, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.58309227, 121.00433268, 'Lechon Manok ni Sr. Pedro, Tejeron Street, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1204, Philippines', 14.57565292, 121.01192680, 1.29, 40.00, 'completed', 1, '2026-06-08 01:49:11', '2026-06-08 01:49:13', 0, '2026-06-07 17:48:59', 'Cash', 4.80, 35.20, NULL, NULL, NULL, NULL, 0, NULL),
(17, 'testtest', 'Polytechnic University of the Philippines, Anonas Street, 508, Santa Mesa, Sixth District, Manila, Capital District, Metro Manila, 1016, Philippines', 14.59795930, 121.01093040, 'SM City Manila, Antonio Villegas Street, 659, Ermita, Fifth District, Manila, Capital District, Metro Manila, 1000, Philippines', 14.58978250, 120.98314250, 4.68, 66.80, 'completed', 1, '2026-06-08 01:55:52', '2026-06-08 01:55:55', 0, '2026-06-07 17:55:38', 'GCash', 8.02, 58.78, NULL, NULL, NULL, NULL, 1, NULL),
(18, 'testtest', 'Barrio Kapampanga, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57964788, 121.00463801, 'Emilio Aguinaldo College, 1113-1117, San Marcelino Street, 734 Zone 80, Ermita, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.58275987, 120.98656861, 2.69, 46.90, 'completed', 1, '2026-06-08 02:09:08', '2026-06-08 02:09:16', 0, '2026-06-07 18:08:36', 'GCash', 5.63, 41.27, NULL, NULL, NULL, NULL, 1, NULL),
(19, 'testtest', 'JEJ Manpower, 1137, Nakpil Street, 688 Zone 75, Barangay 688, Malate, Fifth District, Manila, Capital District, Metro Manila, 1004, Philippines', 14.57557549, 120.99349118, 'Singapore Diagnostics, 131, Dela Rosa Street, Legazpi Village, San Lorenzo, District I, Makati, Southern Manila District, Metro Manila, 1200, Philippines', 14.55768239, 121.01753509, 4.76, 67.60, 'completed', 1, '2026-06-08 02:10:30', '2026-06-08 02:14:37', 0, '2026-06-07 18:09:46', 'GCash', 8.11, 59.49, NULL, NULL, NULL, NULL, 1, NULL),
(20, 'testtest', 'Kapampangan Street, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57962870, 121.00501409, 'Escoda Street, Barangay 676, Ermita, Fifth District, Manila, Capital District, Metro Manila, 1000, Philippines', 14.57733309, 120.98767641, 2.68, 46.80, '', NULL, NULL, NULL, 0, '2026-06-08 13:28:02', 'GCash', 0.00, 0.00, NULL, NULL, NULL, NULL, 0, NULL),
(21, 'testtest', 'Ricerra Dental Place, 1537C, Pedro Gil Street, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.57874590, 120.99965032, 'SM City Caloocan, Malapitan Road, Bagumbong, Zone 15, Caybiga, District 1, Caloocan, Northern Manila District, Metro Manila, 1421, Philippines', 14.75168400, 121.01987600, 23.07, 250.70, 'completed', 1, '2026-06-08 21:30:46', '2026-06-08 21:30:50', 0, '2026-06-08 13:29:47', 'Cash', 30.08, 220.62, NULL, NULL, NULL, NULL, 0, NULL),
(22, 'testtest', 'Kellsons Building, 1202, G. Apacible Street, 734 Zone 80, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.58071768, 120.99241277, 'Pedro Gil Street, San Andres Bukid, Fifth District, Manila, Capital District, Metro Manila, 1017, Philippines', 14.57934708, 121.00478154, 1.67, 40.00, 'completed', 1, '2026-06-08 21:54:41', '2026-06-08 21:54:43', 0, '2026-06-08 13:54:14', 'Cash', 4.80, 35.20, NULL, NULL, NULL, NULL, 0, NULL),
(23, 'testtest', 'Barrio Kapampanga, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57964216, 121.00463731, 'Peñafrancia Street, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.58209490, 120.99725339, 1.38, 40.00, 'completed', 1, '2026-06-09 18:23:30', '2026-06-09 18:23:32', 0, '2026-06-08 13:55:25', 'Cash', 4.80, 35.20, NULL, NULL, NULL, NULL, 0, NULL),
(24, 'testtest', 'Barrio Kapampanga, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57964840, 121.00464117, 'Quirino Avenue Extension, Barangay 831, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.58396405, 120.99560167, 1.61, 40.00, 'completed', 1, '2026-06-09 22:10:26', '2026-06-09 22:10:29', 0, '2026-06-09 14:09:55', 'Cash', 4.80, 35.20, NULL, NULL, NULL, NULL, 0, NULL),
(25, 'testtest', 'Barrio Kapampanga, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57965636, 121.00464318, 'Quirino Avenue Extension, Barangay 831, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.58538279, 120.99395596, 1.82, 40.00, 'completed', 1, '2026-06-10 01:06:01', '2026-06-10 01:06:04', 0, '2026-06-09 17:05:27', 'Cash', 4.80, 35.20, NULL, NULL, NULL, NULL, 0, NULL),
(26, 'testtest', 'LG Flores Memorial Chapels Inc., 1659, Pedro Gil Street, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.57873336, 121.00095830, '1176, G. Apacible Street, 734 Zone 80, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.58089436, 120.99207702, 1.49, 40.00, 'completed', 1, '2026-06-10 01:16:35', '2026-06-10 01:16:39', 0, '2026-06-09 17:09:34', 'Cash', 4.80, 35.20, NULL, NULL, NULL, NULL, 0, NULL),
(27, 'testtest', '1st Street, Fabie Estate, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.57838068, 121.00057046, 'Court Zone, Quirino Avenue Extension, Barangay 831, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.58519609, 120.99537898, 1.37, 40.00, 'completed', 1, '2026-06-10 01:21:34', '2026-06-10 01:21:37', 0, '2026-06-09 17:21:21', 'Cash', 4.80, 35.20, NULL, NULL, NULL, NULL, 0, NULL),
(28, 'testtest', 'Barrio Kapampanga, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57966300, 121.00463177, 'Barcastigue Street, San Miguel, Sixth District, Manila, Capital District, Metro Manila, 1005, Philippines', 14.59310337, 120.99212518, 4.27, 62.70, 'completed', 1, '2026-06-10 01:23:51', '2026-06-10 01:23:53', 0, '2026-06-09 17:23:38', 'Cash', 7.52, 55.18, NULL, NULL, NULL, NULL, 0, NULL),
(29, 'testtest', 'Barrio Kapampanga, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57964432, 121.00464492, 'PNB, Cristobal Street, Barangay 831, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.58507662, 120.99369230, 1.95, 40.00, '', NULL, NULL, NULL, 0, '2026-06-09 17:28:18', 'Cash', 0.00, 0.00, NULL, NULL, NULL, NULL, 0, NULL),
(30, 'testtest', 'Barrio Kapampanga, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57965290, 121.00463710, 'Adamson University MRF, San Marcelino Street, Ermita, Fifth District, Manila, Capital District, Metro Manila, 1000, Philippines', 14.58643614, 120.98699678, 2.86, 48.60, 'completed', 1, '2026-06-10 01:32:27', '2026-06-10 01:32:29', 0, '2026-06-09 17:31:57', 'Cash', 5.83, 42.77, NULL, NULL, NULL, NULL, 0, NULL),
(31, 'testtest', 'Barrio Kapampanga, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57964685, 121.00463453, 'Quirino Avenue Extension, Barangay 831, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.58399614, 120.99558011, 1.61, 40.00, 'completed', 1, '2026-06-10 01:36:06', '2026-06-10 01:36:08', 0, '2026-06-09 17:35:30', 'Cash', 4.80, 35.20, NULL, NULL, NULL, NULL, 0, NULL),
(32, 'testtest', 'National Archives of the Philippines Paco Extension Office, 1153, Cristobal Street, Barangay 831, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.58413639, 120.99353129, 'Concordia College, Pedro Gil Street, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.57968975, 121.00254791, 1.62, 40.00, 'completed', 1, '2026-06-10 03:31:26', '2026-06-10 03:31:29', 0, '2026-06-09 19:30:58', 'Cash', 4.80, 35.20, NULL, NULL, NULL, NULL, 0, NULL),
(33, 'testtest', 'Barrio Kapampanga, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57964208, 121.00464858, 'D. Romualdez Sr. Street, Ermita, Fifth District, Manila, Capital District, Metro Manila, 1000, Philippines', 14.58426106, 120.98713378, 2.67, 46.70, 'completed', 1, '2026-06-10 03:48:43', '2026-06-10 03:48:47', 0, '2026-06-09 19:48:22', 'Cash', 5.60, 41.10, NULL, NULL, NULL, NULL, 0, NULL),
(34, 'testtest', '1st Street, Fabie Estate, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.57761183, 120.99816525, 'Mabini Flyover, San Miguel, Sixth District, Manila, Capital District, Metro Manila, 1213, Philippines', 14.59834840, 121.00074143, 4.32, 63.20, 'completed', 1, '2026-06-10 20:18:57', '2026-06-10 20:19:00', 0, '2026-06-10 12:18:36', 'Cash', 7.58, 55.62, NULL, NULL, NULL, NULL, 0, NULL),
(35, 'testtest', 'Pedro Gil Street, Barangay 685, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.57889021, 120.99758148, 'Philippine Normal University, Ayala Boulevard, 659, Ermita, Fifth District, Manila, Capital District, Metro Manila, 1000, Philippines', 14.58686451, 120.98264694, 2.93, 49.30, 'completed', 1, '2026-06-14 18:26:03', '2026-06-14 18:27:02', 0, '2026-06-14 10:25:54', 'Cash', 5.92, 43.38, NULL, NULL, NULL, NULL, 0, NULL),
(36, 'Airo Pakadyot III', 'Barrio Kapampanga, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57963061, 121.00463352, 'Central Street, Balagtas, 848, Pandacan, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.59137069, 121.00367546, 2.28, 42.80, 'completed', 1, '2026-06-17 02:22:22', '2026-06-17 02:22:37', 0, '2026-06-16 17:29:36', 'Cash', 5.14, 37.66, NULL, NULL, NULL, NULL, 0, NULL),
(37, 'Airo Pakadyot III', 'Fabie Street, Barangay 815, Paco, Fifth District, Manila, Capital District, Metro Manila, 1007, Philippines', 14.57998068, 121.00035816, 'Shore Street, Shore 2 Residences, Barangay 76, Zone 10, District 1, Pasay, Southern Manila District, Metro Manila, 1308, Philippines', 14.54237560, 120.98577243, 7.79, 97.90, 'completed', 1, '2026-06-17 02:23:19', '2026-06-17 02:23:29', 0, '2026-06-16 18:23:10', 'Cash', 11.75, 86.15, NULL, NULL, NULL, NULL, 0, '9a418df52c8e366ea941e065e1f4204d'),
(38, 'testtest', 'Barrio Kapampanga, Santa Ana Villas, 880, Santa Ana, Sixth District, Manila, Capital District, Metro Manila, 1011, Philippines', 14.57966407, 121.00463913, 'San Marcelino Street, Ermita, Fifth District, Manila, Capital District, Metro Manila, 1000, Philippines', 14.58566008, 120.98668098, 2.83, 48.30, 'completed', 1, '2026-06-19 19:31:24', '2026-06-19 19:31:33', 0, '2026-06-19 11:30:45', 'Cash', 5.80, 42.50, NULL, NULL, NULL, NULL, 0, 'c0fdf0c9e84b1c137cd12c210f1889a5');

--
-- Indexes for dumped tables
--

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_token` (`tracking_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer_tbl`
--
ALTER TABLE `customer_tbl`
  MODIFY `customer_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `driver_tbl`
--
ALTER TABLE `driver_tbl`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ride_requests`
--
ALTER TABLE `ride_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
