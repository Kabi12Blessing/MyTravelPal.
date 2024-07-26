-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 26, 2024 at 11:06 AM
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
-- Database: `travelpal`
--

-- --------------------------------------------------------

--
-- Table structure for table `Countries`
--

CREATE TABLE `Countries` (
  `country_id` bigint(20) UNSIGNED NOT NULL,
  `country_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Countries`
--

INSERT INTO `Countries` (`country_id`, `country_name`) VALUES
(1, 'Canada'),
(2, 'Norway'),
(3, 'Cameroon'),
(4, 'USA'),
(5, 'UK'),
(6, 'France'),
(7, 'Ghana'),
(8, 'Kenya'),
(9, 'Nigeria'),
(10, 'South Africa');

-- --------------------------------------------------------

--
-- Table structure for table `Interests`
--

CREATE TABLE `Interests` (
  `interest_id` bigint(20) UNSIGNED NOT NULL,
  `interest_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Locations`
--

CREATE TABLE `Locations` (
  `location_id` bigint(20) UNSIGNED NOT NULL,
  `location_name` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `region` varchar(255) DEFAULT NULL,
  `latitude` float(10,6) NOT NULL,
  `longitude` float(10,6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Locations`
--

INSERT INTO `Locations` (`location_id`, `location_name`, `country`, `region`, `latitude`, `longitude`) VALUES
(1, 'New York', 'USA', 'North America', 40.712776, -74.005974),
(2, 'Los Angeles', 'USA ', 'North America', 34.052235, -118.243683),
(3, 'New York', 'USA', 'North America', 40.712776, -74.005974),
(4, 'Los Angeles', 'USA', 'North America', 34.052235, -118.243683),
(5, 'London', 'UK', 'Europe', 51.507351, -0.127758),
(6, 'Paris', 'France', 'Europe', 48.856613, 2.352222),
(7, 'Tokyo', 'Japan', 'Asia', 35.689487, 139.691711),
(8, 'Cairo', 'Egypt', 'Africa', 30.044420, 31.235712),
(9, 'Cape Town', 'South Africa', 'Africa', -33.924870, 18.424055),
(10, 'Nairobi', 'Kenya', 'Africa', -1.286389, 36.817223),
(11, 'Lagos', 'Nigeria', 'Africa', 6.524379, 3.379206),
(12, 'Accra', 'Ghana', 'Africa', 5.603717, -0.186964),
(13, 'Sydney', 'Australia', 'Australia', -33.868820, 151.209290),
(14, 'Toronto', 'Canada', 'North America', 43.651070, -79.347015),
(15, 'Oslo', 'Norway', 'Europe', 59.913868, 10.752245);

-- --------------------------------------------------------

--
-- Table structure for table `Matches`
--

CREATE TABLE `Matches` (
  `match_id` bigint(20) UNSIGNED NOT NULL,
  `user_needing_space_id` int(11) NOT NULL,
  `user_with_extra_space_id` int(11) NOT NULL,
  `score` int(11) DEFAULT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `origin_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Matches`
--

INSERT INTO `Matches` (`match_id`, `user_needing_space_id`, `user_with_extra_space_id`, `score`, `status`, `origin_id`, `destination_id`) VALUES
(1, 1, 2, 85, 'pending', 1, 2),
(2, 2, 1, 90, 'accepted', 2, 3),
(3, 1, 3, 75, 'rejected', 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `Messages`
--

CREATE TABLE `Messages` (
  `message_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_text` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `preference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `conversation_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Messages`
--

INSERT INTO `Messages` (`message_id`, `sender_id`, `receiver_id`, `message_text`, `sent_at`, `preference_id`, `conversation_id`) VALUES
(56, 21, 7, 'hey. this is yaya', '2024-07-25 22:53:24', 13, 'conv_66a2d764047091.53384375'),
(58, 7, 22, 'hey how are you doing', '2024-07-25 23:51:29', NULL, 'conv_66a2d6d20f8605.85306695'),
(62, 7, 21, 'hey', '2024-07-26 00:34:31', 13, 'conv_66a2d764047091.53384375'),
(68, 7, 21, 'YO', '2024-07-26 00:43:56', 13, 'conv_66a2d764047091.53384375'),
(69, 7, 21, 'WHATSUP', '2024-07-26 00:44:07', 13, 'conv_66a2d764047091.53384375'),
(71, 21, 7, 'hum', '2024-07-26 01:02:19', NULL, 'conv_66a2d764047091.53384375'),
(76, 21, 7, 'yo', '2024-07-26 01:09:48', 13, 'conv_66a2d764047091.53384375'),
(77, 21, 7, 'how are you doing', '2024-07-26 01:10:12', 13, 'conv_66a2d764047091.53384375'),
(78, 7, 21, 'good and you', '2024-07-26 01:13:04', 13, 'conv_66a2d764047091.53384375'),
(79, 7, 21, 'So what are you up to', '2024-07-26 01:18:49', 13, 'conv_66a2d764047091.53384375'),
(81, 21, 7, 'Hey can we match ?', '2024-07-26 01:31:15', 13, 'conv_66a2d764047091.53384375'),
(84, 7, 21, 'hey\r\n', '2024-07-26 01:52:23', 13, 'conv_66a2d764047091.53384375'),
(85, 21, 7, 'yeah what ?', '2024-07-26 01:54:34', 13, 'conv_66a2d764047091.53384375');

-- --------------------------------------------------------

--
-- Table structure for table `Profiles`
--

CREATE TABLE `Profiles` (
  `profile_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` enum('Male','Female','Non-binary','Prefer not to say') NOT NULL,
  `bio` text DEFAULT NULL,
  `profile_photo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Reviews`
--

CREATE TABLE `Reviews` (
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `reviewee_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Travel_Preferences`
--

CREATE TABLE `Travel_Preferences` (
  `preference_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `travel_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `budget` int(11) DEFAULT NULL,
  `has_extra_space` tinyint(1) DEFAULT 0,
  `needs_space` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `number_of_travelers` int(11) DEFAULT NULL,
  `accommodation_type` enum('hotel','hostel','airbnb','other') DEFAULT NULL,
  `preferences` text DEFAULT NULL,
  `origin_country_id` bigint(20) UNSIGNED DEFAULT NULL,
  `destination_country_id` bigint(20) UNSIGNED DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Travel_Preferences`
--

INSERT INTO `Travel_Preferences` (`preference_id`, `user_id`, `travel_date`, `return_date`, `budget`, `has_extra_space`, `needs_space`, `description`, `number_of_travelers`, `accommodation_type`, `preferences`, `origin_country_id`, `destination_country_id`, `image_path`) VALUES
(5, 6, '2024-07-18', '2024-07-23', 2000, 1, 0, 'Mouf', 1, 'hotel', 'male', 3, 6, '[]'),
(6, 7, '2024-07-11', '2024-07-25', 2, 1, 0, 'ok', 2, 'hotel', 'male', 7, 1, '[]'),
(9, 5, '2024-07-26', '2024-07-25', 2000, 0, 1, 'Slay', 2, 'hotel', 'female', 7, 5, '[]'),
(10, 8, '2024-07-14', '2024-07-18', 10000, 0, 1, 'Concert, you want to follow me ?', 1, 'hotel', 'female', 4, 1, '[]'),
(11, 8, '2024-07-24', '2024-08-06', 20000, 0, 1, 'AFRONATION AFRICA', 1, 'hotel', 'female', 1, 8, '[]'),
(12, 6, '2024-07-12', '2024-07-17', 2000, 1, 0, 'ok', 1, 'airbnb', 'male', 5, 3, '[]'),
(13, 7, '2024-12-20', '2025-01-03', 100000, 0, 1, 'Have fun with me ', 1, 'airbnb', 'male', 2, 7, '[]'),
(15, 11, '2024-08-14', '2024-09-19', 20000, 1, 0, 'rwee', 1, 'hotel', 'female', 7, 2, '[]'),
(16, 17, '2024-08-01', '2024-08-10', 190000, 1, 0, 'Hey boo', -1, 'other', 'female', 1, 7, '[]'),
(19, 6, '2024-06-30', '2024-07-05', 7000, 1, 0, 'yooo', 5, 'hostel', 'female', 8, 10, '[]'),
(27, 17, '2024-08-02', '2024-08-10', 1, 1, 0, '', -1, 'hotel', 'other', 4, 6, '[]'),
(29, 17, '2024-08-03', '2024-08-08', 90, 1, 0, '', 2, 'hostel', 'female', 3, 9, 'null'),
(30, 17, '2024-08-02', '2024-08-10', 7, 1, 0, '', -1, 'hotel', 'female', 1, 1, 'null'),
(31, 17, '2024-08-03', '2024-08-10', 90, 1, 0, '', -3, 'hotel', 'female', 1, 1, 'null'),
(32, 17, '2024-08-03', '2024-08-10', 8, 1, 0, '', 7, 'hotel', 'female', 1, 7, 'null'),
(33, 17, '2024-08-02', '2024-08-09', 9008, 1, 0, '', 5, 'hotel', 'male', 9, 5, NULL),
(34, 18, '2024-12-20', '2025-01-04', 1200, 0, 1, 'Meet my daddy ', 1, 'other', 'female', 7, 4, NULL),
(35, 22, '2024-07-26', '2024-07-27', 1000, 1, 0, 'I need a fun person to travel with me', 1, 'airbnb', 'female', 4, 3, NULL),
(36, 21, '2024-07-27', '2024-07-31', 500, 0, 1, '', 1, 'hotel', 'male', 5, 6, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` char(60) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `username` varchar(50) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`user_id`, `email`, `password_hash`, `created_at`, `last_login`, `username`, `profile_picture`) VALUES
(1, 'kabiblessing5@gmail.com', '$2y$10$7vngsq5k/aT6zynztm5GIuuflO.lSNyKhxqs2hipjcYAgXgGLiB0u', '2024-06-09 19:45:29', '0000-00-00 00:00:00', '', NULL),
(2, 'll@gmail.com', '$2y$10$MDGYr6FnPw0eWRxaETXKYeMzMcKtYhj94SmHMmydanOA4laEp4NcO', '2024-06-27 03:24:52', '0000-00-00 00:00:00', 'll', NULL),
(3, 'lucie@gmail.com', '$2y$10$bgAaHMW4es7jFPMNxe4FD.MjhO6KMi3hDaXlnJpgg8CKvu8aXtbye', '2024-06-27 03:25:56', '0000-00-00 00:00:00', 'lucie', NULL),
(4, 'b@gmail.com', '$2y$10$ByFXcerXcDFfYt3iAaic9.ODj2e4vlICgioaQQ3LknPAONY3T8y8e', '2024-06-27 03:28:35', '0000-00-00 00:00:00', 'b', NULL),
(5, 'Hannah@gmail.com', '$2y$10$2dPsvLHv/xNmQpzEuqSb0es/mS7ERC1vlnpne4EZPFqoDCWiDsmmS', '2024-06-27 03:48:40', '0000-00-00 00:00:00', 'Hannah', '/Applications/XAMPP/xamppfiles/htdocs/Travel_Pal/action/../uploads/profile_pictures/5.jpeg'),
(6, 'blessing@gmail.com', '$2y$10$Vblf8rw4ofCahbRMRueW4uox10Zkc4TRX114C2DKhPbKEN05KNfES', '2024-06-27 20:47:08', '0000-00-00 00:00:00', 'Eileen', '/Applications/XAMPP/xamppfiles/htdocs/Travel_Pal/action/../uploads/profile_pictures/6.jpeg'),
(7, 'leslie@gmail.com', '$2y$10$9muDI8OvKdRPG3fOJMvY3OWrjbGYMyfC7pdm3Ic8ott5dAWhQ4fru', '2024-07-02 15:08:06', '0000-00-00 00:00:00', 'Leslie', '/Applications/XAMPP/xamppfiles/htdocs/Travel_Pal/action/../uploads/profile_pictures/7.jpg'),
(8, 'Drake@gmail.com', '$2y$10$JcQjpCO48V75mwZxNL6FUueZicn7pzS/IZCxTTAzB.nZa2CFH3rMe', '2024-07-02 19:49:23', '0000-00-00 00:00:00', 'Drake', '/Applications/XAMPP/xamppfiles/htdocs/Travel_Pal/action/../uploads/profile_pictures/8.png'),
(9, 'faith@gmail.com', '$2y$10$NAu0xOPzhaU/P04EpXpNHOn0fUiL2i9VEXaMuAIAd9JgDFwwOpJqa', '2024-07-16 08:23:13', '0000-00-00 00:00:00', 'Faith', NULL),
(10, 'favour@yahoo.edu', '$2y$10$aOWTax7fbEz7r96c9uATq.Qz5/xjQwQnmFk1eh3YhzFZ2Ru.80v4K', '2024-07-16 08:47:22', '0000-00-00 00:00:00', 'Favour', NULL),
(11, 'anthony@gmail.com', '$2y$10$sdaHR4F2Kz3M0A7CdOl86e0sZqfm1xsjw2ba7LRBOSw93GY9TGtGa', '2024-07-16 09:15:43', '0000-00-00 00:00:00', 'Anthony', NULL),
(12, 'constance@gmail.com', '$2y$10$UxLIqVh5Zta89629maDhrOW5mQgMoZDuX2kR1YcA2tg/56Q6617wi', '2024-07-23 17:15:46', '0000-00-00 00:00:00', 'Constance', NULL),
(13, 'blasius@gmail.com', '$2y$10$Jfy3NtWS96BTE2fyMX07a.8y7Cyh09LeC56UAhixYppqwSFEZall6', '2024-07-23 17:22:49', '0000-00-00 00:00:00', 'Blasius Pwe', NULL),
(14, 'blasiuss@gmail.com', '$2y$10$NPp/r2ln69fH6OyZIAY74eyOo00uf2zxG/2MIWjSsPRCpFvRCyNQq', '2024-07-23 17:24:05', '0000-00-00 00:00:00', 'Blasiuss', NULL),
(15, 'blasiuss@gmail.coms', '$2y$10$uoKlduGmyjo74kUFPKHuVeES0icCnepTYWLUD7BQn1aaQBB7PA0ZW', '2024-07-23 17:30:17', '0000-00-00 00:00:00', 'Blasiussss', NULL),
(16, 'mummyro@gmail.com', '$2y$10$//DSxeNPW5c.XIBCgF314OmGbRgVk52qWgb0lFkJZqNBFXVqE7M7K', '2024-07-23 17:35:11', '0000-00-00 00:00:00', 'Mummy Ro', NULL),
(17, 'clintonkabi@gmail.com', '$2y$10$HSis3XgmGNtREWIiT8XtGuRHIhdl7v/8/p.s2QLJzmr.8JtKJqDIu', '2024-07-23 17:43:04', '0000-00-00 00:00:00', 'Kabi', '/Applications/XAMPP/xamppfiles/htdocs/MyTravelPal/action/../uploads/profile_pictures/17.jpg'),
(18, 'wilson@gmail.com', '$2y$10$.9b9T/KxI3O8ktInbogjUe/FbacHEaWQt8TmQUEqrt6rji.yEOhD.', '2024-07-25 16:14:30', '0000-00-00 00:00:00', 'Wilson', '/Applications/XAMPP/xamppfiles/htdocs/MyTravelPal/action/../uploads/profile_pictures/18.jpeg'),
(19, 'brice@gmail.com', '$2y$10$0xLfVxbtk2oMMx9rWeNBIuebud4PlbHQNZHtrI4sLuo8REBHVMdS6', '2024-07-25 19:53:58', '0000-00-00 00:00:00', 'Brice', NULL),
(20, 'k@gmail.com', '$2y$10$HG0tQM513ng3qNm4wUZw9Oq4.uKr5lz1r4OJjyHxdmEmN9bsZTZOS', '2024-07-25 19:55:49', '0000-00-00 00:00:00', 'kamel', NULL),
(21, 'yaya@gmail.com', '$2y$10$K6Ih.Sicf38prFD9u2JO4ONgAhYs9kRIEPzqvqIPAGXL108zTTZEW', '2024-07-25 19:56:56', '0000-00-00 00:00:00', 'yaya', '/Applications/XAMPP/xamppfiles/htdocs/MyTravelPal/action/../uploads/profile_pictures/21.png'),
(22, 'kamel@gmail.com', '$2y$10$pZqjLiiM967.XW5qB2XiK.dAlnW7q96qvZanpqdX4kdYVHIVteyEy', '2024-07-25 19:59:01', '0000-00-00 00:00:00', 'kamel', '/Applications/XAMPP/xamppfiles/htdocs/MyTravelPal/action/../uploads/profile_pictures/22.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `User_Interests`
--

CREATE TABLE `User_Interests` (
  `user_id` int(11) NOT NULL,
  `interest_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Countries`
--
ALTER TABLE `Countries`
  ADD PRIMARY KEY (`country_id`);

--
-- Indexes for table `Interests`
--
ALTER TABLE `Interests`
  ADD PRIMARY KEY (`interest_id`);

--
-- Indexes for table `Locations`
--
ALTER TABLE `Locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `Matches`
--
ALTER TABLE `Matches`
  ADD PRIMARY KEY (`match_id`);

--
-- Indexes for table `Messages`
--
ALTER TABLE `Messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `fk_preference_id` (`preference_id`);

--
-- Indexes for table `Profiles`
--
ALTER TABLE `Profiles`
  ADD PRIMARY KEY (`profile_id`);

--
-- Indexes for table `Reviews`
--
ALTER TABLE `Reviews`
  ADD PRIMARY KEY (`review_id`);

--
-- Indexes for table `Travel_Preferences`
--
ALTER TABLE `Travel_Preferences`
  ADD PRIMARY KEY (`preference_id`),
  ADD KEY `fk_origin_country` (`origin_country_id`),
  ADD KEY `fk_destination_country` (`destination_country_id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `User_Interests`
--
ALTER TABLE `User_Interests`
  ADD PRIMARY KEY (`user_id`,`interest_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Countries`
--
ALTER TABLE `Countries`
  MODIFY `country_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `Interests`
--
ALTER TABLE `Interests`
  MODIFY `interest_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Locations`
--
ALTER TABLE `Locations`
  MODIFY `location_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `Matches`
--
ALTER TABLE `Matches`
  MODIFY `match_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Messages`
--
ALTER TABLE `Messages`
  MODIFY `message_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `Profiles`
--
ALTER TABLE `Profiles`
  MODIFY `profile_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Reviews`
--
ALTER TABLE `Reviews`
  MODIFY `review_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Travel_Preferences`
--
ALTER TABLE `Travel_Preferences`
  MODIFY `preference_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Messages`
--
ALTER TABLE `Messages`
  ADD CONSTRAINT `fk_preference_id` FOREIGN KEY (`preference_id`) REFERENCES `Travel_Preferences` (`preference_id`);

--
-- Constraints for table `Travel_Preferences`
--
ALTER TABLE `Travel_Preferences`
  ADD CONSTRAINT `fk_destination_country` FOREIGN KEY (`destination_country_id`) REFERENCES `Countries` (`country_id`),
  ADD CONSTRAINT `fk_origin_country` FOREIGN KEY (`origin_country_id`) REFERENCES `Countries` (`country_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
