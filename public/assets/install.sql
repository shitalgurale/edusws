-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 02, 2023 at 09:21 AM
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
-- Database: `ekattor8_v8ins`
--

-- --------------------------------------------------------

--
-- Table structure for table `addons`
--

CREATE TABLE `addons` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `features` varchar(255) DEFAULT NULL,
  `version` float DEFAULT NULL,
  `purchase_code` varchar(255) DEFAULT NULL,
  `unique_identifier` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `copies` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `book_issues`
--

CREATE TABLE `book_issues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `book_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `issue_date` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `school_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_rooms`
--

CREATE TABLE `class_rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `school_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

CREATE TABLE `currency` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `symbol` varchar(255) DEFAULT NULL,
  `paypal_supported` int(11) DEFAULT NULL,
  `stripe_supported` int(11) DEFAULT NULL,
  `flutterwave_supported` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `currency`
--

INSERT INTO `currency` (`id`, `name`, `code`, `symbol`, `paypal_supported`, `stripe_supported`, `flutterwave_supported`) VALUES
(1, 'US Dollar', 'USD', '$', 1, 1, 1),
(2, 'Albanian Lek', 'ALL', 'Lek', 0, 1, 0),
(3, 'Algerian Dinar', 'DZD', 'دج', 1, 1, 0),
(4, 'Angolan Kwanza', 'AOA', 'Kz', 1, 1, 0),
(5, 'Argentine Peso', 'ARS', '$', 1, 1, 1),
(6, 'Armenian Dram', 'AMD', '֏', 1, 1, 0),
(7, 'Aruban Florin', 'AWG', 'ƒ', 1, 1, 0),
(8, 'Australian Dollar', 'AUD', '$', 1, 1, 0),
(9, 'Azerbaijani Manat', 'AZN', 'm', 1, 1, 0),
(10, 'Bahamian Dollar', 'BSD', 'B$', 1, 1, 0),
(11, 'Bahraini Dinar', 'BHD', '.د.ب', 1, 1, 0),
(12, 'Bangladeshi Taka', 'BDT', '৳', 1, 1, 0),
(13, 'Barbadian Dollar', 'BBD', 'Bds$', 1, 1, 0),
(14, 'Belarusian Ruble', 'BYR', 'Br', 0, 0, 0),
(15, 'Belgian Franc', 'BEF', 'fr', 1, 1, 0),
(16, 'Belize Dollar', 'BZD', '$', 1, 1, 0),
(17, 'Bermudan Dollar', 'BMD', '$', 1, 1, 0),
(18, 'Bhutanese Ngultrum', 'BTN', 'Nu.', 1, 1, 0),
(19, 'Bitcoin', 'BTC', '฿', 1, 1, 0),
(20, 'Bolivian Boliviano', 'BOB', 'Bs.', 1, 1, 0),
(21, 'Bosnia', 'BAM', 'KM', 1, 1, 0),
(22, 'Botswanan Pula', 'BWP', 'P', 1, 1, 0),
(23, 'Brazilian Real', 'BRL', 'R$', 1, 1, 1),
(24, 'British Pound Sterling', 'GBP', '£', 1, 1, 1),
(25, 'Brunei Dollar', 'BND', 'B$', 1, 1, 0),
(26, 'Bulgarian Lev', 'BGN', 'Лв.', 1, 1, 0),
(27, 'Burundian Franc', 'BIF', 'FBu', 1, 1, 0),
(28, 'Cambodian Riel', 'KHR', 'KHR', 1, 1, 0),
(29, 'Canadian Dollar', 'CAD', '$', 1, 1, 1),
(30, 'Cape Verdean Escudo', 'CVE', '$', 1, 1, 1),
(31, 'Cayman Islands Dollar', 'KYD', '$', 1, 1, 0),
(32, 'CFA Franc BCEAO', 'XOF', 'CFA', 1, 1, 1),
(33, 'CFA Franc BEAC', 'XAF', 'FCFA', 1, 1, 1),
(34, 'CFP Franc', 'XPF', '₣', 1, 1, 0),
(35, 'Chilean Peso', 'CLP', '$', 1, 1, 1),
(36, 'Chinese Yuan', 'CNY', '¥', 1, 1, 0),
(37, 'Colombian Peso', 'COP', '$', 1, 1, 0),
(38, 'Comorian Franc', 'KMF', 'CF', 1, 1, 0),
(39, 'Congolese Franc', 'CDF', 'FC', 1, 1, 1),
(40, 'Costa Rican ColÃ³n', 'CRC', '₡', 1, 1, 0),
(41, 'Croatian Kuna', 'HRK', 'kn', 1, 1, 0),
(42, 'Cuban Convertible Peso', 'CUC', '$, CUC', 1, 1, 0),
(43, 'Czech Republic Koruna', 'CZK', 'Kč', 1, 1, 0),
(44, 'Danish Krone', 'DKK', 'Kr.', 1, 1, 0),
(45, 'Djiboutian Franc', 'DJF', 'Fdj', 1, 1, 0),
(46, 'Dominican Peso', 'DOP', '$', 1, 1, 0),
(47, 'East Caribbean Dollar', 'XCD', '$', 1, 1, 0),
(48, 'Egyptian Pound', 'EGP', 'ج.م', 1, 1, 1),
(49, 'Eritrean Nakfa', 'ERN', 'Nfk', 1, 1, 0),
(50, 'Estonian Kroon', 'EEK', 'kr', 1, 1, 0),
(51, 'Ethiopian Birr', 'ETB', 'Nkf', 1, 1, 0),
(52, 'Euro', 'EUR', '€', 1, 1, 1),
(53, 'Falkland Islands Pound', 'FKP', '£', 1, 1, 0),
(54, 'Fijian Dollar', 'FJD', 'FJ$', 1, 1, 0),
(55, 'Gambian Dalasi', 'GMD', 'D', 1, 1, 1),
(56, 'Georgian Lari', 'GEL', 'ლ', 1, 1, 0),
(57, 'German Mark', 'DEM', 'DM', 1, 1, 0),
(58, 'Ghanaian Cedi', 'GHS', 'GH₵', 1, 1, 1),
(59, 'Gibraltar Pound', 'GIP', '£', 1, 1, 0),
(60, 'Greek Drachma', 'GRD', '₯, Δρχ, Δρ', 1, 1, 0),
(61, 'Guatemalan Quetzal', 'GTQ', 'Q', 1, 1, 0),
(62, 'Guinean Franc', 'GNF', 'FG', 1, 1, 1),
(63, 'Guyanaese Dollar', 'GYD', '$', 1, 1, 0),
(64, 'Haitian Gourde', 'HTG', 'G', 1, 1, 0),
(65, 'Honduran Lempira', 'HNL', 'L', 1, 1, 0),
(66, 'Hong Kong Dollar', 'HKD', '$', 1, 1, 0),
(67, 'Hungarian Forint', 'HUF', 'Ft', 1, 1, 0),
(68, 'Icelandic KrÃ³na', 'ISK', 'kr', 1, 1, 0),
(69, 'Indian Rupee', 'INR', '₹', 1, 1, 0),
(70, 'Indonesian Rupiah', 'IDR', 'Rp', 1, 1, 0),
(71, 'Iranian Rial', 'IRR', '﷼', 1, 1, 0),
(72, 'Iraqi Dinar', 'IQD', 'د.ع', 1, 1, 0),
(73, 'Israeli New Sheqel', 'ILS', '₪', 1, 1, 0),
(74, 'Italian Lira', 'ITL', 'L,£', 1, 1, 0),
(75, 'Jamaican Dollar', 'JMD', 'J$', 1, 1, 0),
(76, 'Japanese Yen', 'JPY', '¥', 1, 1, 0),
(77, 'Jordanian Dinar', 'JOD', 'ا.د', 1, 1, 0),
(78, 'Kazakhstani Tenge', 'KZT', 'лв', 1, 1, 0),
(79, 'Kenyan Shilling', 'KES', 'KSh', 1, 1, 1),
(80, 'Kuwaiti Dinar', 'KWD', 'ك.د', 1, 1, 0),
(81, 'Kyrgystani Som', 'KGS', 'лв', 1, 1, 0),
(82, 'Laotian Kip', 'LAK', '₭', 1, 1, 0),
(83, 'Latvian Lats', 'LVL', 'Ls', 0, 0, 0),
(84, 'Lebanese Pound', 'LBP', '£', 1, 1, 0),
(85, 'Lesotho Loti', 'LSL', 'L', 1, 1, 0),
(86, 'Liberian Dollar', 'LRD', '$', 1, 1, 1),
(87, 'Libyan Dinar', 'LYD', 'د.ل', 1, 1, 0),
(88, 'Lithuanian Litas', 'LTL', 'Lt', 0, 0, 0),
(89, 'Macanese Pataca', 'MOP', '$', 1, 1, 0),
(90, 'Macedonian Denar', 'MKD', 'ден', 1, 1, 0),
(91, 'Malagasy Ariary', 'MGA', 'Ar', 1, 1, 0),
(92, 'Malawian Kwacha', 'MWK', 'MK', 1, 1, 1),
(93, 'Malaysian Ringgit', 'MYR', 'RM', 1, 1, 0),
(94, 'Maldivian Rufiyaa', 'MVR', 'Rf', 1, 1, 0),
(95, 'Mauritanian Ouguiya', 'MRO', 'MRU', 1, 1, 0),
(96, 'Mauritian Rupee', 'MUR', '₨', 1, 1, 0),
(97, 'Mexican Peso', 'MXN', '$', 1, 1, 0),
(98, 'Moldovan Leu', 'MDL', 'L', 1, 1, 0),
(99, 'Mongolian Tugrik', 'MNT', '₮', 1, 1, 0),
(100, 'Moroccan Dirham', 'MAD', 'MAD', 1, 1, 1),
(101, 'Mozambican Metical', 'MZM', 'MT', 1, 1, 0),
(102, 'Myanmar Kyat', 'MMK', 'K', 1, 1, 0),
(103, 'Namibian Dollar', 'NAD', '$', 1, 1, 0),
(104, 'Nepalese Rupee', 'NPR', '₨', 1, 1, 0),
(105, 'Netherlands Antillean Guilder', 'ANG', 'ƒ', 1, 1, 0),
(106, 'New Taiwan Dollar', 'TWD', '$', 1, 1, 0),
(107, 'New Zealand Dollar', 'NZD', '$', 1, 1, 0),
(108, 'Nicaraguan CÃ³rdoba', 'NIO', 'C$', 1, 1, 0),
(109, 'Nigerian Naira', 'NGN', '₦', 1, 1, 1),
(110, 'North Korean Won', 'KPW', '₩', 0, 0, 0),
(111, 'Norwegian Krone', 'NOK', 'kr', 1, 1, 0),
(112, 'Omani Rial', 'OMR', '.ع.ر', 0, 0, 0),
(113, 'Pakistani Rupee', 'PKR', '₨', 1, 1, 0),
(114, 'Panamanian Balboa', 'PAB', 'B/.', 1, 1, 0),
(115, 'Papua New Guinean Kina', 'PGK', 'K', 1, 1, 0),
(116, 'Paraguayan Guarani', 'PYG', '₲', 1, 1, 0),
(117, 'Peruvian Nuevo Sol', 'PEN', 'S/.', 1, 1, 0),
(118, 'Philippine Peso', 'PHP', '₱', 1, 1, 0),
(119, 'Polish Zloty', 'PLN', 'zł', 1, 1, 0),
(120, 'Qatari Rial', 'QAR', 'ق.ر', 1, 1, 0),
(121, 'Romanian Leu', 'RON', 'lei', 1, 1, 0),
(122, 'Russian Ruble', 'RUB', '₽', 1, 1, 0),
(123, 'Rwandan Franc', 'RWF', 'FRw', 1, 1, 1),
(124, 'Salvadoran ColÃ³n', 'SVC', '₡', 0, 0, 0),
(125, 'Samoan Tala', 'WST', 'SAT', 1, 1, 0),
(126, 'Saudi Riyal', 'SAR', '﷼', 1, 1, 0),
(127, 'Serbian Dinar', 'RSD', 'din', 1, 1, 0),
(128, 'Seychellois Rupee', 'SCR', 'SRe', 1, 1, 0),
(129, 'Sierra Leonean Leone', 'SLL', 'Le', 1, 1, 1),
(130, 'Singapore Dollar', 'SGD', '$', 1, 1, 0),
(131, 'Slovak Koruna', 'SKK', 'Sk', 1, 1, 0),
(132, 'Solomon Islands Dollar', 'SBD', 'Si$', 1, 1, 0),
(133, 'Somali Shilling', 'SOS', 'Sh.so.', 1, 1, 0),
(134, 'South African Rand', 'ZAR', 'R', 1, 1, 1),
(135, 'South Korean Won', 'KRW', '₩', 1, 1, 0),
(136, 'Special Drawing Rights', 'XDR', 'SDR', 1, 1, 0),
(137, 'Sri Lankan Rupee', 'LKR', 'Rs', 1, 1, 0),
(138, 'St. Helena Pound', 'SHP', '£', 1, 1, 0),
(139, 'Sudanese Pound', 'SDG', '.س.ج', 1, 1, 0),
(140, 'Surinamese Dollar', 'SRD', '$', 1, 1, 0),
(141, 'Swazi Lilangeni', 'SZL', 'E', 1, 1, 0),
(142, 'Swedish Krona', 'SEK', 'kr', 1, 1, 0),
(143, 'Swiss Franc', 'CHF', 'CHf', 1, 1, 0),
(144, 'Syrian Pound', 'SYP', 'LS', 0, 0, 0),
(145, 'São Tomé and Príncipe Dobra', 'STD', 'Db', 1, 1, 1),
(146, 'Tajikistani Somoni', 'TJS', 'SM', 1, 1, 0),
(147, 'Tanzanian Shilling', 'TZS', 'TSh', 1, 1, 1),
(148, 'Thai Baht', 'THB', '฿', 1, 1, 0),
(149, 'Tongan pa\'anga', 'TOP', '$', 1, 1, 0),
(150, 'Trinidad & Tobago Dollar', 'TTD', '$', 1, 1, 0),
(151, 'Tunisian Dinar', 'TND', 'ت.د', 1, 1, 0),
(152, 'Turkish Lira', 'TRY', '₺', 1, 1, 0),
(153, 'Turkmenistani Manat', 'TMT', 'T', 1, 1, 0),
(154, 'Ugandan Shilling', 'UGX', 'UGX', 1, 1, 1),
(155, 'Ukrainian Hryvnia', 'UAH', '₴', 1, 1, 0),
(156, 'United Arab Emirates Dirham', 'AED', 'إ.د', 1, 1, 0),
(157, 'Uruguayan Peso', 'UYU', '$', 1, 1, 0),
(158, 'Afghan Afghani', 'AFA', '؋', 1, 1, 0),
(159, 'Uzbekistan Som', 'UZS', 'лв', 1, 1, 0),
(160, 'Vanuatu Vatu', 'VUV', 'VT', 1, 1, 0),
(161, 'Venezuelan BolÃvar', 'VEF', 'Bs', 0, 0, 0),
(162, 'Vietnamese Dong', 'VND', '₫', 1, 1, 0),
(163, 'Yemeni Rial', 'YER', '﷼', 1, 1, 0),
(164, 'Zambian Kwacha', 'ZMK', 'ZK', 1, 1, 1),
(165, 'PesosColombian Peso', 'COP', '$', 0, 0, 1),
(166, 'SEPA', 'EUR', '€', 0, 0, 1),
(167, 'Mozambican Metical', 'MZN', 'MT', 0, 0, 1),
(168, 'Peruvian Sol', 'SOL', 'S/', 0, 0, 1),
(169, 'Zambian Kwacha', 'ZMW', 'ZK', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `daily_attendances`
--

CREATE TABLE `daily_attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `school_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `exam_type` varchar(255) NOT NULL,
  `starting_time` varchar(255) NOT NULL,
  `ending_time` varchar(255) NOT NULL,
  `total_marks` float NOT NULL,
  `status` varchar(255) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_categories`
--

CREATE TABLE `exam_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `expense_category_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `title`, `description`) VALUES
(1, 'What is Ekattor 8?', 'Ekattor 8 is a collection of programs designed to assist schools in administering their executive responsibilities on a day-to-day basis. Ekattor 8 is an updated version of Ekattor ERP (Enterprise Resource Planning). Also, Ekattor 8 is designed for SAAS (Software as a Service) projects.'),
(2, 'How can I get developed my customer features?', 'Custom features do not coming with product support. You can contact our support center and send us details about your requirement. If our schedule is open, we can give you a quotation and take your project according to the contract.'),
(5, 'Which license to choose for my client project?', 'If you use academy LMS for a commercial project of a client, you will be required extended license.'),
(6, 'How much time will I get developer support?', 'By default, you are entitled to developer support for 6 months from the date of your purchase. Later on anytime you can renew the support pack if you need developer support. If you don’t need any developer support, you don’t need to buy it.');

-- --------------------------------------------------------

--
-- Table structure for table `frontend_events`
--

CREATE TABLE `frontend_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `global_settings`
--

CREATE TABLE `global_settings` (
  `id` int(11) NOT NULL,
  `key` varchar(255) DEFAULT NULL,
  `value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `global_settings`
--

INSERT INTO `global_settings` (`id`, `key`, `value`) VALUES
(1, 'system_name', 'Ekattor School Manager'),
(2, 'system_title', 'Ekattor'),
(3, 'system_email', 'ekattor@example.com'),
(4, 'phone', '900500000'),
(5, 'address', '4333 Factoria Blvd SE, Bellevue, WA 98006'),
(6, 'purchase_code', NULL),
(7, 'system_currency', 'USD'),
(8, 'currency_position', 'left-space'),
(9, 'running_session', '1'),
(10, 'language', 'english'),
(11, 'payment_settings', '[]'),
(12, 'footer_text', 'By Creativeitem'),
(13, 'footer_link', 'http://creativeitem.com/'),
(14, 'version', '1.8'),
(15, 'fax', '1234567890'),
(16, 'timezone', 'Asia/Dhaka'),
(17, 'smtp_protocol', 'smtp'),
(18, 'smtp_crypto', 'tls'),
(19, 'smtp_host', 'smtp.googlemail.com'),
(20, 'smtp_port', '587'),
(21, 'smtp_user', 'zohantesting015@gmail.com'),
(22, 'smtp_pass', 'pqmyrfihhjwzeher'),
(28, 'offline', '{\"status\":\"1\"}'),
(29, 'light_logo', 'light-logo.png'),
(30, 'dark_logo', '16630508541.png'),
(31, 'favicon', 'favicon.png'),
(32, 'randCallRange', '30'),
(33, 'help_link', 'http://support.creativeitem.com/'),
(34, 'youtube_api_key', 'youtube-api-key'),
(35, 'vimeo_api_key', 'vimeo-api-key'),
(36, 'banner_title', 'Bringing Excellence To Students'),
(37, 'banner_subtitle', 'Empowering and inspiring all students to excel as life long learners'),
(38, 'facebook_link', 'https://www.facebook.com/CreativeitemApps'),
(39, 'twitter_link', 'https://twitter.com/creativeitem'),
(40, 'linkedin_link', 'https://www.linkedin.com/company/creativeitem'),
(41, 'instagram_link', 'http://www.instagram.com/creativeitem'),
(42, 'price_subtitle', 'Choose the best subscription plan for your school'),
(43, 'copyright_text', '2022 Academy, All rights reserved'),
(44, 'contact_email', 'ekattor@domain.com'),
(45, 'frontend_footer_text', 'Ekattor 8 is a collection of programs designed to assist schools in administering their executive responsibilities on a day-to-day basis. It is designed for SAAS (Software as a Service) projects.'),
(46, 'faq_subtitle', 'Frequently asked questions'),
(49, 'frontend_view', '1'),
(50, 'white_logo', 'white_logo.png'),
(51, 'navbar_title', 'Ekattor8'),
(53, 'email_title', 'Subscription'),
(54, 'email_details', 'Feel free to reach out to us anytime if you have questions or feedback. We value your input and strive to provide the best experience possible.'),
(55, 'warning_text', 'This email is from an automat'),
(56, 'email_logo', '16904374791.png'),
(57, 'socialLogo1', '16907191042.png'),
(58, 'socialLogo2', '16907191913.png'),
(59, 'socialLogo3', '16907194544.png'),
(60, 'paypal', '{\"status\":\"0\",\"mode\":\"test\",\"test_client_id\":\"snd_cl_id_xxxxxxxxxxxxx\",\"test_secret_key\":\"snd_cl_sid_xxxxxxxxxxxx\",\"live_client_id\":\"lv_cl_id_xxxxxxxxxxxxxxx\",\"live_secret_key\":\"lv_cl_sid_xxxxxxxxxxxxxx\"}'),
(61, 'stripe', '{\"status\":\"0\",\"mode\":\"test\",\"test_key\":\"pk_test_xxxxxxxxxxxxx\",\"test_secret_key\":\"sk_test_xxxxxxxxxxxxxx\",\"public_live_key\":\"pk_live_xxxxxxxxxxxxxx\",\"secret_live_key\":\"sk_live_xxxxxxxxxxxxxx\"}'),
(62, 'razorpay', '{\"status\":\"0\",\"mode\":\"test\",\"test_key\":\"rzp_test_xxxxxxxxxxxxx\",\"test_secret_key\":\"rzs_test_xxxxxxxxxxxxx\",\"live_key\":\"rzp_live_xxxxxxxxxxxxx\",\"live_secret_key\":\"rzs_live_xxxxxxxxxxxxx\",\"theme_color\":\"#00ffff\"}'),
(63, 'paytm', '{\"status\":\"0\",\"mode\":\"test\",\"test_merchant_id\":\"tm_id_xxxxxxxxxxxx\",\"test_merchant_key\":\"tm_key_xxxxxxxxxx\",\"live_merchant_id\":\"lv_mid_xxxxxxxxxxx\",\"live_merchant_key\":\"lv_key_xxxxxxxxxxx\",\"environment\":\"provide-a-environment\",\"merchant_website\":\"merchant-website\",\"channel\":\"provide-channel-type\",\"industry_type\":\"provide-industry-type\"}'),
(64, 'flutterwave', '{\"status\":\"0\",\"mode\":\"test\",\"test_key\":\"flwp_test_xxxxxxxxxxxxx\",\"test_secret_key\":\"flws_test_xxxxxxxxxxxxx\",\"test_encryption_key\":\"flwe_test_xxxxxxxxxxxxx\",\"public_live_key\":\"flwp_live_xxxxxxxxxxxxxx\",\"secret_live_key\":\"flws_live_xxxxxxxxxxxxxx\",\"encryption_live_key\":\"flwe_live_xxxxxxxxxxxxxx\"}');

-- --------------------------------------------------------

--
-- Table structure for table `gradebooks`
--

CREATE TABLE `gradebooks` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_category_id` int(11) NOT NULL,
  `marks` varchar(255) DEFAULT NULL,
  `comment` varchar(255) NOT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `grade_point` varchar(255) NOT NULL,
  `mark_from` int(11) NOT NULL,
  `mark_upto` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `phrase` varchar(300) DEFAULT NULL,
  `translated` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`id`, `name`, `phrase`, `translated`) VALUES
(1, 'english', 'Dashboard', 'Dashboard'),
(2, 'english', 'Home', 'Home'),
(3, 'english', 'Schools', 'Schools'),
(4, 'english', 'Total Schools', 'Total Schools'),
(5, 'english', 'Subscription', 'Subscription'),
(6, 'english', 'Total Active Subscription', 'Total Active Subscription'),
(7, 'english', 'Subscription Payment', 'Subscription Payment'),
(8, 'english', 'Superadmin | Ekator 8', 'Superadmin | Ekator 8'),
(9, 'english', 'Close', 'Close'),
(10, 'english', 'School List', 'School List'),
(11, 'english', 'Create school', 'Create school'),
(12, 'english', 'Pending List', 'Pending List'),
(13, 'english', 'Package', 'Package'),
(14, 'english', 'Subscriptions', 'Subscriptions'),
(15, 'english', 'Subscription Report', 'Subscription Report'),
(16, 'english', 'Pending Request', 'Pending Request'),
(17, 'english', 'Confirmed Payment', 'Confirmed Payment'),
(18, 'english', 'Addons', 'Addons'),
(19, 'english', 'Settings', 'Settings'),
(20, 'english', 'System Settings', 'System Settings'),
(21, 'english', 'Session Manager', 'Session Manager'),
(22, 'english', 'Payment Settings', 'Payment Settings'),
(23, 'english', 'Smtp settings', 'Smtp settings'),
(24, 'english', 'About', 'About'),
(25, 'english', 'Superadmin', 'Superadmin'),
(26, 'english', 'My Account', 'My Account'),
(27, 'english', 'Change Password', 'Change Password'),
(28, 'english', 'Log out', 'Log out'),
(29, 'english', 'Loading...', 'Loading...'),
(30, 'english', 'Heads up!', 'Heads up!'),
(31, 'english', 'Are you sure?', 'Are you sure?'),
(32, 'english', 'Back', 'Back'),
(33, 'english', 'Continue', 'Continue'),
(34, 'english', 'You won\'t able to revert this!', 'You won\'t able to revert this!'),
(35, 'english', 'Yes', 'Yes'),
(36, 'english', 'Cancel', 'Cancel'),
(37, 'english', 'Add School', 'Add School'),
(38, 'english', 'Name', 'Name'),
(39, 'english', 'Address', 'Address'),
(40, 'english', 'Phone', 'Phone'),
(41, 'english', 'Info', 'Info'),
(42, 'english', 'Status', 'Status'),
(43, 'english', 'Action', 'Action'),
(44, 'english', 'Active', 'Active'),
(45, 'english', 'Actions', 'Actions'),
(46, 'english', 'Edit School', 'Edit School'),
(47, 'english', 'Edit', 'Edit'),
(48, 'english', 'Delete', 'Delete'),
(49, 'english', 'School Form', 'School Form'),
(50, 'english', 'Provide all the information required for your school.', 'Provide all the information required for your school.'),
(51, 'english', 'Also provide a admin information with email and passwoard.', 'Also provide a admin information with email and passwoard.'),
(52, 'english', 'So that admin can access the created school.', 'So that admin can access the created school.'),
(53, 'english', 'SCHOOL INFO', 'SCHOOL INFO'),
(54, 'english', 'School Name', 'School Name'),
(55, 'english', 'School Address', 'School Address'),
(56, 'english', 'School Email', 'School Email'),
(57, 'english', 'School Phone', 'School Phone'),
(58, 'english', 'ADMIN INFO', 'ADMIN INFO'),
(59, 'english', 'Gender', 'Gender'),
(60, 'english', 'Select a gender', 'Select a gender'),
(61, 'english', 'Male', 'Male'),
(62, 'english', 'Female', 'Female'),
(63, 'english', 'Blood group', 'Blood group'),
(64, 'english', 'Select a blood group', 'Select a blood group'),
(65, 'english', 'A+', 'A+'),
(66, 'english', 'A-', 'A-'),
(67, 'english', 'B+', 'B+'),
(68, 'english', 'B-', 'B-'),
(69, 'english', 'AB+', 'AB+'),
(70, 'english', 'AB-', 'AB-'),
(71, 'english', 'O+', 'O+'),
(72, 'english', 'O-', 'O-'),
(73, 'english', 'Admin Address', 'Admin Address'),
(74, 'english', 'Admin Phone Number', 'Admin Phone Number'),
(75, 'english', 'Photo', 'Photo'),
(76, 'english', 'Admin Email', 'Admin Email'),
(77, 'english', 'Admin Password', 'Admin Password'),
(78, 'english', 'Submit', 'Submit'),
(79, 'english', 'Pending School List', 'Pending School List'),
(80, 'english', 'No data found', 'No data found'),
(81, 'english', 'Packages', 'Packages'),
(82, 'english', 'Add Package', 'Add Package'),
(83, 'english', 'Price', 'Price'),
(84, 'english', 'Interval', 'Interval'),
(85, 'english', 'Preiod', 'Preiod'),
(86, 'english', 'Filter', 'Filter'),
(87, 'english', 'Export', 'Export'),
(88, 'english', 'PDF', 'PDF'),
(89, 'english', 'CSV', 'CSV'),
(90, 'english', 'Print', 'Print'),
(91, 'english', 'Paid By', 'Paid By'),
(92, 'english', 'Purchase Date', 'Purchase Date'),
(93, 'english', 'Expire Date', 'Expire Date'),
(94, 'english', 'Confirmed Request', 'Confirmed Request'),
(95, 'english', 'Payment For', 'Payment For'),
(96, 'english', 'Payment Document', 'Payment Document'),
(97, 'english', 'Approve', 'Approve'),
(98, 'english', 'Manage Addons', 'Manage Addons'),
(99, 'english', 'Install addon', 'Install addon'),
(100, 'english', 'Add new addon', 'Add new addon'),
(101, 'english', 'System Name', 'System Name'),
(102, 'english', 'System Title', 'System Title'),
(103, 'english', 'System Email', 'System Email'),
(104, 'english', 'Fax', 'Fax'),
(105, 'english', 'Timezone', 'Timezone'),
(106, 'english', 'Footer Text', 'Footer Text'),
(107, 'english', 'Footer Link', 'Footer Link'),
(108, 'english', 'PRODUCT UPDATE', 'PRODUCT UPDATE'),
(109, 'english', 'File', 'File'),
(110, 'english', 'Update', 'Update'),
(111, 'english', 'SYSTEM LOGO', 'SYSTEM LOGO'),
(112, 'english', 'Dark logo', 'Dark logo'),
(113, 'english', 'Light logo', 'Light logo'),
(114, 'english', 'Favicon', 'Favicon'),
(115, 'english', 'Update Logo', 'Update Logo'),
(116, 'english', 'Create Session', 'Create Session'),
(117, 'english', 'Add Session', 'Add Session'),
(118, 'english', 'Active session ', 'Active session '),
(119, 'english', 'Select a session', 'Select a session'),
(120, 'english', 'Activate', 'Activate'),
(121, 'english', 'Session title', 'Session title'),
(122, 'english', 'Options', 'Options'),
(123, 'english', 'Edit Session', 'Edit Session'),
(124, 'english', 'Global Currency', 'Global Currency'),
(125, 'english', 'Select system currency', 'Select system currency'),
(126, 'english', 'Currency Position', 'Currency Position'),
(127, 'english', 'Left', 'Left'),
(128, 'english', 'Right', 'Right'),
(129, 'english', 'Left with a space', 'Left with a space'),
(130, 'english', 'Right with a space', 'Right with a space'),
(131, 'english', 'Update Currency', 'Update Currency'),
(132, 'english', 'Protocol', 'Protocol'),
(133, 'english', 'Smtp crypto', 'Smtp crypto'),
(134, 'english', 'Smtp host', 'Smtp host'),
(135, 'english', 'Smtp port', 'Smtp port'),
(136, 'english', 'Smtp username', 'Smtp username'),
(137, 'english', 'Smtp password', 'Smtp password'),
(138, 'english', 'Save', 'Save'),
(139, 'english', 'Not found', 'Not found'),
(140, 'english', 'About this application', 'About this application'),
(141, 'english', 'Software version', 'Software version'),
(142, 'english', 'Check update', 'Check update'),
(143, 'english', 'PHP version', 'PHP version'),
(144, 'english', 'Curl enable', 'Curl enable'),
(145, 'english', 'Enabled', 'Enabled'),
(146, 'english', 'Purchase code', 'Purchase code'),
(147, 'english', 'Product license', 'Product license'),
(148, 'english', 'invalid', 'invalid'),
(149, 'english', 'Enter valid purchase code', 'Enter valid purchase code'),
(150, 'english', 'Customer support status', 'Customer support status'),
(151, 'english', 'Support expiry date', 'Support expiry date'),
(152, 'english', 'Customer name', 'Customer name'),
(153, 'english', 'Get customer support', 'Get customer support'),
(154, 'english', 'Customer support', 'Customer support'),
(155, 'english', 'Email', 'Email'),
(156, 'english', 'Password', 'Password'),
(157, 'english', 'Forgot password', 'Forgot password'),
(158, 'english', 'Help', 'Help'),
(159, 'english', 'Login', 'Login'),
(160, 'english', 'Total Student', 'Total Student'),
(161, 'english', 'Teacher', 'Teacher'),
(162, 'english', 'Total Teacher', 'Total Teacher'),
(163, 'english', 'Parents', 'Parents'),
(164, 'english', 'Total Parent', 'Total Parent'),
(165, 'english', 'Staff', 'Staff'),
(166, 'english', 'Total Staff', 'Total Staff'),
(167, 'english', 'Todays Attendance', 'Todays Attendance'),
(168, 'english', 'Go to Attendance', 'Go to Attendance'),
(169, 'english', 'Income Report', 'Income Report'),
(170, 'english', 'Year', 'Year'),
(171, 'english', 'Month', 'Month'),
(172, 'english', 'Week', 'Week'),
(173, 'english', 'Upcoming Events', 'Upcoming Events'),
(174, 'english', 'See all', 'See all'),
(175, 'english', 'Admin', 'Admin'),
(176, 'english', 'Users', 'Users'),
(177, 'english', 'Accountant', 'Accountant'),
(178, 'english', 'Librarian', 'Librarian'),
(179, 'english', 'Parent', 'Parent'),
(180, 'english', 'Student', 'Student'),
(181, 'english', 'Teacher Permission', 'Teacher Permission'),
(182, 'english', 'Admissions', 'Admissions'),
(183, 'english', 'Examination', 'Examination'),
(184, 'english', 'Exam Category', 'Exam Category'),
(185, 'english', 'Offline Exam', 'Offline Exam'),
(186, 'english', 'Marks', 'Marks'),
(187, 'english', 'Grades', 'Grades'),
(188, 'english', 'Promotion', 'Promotion'),
(189, 'english', 'Academic', 'Academic'),
(190, 'english', 'Daily Attendance', 'Daily Attendance'),
(191, 'english', 'Class List', 'Class List'),
(192, 'english', 'Class Routine', 'Class Routine'),
(193, 'english', 'Subjects', 'Subjects'),
(194, 'english', 'Gradebooks', 'Gradebooks'),
(195, 'english', 'Syllabus', 'Syllabus'),
(196, 'english', 'Class Room', 'Class Room'),
(197, 'english', 'Department', 'Department'),
(198, 'english', 'Accounting', 'Accounting'),
(199, 'english', 'Student Fee Manager', 'Student Fee Manager'),
(200, 'english', 'Offline Payment Request', 'Offline Payment Request'),
(201, 'english', 'Expense Manager', 'Expense Manager'),
(202, 'english', 'Expense Category', 'Expense Category'),
(203, 'english', 'Back Office', 'Back Office'),
(204, 'english', 'Book List Manager', 'Book List Manager'),
(205, 'english', 'Book Issue Report', 'Book Issue Report'),
(206, 'english', 'Noticeboard', 'Noticeboard'),
(207, 'english', 'Events', 'Events'),
(208, 'english', 'School Settings', 'School Settings'),
(209, 'english', 'School information', 'School information'),
(210, 'english', 'Update settings', 'Update settings'),
(211, 'english', 'Deactive', 'Deactive'),
(212, 'english', 'Session has been activated', 'Session has been activated'),
(213, 'english', 'Update session', 'Update session'),
(214, 'english', 'Admins', 'Admins'),
(215, 'english', 'Create Admin', 'Create Admin'),
(216, 'english', 'User Info', 'User Info'),
(217, 'english', 'Oprions', 'Oprions'),
(218, 'english', 'Edit Admin', 'Edit Admin'),
(219, 'english', 'Teachers', 'Teachers'),
(220, 'english', 'Create Teacher', 'Create Teacher'),
(221, 'english', 'Create Accountant', 'Create Accountant'),
(222, 'english', 'Edit Accountant', 'Edit Accountant'),
(223, 'english', 'Librarians', 'Librarians'),
(224, 'english', 'Create Librarian', 'Create Librarian'),
(225, 'english', 'Edit Librarian', 'Edit Librarian'),
(226, 'english', 'Create Parent', 'Create Parent'),
(227, 'english', 'Edit Parent', 'Edit Parent'),
(228, 'english', 'Students', 'Students'),
(229, 'english', 'Create Student', 'Create Student'),
(230, 'english', 'Generate id card', 'Generate id card'),
(231, 'english', 'Assigned Permission For Teacher', 'Assigned Permission For Teacher'),
(232, 'english', 'Select a class', 'Select a class'),
(233, 'english', 'First select a class', 'First select a class'),
(234, 'english', 'Please select a class and section', 'Please select a class and section'),
(235, 'english', 'Attendance', 'Attendance'),
(236, 'english', 'Permission updated successfully.', 'Permission updated successfully.'),
(237, 'english', 'Admission', 'Admission'),
(238, 'english', 'Bulk student admission', 'Bulk student admission'),
(239, 'english', 'Class', 'Class'),
(240, 'english', 'Section', 'Section'),
(241, 'english', 'Select section', 'Select section'),
(242, 'english', 'Birthday', 'Birthday'),
(243, 'english', 'Select gender', 'Select gender'),
(244, 'english', 'Others', 'Others'),
(245, 'english', 'Student profile image', 'Student profile image'),
(246, 'english', 'Add Student', 'Add Student'),
(247, 'english', 'Create Exam Category', 'Create Exam Category'),
(248, 'english', 'Add Exam Category', 'Add Exam Category'),
(249, 'english', 'Title', 'Title'),
(250, 'english', 'Class test', 'Class test'),
(251, 'english', 'Edit Exam Category', 'Edit Exam Category'),
(252, 'english', 'Midterm exam', 'Midterm exam'),
(253, 'english', 'Final exam', 'Final exam'),
(254, 'english', 'Admission exam', 'Admission exam'),
(255, 'english', 'Create Exam', 'Create Exam'),
(256, 'english', 'Add Exam', 'Add Exam'),
(257, 'english', 'Exam', 'Exam'),
(258, 'english', 'Starting Time', 'Starting Time'),
(259, 'english', 'Ending Time', 'Ending Time'),
(260, 'english', 'Total Marks', 'Total Marks'),
(261, 'english', 'Edit Exam', 'Edit Exam'),
(262, 'english', 'Manage Marks', 'Manage Marks'),
(263, 'english', 'Select category', 'Select category'),
(264, 'english', 'Select class', 'Select class'),
(265, 'english', 'Please select all the fields', 'Please select all the fields'),
(266, 'english', 'Examknation', 'Examknation'),
(267, 'english', 'Create Grade', 'Create Grade'),
(268, 'english', 'Add grade', 'Add grade'),
(269, 'english', 'Grade', 'Grade'),
(270, 'english', 'Grade Point', 'Grade Point'),
(271, 'english', 'Mark From', 'Mark From'),
(272, 'english', 'Mark Upto', 'Mark Upto'),
(273, 'english', 'Promotions', 'Promotions'),
(274, 'english', 'Current session', 'Current session'),
(275, 'english', 'Session from', 'Session from'),
(276, 'english', 'Next session', 'Next session'),
(277, 'english', 'Session to', 'Session to'),
(278, 'english', 'Promoting from', 'Promoting from'),
(279, 'english', 'Promoting to', 'Promoting to'),
(280, 'english', 'Manage promotion', 'Manage promotion'),
(281, 'english', 'Take Attendance', 'Take Attendance'),
(282, 'english', 'Select a month', 'Select a month'),
(283, 'english', 'January', 'January'),
(284, 'english', 'February', 'February'),
(285, 'english', 'March', 'March'),
(286, 'english', 'April', 'April'),
(287, 'english', 'May', 'May'),
(288, 'english', 'June', 'June'),
(289, 'english', 'July', 'July'),
(290, 'english', 'August', 'August'),
(291, 'english', 'September', 'September'),
(292, 'english', 'October', 'October'),
(293, 'english', 'November', 'November'),
(294, 'english', 'December', 'December'),
(295, 'english', 'Select a year', 'Select a year'),
(296, 'english', 'Please select in all fields !', 'Please select in all fields !'),
(297, 'english', 'Classes', 'Classes'),
(298, 'english', 'Create Class', 'Create Class'),
(299, 'english', 'Add class', 'Add class'),
(300, 'english', 'Edit Section', 'Edit Section'),
(301, 'english', 'Edit Class', 'Edit Class'),
(302, 'english', 'Routines', 'Routines'),
(303, 'english', 'Add class routine', 'Add class routine'),
(304, 'english', 'Create Subject', 'Create Subject'),
(305, 'english', 'Add subject', 'Add subject'),
(306, 'english', 'Edit Subject', 'Edit Subject'),
(307, 'english', 'Select a exam category', 'Select a exam category'),
(308, 'english', 'Create syllabus', 'Create syllabus'),
(309, 'english', 'Add syllabus', 'Add syllabus'),
(310, 'english', 'Class Rooms', 'Class Rooms'),
(311, 'english', 'Create Class Room', 'Create Class Room'),
(312, 'english', 'Add class room', 'Add class room'),
(313, 'english', 'Edit Class Room', 'Edit Class Room'),
(314, 'english', 'Departments', 'Departments'),
(315, 'english', 'Create Department', 'Create Department'),
(316, 'english', 'Add department', 'Add department'),
(317, 'english', 'Edit Department', 'Edit Department'),
(318, 'english', 'Add Single Invoice', 'Add Single Invoice'),
(319, 'english', 'Add Mass Invoice', 'Add Mass Invoice'),
(320, 'english', 'All class', 'All class'),
(321, 'english', 'All status', 'All status'),
(322, 'english', 'Paid', 'Paid'),
(323, 'english', 'Unpaid', 'Unpaid'),
(324, 'english', 'Invoice No', 'Invoice No'),
(325, 'english', 'Invoice Title', 'Invoice Title'),
(326, 'english', 'Total Amount', 'Total Amount'),
(327, 'english', 'Created at', 'Created at'),
(328, 'english', 'Paid Amount', 'Paid Amount'),
(329, 'english', 'Expense', 'Expense'),
(330, 'english', 'Create Expense', 'Create Expense'),
(331, 'english', 'Add New Expense', 'Add New Expense'),
(332, 'english', 'Create Expense Category', 'Create Expense Category'),
(333, 'english', 'Add Expense Category', 'Add Expense Category'),
(334, 'english', 'Option', 'Option'),
(335, 'english', 'Edit Expense Category', 'Edit Expense Category'),
(336, 'english', 'Book', 'Book'),
(337, 'english', 'Add book', 'Add book'),
(338, 'english', 'Book name', 'Book name'),
(339, 'english', 'Author', 'Author'),
(340, 'english', 'Copies', 'Copies'),
(341, 'english', 'Available copies', 'Available copies'),
(342, 'english', 'Edit Book', 'Edit Book'),
(343, 'english', 'Book Issue', 'Book Issue'),
(344, 'english', 'Issue Book', 'Issue Book'),
(345, 'english', 'Noticeboard calendar', 'Noticeboard calendar'),
(346, 'english', 'Add New Notice', 'Add New Notice'),
(347, 'english', 'Locales:', 'Locales:'),
(348, 'english', 'Current Plan', 'Current Plan'),
(349, 'english', 'Silver', 'Silver'),
(350, 'english', 'Monthly', 'Monthly'),
(351, 'english', 'Subscription Renew Date', 'Subscription Renew Date'),
(352, 'english', 'Amount To Be Charged', 'Amount To Be Charged'),
(353, 'english', 'Create Event', 'Create Event'),
(354, 'english', 'Event title', 'Event title'),
(355, 'english', 'Date', 'Date'),
(356, 'english', 'Update event', 'Update event'),
(357, 'english', 'Upload addons zip file', 'Upload addons zip file'),
(358, 'english', 'Verified', 'Verified'),
(359, 'english', 'Details info', 'Details info'),
(360, 'english', 'Phone Number', 'Phone Number'),
(361, 'english', 'Designation', 'Designation'),
(362, 'english', 'Save Changes', 'Save Changes'),
(363, 'english', 'Select a status', 'Select a status'),
(364, 'english', 'Update school', 'Update school'),
(365, 'english', 'Package price', 'Package price'),
(366, 'english', 'Package Type', 'Package Type'),
(367, 'english', 'Select a package type', 'Select a package type'),
(368, 'english', 'Trail', 'Trail'),
(369, 'english', 'Select a interval', 'Select a interval'),
(370, 'english', 'Days', 'Days'),
(371, 'english', 'Yearly', 'Yearly'),
(372, 'english', 'Interval Preiod', 'Interval Preiod'),
(373, 'english', 'Description', 'Description'),
(374, 'english', 'Create package', 'Create package'),
(375, 'english', 'Update package', 'Update package'),
(376, 'english', 'Invalid purchase code', 'Invalid purchase code'),
(377, 'english', 'Inactive', 'Inactive'),
(378, 'english', 'Save event', 'Save event'),
(379, 'english', 'Create', 'Create'),
(380, 'english', 'Select a department', 'Select a department'),
(381, 'english', 'One', 'One'),
(382, 'english', 'Two', 'Two'),
(383, 'english', 'Three', 'Three'),
(384, 'english', 'Four', 'Four'),
(385, 'english', 'Five', 'Five'),
(386, 'english', 'Six', 'Six'),
(387, 'english', 'Seven', 'Seven'),
(388, 'english', 'Eight', 'Eight'),
(389, 'english', 'Nine', 'Nine'),
(390, 'english', 'Ten', 'Ten'),
(391, 'english', 'Add students', 'Add students'),
(392, 'english', 'Create category', 'Create category'),
(393, 'english', 'Exam Name', 'Exam Name'),
(394, 'english', 'Select exam category name', 'Select exam category name'),
(395, 'english', 'Subject', 'Subject'),
(396, 'english', 'Starting date', 'Starting date'),
(397, 'english', 'Ending date', 'Ending date'),
(398, 'english', 'Student name', 'Student name'),
(399, 'english', 'Mark', 'Mark'),
(400, 'english', 'Comment', 'Comment'),
(401, 'english', 'Value has been updated successfully', 'Value has been updated successfully'),
(402, 'english', 'Required mark field', 'Required mark field'),
(403, 'english', 'Image', 'Image'),
(404, 'english', 'Enroll to', 'Enroll to'),
(405, 'english', 'Select a section', 'Select a section'),
(406, 'english', 'Attendance Report Of', 'Attendance Report Of'),
(407, 'english', 'Last Update at', 'Last Update at'),
(408, 'english', 'Time', 'Time'),
(409, 'english', 'Please select the required fields', 'Please select the required fields'),
(410, 'english', 'Saturday', 'Saturday'),
(411, 'english', 'Sunday', 'Sunday'),
(412, 'english', 'Monday', 'Monday'),
(413, 'english', 'Tuesday', 'Tuesday'),
(414, 'english', 'Wednesday', 'Wednesday'),
(415, 'english', 'Update subject', 'Update subject'),
(416, 'english', 'Select subject', 'Select subject'),
(417, 'english', 'Assign a teacher', 'Assign a teacher'),
(418, 'english', 'Select a class room', 'Select a class room'),
(419, 'english', 'Day', 'Day'),
(420, 'english', 'Select a day', 'Select a day'),
(421, 'english', 'Thursday', 'Thursday'),
(422, 'english', 'Friday', 'Friday'),
(423, 'english', 'Starting hour', 'Starting hour'),
(424, 'english', 'Starting minute', 'Starting minute'),
(425, 'english', 'Ending hour', 'Ending hour'),
(426, 'english', 'Ending minute', 'Ending minute'),
(427, 'english', 'Add routine', 'Add routine'),
(428, 'english', 'Edit class routine', 'Edit class routine'),
(429, 'english', 'Tittle', 'Tittle'),
(430, 'english', 'Upload syllabus', 'Upload syllabus'),
(431, 'english', 'Select student', 'Select student'),
(432, 'english', 'Select a student', 'Select a student'),
(433, 'english', 'Payment method', 'Payment method'),
(434, 'english', 'Select a payment method', 'Select a payment method'),
(435, 'english', 'Cash', 'Cash'),
(436, 'english', 'Paypal', 'Paypal'),
(437, 'english', 'Paytm', 'Paytm'),
(438, 'english', 'Razorpay', 'Razorpay'),
(439, 'english', 'Create invoice', 'Create invoice'),
(440, 'english', 'Payment date', 'Payment date'),
(441, 'english', 'Print invoice', 'Print invoice'),
(442, 'english', 'Edit Invoice', 'Edit Invoice'),
(443, 'english', 'Amount', 'Amount'),
(444, 'english', 'Select an expense category', 'Select an expense category'),
(445, 'english', 'Edit Expense', 'Edit Expense'),
(446, 'english', 'Issue date', 'Issue date'),
(447, 'english', 'Select book', 'Select book'),
(448, 'english', 'Id', 'Id'),
(449, 'english', 'Pending', 'Pending'),
(450, 'english', 'Update issued book', 'Update issued book'),
(451, 'english', 'Return this book', 'Return this book'),
(452, 'english', 'Notice title', 'Notice title'),
(453, 'english', 'Start date', 'Start date'),
(454, 'english', 'Setup additional date & time', 'Setup additional date & time'),
(455, 'english', 'Start time', 'Start time'),
(456, 'english', 'End date', 'End date'),
(457, 'english', 'End time', 'End time'),
(458, 'english', 'Notice', 'Notice'),
(459, 'english', 'Show on website', 'Show on website'),
(460, 'english', 'Show', 'Show'),
(461, 'english', 'Do not need to show', 'Do not need to show'),
(462, 'english', 'Upload notice photo', 'Upload notice photo'),
(463, 'english', 'Save notice', 'Save notice'),
(464, 'english', 'School Currency', 'School Currency'),
(465, 'english', 'Exam List', 'Exam List'),
(466, 'english', 'Profile', 'Profile'),
(467, 'english', ' Download', ' Download'),
(468, 'english', 'Select a subject', 'Select a subject'),
(469, 'english', 'Welcome, to', 'Welcome, to'),
(470, 'english', 'Fee Manager', 'Fee Manager'),
(471, 'english', 'List Of Books', 'List Of Books'),
(472, 'english', 'Issued Book', 'Issued Book'),
(473, 'english', 'Student Code', 'Student Code'),
(474, 'english', 'Candice Kennedy', 'Candice Kennedy'),
(475, 'english', 'English', 'English'),
(476, 'english', 'Natalie Ashley', 'Natalie Ashley'),
(477, 'english', 'Byron Chase', 'Byron Chase'),
(478, 'english', 'Rafael Hardy', 'Rafael Hardy'),
(479, 'english', 'Mathematics', 'Mathematics'),
(480, 'english', 'Aphrodite Shaffer', 'Aphrodite Shaffer'),
(481, 'english', 'Bangla', 'Bangla'),
(482, 'english', 'Fatima Phillips', 'Fatima Phillips'),
(483, 'english', 'Sydney Pearson', 'Sydney Pearson'),
(484, 'english', 'Drawing', 'Drawing'),
(485, 'english', 'Imani Cooper', 'Imani Cooper'),
(486, 'english', 'Ulric Spencer', 'Ulric Spencer'),
(487, 'english', 'Yoshio Gentry', 'Yoshio Gentry'),
(488, 'english', 'Attendance report', 'Attendance report'),
(489, 'english', 'Of', 'Of'),
(490, 'english', 'Last updated at', 'Last updated at'),
(491, 'english', 'View Marks', 'View Marks'),
(492, 'english', 'Subject name', 'Subject name'),
(493, 'english', 'Pay', 'Pay'),
(494, 'english', 'List Of Book', 'List Of Book'),
(495, 'english', 'Child', 'Child'),
(496, 'english', 'Teaches', 'Teaches'),
(498, 'english', 'Student List', 'Student List'),
(499, 'english', 'Id card', 'Id card'),
(500, 'english', 'Code', 'Code'),
(501, 'english', 'Not found', 'Not found'),
(502, 'english', 'Contact', 'Contact'),
(503, 'english', 'Search Attendance Report', 'Search Attendance Report'),
(504, 'english', 'Please select in all fields !', 'Please select in all fields !'),
(505, 'english', 'Please select student', 'Please select student'),
(506, 'english', 'Download', 'Download'),
(507, 'english', 'Ekattor', 'Ekattor'),
(508, 'english', 'Add  Single Invoice', 'Add  Single Invoice'),
(509, 'english', 'Add  Mass Invoice', 'Add  Mass Invoice'),
(510, 'english', 'Update invoice', 'Update invoice'),
(511, 'english', 'Invoice', 'Invoice'),
(512, 'english', 'Please find below the invoice', 'Please find below the invoice'),
(513, 'english', 'Billing Address', 'Billing Address'),
(514, 'english', 'Due Amount', 'Due Amount'),
(515, 'english', 'Student Fee', 'Student Fee'),
(516, 'english', 'Subtotal', 'Subtotal'),
(517, 'english', 'Due', 'Due'),
(518, 'english', 'Grand Total', 'Grand Total'),
(519, 'english', 'Update book issue information', 'Update book issue information'),
(520, 'english', 'Not Subscribed', 'Not Subscribed'),
(521, 'english', 'You are not subscribed to any plan. Subscribe now.', 'You are not subscribed to any plan. Subscribe now.'),
(522, 'english', 'Subscribe', 'Subscribe'),
(523, 'english', 'Package List', 'Package List'),
(524, 'english', 'Payment | Ekator 8', 'Payment | Ekator 8'),
(525, 'english', 'Make Payment', 'Make Payment'),
(526, 'english', 'Payment Gateway', 'Payment Gateway'),
(527, 'english', 'Offline', 'Offline'),
(528, 'english', 'Addon', 'Addon'),
(529, 'english', 'Invoice Summary', 'Invoice Summary'),
(530, 'english', 'Document of your payment', 'Document of your payment'),
(531, 'english', 'Submit payment document', 'Submit payment document'),
(532, 'english', 'Instruction', 'Instruction'),
(533, 'english', 'Admin will review your payment document and then approve the Payment.', 'Admin will review your payment document and then approve the Payment.'),
(534, 'english', 'Pending Payment', 'Pending Payment'),
(535, 'english', 'You payment request has been sent to Superadmin ', 'You payment request has been sent to Superadmin '),
(536, 'english', 'Suspended', 'Suspended'),
(537, 'english', 'Enter your email address to reset your password.', 'Enter your email address to reset your password.'),
(538, 'english', 'Reset password', 'Reset password'),
(539, 'english', 'Language Settings', 'Language Settings'),
(540, 'english', 'Language', 'Language'),
(541, 'english', 'Edit phrase', 'Edit phrase'),
(542, 'english', 'Delete language', 'Delete language'),
(543, 'english', 'edit_phrase', 'edit_phrase'),
(544, 'english', 'delete_language', 'delete_language'),
(545, 'english', 'System default language can not be removed', 'System default language can not be removed'),
(546, 'english', 'language_list', 'language_list'),
(547, 'english', 'add_language', 'add_language'),
(548, 'english', 'Language list', 'Language list'),
(549, 'english', 'Add language', 'Add language'),
(550, 'english', 'Add new phrase', 'Add new phrase'),
(551, 'english', 'add_new_language', 'add_new_language'),
(552, 'english', 'No special character or space is allowed', 'No special character or space is allowed'),
(553, 'english', 'valid_examples', 'valid_examples'),
(554, 'english', 'No special character or space is allowed', 'No special character or space is allowed'),
(555, 'english', 'Validexamples', 'Validexamples'),
(556, 'english', 'Add new language', 'Add new language'),
(557, 'english', 'Valid examples', 'Valid examples'),
(560, 'english', 'Phrase updated', 'Phrase updated'),
(561, 'english', 'System Language', 'System Language'),
(562, 'english', 'Edit Grade', 'Edit Grade'),
(563, 'english', 'Number of scopy', 'Number of scopy'),
(564, 'english', 'Save book', 'Save book'),
(565, 'english', 'New Password', 'New Password'),
(566, 'english', 'Confirm Password', 'Confirm Password'),
(567, 'english', 'Current Password', 'Current Password'),
(568, 'english', 'Add Parent', 'Add Parent'),
(569, 'english', 'Parent profile image', 'Parent profile image'),
(570, 'english', 'Allowances', 'Allowances'),
(571, 'english', 'Type', 'Type'),
(572, 'english', 'Select child', 'Select child'),
(573, 'english', 'Show student list', 'Show student list'),
(574, 'english', 'Update attendance', 'Update attendance'),
(575, 'english', 'Present All', 'Present All'),
(576, 'english', 'Absent All', 'Absent All'),
(577, 'english', 'present', 'present'),
(578, 'english', 'absent', 'absent'),
(579, 'english', 'not_updated_yet', 'not_updated_yet'),
(580, 'english', '31', '31'),
(581, 'english', 'Not updated yet', 'Not updated yet'),
(582, 'english', 'Update class', 'Update class'),
(583, 'english', 'Edit syllabus', 'Edit syllabus'),
(584, 'english', 'Select expense category', 'Select expense category'),
(585, 'english', 'Filter Options', 'Filter Options'),
(586, 'english', 'Reset', 'Reset'),
(587, 'english', 'Apply', 'Apply'),
(588, 'english', 'Profile info updated successfully', 'Profile info updated successfully'),
(589, 'english', 'not_found', 'not_found'),
(590, 'english', 'No date found', 'No date found'),
(591, 'english', 'No date found', 'No date found'),
(592, 'english', 'Blood ', 'Blood '),
(593, 'english', 'Blood Type', 'Blood Type'),
(594, 'english', 'Help Link', 'Help Link'),
(595, 'english', 'From', 'From'),
(596, 'english', 'To', 'To'),
(597, 'english', 'Select a parent', 'Select a parent'),
(598, 'english', 'Add', 'Add'),
(599, 'english', 'Document', 'Document'),
(600, 'english', 'Decline', 'Decline'),
(601, 'english', 'Number of child:', 'Number of child:'),
(602, 'english', 'Number of child', 'Number of child'),
(603, 'english', 'Parent Create', 'Parent Create'),
(604, 'english', 'Parent Update', 'Parent Update'),
(2452, 'english', 'Version updated successfully', 'Version updated successfully'),
(2453, 'english', 'Subcription', 'Subcription'),
(2454, 'english', 'Expired Subscription', 'Expired Subscription'),
(2455, 'english', 'Website Settings', 'Website Settings'),
(2456, 'english', 'Manage Faq', 'Manage Faq'),
(2457, 'english', 'Visit Website', 'Visit Website'),
(2458, 'english', 'Navbar Title', 'Navbar Title'),
(2459, 'english', 'Frontend View', 'Frontend View'),
(2460, 'english', 'No', 'No'),
(2461, 'english', 'Youtube Api Key', 'Youtube Api Key'),
(2462, 'english', 'Vimeo Api Key', 'Vimeo Api Key'),
(2463, 'english', 'Has to be bigger than', 'Has to be bigger than'),
(2464, 'english', 'Nav Bar Logo', 'Nav Bar Logo'),
(2465, 'english', 'Email Template Settings', 'Email Template Settings'),
(2466, 'english', 'Email Title', 'Email Title'),
(2467, 'english', 'Email Details', 'Email Details'),
(2468, 'english', 'Remaining characters is', 'Remaining characters is'),
(2469, 'english', 'Warning Text', 'Warning Text'),
(2470, 'english', 'Email logo', 'Email logo'),
(2471, 'english', 'Social logo-1', 'Social logo-1'),
(2472, 'english', 'Social logo-2', 'Social logo-2'),
(2473, 'english', 'Social logo-3', 'Social logo-3'),
(2474, 'english', 'Images for email templates will only support if the application is hosted on a live server. Localhost will not support this.', 'Images for email templates will only support if the application is hosted on a live server. Localhost will not support this.'),
(2475, 'english', 'School Logo', 'School Logo'),
(2476, 'english', 'Admin List', 'Admin List'),
(2477, 'english', 'Archive ', 'Archive '),
(2478, 'english', 'Trial', 'Trial'),
(2479, 'english', 'GENERAL SETTINGS', 'GENERAL SETTINGS'),
(2480, 'english', 'Banner Title', 'Banner Title'),
(2481, 'english', 'Banner Subtitle', 'Banner Subtitle'),
(2482, 'english', 'Price Subtitle', 'Price Subtitle'),
(2483, 'english', 'Faq Subtitle', 'Faq Subtitle'),
(2484, 'english', 'Facebook Link', 'Facebook Link'),
(2485, 'english', 'Twitter Link', 'Twitter Link'),
(2486, 'english', 'Linkedin Link', 'Linkedin Link'),
(2487, 'english', 'Instagram Link', 'Instagram Link'),
(2488, 'english', 'Contact Mail', 'Contact Mail'),
(2489, 'english', 'Frontend Footer Text', 'Frontend Footer Text'),
(2490, 'english', 'Copyright Text', 'Copyright Text'),
(2491, 'bangla', 'Dashboard', 'Dashboard'),
(2492, 'bangla', 'Home', 'Home'),
(2493, 'bangla', 'Schools', 'Schools'),
(2494, 'bangla', 'Total Schools', 'Total Schools'),
(2495, 'bangla', 'Subscription', 'Subscription'),
(2496, 'bangla', 'Total Active Subscription', 'Total Active Subscription'),
(2497, 'bangla', 'Subscription Payment', 'Subscription Payment'),
(2498, 'bangla', 'Superadmin | Ekator 8', 'Superadmin | Ekator 8'),
(2499, 'bangla', 'Close', 'Close'),
(2500, 'bangla', 'School List', 'School List'),
(2501, 'bangla', 'Create school', 'Create school'),
(2502, 'bangla', 'Pending List', 'Pending List'),
(2503, 'bangla', 'Package', 'Package'),
(2504, 'bangla', 'Subscriptions', 'Subscriptions'),
(2505, 'bangla', 'Subscription Report', 'Subscription Report'),
(2506, 'bangla', 'Pending Request', 'Pending Request'),
(2507, 'bangla', 'Confirmed Payment', 'Confirmed Payment'),
(2508, 'bangla', 'Addons', 'Addons'),
(2509, 'bangla', 'Settings', 'Settings'),
(2510, 'bangla', 'System Settings', 'System Settings'),
(2511, 'bangla', 'Session Manager', 'Session Manager'),
(2512, 'bangla', 'Payment Settings', 'Payment Settings'),
(2513, 'bangla', 'Smtp settings', 'Smtp settings'),
(2514, 'bangla', 'About', 'About'),
(2515, 'bangla', 'Superadmin', 'Superadmin'),
(2516, 'bangla', 'My Account', 'My Account'),
(2517, 'bangla', 'Change Password', 'Change Password'),
(2518, 'bangla', 'Log out', 'Log out'),
(2519, 'bangla', 'Loading...', 'Loading...'),
(2520, 'bangla', 'Heads up!', 'Heads up!'),
(2521, 'bangla', 'Are you sure?', 'Are you sure?'),
(2522, 'bangla', 'Back', 'Back'),
(2523, 'bangla', 'Continue', 'Continue'),
(2524, 'bangla', 'You won\'t able to revert this!', 'You won\'t able to revert this!'),
(2525, 'bangla', 'Yes', 'Yes'),
(2526, 'bangla', 'Cancel', 'Cancel'),
(2527, 'bangla', 'Add School', 'Add School'),
(2528, 'bangla', 'Name', 'Name'),
(2529, 'bangla', 'Address', 'Address'),
(2530, 'bangla', 'Phone', 'Phone'),
(2531, 'bangla', 'Info', 'Info'),
(2532, 'bangla', 'Status', 'Status'),
(2533, 'bangla', 'Action', 'Action'),
(2534, 'bangla', 'Active', 'Active'),
(2535, 'bangla', 'Actions', 'Actions'),
(2536, 'bangla', 'Edit School', 'Edit School'),
(2537, 'bangla', 'Edit', 'Edit'),
(2538, 'bangla', 'Delete', 'Delete'),
(2539, 'bangla', 'School Form', 'School Form'),
(2540, 'bangla', 'Provide all the information required for your school.', 'Provide all the information required for your school.'),
(2541, 'bangla', 'Also provide a admin information with email and passwoard.', 'Also provide a admin information with email and passwoard.'),
(2542, 'bangla', 'So that admin can access the created school.', 'So that admin can access the created school.'),
(2543, 'bangla', 'SCHOOL INFO', 'SCHOOL INFO'),
(2544, 'bangla', 'School Name', 'School Name'),
(2545, 'bangla', 'School Address', 'School Address'),
(2546, 'bangla', 'School Email', 'School Email'),
(2547, 'bangla', 'School Phone', 'School Phone'),
(2548, 'bangla', 'ADMIN INFO', 'ADMIN INFO'),
(2549, 'bangla', 'Gender', 'Gender'),
(2550, 'bangla', 'Select a gender', 'Select a gender'),
(2551, 'bangla', 'Male', 'Male'),
(2552, 'bangla', 'Female', 'Female'),
(2553, 'bangla', 'Blood group', 'Blood group'),
(2554, 'bangla', 'Select a blood group', 'Select a blood group'),
(2555, 'bangla', 'A+', 'A+'),
(2556, 'bangla', 'A-', 'A-'),
(2557, 'bangla', 'B+', 'B+'),
(2558, 'bangla', 'B-', 'B-'),
(2559, 'bangla', 'AB+', 'AB+'),
(2560, 'bangla', 'AB-', 'AB-'),
(2561, 'bangla', 'O+', 'O+'),
(2562, 'bangla', 'O-', 'O-'),
(2563, 'bangla', 'Admin Address', 'Admin Address'),
(2564, 'bangla', 'Admin Phone Number', 'Admin Phone Number'),
(2565, 'bangla', 'Photo', 'Photo'),
(2566, 'bangla', 'Admin Email', 'Admin Email'),
(2567, 'bangla', 'Admin Password', 'Admin Password'),
(2568, 'bangla', 'Submit', 'Submit'),
(2569, 'bangla', 'Pending School List', 'Pending School List'),
(2570, 'bangla', 'No data found', 'No data found'),
(2571, 'bangla', 'Packages', 'Packages'),
(2572, 'bangla', 'Add Package', 'Add Package'),
(2573, 'bangla', 'Price', 'Price'),
(2574, 'bangla', 'Interval', 'Interval'),
(2575, 'bangla', 'Preiod', 'Preiod'),
(2576, 'bangla', 'Filter', 'Filter'),
(2577, 'bangla', 'Export', 'Export'),
(2578, 'bangla', 'PDF', 'PDF'),
(2579, 'bangla', 'CSV', 'CSV'),
(2580, 'bangla', 'Print', 'Print'),
(2581, 'bangla', 'Paid By', 'Paid By'),
(2582, 'bangla', 'Purchase Date', 'Purchase Date'),
(2583, 'bangla', 'Expire Date', 'Expire Date'),
(2584, 'bangla', 'Confirmed Request', 'Confirmed Request'),
(2585, 'bangla', 'Payment For', 'Payment For'),
(2586, 'bangla', 'Payment Document', 'Payment Document'),
(2587, 'bangla', 'Approve', 'Approve'),
(2588, 'bangla', 'Manage Addons', 'Manage Addons'),
(2589, 'bangla', 'Install addon', 'Install addon'),
(2590, 'bangla', 'Add new addon', 'Add new addon'),
(2591, 'bangla', 'System Name', 'System Name'),
(2592, 'bangla', 'System Title', 'System Title'),
(2593, 'bangla', 'System Email', 'System Email'),
(2594, 'bangla', 'Fax', 'Fax'),
(2595, 'bangla', 'Timezone', 'Timezone'),
(2596, 'bangla', 'Footer Text', 'Footer Text'),
(2597, 'bangla', 'Footer Link', 'Footer Link'),
(2598, 'bangla', 'PRODUCT UPDATE', 'PRODUCT UPDATE'),
(2599, 'bangla', 'File', 'File'),
(2600, 'bangla', 'Update', 'Update'),
(2601, 'bangla', 'SYSTEM LOGO', 'SYSTEM LOGO'),
(2602, 'bangla', 'Dark logo', 'Dark logo'),
(2603, 'bangla', 'Light logo', 'Light logo'),
(2604, 'bangla', 'Favicon', 'Favicon'),
(2605, 'bangla', 'Update Logo', 'Update Logo'),
(2606, 'bangla', 'Create Session', 'Create Session'),
(2607, 'bangla', 'Add Session', 'Add Session'),
(2608, 'bangla', 'Active session ', 'Active session '),
(2609, 'bangla', 'Select a session', 'Select a session'),
(2610, 'bangla', 'Activate', 'Activate'),
(2611, 'bangla', 'Session title', 'Session title'),
(2612, 'bangla', 'Options', 'Options'),
(2613, 'bangla', 'Edit Session', 'Edit Session'),
(2614, 'bangla', 'Global Currency', 'Global Currency'),
(2615, 'bangla', 'Select system currency', 'Select system currency'),
(2616, 'bangla', 'Currency Position', 'Currency Position'),
(2617, 'bangla', 'Left', 'Left'),
(2618, 'bangla', 'Right', 'Right'),
(2619, 'bangla', 'Left with a space', 'Left with a space'),
(2620, 'bangla', 'Right with a space', 'Right with a space'),
(2621, 'bangla', 'Update Currency', 'Update Currency'),
(2622, 'bangla', 'Protocol', 'Protocol'),
(2623, 'bangla', 'Smtp crypto', 'Smtp crypto'),
(2624, 'bangla', 'Smtp host', 'Smtp host'),
(2625, 'bangla', 'Smtp port', 'Smtp port'),
(2626, 'bangla', 'Smtp username', 'Smtp username'),
(2627, 'bangla', 'Smtp password', 'Smtp password'),
(2628, 'bangla', 'Save', 'Save'),
(2629, 'bangla', 'Not found', 'Not found'),
(2630, 'bangla', 'About this application', 'About this application'),
(2631, 'bangla', 'Software version', 'Software version'),
(2632, 'bangla', 'Check update', 'Check update'),
(2633, 'bangla', 'PHP version', 'PHP version'),
(2634, 'bangla', 'Curl enable', 'Curl enable'),
(2635, 'bangla', 'Enabled', 'Enabled'),
(2636, 'bangla', 'Purchase code', 'Purchase code'),
(2637, 'bangla', 'Product license', 'Product license'),
(2638, 'bangla', 'invalid', 'invalid'),
(2639, 'bangla', 'Enter valid purchase code', 'Enter valid purchase code'),
(2640, 'bangla', 'Customer support status', 'Customer support status'),
(2641, 'bangla', 'Support expiry date', 'Support expiry date'),
(2642, 'bangla', 'Customer name', 'Customer name'),
(2643, 'bangla', 'Get customer support', 'Get customer support'),
(2644, 'bangla', 'Customer support', 'Customer support'),
(2645, 'bangla', 'Email', 'Email'),
(2646, 'bangla', 'Password', 'Password'),
(2647, 'bangla', 'Forgot password', 'Forgot password'),
(2648, 'bangla', 'Help', 'Help'),
(2649, 'bangla', 'Login', 'Login'),
(2650, 'bangla', 'Total Student', 'Total Student'),
(2651, 'bangla', 'Teacher', 'Teacher'),
(2652, 'bangla', 'Total Teacher', 'Total Teacher'),
(2653, 'bangla', 'Parents', 'Parents'),
(2654, 'bangla', 'Total Parent', 'Total Parent'),
(2655, 'bangla', 'Staff', 'Staff'),
(2656, 'bangla', 'Total Staff', 'Total Staff'),
(2657, 'bangla', 'Todays Attendance', 'Todays Attendance'),
(2658, 'bangla', 'Go to Attendance', 'Go to Attendance'),
(2659, 'bangla', 'Income Report', 'Income Report'),
(2660, 'bangla', 'Year', 'Year'),
(2661, 'bangla', 'Month', 'Month'),
(2662, 'bangla', 'Week', 'Week'),
(2663, 'bangla', 'Upcoming Events', 'Upcoming Events'),
(2664, 'bangla', 'See all', 'See all'),
(2665, 'bangla', 'Admin', 'Admin'),
(2666, 'bangla', 'Users', 'Users'),
(2667, 'bangla', 'Accountant', 'Accountant'),
(2668, 'bangla', 'Librarian', 'Librarian'),
(2669, 'bangla', 'Parent', 'Parent'),
(2670, 'bangla', 'Student', 'Student'),
(2671, 'bangla', 'Teacher Permission', 'Teacher Permission'),
(2672, 'bangla', 'Admissions', 'Admissions'),
(2673, 'bangla', 'Examination', 'Examination'),
(2674, 'bangla', 'Exam Category', 'Exam Category'),
(2675, 'bangla', 'Offline Exam', 'Offline Exam'),
(2676, 'bangla', 'Marks', 'Marks'),
(2677, 'bangla', 'Grades', 'Grades'),
(2678, 'bangla', 'Promotion', 'Promotion'),
(2679, 'bangla', 'Academic', 'Academic'),
(2680, 'bangla', 'Daily Attendance', 'Daily Attendance'),
(2681, 'bangla', 'Class List', 'Class List'),
(2682, 'bangla', 'Class Routine', 'Class Routine'),
(2683, 'bangla', 'Subjects', 'Subjects'),
(2684, 'bangla', 'Gradebooks', 'Gradebooks'),
(2685, 'bangla', 'Syllabus', 'Syllabus'),
(2686, 'bangla', 'Class Room', 'Class Room'),
(2687, 'bangla', 'Department', 'Department'),
(2688, 'bangla', 'Accounting', 'Accounting'),
(2689, 'bangla', 'Student Fee Manager', 'Student Fee Manager'),
(2690, 'bangla', 'Offline Payment Request', 'Offline Payment Request'),
(2691, 'bangla', 'Expense Manager', 'Expense Manager'),
(2692, 'bangla', 'Expense Category', 'Expense Category'),
(2693, 'bangla', 'Back Office', 'Back Office'),
(2694, 'bangla', 'Book List Manager', 'Book List Manager'),
(2695, 'bangla', 'Book Issue Report', 'Book Issue Report'),
(2696, 'bangla', 'Noticeboard', 'Noticeboard'),
(2697, 'bangla', 'Events', 'Events'),
(2698, 'bangla', 'School Settings', 'School Settings'),
(2699, 'bangla', 'School information', 'School information'),
(2700, 'bangla', 'Update settings', 'Update settings'),
(2701, 'bangla', 'Deactive', 'Deactive'),
(2702, 'bangla', 'Session has been activated', 'Session has been activated'),
(2703, 'bangla', 'Update session', 'Update session'),
(2704, 'bangla', 'Admins', 'Admins'),
(2705, 'bangla', 'Create Admin', 'Create Admin'),
(2706, 'bangla', 'User Info', 'User Info'),
(2707, 'bangla', 'Oprions', 'Oprions'),
(2708, 'bangla', 'Edit Admin', 'Edit Admin'),
(2709, 'bangla', 'Teachers', 'Teachers'),
(2710, 'bangla', 'Create Teacher', 'Create Teacher'),
(2711, 'bangla', 'Create Accountant', 'Create Accountant'),
(2712, 'bangla', 'Edit Accountant', 'Edit Accountant'),
(2713, 'bangla', 'Librarians', 'Librarians'),
(2714, 'bangla', 'Create Librarian', 'Create Librarian'),
(2715, 'bangla', 'Edit Librarian', 'Edit Librarian'),
(2716, 'bangla', 'Create Parent', 'Create Parent'),
(2717, 'bangla', 'Edit Parent', 'Edit Parent'),
(2718, 'bangla', 'Students', 'Students'),
(2719, 'bangla', 'Create Student', 'Create Student'),
(2720, 'bangla', 'Generate id card', 'Generate id card'),
(2721, 'bangla', 'Assigned Permission For Teacher', 'Assigned Permission For Teacher'),
(2722, 'bangla', 'Select a class', 'Select a class'),
(2723, 'bangla', 'First select a class', 'First select a class'),
(2724, 'bangla', 'Please select a class and section', 'Please select a class and section'),
(2725, 'bangla', 'Attendance', 'Attendance'),
(2726, 'bangla', 'Permission updated successfully.', 'Permission updated successfully.'),
(2727, 'bangla', 'Admission', 'Admission'),
(2728, 'bangla', 'Bulk student admission', 'Bulk student admission'),
(2729, 'bangla', 'Class', 'Class'),
(2730, 'bangla', 'Section', 'Section'),
(2731, 'bangla', 'Select section', 'Select section'),
(2732, 'bangla', 'Birthday', 'Birthday'),
(2733, 'bangla', 'Select gender', 'Select gender'),
(2734, 'bangla', 'Others', 'Others'),
(2735, 'bangla', 'Student profile image', 'Student profile image'),
(2736, 'bangla', 'Add Student', 'Add Student'),
(2737, 'bangla', 'Create Exam Category', 'Create Exam Category'),
(2738, 'bangla', 'Add Exam Category', 'Add Exam Category'),
(2739, 'bangla', 'Title', 'Title'),
(2740, 'bangla', 'Class test', 'Class test'),
(2741, 'bangla', 'Edit Exam Category', 'Edit Exam Category'),
(2742, 'bangla', 'Midterm exam', 'Midterm exam'),
(2743, 'bangla', 'Final exam', 'Final exam'),
(2744, 'bangla', 'Admission exam', 'Admission exam'),
(2745, 'bangla', 'Create Exam', 'Create Exam'),
(2746, 'bangla', 'Add Exam', 'Add Exam'),
(2747, 'bangla', 'Exam', 'Exam'),
(2748, 'bangla', 'Starting Time', 'Starting Time'),
(2749, 'bangla', 'Ending Time', 'Ending Time'),
(2750, 'bangla', 'Total Marks', 'Total Marks'),
(2751, 'bangla', 'Edit Exam', 'Edit Exam'),
(2752, 'bangla', 'Manage Marks', 'Manage Marks'),
(2753, 'bangla', 'Select category', 'Select category'),
(2754, 'bangla', 'Select class', 'Select class'),
(2755, 'bangla', 'Please select all the fields', 'Please select all the fields'),
(2756, 'bangla', 'Examknation', 'Examknation'),
(2757, 'bangla', 'Create Grade', 'Create Grade'),
(2758, 'bangla', 'Add grade', 'Add grade'),
(2759, 'bangla', 'Grade', 'Grade'),
(2760, 'bangla', 'Grade Point', 'Grade Point'),
(2761, 'bangla', 'Mark From', 'Mark From'),
(2762, 'bangla', 'Mark Upto', 'Mark Upto'),
(2763, 'bangla', 'Promotions', 'Promotions'),
(2764, 'bangla', 'Current session', 'Current session'),
(2765, 'bangla', 'Session from', 'Session from'),
(2766, 'bangla', 'Next session', 'Next session'),
(2767, 'bangla', 'Session to', 'Session to'),
(2768, 'bangla', 'Promoting from', 'Promoting from'),
(2769, 'bangla', 'Promoting to', 'Promoting to'),
(2770, 'bangla', 'Manage promotion', 'Manage promotion'),
(2771, 'bangla', 'Take Attendance', 'Take Attendance'),
(2772, 'bangla', 'Select a month', 'Select a month'),
(2773, 'bangla', 'January', 'January'),
(2774, 'bangla', 'February', 'February'),
(2775, 'bangla', 'March', 'March'),
(2776, 'bangla', 'April', 'April'),
(2777, 'bangla', 'May', 'May'),
(2778, 'bangla', 'June', 'June'),
(2779, 'bangla', 'July', 'July'),
(2780, 'bangla', 'August', 'August'),
(2781, 'bangla', 'September', 'September'),
(2782, 'bangla', 'October', 'October'),
(2783, 'bangla', 'November', 'November'),
(2784, 'bangla', 'December', 'December'),
(2785, 'bangla', 'Select a year', 'Select a year'),
(2786, 'bangla', 'Please select in all fields !', 'Please select in all fields !'),
(2787, 'bangla', 'Classes', 'Classes'),
(2788, 'bangla', 'Create Class', 'Create Class'),
(2789, 'bangla', 'Add class', 'Add class'),
(2790, 'bangla', 'Edit Section', 'Edit Section'),
(2791, 'bangla', 'Edit Class', 'Edit Class'),
(2792, 'bangla', 'Routines', 'Routines'),
(2793, 'bangla', 'Add class routine', 'Add class routine'),
(2794, 'bangla', 'Create Subject', 'Create Subject'),
(2795, 'bangla', 'Add subject', 'Add subject'),
(2796, 'bangla', 'Edit Subject', 'Edit Subject'),
(2797, 'bangla', 'Select a exam category', 'Select a exam category'),
(2798, 'bangla', 'Create syllabus', 'Create syllabus'),
(2799, 'bangla', 'Add syllabus', 'Add syllabus'),
(2800, 'bangla', 'Class Rooms', 'Class Rooms'),
(2801, 'bangla', 'Create Class Room', 'Create Class Room'),
(2802, 'bangla', 'Add class room', 'Add class room'),
(2803, 'bangla', 'Edit Class Room', 'Edit Class Room'),
(2804, 'bangla', 'Departments', 'Departments'),
(2805, 'bangla', 'Create Department', 'Create Department'),
(2806, 'bangla', 'Add department', 'Add department'),
(2807, 'bangla', 'Edit Department', 'Edit Department'),
(2808, 'bangla', 'Add Single Invoice', 'Add Single Invoice'),
(2809, 'bangla', 'Add Mass Invoice', 'Add Mass Invoice'),
(2810, 'bangla', 'All class', 'All class'),
(2811, 'bangla', 'All status', 'All status'),
(2812, 'bangla', 'Paid', 'Paid'),
(2813, 'bangla', 'Unpaid', 'Unpaid'),
(2814, 'bangla', 'Invoice No', 'Invoice No'),
(2815, 'bangla', 'Invoice Title', 'Invoice Title'),
(2816, 'bangla', 'Total Amount', 'Total Amount'),
(2817, 'bangla', 'Created at', 'Created at'),
(2818, 'bangla', 'Paid Amount', 'Paid Amount'),
(2819, 'bangla', 'Expense', 'Expense'),
(2820, 'bangla', 'Create Expense', 'Create Expense'),
(2821, 'bangla', 'Add New Expense', 'Add New Expense'),
(2822, 'bangla', 'Create Expense Category', 'Create Expense Category'),
(2823, 'bangla', 'Add Expense Category', 'Add Expense Category'),
(2824, 'bangla', 'Option', 'Option'),
(2825, 'bangla', 'Edit Expense Category', 'Edit Expense Category'),
(2826, 'bangla', 'Book', 'Book'),
(2827, 'bangla', 'Add book', 'Add book'),
(2828, 'bangla', 'Book name', 'Book name'),
(2829, 'bangla', 'Author', 'Author'),
(2830, 'bangla', 'Copies', 'Copies'),
(2831, 'bangla', 'Available copies', 'Available copies'),
(2832, 'bangla', 'Edit Book', 'Edit Book'),
(2833, 'bangla', 'Book Issue', 'Book Issue'),
(2834, 'bangla', 'Issue Book', 'Issue Book'),
(2835, 'bangla', 'Noticeboard calendar', 'Noticeboard calendar'),
(2836, 'bangla', 'Add New Notice', 'Add New Notice'),
(2837, 'bangla', 'Locales:', 'Locales:'),
(2838, 'bangla', 'Current Plan', 'Current Plan'),
(2839, 'bangla', 'Silver', 'Silver'),
(2840, 'bangla', 'Monthly', 'Monthly'),
(2841, 'bangla', 'Subscription Renew Date', 'Subscription Renew Date'),
(2842, 'bangla', 'Amount To Be Charged', 'Amount To Be Charged'),
(2843, 'bangla', 'Create Event', 'Create Event'),
(2844, 'bangla', 'Event title', 'Event title'),
(2845, 'bangla', 'Date', 'Date'),
(2846, 'bangla', 'Update event', 'Update event'),
(2847, 'bangla', 'Upload addons zip file', 'Upload addons zip file'),
(2848, 'bangla', 'Verified', 'Verified'),
(2849, 'bangla', 'Details info', 'Details info'),
(2850, 'bangla', 'Phone Number', 'Phone Number'),
(2851, 'bangla', 'Designation', 'Designation'),
(2852, 'bangla', 'Save Changes', 'Save Changes'),
(2853, 'bangla', 'Select a status', 'Select a status'),
(2854, 'bangla', 'Update school', 'Update school'),
(2855, 'bangla', 'Package price', 'Package price'),
(2856, 'bangla', 'Package Type', 'Package Type'),
(2857, 'bangla', 'Select a package type', 'Select a package type'),
(2858, 'bangla', 'Trail', 'Trail'),
(2859, 'bangla', 'Select a interval', 'Select a interval'),
(2860, 'bangla', 'Days', 'Days'),
(2861, 'bangla', 'Yearly', 'Yearly'),
(2862, 'bangla', 'Interval Preiod', 'Interval Preiod'),
(2863, 'bangla', 'Description', 'Description'),
(2864, 'bangla', 'Create package', 'Create package'),
(2865, 'bangla', 'Update package', 'Update package'),
(2866, 'bangla', 'Invalid purchase code', 'Invalid purchase code'),
(2867, 'bangla', 'Inactive', 'Inactive'),
(2868, 'bangla', 'Save event', 'Save event'),
(2869, 'bangla', 'Create', 'Create'),
(2870, 'bangla', 'Select a department', 'Select a department'),
(2871, 'bangla', 'One', 'One'),
(2872, 'bangla', 'Two', 'Two'),
(2873, 'bangla', 'Three', 'Three'),
(2874, 'bangla', 'Four', 'Four'),
(2875, 'bangla', 'Five', 'Five'),
(2876, 'bangla', 'Six', 'Six'),
(2877, 'bangla', 'Seven', 'Seven'),
(2878, 'bangla', 'Eight', 'Eight'),
(2879, 'bangla', 'Nine', 'Nine'),
(2880, 'bangla', 'Ten', 'Ten'),
(2881, 'bangla', 'Add students', 'Add students'),
(2882, 'bangla', 'Create category', 'Create category'),
(2883, 'bangla', 'Exam Name', 'Exam Name'),
(2884, 'bangla', 'Select exam category name', 'Select exam category name'),
(2885, 'bangla', 'Subject', 'Subject'),
(2886, 'bangla', 'Starting date', 'Starting date'),
(2887, 'bangla', 'Ending date', 'Ending date'),
(2888, 'bangla', 'Student name', 'Student name');
INSERT INTO `language` (`id`, `name`, `phrase`, `translated`) VALUES
(2889, 'bangla', 'Mark', 'Mark'),
(2890, 'bangla', 'Comment', 'Comment'),
(2891, 'bangla', 'Value has been updated successfully', 'Value has been updated successfully'),
(2892, 'bangla', 'Required mark field', 'Required mark field'),
(2893, 'bangla', 'Image', 'Image'),
(2894, 'bangla', 'Enroll to', 'Enroll to'),
(2895, 'bangla', 'Select a section', 'Select a section'),
(2896, 'bangla', 'Attendance Report Of', 'Attendance Report Of'),
(2897, 'bangla', 'Last Update at', 'Last Update at'),
(2898, 'bangla', 'Time', 'Time'),
(2899, 'bangla', 'Please select the required fields', 'Please select the required fields'),
(2900, 'bangla', 'Saturday', 'Saturday'),
(2901, 'bangla', 'Sunday', 'Sunday'),
(2902, 'bangla', 'Monday', 'Monday'),
(2903, 'bangla', 'Tuesday', 'Tuesday'),
(2904, 'bangla', 'Wednesday', 'Wednesday'),
(2905, 'bangla', 'Update subject', 'Update subject'),
(2906, 'bangla', 'Select subject', 'Select subject'),
(2907, 'bangla', 'Assign a teacher', 'Assign a teacher'),
(2908, 'bangla', 'Select a class room', 'Select a class room'),
(2909, 'bangla', 'Day', 'Day'),
(2910, 'bangla', 'Select a day', 'Select a day'),
(2911, 'bangla', 'Thursday', 'Thursday'),
(2912, 'bangla', 'Friday', 'Friday'),
(2913, 'bangla', 'Starting hour', 'Starting hour'),
(2914, 'bangla', 'Starting minute', 'Starting minute'),
(2915, 'bangla', 'Ending hour', 'Ending hour'),
(2916, 'bangla', 'Ending minute', 'Ending minute'),
(2917, 'bangla', 'Add routine', 'Add routine'),
(2918, 'bangla', 'Edit class routine', 'Edit class routine'),
(2919, 'bangla', 'Tittle', 'Tittle'),
(2920, 'bangla', 'Upload syllabus', 'Upload syllabus'),
(2921, 'bangla', 'Select student', 'Select student'),
(2922, 'bangla', 'Select a student', 'Select a student'),
(2923, 'bangla', 'Payment method', 'Payment method'),
(2924, 'bangla', 'Select a payment method', 'Select a payment method'),
(2925, 'bangla', 'Cash', 'Cash'),
(2926, 'bangla', 'Paypal', 'Paypal'),
(2927, 'bangla', 'Paytm', 'Paytm'),
(2928, 'bangla', 'Razorpay', 'Razorpay'),
(2929, 'bangla', 'Create invoice', 'Create invoice'),
(2930, 'bangla', 'Payment date', 'Payment date'),
(2931, 'bangla', 'Print invoice', 'Print invoice'),
(2932, 'bangla', 'Edit Invoice', 'Edit Invoice'),
(2933, 'bangla', 'Amount', 'Amount'),
(2934, 'bangla', 'Select an expense category', 'Select an expense category'),
(2935, 'bangla', 'Edit Expense', 'Edit Expense'),
(2936, 'bangla', 'Issue date', 'Issue date'),
(2937, 'bangla', 'Select book', 'Select book'),
(2938, 'bangla', 'Id', 'Id'),
(2939, 'bangla', 'Pending', 'Pending'),
(2940, 'bangla', 'Update issued book', 'Update issued book'),
(2941, 'bangla', 'Return this book', 'Return this book'),
(2942, 'bangla', 'Notice title', 'Notice title'),
(2943, 'bangla', 'Start date', 'Start date'),
(2944, 'bangla', 'Setup additional date & time', 'Setup additional date & time'),
(2945, 'bangla', 'Start time', 'Start time'),
(2946, 'bangla', 'End date', 'End date'),
(2947, 'bangla', 'End time', 'End time'),
(2948, 'bangla', 'Notice', 'Notice'),
(2949, 'bangla', 'Show on website', 'Show on website'),
(2950, 'bangla', 'Show', 'Show'),
(2951, 'bangla', 'Do not need to show', 'Do not need to show'),
(2952, 'bangla', 'Upload notice photo', 'Upload notice photo'),
(2953, 'bangla', 'Save notice', 'Save notice'),
(2954, 'bangla', 'School Currency', 'School Currency'),
(2955, 'bangla', 'Exam List', 'Exam List'),
(2956, 'bangla', 'Profile', 'Profile'),
(2957, 'bangla', ' Download', ' Download'),
(2958, 'bangla', 'Select a subject', 'Select a subject'),
(2959, 'bangla', 'Welcome, to', 'Welcome, to'),
(2960, 'bangla', 'Fee Manager', 'Fee Manager'),
(2961, 'bangla', 'List Of Books', 'List Of Books'),
(2962, 'bangla', 'Issued Book', 'Issued Book'),
(2963, 'bangla', 'Student Code', 'Student Code'),
(2964, 'bangla', 'Candice Kennedy', 'Candice Kennedy'),
(2965, 'bangla', 'English', 'English'),
(2966, 'bangla', 'Natalie Ashley', 'Natalie Ashley'),
(2967, 'bangla', 'Byron Chase', 'Byron Chase'),
(2968, 'bangla', 'Rafael Hardy', 'Rafael Hardy'),
(2969, 'bangla', 'Mathematics', 'Mathematics'),
(2970, 'bangla', 'Aphrodite Shaffer', 'Aphrodite Shaffer'),
(2971, 'bangla', 'Bangla', 'Bangla'),
(2972, 'bangla', 'Fatima Phillips', 'Fatima Phillips'),
(2973, 'bangla', 'Sydney Pearson', 'Sydney Pearson'),
(2974, 'bangla', 'Drawing', 'Drawing'),
(2975, 'bangla', 'Imani Cooper', 'Imani Cooper'),
(2976, 'bangla', 'Ulric Spencer', 'Ulric Spencer'),
(2977, 'bangla', 'Yoshio Gentry', 'Yoshio Gentry'),
(2978, 'bangla', 'Attendance report', 'Attendance report'),
(2979, 'bangla', 'Of', 'Of'),
(2980, 'bangla', 'Last updated at', 'Last updated at'),
(2981, 'bangla', 'View Marks', 'View Marks'),
(2982, 'bangla', 'Subject name', 'Subject name'),
(2983, 'bangla', 'Pay', 'Pay'),
(2984, 'bangla', 'List Of Book', 'List Of Book'),
(2985, 'bangla', 'Child', 'Child'),
(2986, 'bangla', 'Teaches', 'Teaches'),
(2987, 'bangla', 'Student List', 'Student List'),
(2988, 'bangla', 'Id card', 'Id card'),
(2989, 'bangla', 'Code', 'Code'),
(2990, 'bangla', 'Not found', 'Not found'),
(2991, 'bangla', 'Contact', 'Contact'),
(2992, 'bangla', 'Search Attendance Report', 'Search Attendance Report'),
(2993, 'bangla', 'Please select in all fields !', 'Please select in all fields !'),
(2994, 'bangla', 'Please select student', 'Please select student'),
(2995, 'bangla', 'Download', 'Download'),
(2996, 'bangla', 'Ekattor', 'Ekattor'),
(2997, 'bangla', 'Add  Single Invoice', 'Add  Single Invoice'),
(2998, 'bangla', 'Add  Mass Invoice', 'Add  Mass Invoice'),
(2999, 'bangla', 'Update invoice', 'Update invoice'),
(3000, 'bangla', 'Invoice', 'Invoice'),
(3001, 'bangla', 'Please find below the invoice', 'Please find below the invoice'),
(3002, 'bangla', 'Billing Address', 'Billing Address'),
(3003, 'bangla', 'Due Amount', 'Due Amount'),
(3004, 'bangla', 'Student Fee', 'Student Fee'),
(3005, 'bangla', 'Subtotal', 'Subtotal'),
(3006, 'bangla', 'Due', 'Due'),
(3007, 'bangla', 'Grand Total', 'Grand Total'),
(3008, 'bangla', 'Update book issue information', 'Update book issue information'),
(3009, 'bangla', 'Not Subscribed', 'Not Subscribed'),
(3010, 'bangla', 'You are not subscribed to any plan. Subscribe now.', 'You are not subscribed to any plan. Subscribe now.'),
(3011, 'bangla', 'Subscribe', 'Subscribe'),
(3012, 'bangla', 'Package List', 'Package List'),
(3013, 'bangla', 'Payment | Ekator 8', 'Payment | Ekator 8'),
(3014, 'bangla', 'Make Payment', 'Make Payment'),
(3015, 'bangla', 'Payment Gateway', 'Payment Gateway'),
(3016, 'bangla', 'Offline', 'Offline'),
(3017, 'bangla', 'Addon', 'Addon'),
(3018, 'bangla', 'Invoice Summary', 'Invoice Summary'),
(3019, 'bangla', 'Document of your payment', 'Document of your payment'),
(3020, 'bangla', 'Submit payment document', 'Submit payment document'),
(3021, 'bangla', 'Instruction', 'Instruction'),
(3022, 'bangla', 'Admin will review your payment document and then approve the Payment.', 'Admin will review your payment document and then approve the Payment.'),
(3023, 'bangla', 'Pending Payment', 'Pending Payment'),
(3024, 'bangla', 'You payment request has been sent to Superadmin ', 'You payment request has been sent to Superadmin '),
(3025, 'bangla', 'Suspended', 'Suspended'),
(3026, 'bangla', 'Enter your email address to reset your password.', 'Enter your email address to reset your password.'),
(3027, 'bangla', 'Reset password', 'Reset password'),
(3028, 'bangla', 'Language Settings', 'Language Settings'),
(3029, 'bangla', 'Language', 'Language'),
(3030, 'bangla', 'Edit phrase', 'Edit phrase'),
(3031, 'bangla', 'Delete language', 'Delete language'),
(3032, 'bangla', 'edit_phrase', 'edit_phrase'),
(3033, 'bangla', 'delete_language', 'delete_language'),
(3034, 'bangla', 'System default language can not be removed', 'System default language can not be removed'),
(3035, 'bangla', 'language_list', 'language_list'),
(3036, 'bangla', 'add_language', 'add_language'),
(3037, 'bangla', 'Language list', 'Language list'),
(3038, 'bangla', 'Add language', 'Add language'),
(3039, 'bangla', 'Add new phrase', 'Add new phrase'),
(3040, 'bangla', 'add_new_language', 'add_new_language'),
(3041, 'bangla', 'No special character or space is allowed', 'No special character or space is allowed'),
(3042, 'bangla', 'valid_examples', 'valid_examples'),
(3043, 'bangla', 'No special character or space is allowed', 'No special character or space is allowed'),
(3044, 'bangla', 'Validexamples', 'Validexamples'),
(3045, 'bangla', 'Add new language', 'Add new language'),
(3046, 'bangla', 'Valid examples', 'Valid examples'),
(3047, 'bangla', 'Phrase updated', 'Phrase updated'),
(3048, 'bangla', 'System Language', 'System Language'),
(3049, 'bangla', 'Edit Grade', 'Edit Grade'),
(3050, 'bangla', 'Number of scopy', 'Number of scopy'),
(3051, 'bangla', 'Save book', 'Save book'),
(3052, 'bangla', 'New Password', 'New Password'),
(3053, 'bangla', 'Confirm Password', 'Confirm Password'),
(3054, 'bangla', 'Current Password', 'Current Password'),
(3055, 'bangla', 'Add Parent', 'Add Parent'),
(3056, 'bangla', 'Parent profile image', 'Parent profile image'),
(3057, 'bangla', 'Allowances', 'Allowances'),
(3058, 'bangla', 'Type', 'Type'),
(3059, 'bangla', 'Select child', 'Select child'),
(3060, 'bangla', 'Show student list', 'Show student list'),
(3061, 'bangla', 'Update attendance', 'Update attendance'),
(3062, 'bangla', 'Present All', 'Present All'),
(3063, 'bangla', 'Absent All', 'Absent All'),
(3064, 'bangla', 'present', 'present'),
(3065, 'bangla', 'absent', 'absent'),
(3066, 'bangla', 'not_updated_yet', 'not_updated_yet'),
(3067, 'bangla', '31', '31'),
(3068, 'bangla', 'Not updated yet', 'Not updated yet'),
(3069, 'bangla', 'Update class', 'Update class'),
(3070, 'bangla', 'Edit syllabus', 'Edit syllabus'),
(3071, 'bangla', 'Select expense category', 'Select expense category'),
(3072, 'bangla', 'Filter Options', 'Filter Options'),
(3073, 'bangla', 'Reset', 'Reset'),
(3074, 'bangla', 'Apply', 'Apply'),
(3075, 'bangla', 'Profile info updated successfully', 'Profile info updated successfully'),
(3076, 'bangla', 'not_found', 'not_found'),
(3077, 'bangla', 'No date found', 'No date found'),
(3078, 'bangla', 'No date found', 'No date found'),
(3079, 'bangla', 'Blood ', 'Blood '),
(3080, 'bangla', 'Blood Type', 'Blood Type'),
(3081, 'bangla', 'Help Link', 'Help Link'),
(3082, 'bangla', 'From', 'From'),
(3083, 'bangla', 'To', 'To'),
(3084, 'bangla', 'Select a parent', 'Select a parent'),
(3085, 'bangla', 'Add', 'Add'),
(3086, 'bangla', 'Document', 'Document'),
(3087, 'bangla', 'Decline', 'Decline'),
(3088, 'bangla', 'Number of child:', 'Number of child:'),
(3089, 'bangla', 'Number of child', 'Number of child'),
(3090, 'bangla', 'Parent Create', 'Parent Create'),
(3091, 'bangla', 'Parent Update', 'Parent Update'),
(3092, 'bangla', 'Version updated successfully', 'Version updated successfully'),
(3093, 'bangla', 'Subcription', 'Subcription'),
(3094, 'bangla', 'Expired Subscription', 'Expired Subscription'),
(3095, 'bangla', 'Website Settings', 'Website Settings'),
(3096, 'bangla', 'Manage Faq', 'Manage Faq'),
(3097, 'bangla', 'Visit Website', 'Visit Website'),
(3098, 'bangla', 'Navbar Title', 'Navbar Title'),
(3099, 'bangla', 'Frontend View', 'Frontend View'),
(3100, 'bangla', 'No', 'No'),
(3101, 'bangla', 'Youtube Api Key', 'Youtube Api Key'),
(3102, 'bangla', 'Vimeo Api Key', 'Vimeo Api Key'),
(3103, 'bangla', 'Has to be bigger than', 'Has to be bigger than'),
(3104, 'bangla', 'Nav Bar Logo', 'Nav Bar Logo'),
(3105, 'bangla', 'Email Template Settings', 'Email Template Settings'),
(3106, 'bangla', 'Email Title', 'Email Title'),
(3107, 'bangla', 'Email Details', 'Email Details'),
(3108, 'bangla', 'Remaining characters is', 'Remaining characters is'),
(3109, 'bangla', 'Warning Text', 'Warning Text'),
(3110, 'bangla', 'Email logo', 'Email logo'),
(3111, 'bangla', 'Social logo-1', 'Social logo-1'),
(3112, 'bangla', 'Social logo-2', 'Social logo-2'),
(3113, 'bangla', 'Social logo-3', 'Social logo-3'),
(3114, 'bangla', 'Images for email templates will only support if the application is hosted on a live server. Localhost will not support this.', 'Images for email templates will only support if the application is hosted on a live server. Localhost will not support this.'),
(3115, 'bangla', 'School Logo', 'School Logo'),
(3116, 'bangla', 'Admin List', 'Admin List'),
(3117, 'bangla', 'Archive ', 'Archive '),
(3118, 'bangla', 'Trial', 'Trial'),
(3119, 'bangla', 'GENERAL SETTINGS', 'GENERAL SETTINGS'),
(3120, 'bangla', 'Banner Title', 'Banner Title'),
(3121, 'bangla', 'Banner Subtitle', 'Banner Subtitle'),
(3122, 'bangla', 'Price Subtitle', 'Price Subtitle'),
(3123, 'bangla', 'Faq Subtitle', 'Faq Subtitle'),
(3124, 'bangla', 'Facebook Link', 'Facebook Link'),
(3125, 'bangla', 'Twitter Link', 'Twitter Link'),
(3126, 'bangla', 'Linkedin Link', 'Linkedin Link'),
(3127, 'bangla', 'Instagram Link', 'Instagram Link'),
(3128, 'bangla', 'Contact Mail', 'Contact Mail'),
(3129, 'bangla', 'Frontend Footer Text', 'Frontend Footer Text'),
(3130, 'bangla', 'Copyright Text', 'Copyright Text'),
(3131, 'english', 'Password changed successfully', 'Password changed successfully'),
(3132, 'bangla', 'Password changed successfully', 'Password changed successfully'),
(3133, 'english', 'Feature', 'Feature'),
(3134, 'bangla', 'Feature', 'Feature'),
(3135, 'english', 'Faq', 'Faq'),
(3136, 'bangla', 'Faq', 'Faq'),
(3137, 'english', 'Register', 'Register'),
(3138, 'bangla', 'Register', 'Register'),
(3139, 'english', 'School Register Form', 'School Register Form'),
(3140, 'bangla', 'School Register Form', 'School Register Form'),
(3141, 'english', 'Admin Name', 'Admin Name'),
(3142, 'bangla', 'Admin Name', 'Admin Name'),
(3143, 'english', 'User Account', 'User Account'),
(3144, 'bangla', 'User Account', 'User Account'),
(3145, 'english', 'Our Features', 'Our Features'),
(3146, 'bangla', 'Our Features', 'Our Features'),
(3147, 'english', 'Features', 'Features'),
(3148, 'bangla', 'Features', 'Features'),
(3149, 'english', 'Students Admission', 'Students Admission'),
(3150, 'bangla', 'Students Admission', 'Students Admission'),
(3151, 'english', 'Your schools can add their students in two different ways', 'Your schools can add their students in two different ways'),
(3152, 'bangla', 'Your schools can add their students in two different ways', 'Your schools can add their students in two different ways'),
(3153, 'english', 'Take your students attendance in a smart way', 'Take your students attendance in a smart way'),
(3154, 'bangla', 'Take your students attendance in a smart way', 'Take your students attendance in a smart way'),
(3155, 'english', 'Manage your schools class list whenever you want', 'Manage your schools class list whenever you want'),
(3156, 'bangla', 'Manage your schools class list whenever you want', 'Manage your schools class list whenever you want'),
(3157, 'english', 'Add different subjects for different classes', 'Add different subjects for different classes'),
(3158, 'bangla', 'Add different subjects for different classes', 'Add different subjects for different classes'),
(3159, 'english', 'Event Calender', 'Event Calender'),
(3160, 'bangla', 'Event Calender', 'Event Calender'),
(3161, 'english', 'The school admin can manage their schools events separately', 'The school admin can manage their schools events separately'),
(3162, 'bangla', 'The school admin can manage their schools events separately', 'The school admin can manage their schools events separately'),
(3163, 'english', 'Routine', 'Routine'),
(3164, 'bangla', 'Routine', 'Routine'),
(3165, 'english', 'Manage your schools class routine easily', 'Manage your schools class routine easily'),
(3166, 'bangla', 'Manage your schools class routine easily', 'Manage your schools class routine easily'),
(3167, 'english', 'Student Information', 'Student Information'),
(3168, 'bangla', 'Student Information', 'Student Information'),
(3169, 'english', 'Add your students information within a few minute', 'Add your students information within a few minute'),
(3170, 'bangla', 'Add your students information within a few minute', 'Add your students information within a few minute'),
(3171, 'english', 'Manage syllabuses based on the classes', 'Manage syllabuses based on the classes'),
(3172, 'bangla', 'Manage syllabuses based on the classes', 'Manage syllabuses based on the classes'),
(3173, 'english', 'Fees Management', 'Fees Management'),
(3174, 'bangla', 'Fees Management', 'Fees Management'),
(3175, 'english', 'Pay academic fees in a smart way with Ekattor 8', 'Pay academic fees in a smart way with Ekattor 8'),
(3176, 'bangla', 'Pay academic fees in a smart way with Ekattor 8', 'Pay academic fees in a smart way with Ekattor 8'),
(3177, 'english', 'ID Card Generator', 'ID Card Generator'),
(3178, 'bangla', 'ID Card Generator', 'ID Card Generator'),
(3179, 'english', 'Generate your students ID card whenever you want', 'Generate your students ID card whenever you want'),
(3180, 'bangla', 'Generate your students ID card whenever you want', 'Generate your students ID card whenever you want'),
(3181, 'english', 'Online Payment Gateway', 'Online Payment Gateway'),
(3182, 'bangla', 'Online Payment Gateway', 'Online Payment Gateway'),
(3183, 'english', 'Pay your subscription and academic fees', 'Pay your subscription and academic fees'),
(3184, 'bangla', 'Pay your subscription and academic fees', 'Pay your subscription and academic fees'),
(3185, 'english', 'Invoice Generator', 'Invoice Generator'),
(3186, 'bangla', 'Invoice Generator', 'Invoice Generator'),
(3187, 'english', 'Generate invoices to make the payments more reliable', 'Generate invoices to make the payments more reliable'),
(3188, 'bangla', 'Generate invoices to make the payments more reliable', 'Generate invoices to make the payments more reliable'),
(3189, 'english', 'Offline Payment', 'Offline Payment'),
(3190, 'bangla', 'Offline Payment', 'Offline Payment'),
(3191, 'english', 'Complete payment with local money', 'Complete payment with local money'),
(3192, 'bangla', 'Complete payment with local money', 'Complete payment with local money'),
(3193, 'english', 'Book List', 'Book List'),
(3194, 'bangla', 'Book List', 'Book List'),
(3195, 'english', 'Manage your schools library within a few clicks', 'Manage your schools library within a few clicks'),
(3196, 'bangla', 'Manage your schools library within a few clicks', 'Manage your schools library within a few clicks'),
(3197, 'english', 'Manage your schools notices smartly', 'Manage your schools notices smartly'),
(3198, 'bangla', 'Manage your schools notices smartly', 'Manage your schools notices smartly'),
(3199, 'english', 'Create and manage your schools exams and categories', 'Create and manage your schools exams and categories'),
(3200, 'bangla', 'Create and manage your schools exams and categories', 'Create and manage your schools exams and categories'),
(3201, 'english', 'Marks Management', 'Marks Management'),
(3202, 'bangla', 'Marks Management', 'Marks Management'),
(3203, 'english', 'Manage your students exam marks', 'Manage your students exam marks'),
(3204, 'bangla', 'Manage your students exam marks', 'Manage your students exam marks'),
(3205, 'english', 'Add and manage grades in the examination', 'Add and manage grades in the examination'),
(3206, 'bangla', 'Add and manage grades in the examination', 'Add and manage grades in the examination'),
(3207, 'english', 'Have Any Question', 'Have Any Question'),
(3208, 'bangla', 'Have Any Question', 'Have Any Question'),
(3209, 'english', 'Contact us with any questions', 'Contact us with any questions'),
(3210, 'bangla', 'Contact us with any questions', 'Contact us with any questions'),
(3211, 'english', 'Contact Us', 'Contact Us'),
(3212, 'bangla', 'Contact Us', 'Contact Us'),
(3213, 'english', 'Social Link', 'Social Link'),
(3214, 'bangla', 'Social Link', 'Social Link'),
(3215, 'english', 'Admin Profile', 'Admin Profile'),
(3216, 'bangla', 'Admin Profile', 'Admin Profile'),
(3217, 'english', 'Showing', 'Showing'),
(3218, 'bangla', 'Showing', 'Showing'),
(3219, 'english', 'data', 'data'),
(3220, 'bangla', 'data', 'data'),
(3221, 'english', 'Excel upload', 'Excel upload'),
(3222, 'bangla', 'Excel upload', 'Excel upload'),
(3223, 'english', 'Teacher Profile', 'Teacher Profile'),
(3224, 'bangla', 'Teacher Profile', 'Teacher Profile'),
(3225, 'english', 'Accountant Profile', 'Accountant Profile'),
(3226, 'bangla', 'Accountant Profile', 'Accountant Profile'),
(3227, 'english', 'librarian Profile', 'librarian Profile'),
(3228, 'bangla', 'librarian Profile', 'librarian Profile'),
(3229, 'english', '', ''),
(3230, 'bangla', '', ''),
(3231, 'english', 'Student Profile', 'Student Profile'),
(3232, 'bangla', 'Student Profile', 'Student Profile'),
(3233, 'english', 'Email receipt title', 'Email receipt title'),
(3234, 'bangla', 'Email receipt title', 'Email receipt title'),
(3235, 'english', 'Social Link 1', 'Social Link 1'),
(3236, 'bangla', 'Social Link 1', 'Social Link 1'),
(3237, 'english', 'Social Link 2', 'Social Link 2'),
(3238, 'bangla', 'Social Link 2', 'Social Link 2'),
(3239, 'english', 'Social Link 3', 'Social Link 3'),
(3240, 'bangla', 'Social Link 3', 'Social Link 3'),
(3241, 'english', 'Email template Logo', 'Email template Logo'),
(3242, 'bangla', 'Email template Logo', 'Email template Logo'),
(3243, 'english', 'Update routine', 'Update routine'),
(3244, 'bangla', 'Update routine', 'Update routine'),
(3245, 'english', 'Class & Section', 'Class & Section'),
(3246, 'bangla', 'Class & Section', 'Class & Section'),
(3247, 'english', 'Stripe', 'Stripe'),
(3248, 'bangla', 'Stripe', 'Stripe'),
(3249, 'english', 'Flutterwave', 'Flutterwave'),
(3250, 'bangla', 'Flutterwave', 'Flutterwave'),
(3251, 'english', 'Paystack', 'Paystack'),
(3252, 'bangla', 'Paystack', 'Paystack'),
(3253, 'english', 'Expense category name', 'Expense category name'),
(3254, 'bangla', 'Expense category name', 'Expense category name'),
(3255, 'english', 'Save category', 'Save category'),
(3256, 'bangla', 'Save category', 'Save category'),
(3257, 'english', 'Number of copy', 'Number of copy'),
(3258, 'bangla', 'Number of copy', 'Number of copy'),
(3259, 'english', 'Update book info', 'Update book info'),
(3260, 'bangla', 'Update book info', 'Update book info'),
(3261, 'english', 'Parent Profile', 'Parent Profile'),
(3262, 'bangla', 'Parent Profile', 'Parent Profile'),
(3263, 'english', 'Returned', 'Returned'),
(3264, 'bangla', 'Returned', 'Returned'),
(3265, 'english', 'Addon updated successfully', 'Addon updated successfully'),
(3266, 'bangla', 'Addon updated successfully', 'Addon updated successfully'),
(3267, 'english', 'Bundle name', 'Bundle name'),
(3268, 'bangla', 'Bundle name', 'Bundle name'),
(3269, 'english', 'Version', 'Version'),
(3270, 'bangla', 'Version', 'Version'),
(3271, 'english', 'Purchase code verification failed', 'Purchase code verification failed'),
(3272, 'bangla', 'Purchase code verification failed', 'Purchase code verification failed'),
(3273, 'english', 'Addon installed successfully', 'Addon installed successfully'),
(3274, 'bangla', 'Addon installed successfully', 'Addon installed successfully'),
(3275, 'english', 'Transport', 'Transport'),
(3276, 'bangla', 'Transport', 'Transport'),
(3277, 'english', 'Driver', 'Driver'),
(3278, 'bangla', 'Driver', 'Driver'),
(3279, 'english', 'Vehicle', 'Vehicle'),
(3280, 'bangla', 'Vehicle', 'Vehicle'),
(3281, 'english', 'Assign student', 'Assign student'),
(3282, 'bangla', 'Assign student', 'Assign student'),
(3283, 'english', 'Alumni', 'Alumni'),
(3284, 'bangla', 'Alumni', 'Alumni'),
(3285, 'english', 'Manage Alumni', 'Manage Alumni'),
(3286, 'bangla', 'Manage Alumni', 'Manage Alumni'),
(3287, 'english', 'Gallery', 'Gallery'),
(3288, 'bangla', 'Gallery', 'Gallery'),
(3289, 'english', 'Sms Center', 'Sms Center'),
(3290, 'bangla', 'Sms Center', 'Sms Center'),
(3291, 'english', 'Sms Settings', 'Sms Settings'),
(3292, 'bangla', 'Sms Settings', 'Sms Settings'),
(3293, 'english', 'Sms sender', 'Sms sender'),
(3294, 'bangla', 'Sms sender', 'Sms sender'),
(3295, 'english', 'Create Driver', 'Create Driver'),
(3296, 'bangla', 'Create Driver', 'Create Driver'),
(3297, 'english', 'Edit Driver', 'Edit Driver'),
(3298, 'bangla', 'Edit Driver', 'Edit Driver'),
(3299, 'english', 'Create Vehicle', 'Create Vehicle'),
(3300, 'bangla', 'Create Vehicle', 'Create Vehicle'),
(3301, 'english', 'Vehicle Number', 'Vehicle Number'),
(3302, 'bangla', 'Vehicle Number', 'Vehicle Number'),
(3303, 'english', 'Vehicle Model', 'Vehicle Model'),
(3304, 'bangla', 'Vehicle Model', 'Vehicle Model'),
(3305, 'english', 'Chassis Number', 'Chassis Number'),
(3306, 'bangla', 'Chassis Number', 'Chassis Number'),
(3307, 'english', 'Seat Capacity', 'Seat Capacity'),
(3308, 'bangla', 'Seat Capacity', 'Seat Capacity'),
(3309, 'english', 'Assign driver', 'Assign driver'),
(3310, 'bangla', 'Assign driver', 'Assign driver'),
(3311, 'english', 'Select a driver', 'Select a driver'),
(3312, 'bangla', 'Select a driver', 'Select a driver'),
(3313, 'english', 'Route', 'Route'),
(3314, 'bangla', 'Route', 'Route'),
(3315, 'english', 'Made Year', 'Made Year'),
(3316, 'bangla', 'Made Year', 'Made Year'),
(3317, 'english', 'Vehicle Info', 'Vehicle Info'),
(3318, 'bangla', 'Vehicle Info', 'Vehicle Info'),
(3319, 'english', 'Driver Info', 'Driver Info'),
(3320, 'bangla', 'Driver Info', 'Driver Info'),
(3321, 'english', 'Capacity', 'Capacity'),
(3322, 'bangla', 'Capacity', 'Capacity'),
(3323, 'english', 'Vh No: ', 'Vh No: '),
(3324, 'bangla', 'Vh No: ', 'Vh No: '),
(3325, 'english', 'Ch No: ', 'Ch No: '),
(3326, 'bangla', 'Ch No: ', 'Ch No: '),
(3327, 'english', 'Name: ', 'Name: '),
(3328, 'bangla', 'Name: ', 'Name: '),
(3329, 'english', 'Phone: ', 'Phone: '),
(3330, 'bangla', 'Phone: ', 'Phone: '),
(3331, 'english', 'Edit Vehicle', 'Edit Vehicle'),
(3332, 'bangla', 'Edit Vehicle', 'Edit Vehicle'),
(3333, 'english', 'Individual', 'Individual'),
(3334, 'bangla', 'Individual', 'Individual'),
(3335, 'english', 'Assign by class', 'Assign by class'),
(3336, 'bangla', 'Assign by class', 'Assign by class'),
(3337, 'english', 'By Class', 'By Class'),
(3338, 'bangla', 'By Class', 'By Class'),
(3339, 'english', 'Category', 'Category'),
(3340, 'bangla', 'Category', 'Category'),
(3341, 'english', 'First select category', 'First select category'),
(3342, 'bangla', 'First select category', 'First select category'),
(3343, 'english', 'Selecct vehicle', 'Selecct vehicle'),
(3344, 'bangla', 'Selecct vehicle', 'Selecct vehicle'),
(3345, 'english', 'Select a vehicle', 'Select a vehicle'),
(3346, 'bangla', 'Select a vehicle', 'Select a vehicle'),
(3347, 'english', 'Selecct class', 'Selecct class'),
(3348, 'bangla', 'Selecct class', 'Selecct class'),
(3349, 'english', 'First select class', 'First select class'),
(3350, 'bangla', 'First select class', 'First select class'),
(3351, 'english', 'Assign', 'Assign'),
(3352, 'bangla', 'Assign', 'Assign'),
(3353, 'english', 'Driver Name', 'Driver Name'),
(3354, 'bangla', 'Driver Name', 'Driver Name'),
(3355, 'english', 'Remove', 'Remove'),
(3356, 'bangla', 'Remove', 'Remove'),
(3357, 'english', 'Selecct a vehicle', 'Selecct a vehicle'),
(3358, 'bangla', 'Selecct a vehicle', 'Selecct a vehicle'),
(3359, 'english', 'Create Alumni ', 'Create Alumni '),
(3360, 'bangla', 'Create Alumni ', 'Create Alumni '),
(3361, 'english', 'Add Alumni', 'Add Alumni'),
(3362, 'bangla', 'Add Alumni', 'Add Alumni'),
(3363, 'english', 'Edit Alumni', 'Edit Alumni'),
(3364, 'bangla', 'Edit Alumni', 'Edit Alumni'),
(3365, 'english', 'Alumni Events', 'Alumni Events'),
(3366, 'bangla', 'Alumni Events', 'Alumni Events'),
(3367, 'english', 'Add new event ', 'Add new event '),
(3368, 'bangla', 'Add new event ', 'Add new event '),
(3369, 'english', 'Upcoming Event', 'Upcoming Event'),
(3370, 'bangla', 'Upcoming Event', 'Upcoming Event'),
(3371, 'english', 'Archive Events', 'Archive Events'),
(3372, 'bangla', 'Archive Events', 'Archive Events'),
(3373, 'english', 'No event ', 'No event '),
(3374, 'bangla', 'No event ', 'No event '),
(3375, 'english', 'No  event ', 'No  event '),
(3376, 'bangla', 'No  event ', 'No  event '),
(3377, 'english', 'Event starting date', 'Event starting date'),
(3378, 'bangla', 'Event starting date', 'Event starting date'),
(3379, 'english', 'Event ending date', 'Event ending date'),
(3380, 'bangla', 'Event ending date', 'Event ending date'),
(3381, 'english', 'Visibility on website', 'Visibility on website'),
(3382, 'bangla', 'Visibility on website', 'Visibility on website'),
(3383, 'english', 'Upload event photo', 'Upload event photo'),
(3384, 'bangla', 'Upload event photo', 'Upload event photo'),
(3385, 'english', 'Event photo', 'Event photo'),
(3386, 'bangla', 'Event photo', 'Event photo'),
(3387, 'english', 'Visible', 'Visible'),
(3388, 'bangla', 'Visible', 'Visible'),
(3389, 'english', 'Alumni Gallery', 'Alumni Gallery'),
(3390, 'bangla', 'Alumni Gallery', 'Alumni Gallery'),
(3391, 'english', 'Add new Gallery', 'Add new Gallery'),
(3392, 'bangla', 'Add new Gallery', 'Add new Gallery'),
(3393, 'english', 'No Gallery found', 'No Gallery found'),
(3394, 'bangla', 'No Gallery found', 'No Gallery found'),
(3395, 'english', 'Gallery title', 'Gallery title'),
(3396, 'bangla', 'Gallery title', 'Gallery title'),
(3397, 'english', 'No need to show', 'No need to show'),
(3398, 'bangla', 'No need to show', 'No need to show'),
(3399, 'english', 'Save Gellary', 'Save Gellary'),
(3400, 'bangla', 'Save Gellary', 'Save Gellary'),
(3401, 'english', 'Add Image', 'Add Image'),
(3402, 'bangla', 'Add Image', 'Add Image'),
(3403, 'english', 'Add Photo', 'Add Photo'),
(3404, 'bangla', 'Add Photo', 'Add Photo'),
(3405, 'english', 'No Photos found', 'No Photos found'),
(3406, 'bangla', 'No Photos found', 'No Photos found'),
(3407, 'english', 'Upload gallery photo', 'Upload gallery photo'),
(3408, 'bangla', 'Upload gallery photo', 'Upload gallery photo'),
(3409, 'english', 'Save Image ', 'Save Image '),
(3410, 'bangla', 'Save Image ', 'Save Image '),
(3411, 'english', 'Active a sms getway', 'Active a sms getway'),
(3412, 'bangla', 'Active a sms getway', 'Active a sms getway'),
(3413, 'english', 'Twilio', 'Twilio'),
(3414, 'bangla', 'Twilio', 'Twilio'),
(3415, 'english', 'MSG91', 'MSG91'),
(3416, 'bangla', 'MSG91', 'MSG91'),
(3417, 'english', 'None', 'None'),
(3418, 'bangla', 'None', 'None'),
(3419, 'english', 'SID', 'SID'),
(3420, 'bangla', 'SID', 'SID'),
(3421, 'english', 'token', 'token'),
(3422, 'bangla', 'token', 'token'),
(3423, 'english', 'sender phone number', 'sender phone number'),
(3424, 'bangla', 'sender phone number', 'sender phone number'),
(3425, 'english', 'auth key', 'auth key'),
(3426, 'bangla', 'auth key', 'auth key'),
(3427, 'english', 'sender id', 'sender id'),
(3428, 'bangla', 'sender id', 'sender id'),
(3429, 'english', 'country code', 'country code'),
(3430, 'bangla', 'country code', 'country code'),
(3431, 'english', 'Choose sms receiver', 'Choose sms receiver'),
(3432, 'bangla', 'Choose sms receiver', 'Choose sms receiver'),
(3433, 'english', 'Select receiver', 'Select receiver'),
(3434, 'bangla', 'Select receiver', 'Select receiver'),
(3435, 'english', 'Show receiver', 'Show receiver'),
(3436, 'bangla', 'Show receiver', 'Show receiver'),
(3437, 'english', 'List of receivers', 'List of receivers'),
(3438, 'bangla', 'List of receivers', 'List of receivers'),
(3439, 'english', 'message', 'message'),
(3440, 'bangla', 'message', 'message'),
(3441, 'english', 'Message to send', 'Message to send'),
(3442, 'bangla', 'Message to send', 'Message to send'),
(3443, 'english', 'Write down your message within 160 characters', 'Write down your message within 160 characters'),
(3444, 'bangla', 'Write down your message within 160 characters', 'Write down your message within 160 characters'),
(3445, 'english', 'Send sms', 'Send sms'),
(3446, 'bangla', 'Send sms', 'Send sms'),
(3447, 'english', 'Before sending sms to the receivers please make sure that you have set up sms settings perfectly.', 'Before sending sms to the receivers please make sure that you have set up sms settings perfectly.'),
(3448, 'bangla', 'Before sending sms to the receivers please make sure that you have set up sms settings perfectly.', 'Before sending sms to the receivers please make sure that you have set up sms settings perfectly.'),
(3449, 'english', 'You can set sms settings', 'You can set sms settings'),
(3450, 'bangla', 'You can set sms settings', 'You can set sms settings'),
(3451, 'english', 'here', 'here'),
(3452, 'bangla', 'here', 'here'),
(3453, 'english', 'Currently activated', 'Currently activated'),
(3454, 'bangla', 'Currently activated', 'Currently activated'),
(3455, 'english', 'Please select a receiver !', 'Please select a receiver !'),
(3456, 'bangla', 'Please select a receiver !', 'Please select a receiver !'),
(3457, 'english', 'Please select class and section !', 'Please select class and section !'),
(3458, 'bangla', 'Please select class and section !', 'Please select class and section !'),
(3459, 'english', 'Receiver can not be empty', 'Receiver can not be empty'),
(3460, 'bangla', 'Receiver can not be empty', 'Receiver can not be empty'),
(3461, 'english', 'Event', 'Event'),
(3462, 'bangla', 'Event', 'Event'),
(3463, 'english', 'Trips', 'Trips'),
(3464, 'bangla', 'Trips', 'Trips'),
(3465, 'english', 'Total Trips', 'Total Trips'),
(3466, 'bangla', 'Total Trips', 'Total Trips'),
(3467, 'english', 'Vehicles', 'Vehicles'),
(3468, 'bangla', 'Vehicles', 'Vehicles'),
(3469, 'english', 'Total vehicles', 'Total vehicles'),
(3470, 'bangla', 'Total vehicles', 'Total vehicles'),
(3471, 'english', 'Total students', 'Total students'),
(3472, 'bangla', 'Total students', 'Total students'),
(3473, 'english', 'Bcak Office', 'Bcak Office'),
(3474, 'bangla', 'Bcak Office', 'Bcak Office'),
(3475, 'english', 'Assigned student', 'Assigned student'),
(3476, 'bangla', 'Assigned student', 'Assigned student'),
(3477, 'english', 'Search', 'Search'),
(3478, 'bangla', 'Search', 'Search'),
(3479, 'english', 'Vehicle No', 'Vehicle No'),
(3480, 'bangla', 'Vehicle No', 'Vehicle No'),
(3481, 'english', 'Start Trip', 'Start Trip'),
(3482, 'bangla', 'Start Trip', 'Start Trip'),
(3483, 'english', 'End Trip', 'End Trip'),
(3484, 'bangla', 'End Trip', 'End Trip'),
(3485, 'english', 'Check all', 'Check all'),
(3486, 'bangla', 'Check all', 'Check all'),
(3487, 'english', 'Live class ', 'Live class '),
(3488, 'bangla', 'Live class ', 'Live class '),
(3489, 'english', 'Add new ', 'Add new '),
(3490, 'bangla', 'Add new ', 'Add new '),
(3491, 'english', 'Live Classes ', 'Live Classes '),
(3492, 'bangla', 'Live Classes ', 'Live Classes '),
(3493, 'english', 'Add New Live Class', 'Add New Live Class'),
(3494, 'bangla', 'Add New Live Class', 'Add New Live Class'),
(3495, 'english', 'Enable Waiting', 'Enable Waiting'),
(3496, 'bangla', 'Enable Waiting', 'Enable Waiting'),
(3497, 'english', 'Upload attachment', 'Upload attachment'),
(3498, 'bangla', 'Upload attachment', 'Upload attachment'),
(3499, 'english', 'Live Class Url', 'Live Class Url'),
(3500, 'bangla', 'Live Class Url', 'Live Class Url'),
(3501, 'english', 'Topic', 'Topic'),
(3502, 'bangla', 'Topic', 'Topic'),
(3503, 'english', 'Create meeting', 'Create meeting'),
(3504, 'bangla', 'Create meeting', 'Create meeting'),
(3505, 'english', 'Your Live classes', 'Your Live classes'),
(3506, 'bangla', 'Your Live classes', 'Your Live classes'),
(3507, 'english', 'Upcoming ', 'Upcoming '),
(3508, 'bangla', 'Upcoming ', 'Upcoming '),
(3509, 'english', 'Schedule', 'Schedule'),
(3510, 'bangla', 'Schedule', 'Schedule'),
(3511, 'english', 'Date : ', 'Date : '),
(3512, 'bangla', 'Date : ', 'Date : '),
(3513, 'english', 'Time : ', 'Time : '),
(3514, 'bangla', 'Time : ', 'Time : '),
(3515, 'english', 'Start class', 'Start class'),
(3516, 'bangla', 'Start class', 'Start class'),
(3517, 'english', 'Edit Meeting', 'Edit Meeting'),
(3518, 'bangla', 'Edit Meeting', 'Edit Meeting'),
(3519, 'english', 'Join', 'Join'),
(3520, 'bangla', 'Join', 'Join'),
(3521, 'english', 'Online Courses', 'Online Courses'),
(3522, 'bangla', 'Online Courses', 'Online Courses'),
(3523, 'english', 'Inventory', 'Inventory'),
(3524, 'bangla', 'Inventory', 'Inventory'),
(3525, 'english', 'Inventory Manager', 'Inventory Manager'),
(3526, 'bangla', 'Inventory Manager', 'Inventory Manager'),
(3527, 'english', 'Inventory Category', 'Inventory Category'),
(3528, 'bangla', 'Inventory Category', 'Inventory Category'),
(3529, 'english', 'Buy & Sell Report', 'Buy & Sell Report'),
(3530, 'bangla', 'Buy & Sell Report', 'Buy & Sell Report'),
(3531, 'english', 'Create Inventory', 'Create Inventory'),
(3532, 'bangla', 'Create Inventory', 'Create Inventory'),
(3533, 'english', 'Add Inventory', 'Add Inventory'),
(3534, 'bangla', 'Add Inventory', 'Add Inventory'),
(3535, 'english', 'Create Inventory Category', 'Create Inventory Category'),
(3536, 'bangla', 'Create Inventory Category', 'Create Inventory Category'),
(3537, 'english', 'Add inventory category', 'Add inventory category'),
(3538, 'bangla', 'Add inventory category', 'Add inventory category'),
(3539, 'english', 'Buy Report', 'Buy Report'),
(3540, 'bangla', 'Buy Report', 'Buy Report'),
(3541, 'english', 'Sell Report', 'Sell Report'),
(3542, 'bangla', 'Sell Report', 'Sell Report'),
(3543, 'english', 'All Courses', 'All Courses'),
(3544, 'bangla', 'All Courses', 'All Courses'),
(3545, 'english', 'Create new course', 'Create new course'),
(3546, 'bangla', 'Create new course', 'Create new course'),
(3547, 'english', 'Active Courses', 'Active Courses'),
(3548, 'bangla', 'Active Courses', 'Active Courses'),
(3549, 'english', 'Inactive Courses', 'Inactive Courses'),
(3550, 'bangla', 'Inactive Courses', 'Inactive Courses'),
(3551, 'english', 'Add new course', 'Add new course'),
(3552, 'bangla', 'Add new course', 'Add new course'),
(3553, 'english', 'Online Course', 'Online Course'),
(3554, 'bangla', 'Online Course', 'Online Course'),
(3555, 'english', 'COURSE ADDING FORM', 'COURSE ADDING FORM'),
(3556, 'bangla', 'COURSE ADDING FORM', 'COURSE ADDING FORM'),
(3557, 'english', 'Go Back', 'Go Back'),
(3558, 'bangla', 'Go Back', 'Go Back'),
(3559, 'english', 'Basic', 'Basic'),
(3560, 'bangla', 'Basic', 'Basic'),
(3561, 'english', 'Outcomes', 'Outcomes'),
(3562, 'bangla', 'Outcomes', 'Outcomes'),
(3563, 'english', 'Media', 'Media'),
(3564, 'bangla', 'Media', 'Media'),
(3565, 'english', 'Finish', 'Finish'),
(3566, 'bangla', 'Finish', 'Finish'),
(3567, 'english', 'Course title', 'Course title'),
(3568, 'bangla', 'Course title', 'Course title'),
(3569, 'english', 'Instructor', 'Instructor'),
(3570, 'bangla', 'Instructor', 'Instructor'),
(3571, 'english', 'Select a teacher', 'Select a teacher'),
(3572, 'bangla', 'Select a teacher', 'Select a teacher'),
(3573, 'english', 'Course overview provider', 'Course overview provider'),
(3574, 'bangla', 'Course overview provider', 'Course overview provider'),
(3575, 'english', 'Youtube', 'Youtube'),
(3576, 'bangla', 'Youtube', 'Youtube'),
(3577, 'english', 'Vimeo', 'Vimeo'),
(3578, 'bangla', 'Vimeo', 'Vimeo'),
(3579, 'english', 'HTML5', 'HTML5'),
(3580, 'bangla', 'HTML5', 'HTML5'),
(3581, 'english', 'Course overview url', 'Course overview url'),
(3582, 'bangla', 'Course overview url', 'Course overview url'),
(3583, 'english', 'Course thumbnail', 'Course thumbnail'),
(3584, 'bangla', 'Course thumbnail', 'Course thumbnail'),
(3585, 'english', 'Thank you', 'Thank you'),
(3586, 'bangla', 'Thank you', 'Thank you'),
(3587, 'english', 'You are just one click away', 'You are just one click away'),
(3588, 'bangla', 'You are just one click away', 'You are just one click away'),
(3589, 'english', 'Administrator', 'Administrator'),
(3590, 'bangla', 'Administrator', 'Administrator'),
(3591, 'english', 'Lesson and Section', 'Lesson and Section'),
(3592, 'bangla', 'Lesson and Section', 'Lesson and Section'),
(3593, 'english', 'Total section', 'Total section'),
(3594, 'bangla', 'Total section', 'Total section'),
(3595, 'english', 'Total lesson', 'Total lesson'),
(3596, 'bangla', 'Total lesson', 'Total lesson'),
(3597, 'english', 'Edit course', 'Edit course'),
(3598, 'bangla', 'Edit course', 'Edit course'),
(3599, 'english', 'COURSE EDITING FORM', 'COURSE EDITING FORM'),
(3600, 'bangla', 'COURSE EDITING FORM', 'COURSE EDITING FORM'),
(3601, 'english', 'Curriculum', 'Curriculum'),
(3602, 'bangla', 'Curriculum', 'Curriculum'),
(3603, 'english', 'Add new section', 'Add new section'),
(3604, 'bangla', 'Add new section', 'Add new section'),
(3605, 'english', 'Add section', 'Add section'),
(3606, 'bangla', 'Add section', 'Add section'),
(3607, 'english', 'Add new lesson', 'Add new lesson'),
(3608, 'bangla', 'Add new lesson', 'Add new lesson'),
(3609, 'english', 'Add lesson', 'Add lesson'),
(3610, 'bangla', 'Add lesson', 'Add lesson'),
(3611, 'english', 'Sort Section', 'Sort Section'),
(3612, 'bangla', 'Sort Section', 'Sort Section'),
(3613, 'english', 'Sort sections', 'Sort sections'),
(3614, 'bangla', 'Sort sections', 'Sort sections'),
(3615, 'english', 'Provide a section name', 'Provide a section name'),
(3616, 'bangla', 'Provide a section name', 'Provide a section name'),
(3617, 'english', 'Sort Lesson ', 'Sort Lesson '),
(3618, 'bangla', 'Sort Lesson ', 'Sort Lesson '),
(3619, 'english', 'Update Section ', 'Update Section '),
(3620, 'bangla', 'Update Section ', 'Update Section '),
(3621, 'english', 'Delete section', 'Delete section'),
(3622, 'bangla', 'Delete section', 'Delete section'),
(3623, 'english', 'Lesson type', 'Lesson type'),
(3624, 'bangla', 'Lesson type', 'Lesson type'),
(3625, 'english', 'Select type of lesson', 'Select type of lesson'),
(3626, 'bangla', 'Select type of lesson', 'Select type of lesson'),
(3627, 'english', 'Video', 'Video'),
(3628, 'bangla', 'Video', 'Video'),
(3629, 'english', 'Text file', 'Text file'),
(3630, 'bangla', 'Text file', 'Text file'),
(3631, 'english', 'Pdf file', 'Pdf file'),
(3632, 'bangla', 'Pdf file', 'Pdf file'),
(3633, 'english', 'Document file', 'Document file'),
(3634, 'bangla', 'Document file', 'Document file'),
(3635, 'english', 'Image file', 'Image file'),
(3636, 'bangla', 'Image file', 'Image file'),
(3637, 'english', 'Lesson provider', 'Lesson provider'),
(3638, 'bangla', 'Lesson provider', 'Lesson provider'),
(3639, 'english', 'For web application', 'For web application'),
(3640, 'bangla', 'For web application', 'For web application'),
(3641, 'english', 'Select lesson provider', 'Select lesson provider'),
(3642, 'bangla', 'Select lesson provider', 'Select lesson provider'),
(3643, 'english', 'Video url', 'Video url'),
(3644, 'bangla', 'Video url', 'Video url'),
(3645, 'english', 'This video will be shown on web application', 'This video will be shown on web application'),
(3646, 'bangla', 'This video will be shown on web application', 'This video will be shown on web application'),
(3647, 'english', 'Analyzing the url', 'Analyzing the url'),
(3648, 'bangla', 'Analyzing the url', 'Analyzing the url'),
(3649, 'english', 'Invalid url', 'Invalid url'),
(3650, 'bangla', 'Invalid url', 'Invalid url'),
(3651, 'english', 'Your video source has to be either youtube or vimeo', 'Your video source has to be either youtube or vimeo'),
(3652, 'bangla', 'Your video source has to be either youtube or vimeo', 'Your video source has to be either youtube or vimeo'),
(3653, 'english', 'Duration', 'Duration'),
(3654, 'bangla', 'Duration', 'Duration'),
(3655, 'english', 'Attachment', 'Attachment'),
(3656, 'bangla', 'Attachment', 'Attachment'),
(3657, 'english', 'Summary', 'Summary'),
(3658, 'bangla', 'Summary', 'Summary'),
(3659, 'english', 'Lesson', 'Lesson'),
(3660, 'bangla', 'Lesson', 'Lesson'),
(3661, 'english', 'Edit Lesson ', 'Edit Lesson '),
(3662, 'bangla', 'Edit Lesson ', 'Edit Lesson '),
(3663, 'english', 'List of Sections', 'List of Sections'),
(3664, 'bangla', 'List of Sections', 'List of Sections'),
(3665, 'english', 'Update Sorting', 'Update Sorting'),
(3666, 'bangla', 'Update Sorting', 'Update Sorting'),
(3667, 'english', 'Sections have been Sorted', 'Sections have been Sorted'),
(3668, 'bangla', 'Sections have been Sorted', 'Sections have been Sorted'),
(3669, 'english', 'Continue lesson ', 'Continue lesson '),
(3670, 'bangla', 'Continue lesson ', 'Continue lesson '),
(3671, 'english', 'Back to Course', 'Back to Course'),
(3672, 'bangla', 'Back to Course', 'Back to Course'),
(3673, 'english', 'Note:', 'Note:'),
(3674, 'bangla', 'Note:', 'Note:'),
(3675, 'english', 'No Added Summary ', 'No Added Summary '),
(3676, 'bangla', 'No Added Summary ', 'No Added Summary '),
(3677, 'english', 'Course content', 'Course content'),
(3678, 'bangla', 'Course content', 'Course content'),
(3679, 'english', 'Product Name', 'Product Name'),
(3680, 'bangla', 'Product Name', 'Product Name'),
(3681, 'english', 'Select an Inventory', 'Select an Inventory'),
(3682, 'bangla', 'Select an Inventory', 'Select an Inventory'),
(3683, 'english', 'Quantity', 'Quantity'),
(3684, 'bangla', 'Quantity', 'Quantity'),
(3685, 'english', 'Unit Price', 'Unit Price'),
(3686, 'bangla', 'Unit Price', 'Unit Price'),
(3687, 'english', 'Total Price', 'Total Price'),
(3688, 'bangla', 'Total Price', 'Total Price'),
(3689, 'english', 'Sell', 'Sell'),
(3690, 'bangla', 'Sell', 'Sell'),
(3691, 'english', 'Buy Invoice', 'Buy Invoice'),
(3692, 'bangla', 'Buy Invoice', 'Buy Invoice'),
(3693, 'english', 'Nayda Bonner', 'Nayda Bonner'),
(3694, 'bangla', 'Nayda Bonner', 'Nayda Bonner'),
(3695, 'english', 'Odessa Holman', 'Odessa Holman'),
(3696, 'bangla', 'Odessa Holman', 'Odessa Holman'),
(3697, 'english', '08-23-23', '08-23-23'),
(3698, 'bangla', '08-23-23', '08-23-23'),
(3699, 'english', 'Total', 'Total'),
(3700, 'bangla', 'Total', 'Total'),
(3701, 'english', '1', '1'),
(3702, 'bangla', '1', '1'),
(3703, 'english', '811', '811'),
(3704, 'bangla', '811', '811'),
(3705, 'english', '299', '299'),
(3706, 'bangla', '299', '299'),
(3707, 'english', '242489', '242489'),
(3708, 'bangla', '242489', '242489'),
(3709, 'english', 'Regan Benson', 'Regan Benson'),
(3710, 'bangla', 'Regan Benson', 'Regan Benson'),
(3711, 'english', '2', '2'),
(3712, 'bangla', '2', '2'),
(3713, 'english', '435', '435'),
(3714, 'bangla', '435', '435'),
(3715, 'english', '120', '120'),
(3716, 'bangla', '120', '120'),
(3717, 'english', '52200', '52200'),
(3718, 'bangla', '52200', '52200'),
(3719, 'english', 'Human Resource', 'Human Resource'),
(3720, 'bangla', 'Human Resource', 'Human Resource'),
(3721, 'english', 'User Roles', 'User Roles'),
(3722, 'bangla', 'User Roles', 'User Roles'),
(3723, 'english', 'User List', 'User List'),
(3724, 'bangla', 'User List', 'User List'),
(3725, 'english', 'Take Attendence', 'Take Attendence'),
(3726, 'bangla', 'Take Attendence', 'Take Attendence'),
(3727, 'english', 'Leave', 'Leave'),
(3728, 'bangla', 'Leave', 'Leave'),
(3729, 'english', 'Payroll', 'Payroll'),
(3730, 'bangla', 'Payroll', 'Payroll'),
(3731, 'english', 'Pay by Paypal ', 'Pay by Paypal '),
(3732, 'bangla', 'Pay by Paypal ', 'Pay by Paypal '),
(3733, 'english', 'Pay by Stripe ', 'Pay by Stripe '),
(3734, 'bangla', 'Pay by Stripe ', 'Pay by Stripe '),
(3735, 'english', 'Pay by Paytm ', 'Pay by Paytm '),
(3736, 'bangla', 'Pay by Paytm ', 'Pay by Paytm '),
(3737, 'english', 'Pay by Flutterwave ', 'Pay by Flutterwave '),
(3738, 'bangla', 'Pay by Flutterwave ', 'Pay by Flutterwave '),
(3739, 'english', 'Create Roles', 'Create Roles'),
(3740, 'bangla', 'Create Roles', 'Create Roles'),
(3741, 'english', 'Role', 'Role'),
(3742, 'bangla', 'Role', 'Role'),
(3743, 'english', 'Permanent', 'Permanent'),
(3744, 'bangla', 'Permanent', 'Permanent'),
(3745, 'english', 'Not Editable', 'Not Editable'),
(3746, 'bangla', 'Not Editable', 'Not Editable'),
(3747, 'english', 'Role name', 'Role name'),
(3748, 'bangla', 'Role name', 'Role name'),
(3749, 'english', 'Create role', 'Create role'),
(3750, 'bangla', 'Create role', 'Create role'),
(3751, 'english', 'Peon', 'Peon'),
(3752, 'bangla', 'Peon', 'Peon'),
(3753, 'english', 'Update role', 'Update role'),
(3754, 'bangla', 'Update role', 'Update role'),
(3755, 'english', 'User Lists', 'User Lists'),
(3756, 'bangla', 'User Lists', 'User Lists'),
(3757, 'english', 'Import Users', 'Import Users'),
(3758, 'bangla', 'Import Users', 'Import Users'),
(3759, 'english', 'Create New User', 'Create New User'),
(3760, 'bangla', 'Create New User', 'Create New User'),
(3761, 'english', 'Select a role', 'Select a role'),
(3762, 'bangla', 'Select a role', 'Select a role'),
(3763, 'english', 'Please select a role', 'Please select a role'),
(3764, 'bangla', 'Please select a role', 'Please select a role'),
(3765, 'english', 'Import user', 'Import user'),
(3766, 'bangla', 'Import user', 'Import user'),
(3767, 'english', 'Lila Vazquez', 'Lila Vazquez'),
(3768, 'bangla', 'Lila Vazquez', 'Lila Vazquez'),
(3769, 'english', 'Admin2@gmail.com', 'Admin2@gmail.com'),
(3770, 'bangla', 'Admin2@gmail.com', 'Admin2@gmail.com'),
(3771, 'english', 'Similique occaecat q', 'Similique occaecat q'),
(3772, 'bangla', 'Similique occaecat q', 'Similique occaecat q'),
(3773, 'english', 'Update user', 'Update user'),
(3774, 'bangla', 'Update user', 'Update user'),
(3775, 'english', 'Attendence', 'Attendence'),
(3776, 'bangla', 'Attendence', 'Attendence'),
(3777, 'english', 'Show user list', 'Show user list'),
(3778, 'bangla', 'Show user list', 'Show user list'),
(3779, 'english', 'please_select_in_all_fields !', 'please_select_in_all_fields !'),
(3780, 'bangla', 'please_select_in_all_fields !', 'please_select_in_all_fields !'),
(3781, 'english', 'Leave Lists', 'Leave Lists'),
(3782, 'bangla', 'Leave Lists', 'Leave Lists'),
(3783, 'english', 'Leave List', 'Leave List'),
(3784, 'bangla', 'Leave List', 'Leave List'),
(3785, 'english', 'Crete new leave', 'Crete new leave'),
(3786, 'bangla', 'Crete new leave', 'Crete new leave'),
(3787, 'english', 'Create New Leave', 'Create New Leave'),
(3788, 'bangla', 'Create New Leave', 'Create New Leave'),
(3789, 'english', ' No data found', ' No data found'),
(3790, 'bangla', ' No data found', ' No data found'),
(3791, 'english', 'Roles', 'Roles'),
(3792, 'bangla', 'Roles', 'Roles'),
(3793, 'english', 'User name', 'User name'),
(3794, 'bangla', 'User name', 'User name'),
(3795, 'english', 'Select a user', 'Select a user'),
(3796, 'bangla', 'Select a user', 'Select a user'),
(3797, 'english', 'Reason', 'Reason'),
(3798, 'bangla', 'Reason', 'Reason'),
(3799, 'english', 'User role', 'User role'),
(3800, 'bangla', 'User role', 'User role'),
(3801, 'english', 'Joining salary', 'Joining salary'),
(3802, 'bangla', 'Joining salary', 'Joining salary'),
(3803, 'english', 'Create user', 'Create user'),
(3804, 'bangla', 'Create user', 'Create user'),
(3805, 'english', 'Rina Avila', 'Rina Avila'),
(3806, 'bangla', 'Rina Avila', 'Rina Avila'),
(3807, 'english', 'Librarian@gmail.com', 'Librarian@gmail.com'),
(3808, 'bangla', 'Librarian@gmail.com', 'Librarian@gmail.com'),
(3809, 'english', 'Iure dolores consequ', 'Iure dolores consequ'),
(3810, 'bangla', 'Iure dolores consequ', 'Iure dolores consequ'),
(3811, 'english', 'Kareem Kidd', 'Kareem Kidd'),
(3812, 'bangla', 'Kareem Kidd', 'Kareem Kidd'),
(3813, 'english', 'Accounted@gmail.com', 'Accounted@gmail.com'),
(3814, 'bangla', 'Accounted@gmail.com', 'Accounted@gmail.com'),
(3815, 'english', 'Voluptatem commodo', 'Voluptatem commodo'),
(3816, 'bangla', 'Voluptatem commodo', 'Voluptatem commodo'),
(3817, 'english', 'Pogulil', 'Pogulil'),
(3818, 'bangla', 'Pogulil', 'Pogulil'),
(3819, 'english', 'Waryhu@mailinator.com', 'Waryhu@mailinator.com'),
(3820, 'bangla', 'Waryhu@mailinator.com', 'Waryhu@mailinator.com'),
(3821, 'english', 'Et placeat eum ea o', 'Et placeat eum ea o'),
(3822, 'bangla', 'Et placeat eum ea o', 'Et placeat eum ea o'),
(3823, 'english', 'Employee', 'Employee');
INSERT INTO `language` (`id`, `name`, `phrase`, `translated`) VALUES
(3824, 'bangla', 'Employee', 'Employee'),
(3825, 'english', 'Payslip list', 'Payslip list'),
(3826, 'bangla', 'Payslip list', 'Payslip list'),
(3827, 'english', 'Create payslip', 'Create payslip'),
(3828, 'bangla', 'Create payslip', 'Create payslip'),
(3829, 'english', 'Select a role first', 'Select a role first'),
(3830, 'bangla', 'Select a role first', 'Select a role first'),
(3831, 'english', 'Deductions', 'Deductions'),
(3832, 'bangla', 'Deductions', 'Deductions'),
(3833, 'english', 'Total allowance', 'Total allowance'),
(3834, 'bangla', 'Total allowance', 'Total allowance'),
(3835, 'english', 'Total deduction', 'Total deduction'),
(3836, 'bangla', 'Total deduction', 'Total deduction'),
(3837, 'english', 'Net salary', 'Net salary'),
(3838, 'bangla', 'Net salary', 'Net salary'),
(3839, 'english', 'select_a_role_first', 'select_a_role_first'),
(3840, 'bangla', 'select_a_role_first', 'select_a_role_first'),
(3841, 'english', 'USER', 'USER'),
(3842, 'bangla', 'USER', 'USER'),
(3843, 'english', 'Payslip details', 'Payslip details'),
(3844, 'bangla', 'Payslip details', 'Payslip details'),
(3845, 'english', 'view payslip details', 'view payslip details'),
(3846, 'bangla', 'view payslip details', 'view payslip details'),
(3847, 'english', 'Teachers List', 'Teachers List'),
(3848, 'bangla', 'Teachers List', 'Teachers List'),
(3849, 'english', 'Accountant List', 'Accountant List'),
(3850, 'bangla', 'Accountant List', 'Accountant List'),
(3851, 'english', 'Librarian List', 'Librarian List'),
(3852, 'bangla', 'Librarian List', 'Librarian List'),
(3853, 'english', 'Parent List', 'Parent List'),
(3854, 'bangla', 'Parent List', 'Parent List'),
(3855, 'english', 'Admin Students', 'Admin Students'),
(3856, 'bangla', 'Admin Students', 'Admin Students'),
(3857, 'english', ' Students List', ' Students List'),
(3858, 'bangla', ' Students List', ' Students List'),
(3859, 'english', 'Driver List', 'Driver List'),
(3860, 'bangla', 'Driver List', 'Driver List'),
(3861, 'english', 'Vehicle List', 'Vehicle List'),
(3862, 'bangla', 'Vehicle List', 'Vehicle List'),
(3863, 'english', 'Corrupti aut amet', 'Corrupti aut amet'),
(3864, 'bangla', 'Corrupti aut amet', 'Corrupti aut amet'),
(3865, 'english', 'Pion', 'Pion'),
(3866, 'bangla', 'Pion', 'Pion'),
(3867, 'english', 'Pion@gmail.com', 'Pion@gmail.com'),
(3868, 'bangla', 'Pion@gmail.com', 'Pion@gmail.com'),
(3869, 'english', 'Sff', 'Sff'),
(3870, 'bangla', 'Sff', 'Sff'),
(3871, 'english', 'Pion2', 'Pion2'),
(3872, 'bangla', 'Pion2', 'Pion2'),
(3873, 'english', 'Pion2@gmail.com', 'Pion2@gmail.com'),
(3874, 'bangla', 'Pion2@gmail.com', 'Pion2@gmail.com'),
(3875, 'english', 'Sf', 'Sf'),
(3876, 'bangla', 'Sf', 'Sf'),
(3877, 'english', 'Brenden Faulkner', 'Brenden Faulkner'),
(3878, 'bangla', 'Brenden Faulkner', 'Brenden Faulkner'),
(3879, 'english', 'Attendence Report', 'Attendence Report'),
(3880, 'bangla', 'Attendence Report', 'Attendence Report'),
(3881, 'english', 'You payment request has been suspended', 'You payment request has been suspended'),
(3882, 'bangla', 'You payment request has been suspended', 'You payment request has been suspended'),
(3883, 'english', 'You payment request has been <span style=\"color:red;\"> suspended </span>', 'You payment request has been <span style=\"color:red;\"> suspended </span>'),
(3884, 'bangla', 'You payment request has been <span style=\"color:red;\"> suspended </span>', 'You payment request has been <span style=\"color:red;\"> suspended </span>'),
(3885, 'english', 'Department List', 'Department List'),
(3886, 'bangla', 'Department List', 'Department List'),
(3887, 'english', '3', '3'),
(3888, 'bangla', '3', '3'),
(3889, 'english', '4', '4'),
(3890, 'bangla', '4', '4'),
(3891, 'english', '5', '5'),
(3892, 'bangla', '5', '5'),
(3893, 'english', '6', '6'),
(3894, 'bangla', '6', '6'),
(3895, 'english', '7', '7'),
(3896, 'bangla', '7', '7'),
(3897, 'english', '9', '9'),
(3898, 'bangla', '9', '9'),
(3899, 'english', '10', '10'),
(3900, 'bangla', '10', '10'),
(3901, 'english', '11', '11'),
(3902, 'bangla', '11', '11'),
(3903, 'english', '12', '12'),
(3904, 'bangla', '12', '12'),
(3905, 'english', '13', '13'),
(3906, 'bangla', '13', '13'),
(3907, 'english', '14', '14'),
(3908, 'bangla', '14', '14'),
(3909, 'english', '15', '15'),
(3910, 'bangla', '15', '15'),
(3911, 'english', '16', '16'),
(3912, 'bangla', '16', '16'),
(3913, 'english', '17', '17'),
(3914, 'bangla', '17', '17'),
(3915, 'english', '18', '18'),
(3916, 'bangla', '18', '18'),
(3917, 'english', '19', '19'),
(3918, 'bangla', '19', '19'),
(3919, 'english', '20', '20'),
(3920, 'bangla', '20', '20'),
(3921, 'english', '21', '21'),
(3922, 'bangla', '21', '21'),
(3923, 'english', '22', '22'),
(3924, 'bangla', '22', '22'),
(3925, 'english', '23', '23'),
(3926, 'bangla', '23', '23'),
(3927, 'english', '24', '24'),
(3928, 'bangla', '24', '24'),
(3929, 'english', '25', '25'),
(3930, 'bangla', '25', '25'),
(3931, 'english', '26', '26'),
(3932, 'bangla', '26', '26'),
(3933, 'english', '27', '27'),
(3934, 'bangla', '27', '27'),
(3935, 'english', '28', '28'),
(3936, 'bangla', '28', '28'),
(3937, 'english', '29', '29'),
(3938, 'bangla', '29', '29'),
(3939, 'english', '30', '30'),
(3940, 'bangla', '30', '30'),
(3941, 'english', 'Alumni List', 'Alumni List'),
(3942, 'bangla', 'Alumni List', 'Alumni List'),
(3943, 'english', 'Book issue list', 'Book issue list'),
(3944, 'bangla', 'Book issue list', 'Book issue list'),
(3945, 'english', 'Event List', 'Event List'),
(3946, 'bangla', 'Event List', 'Event List'),
(3947, 'english', 'Paypal settings', 'Paypal settings'),
(3948, 'bangla', 'Paypal settings', 'Paypal settings'),
(3949, 'english', 'Live', 'Live'),
(3950, 'bangla', 'Live', 'Live'),
(3951, 'english', 'Sandbox', 'Sandbox'),
(3952, 'bangla', 'Sandbox', 'Sandbox'),
(3953, 'english', 'Client ID (Sandbox)', 'Client ID (Sandbox)'),
(3954, 'bangla', 'Client ID (Sandbox)', 'Client ID (Sandbox)'),
(3955, 'english', 'Client Secrect (Sandbox)', 'Client Secrect (Sandbox)'),
(3956, 'bangla', 'Client Secrect (Sandbox)', 'Client Secrect (Sandbox)'),
(3957, 'english', 'Client ID (Live)', 'Client ID (Live)'),
(3958, 'bangla', 'Client ID (Live)', 'Client ID (Live)'),
(3959, 'english', 'Client Secrect (Live)', 'Client Secrect (Live)'),
(3960, 'bangla', 'Client Secrect (Live)', 'Client Secrect (Live)'),
(3961, 'english', 'Update Paypal', 'Update Paypal'),
(3962, 'bangla', 'Update Paypal', 'Update Paypal'),
(3963, 'english', 'Stripe settings', 'Stripe settings'),
(3964, 'bangla', 'Stripe settings', 'Stripe settings'),
(3965, 'english', 'Test', 'Test'),
(3966, 'bangla', 'Test', 'Test'),
(3967, 'english', 'Test Public Key', 'Test Public Key'),
(3968, 'bangla', 'Test Public Key', 'Test Public Key'),
(3969, 'english', 'Test Secrect Key', 'Test Secrect Key'),
(3970, 'bangla', 'Test Secrect Key', 'Test Secrect Key'),
(3971, 'english', 'Live Public Key', 'Live Public Key'),
(3972, 'bangla', 'Live Public Key', 'Live Public Key'),
(3973, 'english', 'Live Secrect Key', 'Live Secrect Key'),
(3974, 'bangla', 'Live Secrect Key', 'Live Secrect Key'),
(3975, 'english', 'Update Stripe ', 'Update Stripe '),
(3976, 'bangla', 'Update Stripe ', 'Update Stripe '),
(3977, 'english', 'Razorpay settings', 'Razorpay settings'),
(3978, 'bangla', 'Razorpay settings', 'Razorpay settings'),
(3979, 'english', 'Theme Color', 'Theme Color'),
(3980, 'bangla', 'Theme Color', 'Theme Color'),
(3981, 'english', 'Update razorpay ', 'Update razorpay '),
(3982, 'bangla', 'Update razorpay ', 'Update razorpay '),
(3983, 'english', 'Paytm settings', 'Paytm settings'),
(3984, 'bangla', 'Paytm settings', 'Paytm settings'),
(3985, 'english', 'Test Merchant Id', 'Test Merchant Id'),
(3986, 'bangla', 'Test Merchant Id', 'Test Merchant Id'),
(3987, 'english', 'Test Merchant Key', 'Test Merchant Key'),
(3988, 'bangla', 'Test Merchant Key', 'Test Merchant Key'),
(3989, 'english', 'Live Merchant Id', 'Live Merchant Id'),
(3990, 'bangla', 'Live Merchant Id', 'Live Merchant Id'),
(3991, 'english', 'Live Merchant Key', 'Live Merchant Key'),
(3992, 'bangla', 'Live Merchant Key', 'Live Merchant Key'),
(3993, 'english', 'Environment', 'Environment'),
(3994, 'bangla', 'Environment', 'Environment'),
(3995, 'english', 'Merchant_Website', 'Merchant_Website'),
(3996, 'bangla', 'Merchant_Website', 'Merchant_Website'),
(3997, 'english', 'Channel', 'Channel'),
(3998, 'bangla', 'Channel', 'Channel'),
(3999, 'english', 'industry_type', 'industry_type'),
(4000, 'bangla', 'industry_type', 'industry_type'),
(4001, 'english', 'Update Paytm ', 'Update Paytm '),
(4002, 'bangla', 'Update Paytm ', 'Update Paytm '),
(4003, 'english', 'Flutterwave settings', 'Flutterwave settings'),
(4004, 'bangla', 'Flutterwave settings', 'Flutterwave settings'),
(4005, 'english', 'Test Encryption Key', 'Test Encryption Key'),
(4006, 'bangla', 'Test Encryption Key', 'Test Encryption Key'),
(4007, 'english', 'Live Encryption Key', 'Live Encryption Key'),
(4008, 'bangla', 'Live Encryption Key', 'Live Encryption Key'),
(4009, 'english', 'List of Lesson', 'List of Lesson'),
(4010, 'bangla', 'List of Lesson', 'List of Lesson'),
(4011, 'english', 'Lesson have been Sorted', 'Lesson have been Sorted'),
(4012, 'bangla', 'Lesson have been Sorted', 'Lesson have been Sorted'),
(4013, 'english', 'Add Leave', 'Add Leave'),
(4014, 'bangla', 'Add Leave', 'Add Leave'),
(4015, 'english', 'Add New Leave', 'Add New Leave'),
(4016, 'bangla', 'Add New Leave', 'Add New Leave'),
(4017, 'english', 'Child List', 'Child List'),
(4018, 'bangla', 'Child List', 'Child List'),
(4019, 'english', 'Test Sectect Key', 'Test Sectect Key'),
(4020, 'bangla', 'Test Sectect Key', 'Test Sectect Key'),
(4021, 'english', 'Edit Leave', 'Edit Leave'),
(4022, 'bangla', 'Edit Leave', 'Edit Leave'),
(4023, 'english', 'Student Limit', 'Student Limit'),
(4024, 'bangla', 'Student Limit', 'Student Limit'),
(4025, 'english', 'Life Time', 'Life Time'),
(4026, 'bangla', 'Life Time', 'Life Time'),
(4027, 'english', 'Students Limit', 'Students Limit'),
(4028, 'bangla', 'Students Limit', 'Students Limit'),
(4029, 'english', 'Write Features', 'Write Features'),
(4030, 'bangla', 'Write Features', 'Write Features'),
(4031, 'english', 'Write service', 'Write service'),
(4032, 'bangla', 'Write service', 'Write service'),
(4033, 'english', 'Write a new features', 'Write a new features'),
(4034, 'bangla', 'Write a new features', 'Write a new features'),
(4035, 'english', 'Subscription Purchase Date', 'Subscription Purchase Date'),
(4036, 'bangla', 'Subscription Purchase Date', 'Subscription Purchase Date'),
(4037, 'english', 'Upgrade Subscribe ', 'Upgrade Subscribe '),
(4038, 'bangla', 'Upgrade Subscribe ', 'Upgrade Subscribe '),
(4039, 'english', 'Payment', 'Payment'),
(4040, 'bangla', 'Payment', 'Payment'),
(4041, 'english', 'Deliverable', 'Deliverable'),
(4042, 'bangla', 'Deliverable', 'Deliverable'),
(4043, 'english', 'Rate', 'Rate'),
(4044, 'bangla', 'Rate', 'Rate'),
(4045, 'english', ' All Rights Reserved', ' All Rights Reserved'),
(4046, 'bangla', ' All Rights Reserved', ' All Rights Reserved');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `noticeboard`
--

CREATE TABLE `noticeboard` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notice_title` longtext NOT NULL,
  `notice` longtext NOT NULL,
  `start_date` varchar(255) DEFAULT NULL,
  `start_time` varchar(255) DEFAULT NULL,
  `end_date` varchar(255) DEFAULT NULL,
  `end_time` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `show_on_website` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `package_type` varchar(255) NOT NULL,
  `interval` varchar(255) DEFAULT NULL,
  `days` int(11) DEFAULT NULL,
  `studentLimit` varchar(255) DEFAULT NULL,
  `features` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `expense_type` longtext NOT NULL,
  `expense_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL,
  `transaction_keys` longtext NOT NULL,
  `created_at` varchar(255) DEFAULT NULL,
  `updated_at` varchar(255) DEFAULT NULL,
  `school_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_gateways`
--

CREATE TABLE `payment_gateways` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` int(11) DEFAULT NULL,
  `configuration` longtext NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_history`
--

CREATE TABLE `payment_history` (
  `id` int(11) NOT NULL,
  `payment_type` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `transaction_keys` longtext DEFAULT NULL,
  `document_image` varchar(255) DEFAULT NULL,
  `paid_by` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `payment_keys` longtext NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0=inactive , 1=active',
  `mode` varchar(255) NOT NULL DEFAULT 'test' COMMENT 'test / live',
  `created_at` varchar(255) DEFAULT NULL,
  `updated_at` varchar(255) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `payment_keys`, `image`, `status`, `mode`, `created_at`, `updated_at`, `school_id`) VALUES
(1, 'offline', '{}', 'offline.png', 1, 'offline', '', '', NULL),
(11, 'paypal', '{\"test_client_id\":\"snd_cl_id_xxxxxxxxxxxxx\",\"test_secret_key\":\"snd_cl_sid_xxxxxxxxxxxx\",\"live_client_id\":\"lv_cl_id_xxxxxxxxxxxxxxx\",\"live_secret_key\":\"lv_cl_sid_xxxxxxxxxxxxxx\"}', 'paypal.png', 1, 'test', NULL, NULL, 2),
(12, 'stripe', '{\"test_key\":\"pk_test_xxxxxxxxxxxxx\",\"test_secret_key\":\"sk_test_xxxxxxxxxxxxxx\",\"public_live_key\":\"pk_live_xxxxxxxxxxxxxx\",\"secret_live_key\":\"sk_live_xxxxxxxxxxxxxx\"}', 'stripe.png', 1, 'test', NULL, NULL, 2),
(13, 'razorpay', '{\"test_key\":\"rzp_test_xxxxxxxxxxxxx\",\"test_secret_key\":\"rzs_test_xxxxxxxxxxxxx\",\"live_key\":\"rzp_live_xxxxxxxxxxxxx\",\"live_secret_key\":\"rzs_live_xxxxxxxxxxxxx\",\"theme_color\":\"#c7a600\"}', 'razorpay.png', 1, 'test', NULL, NULL, 2),
(14, 'paytm', '{\"test_merchant_id\":\"tm_id_xxxxxxxxxxxx\",\"test_merchant_key\":\"tm_key_xxxxxxxxxx\",\"live_merchant_id\":\"lv_mid_xxxxxxxxxxx\",\"live_merchant_key\":\"lv_key_xxxxxxxxxxx\",\"environment\":\"provide-a-environment\",\"merchant_website\":\"merchant-website\",\"channel\":\"provide-channel-type\",\"industry_type\":\"provide-industry-type\"}', 'paytm.png', 1, 'test', NULL, NULL, 2),
(15, 'flutterwave', '{\"test_key\":\"flwp_test_xxxxxxxxxxxxx\",\"test_secret_key\":\"flws_test_xxxxxxxxxxxxx\",\"test_encryption_key\":\"flwe_test_xxxxxxxxxxxxx\",\"public_live_key\":\"flwp_live_xxxxxxxxxxxxxx\",\"secret_live_key\":\"flws_live_xxxxxxxxxxxxxx\",\"encryption_live_key\":\"flwe_live_xxxxxxxxxxxxxx\"}', 'flutterwave.png', 1, 'test', NULL, NULL, 2),
(16, 'paystack', '{\"test_key\":\"pk_test_xxxxxxxxxx\",\"test_secret_key\":\"sk_test_xxxxxxxxxxxxxx\",\"public_live_key\":\"pk_live_xxxxxxxxxxxxxx\",\"secret_live_key\":\"sk_live_xxxxxxxxxxxxxx\"}', 'paystack.png', 1, 'test', NULL, NULL, 2),
(17, 'flutterwave', '{\"test_key\":\"flwp_test_xxxxxxxxxxxxx\",\"test_secret_key\":\"flws_test_xxxxxxxxxxxxx\",\"test_encryption_key\":\"flwe_test_xxxxxxxxxxxxx\",\"public_live_key\":\"flwp_live_xxxxxxxxxxxxxx\",\"secret_live_key\":\"flws_live_xxxxxxxxxxxxxx\",\"encryption_live_key\":\"flwe_live_xxxxxxxxxxxxxx\"}', 'flutterwave.png', 1, 'test', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', '2022-05-17 07:14:27', '2022-05-17 07:14:27'),
(2, 'admin', '2022-05-17 07:14:27', '2022-05-17 07:14:27'),
(3, 'teacher', '2022-05-17 07:15:14', '2022-05-17 07:14:27'),
(4, 'accountant', '2022-05-17 07:15:14', '2022-05-17 07:14:27'),
(5, 'librarian', '2022-05-17 07:15:14', '2022-05-17 07:14:27'),
(6, 'parent', '2022-05-17 07:15:14', '2022-05-17 07:14:27'),
(7, 'student', '2022-05-17 07:15:14', '2022-05-17 07:14:27'),
(8, 'user', '2023-05-24 06:06:50', '2023-05-24 06:06:50'),
(9, 'alumni', '2023-06-01 11:38:30', '2023-06-01 11:38:30');

-- --------------------------------------------------------

--
-- Table structure for table `routines`
--

CREATE TABLE `routines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `starting_hour` int(11) NOT NULL,
  `ending_hour` int(11) NOT NULL,
  `starting_minute` int(11) NOT NULL,
  `ending_minute` int(11) NOT NULL,
  `day` varchar(255) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `school_info` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `running_session` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `school_currency` varchar(255) DEFAULT NULL,
  `currency_position` varchar(255) DEFAULT NULL,
  `school_logo` varchar(255) DEFAULT NULL,
  `email_title` varchar(255) DEFAULT NULL,
  `email_details` varchar(255) DEFAULT NULL,
  `warning_text` varchar(255) DEFAULT NULL,
  `socialLink1` varchar(255) DEFAULT NULL,
  `socialLink2` varchar(255) DEFAULT NULL,
  `socialLink3` varchar(255) DEFAULT NULL,
  `email_logo` varchar(255) DEFAULT NULL,
  `socialLogo1` varchar(255) DEFAULT NULL,
  `socialLogo2` varchar(255) DEFAULT NULL,
  `socialLogo3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `class_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `session_title` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `school_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `session_title`, `status`, `school_id`, `created_at`, `updated_at`) VALUES
(1, '2023', 1, NULL, NULL, NULL),
(2, '2023', 1, 1, '2023-08-20 10:00:58', '2023-08-20 10:00:58'),
(3, '2023', 1, 2, '2023-08-20 10:09:02', '2023-08-20 11:27:53'),
(4, '2024', 0, 2, '2023-08-20 11:27:09', '2023-08-20 11:27:53'),
(5, '2023', 1, 3, '2023-08-24 12:17:58', '2023-08-24 12:17:58'),
(6, '2023', 1, 4, '2023-08-29 11:53:48', '2023-08-29 11:53:48'),
(7, '2023', 1, 5, '2023-08-30 06:38:13', '2023-08-30 06:38:13'),
(8, '2023', 1, 6, '2023-08-31 05:29:12', '2023-08-31 05:29:12'),
(9, '2023', 1, 7, '2023-08-31 06:25:41', '2023-08-31 06:25:41'),
(10, '2023', 1, 8, '2023-08-31 06:26:11', '2023-08-31 06:26:11'),
(11, '2023', 1, 9, '2023-08-31 06:28:23', '2023-08-31 06:28:23'),
(12, '2023', 1, 10, '2023-08-31 06:36:17', '2023-08-31 06:36:17'),
(13, '2023', 1, 11, '2023-08-31 06:37:07', '2023-08-31 06:37:07'),
(14, '2023', 1, 12, '2023-08-31 06:39:19', '2023-08-31 06:39:19'),
(15, '2023', 1, 13, '2023-08-31 07:27:26', '2023-08-31 07:27:26'),
(16, '2023', 1, 14, '2023-08-31 07:33:46', '2023-08-31 07:33:46');

-- --------------------------------------------------------

--
-- Table structure for table `student_fee_managers`
--

CREATE TABLE `student_fee_managers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `total_amount` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `paid_amount` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `document_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `class_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `package_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `paid_amount` double(8,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `transaction_keys` longtext NOT NULL,
  `expire_date` int(11) NOT NULL,
  `studentLimit` varchar(255) DEFAULT NULL,
  `date_added` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `syllabuses`
--

CREATE TABLE `syllabuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_permissions`
--

CREATE TABLE `teacher_permissions` (
  `id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `marks` int(11) DEFAULT NULL,
  `attendance` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `user_information` longtext DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addons`
--
ALTER TABLE `addons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book_issues`
--
ALTER TABLE `book_issues`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_rooms`
--
ALTER TABLE `class_rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currency`
--
ALTER TABLE `currency`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daily_attendances`
--
ALTER TABLE `daily_attendances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_categories`
--
ALTER TABLE `exam_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frontend_events`
--
ALTER TABLE `frontend_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `global_settings`
--
ALTER TABLE `global_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gradebooks`
--
ALTER TABLE `gradebooks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `noticeboard`
--
ALTER TABLE `noticeboard`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_gateways`
--
ALTER TABLE `payment_gateways`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `routines`
--
ALTER TABLE `routines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_fee_managers`
--
ALTER TABLE `student_fee_managers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `syllabuses`
--
ALTER TABLE `syllabuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teacher_permissions`
--
ALTER TABLE `teacher_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addons`
--
ALTER TABLE `addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `book_issues`
--
ALTER TABLE `book_issues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_rooms`
--
ALTER TABLE `class_rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daily_attendances`
--
ALTER TABLE `daily_attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_categories`
--
ALTER TABLE `exam_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `frontend_events`
--
ALTER TABLE `frontend_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `global_settings`
--
ALTER TABLE `global_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gradebooks`
--
ALTER TABLE `gradebooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `noticeboard`
--
ALTER TABLE `noticeboard`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_gateways`
--
ALTER TABLE `payment_gateways`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_history`
--
ALTER TABLE `payment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `routines`
--
ALTER TABLE `routines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_fee_managers`
--
ALTER TABLE `student_fee_managers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `syllabuses`
--
ALTER TABLE `syllabuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teacher_permissions`
--
ALTER TABLE `teacher_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
