-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 27, 2023 at 08:45 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ekattor8_v9`
--

-- --------------------------------------------------------

--
-- Table structure for table `frontend_features`
--

DROP TABLE IF EXISTS `frontend_features`;
CREATE TABLE `frontend_features` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frontend_features`
--

INSERT INTO `frontend_features` (`id`, `title`, `description`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'Students Admission', 'Your schools can add their students in two different ways.', 'fas fa-graduation-cap', '2023-11-30 10:15:46', '2023-12-19 08:34:00'),
(2, 'Daily Attendance', 'Take your students attendance in a smart way', 'fas fa-arrows-alt-h', '2023-11-30 10:18:02', '2023-12-19 08:12:59'),
(3, 'Class List', 'Manage your schools class list whenever you want.', 'fab fa-black-tie', '2023-11-30 10:18:48', '2023-12-19 08:12:47'),
(4, 'Subjects', 'Add different subjects for different classes.', 'far fa-address-card', '2023-11-30 10:25:05', '2023-12-19 08:12:34'),
(5, 'Event Calender', 'The school admin can manage their schools events separately.', 'fab fa-accusoft', '2023-12-11 07:51:50', '2023-12-19 07:50:51'),
(8, 'Routine', 'Manage your schools class routine easily.', 'far fa-address-card', '2023-12-18 08:57:16', '2023-12-19 08:12:05'),
(11, 'Student Information', 'Add your students information within a few minute', 'fab fa-affiliatetheme', '2023-12-18 10:16:12', '2023-12-19 08:02:27'),
(12, 'Syllabus', 'Manage syllabuses based on the classes.', 'fab fa-android', '2023-12-18 10:16:22', '2023-12-19 08:02:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `frontend_features`
--
ALTER TABLE `frontend_features`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `frontend_features`
--
ALTER TABLE `frontend_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
