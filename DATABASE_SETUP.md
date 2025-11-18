-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 18, 2025 at 11:07 PM
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
  `phone_number` varchar(20) NOT NULL,
  `year_level` varchar(50) NOT NULL,
  `course_section` varchar(100) NOT NULL,
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_vip` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `seat_id`, `event_id`, `student_name`, `phone_number`, `year_level`, `course_section`, `booked_at`, `is_vip`) VALUES
(16, 1, 3, 'Genesis A. Perez', '09123456789', '4th Year', 'BSCS', '2025-11-18 11:56:58', 0),
(18, 24, 3, 'ksks jsjs', '09123456780', '4th Year', 'BSCS', '2025-11-18 12:21:48', 0),
(19, 27, 3, 'Juan Dela Cruz', '09123456781', '4th Year', 'BSCS', '2025-11-18 12:22:43', 0),
(20, 8, 3, 'Erika Amano', '09123456799', '4th Year', 'BSCS', '2025-11-18 12:39:26', 0),
(21, 6, 4, 'Bea P. Baldonado', '09123456000', '4th Year', 'BSCS', '2025-11-18 16:57:29', 0),
(23, 1, 6, 'VIP: Reserved Guest', '000-000-0000', 'VIP', 'Reserved Seat', '2025-11-18 17:23:56', 1),
(27, 5, 3, 'VIP: Reserved Guest', '000-000-0000', 'VIP', 'Reserved Seat', '2025-11-18 17:24:54', 1),
(29, 4, 3, 'VIP Seat A4: Reserved Guest', '555-004-VIP', 'VIP', 'Reserved Seat', '2025-11-18 17:27:42', 1),
(31, 2, 6, 'VIP Seat A2: Reserved Guest', '555-002-VIP', 'VIP', 'Reserved Seat', '2025-11-18 17:28:08', 1),
(32, 3, 6, 'VIP Seat A3: Reserved Guest', '555-003-VIP', 'VIP', 'Reserved Seat', '2025-11-18 17:28:10', 1),
(33, 4, 6, 'VIP Seat A4: Reserved Guest', '555-004-VIP', 'VIP', 'Reserved Seat', '2025-11-18 17:28:12', 1),
(34, 5, 6, 'VIP Seat A5: Reserved Guest', '555-005-VIP', 'VIP', 'Reserved Seat', '2025-11-18 17:28:14', 1),
(35, 6, 6, 'VIP Seat A6: Reserved Guest', '555-006-VIP', 'VIP', 'Reserved Seat', '2025-11-18 17:28:17', 1),
(37, 8, 6, 'VIP Seat A8: Reserved Guest', '555-008-VIP', 'VIP', 'Reserved Seat', '2025-11-18 17:28:22', 1),
(38, 9, 6, 'VIP Seat A9: Reserved Guest', '555-009-VIP', 'VIP', 'Reserved Seat', '2025-11-18 17:28:24', 1),
(39, 10, 6, 'VIP Seat A10: Reserved Guest', '555-010-VIP', 'VIP', 'Reserved Seat', '2025-11-18 17:28:26', 1),
(40, 2, 3, 'Melanie Degamo', '09123456011', '3rd Year', 'BSA', '2025-11-18 18:02:18', 0),
(41, 23, 6, 'Genesis A. Perez', '09123456789', '4th Year', 'BSCS', '2025-11-18 18:02:41', 0),
(42, 22, 6, 'Melanie Degamo', '09123456011', '3rd Year', 'BSA', '2025-11-18 18:03:08', 0),
(43, 5, 7, 'Genesis A. Perez', '09123456789', '4th Year', 'BSCS', '2025-11-18 18:03:27', 0),
(46, 10, 7, 'VIP Seat A10: Reserved Guest', '555-010-VIP', 'VIP', 'Reserved Seat', '2025-11-18 18:04:12', 1),
(47, 20, 7, 'VIP Seat B10: Reserved Guest', '555-020-VIP', 'VIP', 'Reserved Seat', '2025-11-18 18:04:14', 1),
(48, 6, 3, 'VIP Seat A6: Reserved Guest', '555-006-VIP', 'VIP', 'Reserved Seat', '2025-11-18 19:09:08', 1),
(50, 7, 3, 'VIP Seat A7: Reserved Guest', '555-007-VIP', 'VIP', 'Reserved Seat', '2025-11-18 19:09:58', 1),
(51, 7, 6, 'Juan Dela Cruz', '09123456780', '4th Year', 'BSCS', '2025-11-18 19:15:22', 0),
(54, 6, 9, 'VIP Seat A6: Reserved Guest', '555-006-VIP', 'VIP', 'Reserved Seat', '2025-11-18 20:36:30', 1),
(55, 7, 9, 'VIP Seat A7: Reserved Guest', '555-007-VIP', 'VIP', 'Reserved Seat', '2025-11-18 20:36:33', 1),
(56, 8, 9, 'VIP Seat A8: Reserved Guest', '555-008-VIP', 'VIP', 'Reserved Seat', '2025-11-18 20:36:36', 1),
(57, 3, 9, 'Genesis A. Perez', '09123456789', '4th Year', 'BSCS', '2025-11-18 20:37:03', 0),
(58, 24, 9, 'Reniel Medrano', '09111111111', '4th Year', 'BSCS', '2025-11-18 20:37:32', 0),
(59, 23, 9, 'Joshua Charles Garcia', '09111111112', '4th Year', 'BSCS', '2025-11-18 20:38:22', 0),
(60, 38, 9, 'Kirby Caringal', '09111111132', '4th Year', 'BSCS', '2025-11-18 20:38:42', 0),
(61, 19, 9, 'Ariel Escobilla', '09111111134', '4th Year', 'BSCS', '2025-11-18 20:39:28', 0),
(62, 31, 9, 'Ljay Bico', '09111111122', '4th Year', 'BSCS', '2025-11-18 20:39:48', 0),
(63, 50, 9, 'Dexter Francisco', '09111111165', '4th Year', 'BSCS', '2025-11-18 20:40:20', 0);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `event_details` text DEFAULT NULL,
  `person_in_charge` varchar(255) DEFAULT NULL,
  `status` enum('active','completed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reservations_enabled` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `date`, `event_details`, `person_in_charge`, `status`, `created_at`, `updated_at`, `reservations_enabled`) VALUES
(1, 'Spring Musical 2025', '2025-10-23', NULL, NULL, 'completed', '2025-11-18 00:21:36', '2025-11-18 01:33:11', 1),
(2, 'Drama Club Performance', '2025-10-20', NULL, NULL, 'completed', '2025-11-18 00:21:36', '2025-11-18 01:32:40', 1),
(3, 'BSCS Panitikan Finals', '2025-11-20', 'Performance of BSCS 4th Year Students in subject Panitikan...', 'Ms. Trecia Manalo', 'active', '2025-11-18 00:29:52', '2025-11-18 16:59:02', 1),
(4, 'Testing 1', '2025-11-18', 'testing', '', 'completed', '2025-11-18 00:30:07', '2025-11-18 17:01:18', 1),
(5, 'Testing 3', '2025-11-19', NULL, NULL, 'completed', '2025-11-18 01:44:18', '2025-11-18 01:58:55', 1),
(6, 'BSCS Seminar', '2025-11-17', 'Web Development Seminar with Guest Speakers', 'Mr. Aldwin M. Ilumin', 'completed', '2025-11-18 14:30:03', '2025-11-18 20:31:01', 1),
(7, 'Newest Event', '2025-11-22', 'filter testing purposes', 'Gen -', 'completed', '2025-11-18 15:55:54', '2025-11-18 20:32:29', 0),
(8, 'Thesis Presentation', '2025-11-28', 'Presentation of Thesis of BSBA-4 Students', 'Ms. Elocelle Delgado', 'completed', '2025-11-18 19:11:30', '2025-11-18 20:33:32', 0),
(9, 'Presentation in PHP', '2025-11-19', 'Presentation of BSCS 4th year students in Parallel and Distributed Computing subject', 'Mr. Aldwin M. Ilumin', 'active', '2025-11-18 20:36:08', '2025-11-18 20:42:05', 0);

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
(8, 56, 7, 'maintenance', '2025-11-18 18:05:44'),
(9, 7, 3, 'maintenance', '2025-11-18 19:09:49'),
(10, 51, 6, 'maintenance', '2025-11-18 20:10:21'),
(11, 41, 8, 'maintenance', '2025-11-18 20:12:48'),
(12, 41, 8, 'maintenance', '2025-11-18 20:24:42'),
(13, 51, 8, 'maintenance', '2025-11-18 20:26:17'),
(14, 53, 8, 'maintenance', '2025-11-18 20:26:26'),
(15, 50, 8, 'maintenance', '2025-11-18 20:26:31'),
(16, 52, 3, 'maintenance', '2025-11-18 20:41:15');

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
(1, 1, 1, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(2, 1, 2, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(3, 1, 3, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(4, 1, 4, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(5, 1, 5, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(6, 1, 6, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(7, 1, 7, 'available', '2025-11-18 11:19:47', '2025-11-18 19:09:53'),
(8, 1, 8, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(9, 1, 9, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(10, 1, 10, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(11, 2, 1, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(12, 2, 2, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(13, 2, 3, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(14, 2, 4, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(15, 2, 5, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(16, 2, 6, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(17, 2, 7, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(18, 2, 8, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(19, 2, 9, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(20, 2, 10, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(21, 3, 1, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(22, 3, 2, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(23, 3, 3, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(24, 3, 4, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(25, 3, 5, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(26, 3, 6, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(27, 3, 7, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(28, 3, 8, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(29, 3, 9, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(30, 3, 10, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(31, 4, 1, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(32, 4, 2, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(33, 4, 3, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(34, 4, 4, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(35, 4, 5, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(36, 4, 6, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(37, 4, 7, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(38, 4, 8, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(39, 4, 9, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(40, 4, 10, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(41, 5, 1, 'available', '2025-11-18 11:19:47', '2025-11-18 20:24:48'),
(42, 5, 2, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(43, 5, 3, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(44, 5, 4, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(45, 5, 5, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(46, 5, 6, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(47, 5, 7, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(48, 5, 8, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(49, 5, 9, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(50, 5, 10, 'available', '2025-11-18 11:19:47', '2025-11-18 20:33:17'),
(51, 6, 1, 'available', '2025-11-18 11:19:47', '2025-11-18 20:33:24'),
(52, 6, 2, 'maintenance', '2025-11-18 11:19:47', '2025-11-18 20:41:15'),
(53, 6, 3, 'available', '2025-11-18 11:19:47', '2025-11-18 20:33:20'),
(54, 6, 4, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(55, 6, 5, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(56, 6, 6, 'available', '2025-11-18 11:19:47', '2025-11-18 18:06:20'),
(57, 6, 7, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(58, 6, 8, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(59, 6, 9, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47'),
(60, 6, 10, 'available', '2025-11-18 11:19:47', '2025-11-18 11:19:47');

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
  ADD UNIQUE KEY `unique_name_event` (`event_id`,`student_name`),
  ADD UNIQUE KEY `unique_phone_event` (`event_id`,`phone_number`),
  ADD KEY `idx_bookings_event` (`event_id`),
  ADD KEY `idx_bookings_seat` (`seat_id`),
  ADD KEY `idx_bookings_date` (`booked_at`),
  ADD KEY `idx_student_phone` (`student_name`,`phone_number`),
  ADD KEY `idx_student_name` (`student_name`),
  ADD KEY `idx_phone_number` (`phone_number`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `maintenance_history`
--
ALTER TABLE `maintenance_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

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