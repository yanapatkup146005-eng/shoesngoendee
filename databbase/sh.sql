-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2026 at 10:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sh`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `status` enum('pending','paid','shipped','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `username`, `firstname`, `lastname`, `total_price`, `address`, `phone`, `status`, `created_at`) VALUES
(6, 'rr', 'dd', 'rr', 6900.00, 'sfsdf', '12321', 'pending', '2026-02-07 11:13:20'),
(7, 'rr', 'ตึง', 'แนวว', 2300.00, 'reegrg', '2341442', 'pending', '2026-02-07 11:17:59'),
(9, 'ืnoey', 'noey', 'zaza', 2500.00, 'dgdfg', '2234354543', 'pending', '2026-02-07 15:51:29'),
(11, 'uten', 'ดหกดกห', 'กหดห', 2300.00, 'sdfsdfsgf', '35423466', 'shipped', '2026-02-08 15:56:10'),
(15, 'user2', 'fost', 'tf', 11600.00, '345ggg', '242343543534', 'shipped', '2026-04-01 15:48:13'),
(17, 'user2', 'fost', 'tf', 2222.00, '345ggg', '242343543534', 'shipped', '2026-04-01 17:16:53'),
(18, 'user2', 'fost', 'tf', 444.00, '345ggg', '242343543534', 'pending', '2026-04-01 17:26:43'),
(19, 'user2', 'fost', 'tf', 444.00, '345ggg', '242343543534', 'pending', '2026-04-01 19:08:09'),
(20, 'user2', 'fost', 'tf', 2500.00, '345ggg', '242343543534', 'shipped', '2026-04-01 19:11:06'),
(21, 'user2', 'fost', 'tf', 2500.00, '345ggg', '242343543534', 'cancelled', '2026-04-01 19:23:40'),
(22, 'user2', 'fost', 'tf', 2500.00, '345ggg', '242343543534', 'pending', '2026-04-01 19:28:18'),
(23, 'user2', 'fost', 'tf', 2500.00, '345ggg', '242343543534', 'shipped', '2026-04-01 19:33:40'),
(24, 'user3', 'eeww', 'eerr', 2000.00, '4/90\r\n4/90', '432325465464', 'pending', '2026-04-01 20:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` varchar(10) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `selected_size` varchar(50) DEFAULT NULL,
  `selected_color` varchar(50) DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `size`, `color`, `selected_size`, `selected_color`, `qty`, `price_at_purchase`) VALUES
(6, 6, 8, NULL, NULL, NULL, NULL, 3, 2300.00),
(7, 7, 8, NULL, NULL, NULL, NULL, 1, 2300.00),
(9, 9, 7, NULL, NULL, NULL, NULL, 1, 2500.00),
(11, 11, 8, NULL, NULL, NULL, NULL, 1, 2300.00),
(16, 15, 10, NULL, NULL, NULL, NULL, 2, 2500.00),
(17, 15, 8, NULL, NULL, NULL, NULL, 2, 2300.00),
(18, 15, 9, NULL, NULL, NULL, NULL, 1, 2000.00),
(19, 19, 13, NULL, NULL, NULL, NULL, 1, 444.00),
(20, 20, 15, NULL, NULL, NULL, NULL, 1, 2500.00),
(21, 21, 15, NULL, NULL, NULL, NULL, 1, 2500.00),
(22, 22, 15, NULL, NULL, NULL, NULL, 1, 2500.00),
(23, 23, 15, '38', 'red', NULL, NULL, 1, 2500.00),
(24, 24, 16, '39', 'brown', NULL, NULL, 1, 2000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sizes` varchar(255) DEFAULT '38,39,40,41,42,43',
  `colors` varchar(255) DEFAULT 'Black,White,Grey',
  `brand` varchar(50) DEFAULT 'ไม่ระบุแบรนด์',
  `image_variants` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `description`, `sizes`, `colors`, `brand`, `image_variants`) VALUES
(16, 'AidasMax', 2000.00, '1775073401.png', 'รองเท้า AdidasMax รองเท้าผ้าใบดีไซน์คลาสสิก ผสมผสานความเรียบง่ายและความทันสมัย เหมาะสำหรับการสวมใส่ในชีวิตประจำวัน ให้ความสบายตลอดการใช้งาน ตัวรองเท้าผลิตจากวัสดุคุณภาพดี มีความทนทาน และระบายอากาศได้ดี ช่วยลดความอับชื้นภายในรองเท้า\r\n\r\nพื้นรองเท้าออกแบบมาเพื่อรองรับแรงกระแทก ทำให้เดินสบาย ไม่เมื่อยล้า เหมาะสำหรับการเดินทาง ท่องเที่ยว หรือใส่ทำงานในวันสบาย ๆ สามารถเข้ากับการแต่งตัวได้หลายสไตล์ ทั้งแนวลำลองและสปอร์ต', '38,39,40,41', 'black,brown', 'Adidas', 'black:blackadidas.png,brown:bronadidas.png'),
(18, 'AirMax', 2000.00, '1775073555.png', 'รองเท้า AirMax ออกแบบมาเพื่อความสบายและสไตล์ที่ทันสมัย เหมาะสำหรับการสวมใส่ในชีวิตประจำวัน ไม่ว่าจะเป็นการเดินทาง ท่องเที่ยว หรือออกกำลังกาย ตัวรองเท้าผลิตจากวัสดุคุณภาพดี ระบายอากาศได้ดี ช่วยลดความอับชื้น พร้อมพื้นรองเท้าที่รองรับแรงกระแทก ทำให้สวมใส่สบายตลอดวัน\r\n\r\nดีไซน์ทันสมัย น้ำหนักเบา เคลื่อนไหวได้คล่องตัว เหมาะสำหรับทุกเพศทุกวัย สามารถแมทช์กับเสื้อผ้าได้หลายสไตล์ เช่น ลำลอง สปอร์ต หรือแฟชั่น', '38,39,40,41', 'red,gray', 'Nike', 'red:rednike.png,gray:graynike.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `firstname`, `lastname`, `email`, `phone`, `address`, `created_at`, `role`) VALUES
(4, 'mintap', '$2y$10$yTCG5Uq6MUT0ZUuQ/5Z9f.iWydaM2W8D95TIIgfyRug102sZtPHiK', 'ญาณพัฒน์', 'เงินดี', 'yanapatkup146005@gmail.com', '0909757343', '4/90\r\n4/90', '2026-02-03 16:22:19', 'user'),
(5, 'rr', '$2y$10$uQ6VVN1KsVkvG0r4n/iwteyy9oIRi.7nPw3C7hPOnE3FqWcGHF8rC', 'รัก', 'ใจดี', 'ัััyy@gmail.com', '123456', 'homepromaxqw', '2026-02-03 16:34:39', 'user'),
(6, 'hh', '$2y$10$eEC24GLgQlEGZpvkeqcY1eW7t2kT5xUhnIaqgY8cZ5fk4qq.Go4Ea', 'ญาณพัฒน์', 'เงินดี', 'yanapatkup146005@gmail.com', '0909757343', '4/90\r\n4/90', '2026-02-03 17:00:42', 'user'),
(7, 'lamai', '$2y$10$aInhV7XCINglpUmEsVgpa./XqXiEv2avmmVprElgzPyYcXV06pBYu', 'ััyanapat', 'ngoendee', 'yanapat@gmail.com', '098765432', 'ggg', '2026-02-05 11:56:43', 'admin'),
(8, 'ืnoey', '$2y$10$BpEoMo57.nw/MzVF9RRjqO3t1oOK.PJJNiD8PfwvSJwDpCXO./1hm', 'noeyza', 'zaza', 'sdfs@gmail.com', '1232423423', 'promaxfftt', '2026-02-07 15:50:05', 'user'),
(9, 'uten', '$2y$10$GDaJP56rMdlPAEMrrE1FhOPQu3hPWUJdulDfyNk8DvtHHtpDJtDfa', 'utensirarom', 'deemak', 'rrttqqeett@gmail.com', '293798579', 'macko', '2026-02-08 15:17:52', 'user'),
(11, 'qq', '$2y$10$fvomrJPmwT9wR4JBwByq/eoX2XLb.XuxUxkF6B.MYJhsVJ4FO/Nmi', 'qq', 'ww', 'dfsdf@gmail.com', '123456778', '490', '2026-03-09 06:26:59', 'admin'),
(12, 'ff123', '$2y$10$w/Qd2lmf27mktgs4ILSV6Os0Bvt0Xv6jmvX4PKdAi5D99nJVbZHSy', 'sdfs', 'sdfsf', 'sdfsQ@gdfg.com', '234125435', 'fed', '2026-03-14 12:19:29', 'user'),
(13, 'user2', '$2y$10$RTwjLI7OHw9I9dHrHZ45gekoPQ184shIvfvbr6O44lj5qIA7cR1eW', 'fost', 'tf', 'sfdsfs@gmail.com', '242343543534', '345ggg', '2026-03-16 12:27:01', 'user'),
(14, 'user3', '$2y$10$FG9z5c.QBlUg6VnJJ5qsiuScgRLjjBaOy8esaDI6IJkoozQPuFdKS', 'eeww', 'eerr', 'fsnlskn@gmail.com', '432325465464', '4/90\r\n4/90', '2026-03-16 12:59:04', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
