-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 08, 2025 at 10:24 PM
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
-- Database: `skincare_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `dermatologist_id` int(11) NOT NULL,
  `patient_name` varchar(100) NOT NULL,
  `patient_email` varchar(100) NOT NULL,
  `patient_phone` varchar(20) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `concern` text NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `dermatologist_id`, `patient_name`, `patient_email`, `patient_phone`, `appointment_date`, `appointment_time`, `concern`, `status`, `created_at`) VALUES
(1, 10, 'Balon', 'Mary@gmail.com', '677059131', '2025-09-20', '09:00:00', 'check beuaty', 'cancelled', '2025-09-07 21:00:16'),
(2, 11, 'Test', 'test@gmail.com', '677059131', '2025-09-17', '17:00:00', 'I would like to find out the skin tone i have for a good rubbing lotion (Test Final)', 'pending', '2025-09-07 21:16:07'),
(3, 13, 'Test final', 'testfinal1@gmail.com', '677059131', '2025-09-20', '15:00:00', 'test final', 'confirmed', '2025-09-07 22:09:10');

-- --------------------------------------------------------

--
-- Table structure for table `client_profiles`
--

CREATE TABLE `client_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `skintone` varchar(50) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `skin_condition` text DEFAULT NULL,
  `suffering_from` text DEFAULT NULL,
  `goals` text DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `condition_start` date DEFAULT NULL,
  `taken_meds` enum('yes','no') DEFAULT NULL,
  `products_used` text DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_profiles`
--

INSERT INTO `client_profiles` (`id`, `user_id`, `name`, `age`, `skintone`, `picture`, `skin_condition`, `suffering_from`, `goals`, `gender`, `condition_start`, `taken_meds`, `products_used`, `blood_group`, `created_at`, `updated_at`) VALUES
(1, 16, 'TEST', 12, 'Brown', 'uploads/68bed90fb4289.jpeg', 'RASH', 'rash', 'rash', 'female', '2025-09-12', 'no', 'alovera', 'AB+', '2025-09-08 13:22:27', '2025-09-08 13:24:31'),
(2, 15, '0', 17, 'Very Fair', 'uploads/68bee6b7832b2.jpeg', 'nope', 'nope', 'nope', 'female', '2025-10-01', 'yes', 'ope', 'A+', '2025-09-08 14:22:47', '2025-09-08 15:10:16');

-- --------------------------------------------------------

--
-- Table structure for table `dermatologist_profiles`
--

CREATE TABLE `dermatologist_profiles` (
  `id` int(11) NOT NULL,
  `dermatologist_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `years_experience` int(11) NOT NULL,
  `address` text NOT NULL,
  `experience1` text NOT NULL,
  `experience2` text NOT NULL,
  `availability` text NOT NULL,
  `quote` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dermatologist_profiles`
--

INSERT INTO `dermatologist_profiles` (`id`, `dermatologist_id`, `name`, `years_experience`, `address`, `experience1`, `experience2`, `availability`, `quote`, `updated_at`, `profile_image`) VALUES
(1, 10, 'Final Test', 14, 'Yaounde', 'ok', 'ok', 'Monday to Friday 6am to 1pm', 'i have the power to be resilient ', '2025-09-07 20:51:48', 'uploads/profiles/profile_10_1757275497.jpeg'),
(2, 11, 'Test two final', 23, 'Testing', 'Testing', 'testing', 'Monday to Wednesday (5am to 8am)', 'I am capable', '2025-09-07 21:14:20', 'uploads/profiles/profile_11_1757279660.jpeg'),
(3, 13, 'Keziah Mbinda', 12, 'Youande', 'Yaounde', 'Messasi', 'Monday to thurday (2am to 3pm)', 'Your body is your crown', '2025-09-07 22:00:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `area` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `area`, `image`, `user_id`, `created_at`, `updated_at`) VALUES
(6, 'Mosquitoe Spray (For Men)', 'This is a mosquitoe spray meant for Men to aviod getting bitten. ', 25.00, 'Mutengene ', 'uploads/1756934313_Img (14).jpeg', 7, '2025-09-03 21:16:59', '2025-09-03 21:18:33'),
(7, 'Honey (Bees)', 'This is natural honey from the bush', 10.00, 'Tiko, Mamfe', 'uploads/1756941722_Img (18).jpeg', 7, '2025-09-03 23:22:02', '2025-09-03 23:22:45'),
(8, 'Testing the software', 'test oh just test am exhuasted writting codes', 12.00, 'buea', 'uploads/1757104667_Img (18).jpeg', 7, '2025-09-05 20:37:47', '2025-09-05 20:37:47'),
(9, 'Savon Moringa The Great', 'This is a savon that heals the soul', 1.00, 'Yaounde (Messasi)', 'uploads/1757113967_Img (24).jpeg', 8, '2025-09-05 23:12:47', '2025-09-05 23:13:33'),
(10, 'Final Test', 'Test final Des', 10.00, 'Yaounde', 'uploads/1757282259_Img (14).jpeg', 12, '2025-09-07 21:57:40', '2025-09-07 21:58:09');

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dermatologist_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `review_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `review` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `product_id`, `user_id`, `dermatologist_id`, `rating`, `review_text`, `created_at`, `review`) VALUES
(1, 6, 0, 10, 3, 'This is good sha', '2025-09-07 19:37:37', NULL),
(2, 6, 0, 10, 3, 'This is good sha', '2025-09-07 20:04:20', NULL),
(3, 8, 0, 10, 4, 'very good', '2025-09-07 20:05:28', NULL),
(4, 9, 0, 11, 1, 'Remove this right now', '2025-09-07 20:07:27', NULL),
(5, 8, 0, 11, 2, 'Corrosion', '2025-09-07 21:20:09', NULL),
(6, 10, 0, 13, 4, 'From my Knowledge i think the product is not good for people with blood group B', '2025-09-07 22:01:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','client','dermatologist','cosmetic') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'balon', 'balonleslie@gmail.com', '$2y$10$Z5tTF1cI3eew7bqbkds8RuxW4oPOjD9FrlrIn2Cq5hQvnoRCNgGtq', 'admin', '2025-09-01 19:46:36'),
(2, 'electa', 'electa@gmail.com', '$2y$10$Pfu6SLnmXYh6HbQ2m5F23.8qD0hj9eACRctbewaXrWtu157JSBEAm', 'admin', '2025-09-01 19:48:37'),
(6, 'test1', 'test1@gmail.com', '$2y$10$UB3hFRDs5w6Ty8I8UJJZ7ue0Nw9Wy00AHdfsVjUzsNDO9dY2f.DZe', 'cosmetic', '2025-09-03 16:52:34'),
(7, 'Cosmetic', 'cosmetic@gmail.com', '$2y$10$/sEdsmwdpWP/qyrlNSEOG./M0OcNGqb.OdnhoXaTIGVuXPme1b23a', 'cosmetic', '2025-09-03 17:15:56'),
(8, 'test 2', 'test2@gmail.com', '$2y$10$WSd.2hm/t05tubIehJr9yeyY7jB/qxI2SwclCPSDFrBHnIUjlIdbC', 'cosmetic', '2025-09-05 23:11:07'),
(9, 'Kezia1', 'keziah1@gmail.com', '$2y$10$j/ZdmyN55cBpc6MLB/N/veo9s5z17egsRGltk9xWKn1jhRbs2FyDG', 'admin', '2025-09-05 23:18:03'),
(10, 'dematologist', 'dematologist@gmail.com', '$2y$10$DHCzDBqHh.davmtY5tpjnO5LaEYmwfocbygv8x6U5AdBz3g9jHR86', 'dermatologist', '2025-09-07 18:50:11'),
(11, 'Test Two Dematolog', 'dematolog@gmail.com', '$2y$10$20VtMotL4YdJtAfi9sgcFeRbTKqsweoHUrYcP3Bo6XXg1vP/ZYlcO', 'dermatologist', '2025-09-07 20:06:40'),
(12, 'Final Test', 'finaltest@gmail.com', '$2y$10$HF5HWT7Yp/XfzNULQC/sYuWuy50XWZ1utBlVrC7ZQ/RfSefcWwHSO', 'cosmetic', '2025-09-07 21:56:45'),
(13, 'Test for Dematologist', 'testdematolog@gmail.com', '$2y$10$n0i.cenw4pYum2rGB1mjDeacXJSQOtsHrcx.bxnDY9T4DCyXB9D.q', 'dermatologist', '2025-09-07 21:59:01'),
(14, 'Test Final Admin', 'admin@gmail.com', '$2y$10$ecw8AYH8zNweL3ZsT9A/ReD5COiaXp0OfcKkPuVs3SHoEkSqMEGH.', 'admin', '2025-09-07 22:05:32'),
(15, 'Test Client', 'client@gmail.com', '$2y$10$Sgx9Y2j1SYScXVDM7jYbOumeSx4tyaWzPinqC5UgwGLroyX5wXO0.', 'client', '2025-09-08 13:17:51'),
(16, 'Lina', 'lina1@gmail.com', '$2y$10$WRTlJ/MWGAMduX65SMESkOGqIuPki2rTccEliiVT6CYk8fdKb/Um6', 'client', '2025-09-08 13:21:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dermatologist_id` (`dermatologist_id`);

--
-- Indexes for table `client_profiles`
--
ALTER TABLE `client_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `dermatologist_profiles`
--
ALTER TABLE `dermatologist_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dermatologist_id` (`dermatologist_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_dermatologist` (`dermatologist_id`);

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
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `client_profiles`
--
ALTER TABLE `client_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dermatologist_profiles`
--
ALTER TABLE `dermatologist_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`dermatologist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_profiles`
--
ALTER TABLE `client_profiles`
  ADD CONSTRAINT `client_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dermatologist_profiles`
--
ALTER TABLE `dermatologist_profiles`
  ADD CONSTRAINT `dermatologist_profiles_ibfk_1` FOREIGN KEY (`dermatologist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `fk_dermatologist` FOREIGN KEY (`dermatologist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`dermatologist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
