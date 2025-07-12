-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 13, 2023 at 10:04 AM
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
-- Database: `ekattor8_sms`
--

-- --------------------------------------------------------

--
-- Table structure for table `sms_settings`
--

CREATE TABLE `sms_settings` (
  `id` int(11) NOT NULL,
  `twilio_sid` varchar(255) DEFAULT NULL,
  `twilio_token` varchar(255) DEFAULT NULL,
  `twilio_from` varchar(255) DEFAULT NULL,
  `msg91_authentication_key` varchar(255) DEFAULT NULL,
  `msg91_sender_id` varchar(255) DEFAULT NULL,
  `msg91_route` varchar(255) DEFAULT NULL,
  `msg91_country_code` varchar(255) DEFAULT NULL,
  `school_id` int(50) NOT NULL,
  `active_sms` varchar(50) DEFAULT NULL,
  `updated_at` varchar(255) NOT NULL,
  `created_at` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sms_settings`
--

INSERT INTO `sms_settings` (`id`, `twilio_sid`, `twilio_token`, `twilio_from`, `msg91_authentication_key`, `msg91_sender_id`, `msg91_route`, `msg91_country_code`, `school_id`, `active_sms`, `updated_at`, `created_at`) VALUES
(1, 'Test_sid_xxxxxxxxx', 'Test_token_xxxxxxxx', 'Test_number_xxxxxxxxx', 'Test_auth_xxxxxxxxx', 'Test_sender_id_xxxxxxxxxxxx', 'Test_route_xxxxxxxxxxx', 'Test_country_code_xxxx', 1, 'none', '2023-07-12 13:33:57', '2023-06-24 17:03:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sms_settings`
--
ALTER TABLE `sms_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sms_settings`
--
ALTER TABLE `sms_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
