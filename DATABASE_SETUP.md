-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 18, 2025 at 02:25 AM
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
-- Database: `theater_seat_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$lTNT.SLF42WBNcLjre0UBuTh6GLa0rOczAHGmQQiaebcMaoWCObFG', '2025-11-18 00:21:36');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `seat_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `status` enum('active','completed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Spring Musical 2025', '2025-03-15', 'completed', '2025-11-18 00:21:36', '2025-11-18 00:28:33'),
(2, 'Drama Club Performance', '2025-04-20', 'completed', '2025-11-18 00:21:36', '2025-11-18 00:28:33'),
(3, 'haha', '2025-11-20', 'active', '2025-11-18 00:29:52', '2025-11-18 00:29:52'),
(4, 'hehe', '2025-11-28', 'active', '2025-11-18 00:30:07', '2025-11-18 00:30:07');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_history`
--

CREATE TABLE `maintenance_history` (
  `id` int(11) NOT NULL,
  `seat_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `status` enum('maintenance','available') DEFAULT 'maintenance',
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `maintenance_history`
--

INSERT INTO `maintenance_history` (`id`, `seat_id`, `event_id`, `status`, `recorded_at`) VALUES
(1, 10, 1, 'maintenance', '2025-11-18 00:29:16'),
(2, 10, 4, 'maintenance', '2025-11-18 00:37:45');

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `id` int(11) NOT NULL,
  `row_number` int(11) NOT NULL,
  `seat_number` int(11) NOT NULL,
  `status` enum('available','maintenance') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`id`, `row_number`, `seat_number`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(2, 1, 2, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(3, 1, 3, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(4, 1, 4, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(5, 1, 5, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(6, 1, 6, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(7, 1, 9, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(8, 1, 10, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(9, 1, 11, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(10, 1, 12, 'available', '2025-11-18 00:21:36', '2025-11-18 00:41:07'),
(11, 1, 13, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(12, 1, 14, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(13, 2, 1, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(14, 2, 2, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(15, 2, 3, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(16, 2, 4, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(17, 2, 5, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(18, 2, 6, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(19, 2, 9, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(20, 2, 10, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(21, 2, 11, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(22, 2, 12, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(23, 2, 13, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(24, 2, 14, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(25, 3, 1, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(26, 3, 2, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(27, 3, 3, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(28, 3, 4, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(29, 3, 5, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(30, 3, 6, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(31, 3, 9, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(32, 3, 10, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(33, 3, 11, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(34, 3, 12, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(35, 3, 13, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(36, 3, 14, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(37, 4, 1, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(38, 4, 2, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(39, 4, 3, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(40, 4, 4, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(41, 4, 5, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(42, 4, 6, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(43, 4, 9, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(44, 4, 10, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(45, 4, 11, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(46, 4, 12, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(47, 4, 13, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(48, 4, 14, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(49, 5, 1, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(50, 5, 2, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(51, 5, 3, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(52, 5, 4, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(53, 5, 5, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(54, 5, 6, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(55, 5, 9, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(56, 5, 10, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(57, 5, 11, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(58, 5, 12, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(59, 5, 13, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(60, 5, 14, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(61, 6, 1, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(62, 6, 2, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(63, 6, 3, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(64, 6, 4, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(65, 6, 5, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(66, 6, 6, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(67, 6, 9, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(68, 6, 10, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(69, 6, 11, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(70, 6, 12, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(71, 6, 13, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(72, 6, 14, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(73, 7, 1, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(74, 7, 2, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(75, 7, 3, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(76, 7, 4, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(77, 7, 5, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(78, 7, 6, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(79, 7, 9, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(80, 7, 10, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(81, 7, 11, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(82, 7, 12, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(83, 7, 13, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(84, 7, 14, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(85, 8, 1, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(86, 8, 2, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(87, 8, 3, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(88, 8, 4, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(89, 8, 5, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(90, 8, 6, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(91, 8, 9, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(92, 8, 10, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(93, 8, 11, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(94, 8, 12, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(95, 8, 13, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(96, 8, 14, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(97, 9, 1, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(98, 9, 2, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(99, 9, 3, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(100, 9, 4, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(101, 9, 5, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(102, 9, 6, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(103, 9, 9, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(104, 9, 10, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(105, 9, 11, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(106, 9, 12, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(107, 9, 13, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(108, 9, 14, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(109, 10, 1, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(110, 10, 2, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(111, 10, 3, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(112, 10, 4, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(113, 10, 5, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(114, 10, 6, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(115, 10, 9, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(116, 10, 10, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(117, 10, 11, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(118, 10, 12, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(119, 10, 13, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36'),
(120, 10, 14, 'available', '2025-11-18 00:21:36', '2025-11-18 00:21:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_booking` (`seat_id`,`event_id`),
  ADD KEY `idx_bookings_event` (`event_id`),
  ADD KEY `idx_bookings_seat` (`seat_id`),
  ADD KEY `idx_bookings_date` (`booked_at`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_events_status` (`status`),
  ADD KEY `idx_events_date` (`date`);

--
-- Indexes for table `maintenance_history`
--
ALTER TABLE `maintenance_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seat_id` (`seat_id`),
  ADD KEY `idx_maintenance_event` (`event_id`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_seat` (`row_number`,`seat_number`),
  ADD KEY `idx_seats_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `maintenance_history`
--
ALTER TABLE `maintenance_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_history`
--
ALTER TABLE `maintenance_history`
  ADD CONSTRAINT `maintenance_history_ibfk_1` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_history_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;