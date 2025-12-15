-- Disable foreign key checks to avoid errors during creation
SET FOREIGN_KEY_CHECKS = 0;



-- 1. Table structure for `classes`
DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_name` varchar(50) NOT NULL,
  `grade_block` varchar(50) NOT NULL,
  `teacher_name` varchar(100) NOT NULL,
  `teacher_phone` varchar(20) DEFAULT NULL,
  `teacher_email` varchar(100) DEFAULT NULL,
  `teacher_photo` varchar(255) DEFAULT NULL COMMENT 'Path to teacher photo file',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for `classes`
INSERT INTO `classes` (`id`, `class_name`, `grade_block`, `teacher_name`, `teacher_phone`, `teacher_email`, `teacher_photo`) VALUES
	(1, '10A1', '10', 'Dang Phuong Nam', '5345345346', 'teacher@gmail.com', 'uploads/691c124065208_1763447360.jpg'),
	(2, '11B2', '11', 'Trần Văn C', '435345345345', 'admin@gmail.com', ''),
	(3, '12C3', '12', 'Lê Hùng D', NULL, NULL, NULL),
	(6, '10A2', '10', 'Nguyen Thi Mai', '0987654321', 'mai.nguyen@school.edu.vn', NULL),
	(7, '11B1', '11', 'Tran Van C', '0909090909', 'c.tran@school.edu.vn', NULL),
	(9, '12C1', '12', 'Hoang Van E', '0888777666', 'e.hoang@school.edu.vn', NULL);


-- 2. Table structure for `students`
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(10) DEFAULT 'Other',
  `phone` varchar(15) DEFAULT NULL,
  `address` text,
  `student_photo` varchar(255) DEFAULT NULL COMMENT 'Path to student photo file',
  PRIMARY KEY (`id`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for `students`
INSERT INTO `students` (`id`, `class_id`, `full_name`, `dob`, `gender`, `phone`, `address`, `student_photo`) VALUES
	(1, 1, 'Nguyen Ngoc The', '2008-01-15', 'Male', '090111222', 'Nam Dinh', 'uploads/691c1252ce300_1763447378.jpg'),
	(2, 1, 'Nguyễn Thị Nga', '2008-03-20', 'Female', '090333444', '456 Cầu Giấy, Hà Nội', 'uploads/691c125878ac1_1763447384.jpg'),
	(3, 2, 'Lê Văn C', '2007-05-10', 'Male', '090555666', '789 Thanh Xuân, Hà Nội', NULL),
	(5, 1, 'Nguyen Quang Thai ', '2004-11-11', 'Male', '546456456', 'Thanh Tri ', 'uploads/691c1273ab7f3_1763447411.jpg'),
	(6, 3, 'Dang phuong Nam', '2004-01-11', 'Female', '3423423523', 'Ha Noi', 'uploads/691c188d61238_1763448973.jpg'),
	(8, 1, 'Phan Hong Quang', '2004-05-12', 'Male', '5435435436436', 'Ha Tinh', 'uploads/691c12b553b2f_1763447477.jpg'),
	(9, 1, 'Tran Tien Dat', '2004-03-04', 'Male', '0943903294', 'Hai Duong', 'uploads/691c1333a8d00_1763447603.jpg'),
	(10, 1, 'Tan Bao The', '2009-03-04', 'Female', '34535345345', 'Ha Tinh', 'uploads/693589e1082e3_1765116385.jpg'),
	(11, 1, 'Nguyen Ngoc The', '2008-01-15', 'Male', '090111222', 'Nam Dinh', 'uploads/hs1.jpg'),
	(12, 1, 'Nguyen Thi Nga', '2008-03-20', 'Female', '090333444', '456 Cau Giay, Ha Noi', 'uploads/hs2.jpg'),
	(13, 1, 'Nguyen Quang Thai', '2008-11-11', 'Male', '0546456456', 'Thanh Tri, Ha Noi', 'uploads/hs3.jpg'),
	(14, 1, 'Phan Hong Quang', '2008-05-12', 'Male', '0543543543', 'Ha Tinh', 'uploads/hs4.jpg'),
	(15, 1, 'Tran Tien Dat', '2008-03-04', 'Male', '0943903294', 'Hai Duong', 'uploads/hs5.jpg'),
	(16, 1, 'Tan Bao The', '2008-09-04', 'Female', '0345353453', 'Ha Tinh', 'uploads/hs6.jpg'),
	(17, 2, 'Le Van C', '2008-05-10', 'Male', '090555666', '789 Thanh Xuan, Ha Noi', NULL),
	(18, 2, 'Do Thi D', '2008-07-22', 'Female', '090777888', 'Hoang Mai, Ha Noi', NULL),
	(19, 2, 'Bui Van E', '2008-09-09', 'Male', '090999000', 'Dong Da, Ha Noi', NULL),
	(20, 3, 'Dang Phuong Nam', '2007-01-11', 'Female', '0342342352', 'Ha Noi', 'uploads/hs_nam.jpg'),
	(21, 3, 'Vu Van F', '2007-02-14', 'Male', '091222333', 'Long Bien, Ha Noi', NULL),
	(22, 3, 'Trinh Thi G', '2007-12-25', 'Female', '091444555', 'Gia Lam, Ha Noi', NULL);


-- 3. Table structure for `class_points`
DROP TABLE IF EXISTS `class_points`;
CREATE TABLE `class_points` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `description` text NOT NULL,
  `point_change` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `class_id` (`class_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `class_points_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_points_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for `class_points`
INSERT INTO `class_points` (`id`, `class_id`, `student_id`, `description`, `point_change`, `created_at`, `ngay_tao`) VALUES
	(3, 1, 10, 'Late', -10, '2025-12-08 18:09:33', '2025-12-09 01:09:33'),
	(5, 1, NULL, 'good cleaning duty', 30, '2025-12-09 07:57:24', '2025-12-09 14:57:24'),
	(6, 3, 6, 'late', -20, '2025-12-09 07:58:00', '2025-12-09 14:58:00');


-- 4. Table structure for `grades`
DROP TABLE IF EXISTS `grades`;
CREATE TABLE `grades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `term` varchar(50) NOT NULL,
  `school_year` varchar(20) NOT NULL DEFAULT '2024-2025',
  `math` float DEFAULT NULL,
  `english` float DEFAULT NULL,
  `physics` float DEFAULT NULL,
  `chemistry` float DEFAULT NULL,
  `literature` float DEFAULT NULL,
  `history` float DEFAULT NULL,
  `geography` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for `grades`
INSERT INTO `grades` (`id`, `student_id`, `term`, `school_year`, `math`, `english`, `physics`, `chemistry`, `literature`, `history`, `geography`) VALUES
	(1, 1, 'Semester 1', '2024-2025', 9, 6.8, 9, 6, 4, 7, 5),
	(2, 10, 'Semester 1', '2025-2026', 7, 8, 6, 5, 6, 7, 4),
	(7, 6, 'Semester 1', '2023-2024', 5, 6, 6, 6, 6, 6, 6);


-- 5. Table structure for `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for `users`
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`) VALUES
	(1, 'nam', 'admin@gmail.com', '$2y$10$IpDB3nhs3XM4q9O0r5IFGemPZB.73fuop4mHNLDkA/Iu0n2/6h9vy', 'admin'),
	(2, 'thai', 'thaingu@gmail.com', '$2y$10$a6f.8I5XXDRMrUNuhL/Px.AZFHABSIf4e1Z4Cn66Nvc/35OkqNErq', 'user'),
	(3, 'quang12', '', '$2y$10$mOnwEUq2gU87DaHdpOxJYupJIbbhJ6fUr/6WJRj.QOvwvUwBsaUNe', 'user'),
	(4, 'nam11', 'admin11@gmail.com', '$2y$10$e9pzRXyZrgZlz740GWDDNOdfVXBBXKGxR8JYzQMcawK69VxWYRPoi', 'admin'),
	(5, 'thai04', 'admin04@gmail.com', '$2y$10$rd4MzKpYS4ktB3FOwU8QBu.sQUdxEAn7nIdaruTxhCFMTFGnq8ixq', 'user'),
	(6, 'nam0111', 'thaingu11@gmail.com', '$2y$10$1Zj0pQdw0KvZ6Kt3V78uLe2dAqUwU6bu70hdJSjQk6fURnkknuBnu', 'admin');

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;
