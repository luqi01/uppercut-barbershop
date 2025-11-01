-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 01, 2025 at 08:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barber_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `barber_id` int(11) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointment_services`
--

CREATE TABLE `appointment_services` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `password`) VALUES
(1, 'Luqmaan Haffejee', '0646871511', 'luqmaanhaffejee80@gmail.com', '$2y$10$asLp5NI6F2S/z8455Ge8G.nxClDs45AFHqLzEhkJGTTYrtyP2FeXy'),
(4, 'test', '0764851211', 'luqmaanhaffejee04@gmail.com', '$2y$10$cWZ.Sj6xixR1G6ztgy9qlu2B/Y0jQ4xUyV36z46onkQMv1avkGkna');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int(3) NOT NULL DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `price`, `duration`) VALUES
(1, 'Haircut', 120.00, 30),
(2, 'Beard Trim', 80.00, 30),
(3, 'Full Grooming', 200.00, 30),
(4, 'Haircut Men', 130.00, 30),
(5, 'Haircut Boys', 80.00, 30),
(6, 'Boys Fade', 100.00, 30),
(7, 'Blade Fades', 70.00, 30),
(8, 'Steam Shave', 80.00, 30),
(9, 'Semi Permanent Beard Colour', 50.00, 30),
(10, 'Hot Towel Shave', 60.00, 30),
(11, 'Shampoo After Cut', 30.00, 15),
(12, 'Brazilian Blowouts Short', 350.00, 60),
(13, 'Brazilian Beard Blowout', 300.00, 45),
(14, 'Line', 30.00, 15),
(15, 'Beard Machine Trim', 20.00, 15),
(16, 'Pensioners', 50.00, 30),
(17, 'Basic Facial', 200.00, 45),
(18, 'Deep Cleanse', 250.00, 45),
(19, 'Black Peel Off Mask Detoxifying', 50.00, 30),
(20, 'Eye Treatment', 100.00, 30),
(21, 'Manicure', 150.00, 45),
(22, 'Express Manicure', 70.00, 20),
(23, 'Pedicure', 150.00, 45),
(24, 'Express Pedicure', 70.00, 20),
(25, 'Foot Peel', 250.00, 30),
(26, 'Full Body Massage', 170.00, 60),
(27, 'Sinus Massage 20min', 90.00, 20),
(28, 'Neck and Back 30min', 80.00, 30),
(29, 'Neck and Back 20min', 60.00, 20),
(30, 'Leg and Feet 30min', 80.00, 30),
(31, 'Back Scrub and Massage 45min', 180.00, 45),
(32, 'Sport Massage', 180.00, 45),
(33, 'Full Body + Hot Stones 2hr', 400.00, 120),
(34, 'Full Body + Hot Stones 90min', 320.00, 90),
(35, 'Nose Wax', 50.00, 10),
(36, 'Eyebrows', 10.00, 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','barber') DEFAULT 'barber'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'Faizal', '$2y$10$TbrwPBac3jB6NAE6NcfIDOrTWw4.K3c/sAkrqoulPTAXDQrgDgP76', 'barber'),
(2, 'Zaid', '$2y$10$D79TYtQ5VQIddTbnbbUdreKYKOfuD9899nCLgaxBDgdt4B6H1KFDe', 'barber');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `barber_id` (`barber_id`);

--
-- Indexes for table `appointment_services`
--
ALTER TABLE `appointment_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `appointment_services`
--
ALTER TABLE `appointment_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`barber_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `appointment_services`
--
ALTER TABLE `appointment_services`
  ADD CONSTRAINT `appointment_services_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
