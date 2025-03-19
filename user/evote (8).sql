-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 16, 2025 at 11:27 AM
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
-- Database: `evote`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `candidate_id` int(11) NOT NULL,
  `candidate_position` int(11) NOT NULL,
  `candidate_name` varchar(255) NOT NULL,
  `partylist_id` int(11) NOT NULL,
  `department` int(11) NOT NULL,
  `candidate_image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`candidate_id`, `candidate_position`, `candidate_name`, `partylist_id`, `department`, `candidate_image_path`) VALUES
(1, 1, 'Romel Moster', 1, 1, 'uploads/candidates/1739630836_462555467_2010331759443680_5032823974413162076_n.jpg'),
(3, 1, 'Renald', 2, 2, 'uploads/candidates/1739630791_473527387_987883933220149_3874541490629753716_n.jpg'),
(4, 2, 'Jannie', 1, 3, 'uploads/candidates/1739630920_440736738_1808074766361559_892188753434341060_n.jpg'),
(5, 1, 'Calvin', 2, 2, 'uploads/candidates/1739630960_462583243_463055916485943_856148722633376863_n.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `candidate_accounts`
--

CREATE TABLE `candidate_accounts` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `course_id` int(11) NOT NULL,
  `course_department` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course_id`, `course_department`, `course_name`) VALUES
(1, 1, 'Bachelor of Science in Information Technology'),
(2, 1, 'Bachelor of Arts in English Language'),
(3, 1, 'Bachelor of Arts in Social Science'),
(4, 1, 'Bachelor of Science in Mathematics'),
(5, 1, 'Bachelor of Arts in Psychology'),
(6, 1, 'Bachelor of Science in Public Administration'),
(9, 2, 'Bachelor of Secondary Education Major in English'),
(10, 2, 'Bachelor of Secondary Education Major in Mathematics'),
(11, 2, 'Bachelor of Secondary Education Major in Science'),
(13, 2, 'Bachelor of Secondary Education Major in Filipino'),
(14, 2, 'Bachelor of Secondary Education Major in Social Studies'),
(15, 2, 'Bachelor of Elementary Education'),
(16, 2, 'Bachelor of Physical Education'),
(17, 3, 'Bachelor of Science in Business Administration Major in Human Resource Management'),
(18, 3, 'Bachelor of Science in Business Administration Major in Marketing Management'),
(19, 3, 'Bachelor of Science in Business Administration Major in Financial Management'),
(20, 3, 'Bachelor of Science in Entrepreneurship');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`department_id`, `department_name`) VALUES
(1, 'College of Arts and Science'),
(2, 'College of Teacher and Education'),
(3, 'College of Business Management and Entrepreneurship');

-- --------------------------------------------------------

--
-- Table structure for table `election_results`
--

CREATE TABLE `election_results` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `vote_count` int(11) NOT NULL DEFAULT 0,
  `rank` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `election_results`
--

INSERT INTO `election_results` (`id`, `election_id`, `position_id`, `candidate_id`, `vote_count`, `rank`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 1, 2, 1, '2025-02-16 00:24:36', '2025-02-16 00:24:36'),
(2, 5, 1, 3, 1, 2, '2025-02-16 00:24:36', '2025-02-16 00:24:36'),
(3, 5, 1, 5, 0, 3, '2025-02-16 00:24:36', '2025-02-16 00:24:36'),
(4, 5, 2, 4, 3, 1, '2025-02-16 00:24:36', '2025-02-16 00:24:36'),
(8, 7, 1, 1, 0, 1, '2025-02-16 00:45:48', '2025-02-16 00:45:48'),
(9, 7, 1, 3, 0, 1, '2025-02-16 00:45:48', '2025-02-16 00:45:48'),
(10, 7, 1, 5, 0, 1, '2025-02-16 00:45:48', '2025-02-16 00:45:48'),
(11, 7, 2, 4, 0, 1, '2025-02-16 00:45:48', '2025-02-16 00:45:48');

-- --------------------------------------------------------

--
-- Table structure for table `election_result_signatures`
--

CREATE TABLE `election_result_signatures` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `ssc_advisor_name` varchar(255) DEFAULT NULL,
  `ssc_advisor_position` varchar(255) DEFAULT NULL,
  `osa_name` varchar(255) DEFAULT NULL,
  `osa_position` varchar(255) DEFAULT NULL,
  `twg_name` varchar(255) DEFAULT NULL,
  `twg_position` varchar(255) DEFAULT NULL,
  `extractor_name` varchar(255) DEFAULT NULL,
  `extractor_position` varchar(255) DEFAULT NULL,
  `admin_name` varchar(255) DEFAULT NULL,
  `admin_position` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `election_result_signatures`
--

INSERT INTO `election_result_signatures` (`id`, `election_id`, `ssc_advisor_name`, `ssc_advisor_position`, `osa_name`, `osa_position`, `twg_name`, `twg_position`, `extractor_name`, `extractor_position`, `admin_name`, `admin_position`, `created_at`, `updated_at`) VALUES
(1, 5, 'Dennis T. Millare', 'SSC Adviser', 'MARY ROSE S. ABANIZ', 'OSA Coordinator', 'Dr. GEORGE R. VILLANEUVA, JR.', 'Technical Working Group', 'JIM_MAR F. DE LOS REYES', 'Technical Working Group', 'EDERLINA M. SUMAIL', 'CAMPUS ADMINISTRATOR', '2025-02-16 00:24:36', '2025-02-16 00:24:36'),
(2, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-16 00:45:48', '2025-02-16 00:45:48');

-- --------------------------------------------------------

--
-- Table structure for table `election_settings`
--

CREATE TABLE `election_settings` (
  `id` int(11) NOT NULL,
  `election_title` varchar(255) NOT NULL,
  `election_start` datetime NOT NULL,
  `election_end` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(20) DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `election_settings`
--

INSERT INTO `election_settings` (`id`, `election_title`, `election_start`, `election_end`, `is_active`, `created_at`, `updated_at`, `status`) VALUES
(4, 'SSC election 2025', '2025-02-16 09:22:00', '2025-02-16 21:22:00', 0, '2025-02-15 17:23:04', '2025-02-16 00:04:59', 'inactive'),
(5, 'SSC Election 2024', '2024-12-01 08:00:00', '2024-12-01 17:00:00', 0, '2025-02-16 00:24:36', '2025-02-16 00:24:36', 'ended'),
(6, 'SSC Election 2025', '2025-02-17 00:24:36', '2025-02-18 00:24:36', 0, '2025-02-16 00:24:36', '2025-02-16 00:29:10', 'active'),
(7, 'sdaf', '2025-02-03 00:33:00', '2025-02-04 00:33:00', 0, '2025-02-16 00:33:54', '2025-02-16 00:45:48', 'ended');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `login_attempts_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attempt` int(10) NOT NULL DEFAULT 1,
  `ip_address` varchar(24) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`login_attempts_id`, `user_id`, `attempt`, `ip_address`, `date`) VALUES
(2, 1, 1, '::1', '2024-08-17'),
(3, 1, 1, '::1', '2024-08-27'),
(4, 1, 1, '::1', '2024-09-03'),
(5, 3, 1, '::1', '2025-02-16'),
(6, 4, 2, '::1', '2025-02-16'),
(7, 6, 3, '::1', '2025-02-16'),
(8, 5, 1, '::1', '2025-02-16'),
(9, 7, 2, '::1', '2025-02-16'),
(10, 1, 1, '::1', '2025-02-16');

-- --------------------------------------------------------

--
-- Table structure for table `login_sessions`
--

CREATE TABLE `login_sessions` (
  `login_sessions_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fingerprint` varchar(255) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `ip_address` varchar(24) NOT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `login_sessions`
--

INSERT INTO `login_sessions` (`login_sessions_id`, `user_id`, `fingerprint`, `user_agent`, `ip_address`, `datetime`) VALUES
(1, 1, '1b3a35342f7756d39a7bc2a244ce9153cb59cb9c2cb74f131d3065136d18a07c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36', '::1', '2025-02-16 15:26:05'),
(2, 2, '1b3a35342f7756d39a7bc2a244ce9153cb59cb9c2cb74f131d3065136d18a07c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36', '::1', '2024-08-17 17:23:31'),
(3, 1, 'b4c57aa41b7aa9c6952423c308c0141e4549c3ffe8d62cadd8277e2cf2e1d6c5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', '::1', '2024-08-27 01:40:16'),
(4, 1, 'fec84459f8d2acb8064fc3faada61513ba1a3f7d96699e765d2ace4330d6fde0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', '::1', '2024-09-28 22:53:06'),
(5, 1, '36c455488b9ab7300f9a5548232c3cd1da3103986375938f3ab4031e725b5857', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 Edg/132.0.0.0', '::1', '2025-02-15 22:20:38'),
(6, 1, '7f2e22ba5db22666b1bc687e5e17fd7d826033d08f75825506b2fd09d380147a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 Edg/133.0.0.0', '::1', '2025-02-16 08:02:55'),
(7, 8, '7f2e22ba5db22666b1bc687e5e17fd7d826033d08f75825506b2fd09d380147a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 Edg/133.0.0.0', '::1', '2025-02-16 14:48:55'),
(8, 9, '7f2e22ba5db22666b1bc687e5e17fd7d826033d08f75825506b2fd09d380147a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 Edg/133.0.0.0', '::1', '2025-02-16 14:50:07'),
(9, 10, '7f2e22ba5db22666b1bc687e5e17fd7d826033d08f75825506b2fd09d380147a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 Edg/133.0.0.0', '::1', '2025-02-16 15:30:14'),
(10, 11, '7f2e22ba5db22666b1bc687e5e17fd7d826033d08f75825506b2fd09d380147a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 Edg/133.0.0.0', '::1', '2025-02-16 15:29:45');

-- --------------------------------------------------------

--
-- Table structure for table `partylists`
--

CREATE TABLE `partylists` (
  `partylist_id` int(11) NOT NULL,
  `partylist_name` varchar(255) NOT NULL,
  `department` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `partylists`
--

INSERT INTO `partylists` (`partylist_id`, `partylist_name`, `department`) VALUES
(1, 'Kahit tress lang', 1),
(2, 'Pasado', 2);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE `password_reset` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `is_valid` enum('0','1') NOT NULL DEFAULT '1',
  `expire_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset`
--

INSERT INTO `password_reset` (`id`, `user_id`, `token`, `is_valid`, `expire_at`) VALUES
(1, 1, '8a990b902372fc6eaf94d4a5495d1016', '0', '2024-08-17 16:51:21'),
(2, 1, 'f7990478f1e1618f8a4fa59bd6400e65', '1', '2024-08-18 01:09:29'),
(3, 1, '41fb29703b69e81524c016e82d778cfc', '0', '2024-08-17 17:13:11');

-- --------------------------------------------------------

--
-- Table structure for table `platforms`
--

CREATE TABLE `platforms` (
  `platform_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `platform_comments`
--

CREATE TABLE `platform_comments` (
  `comment_id` int(11) NOT NULL,
  `platform_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `platform_likes`
--

CREATE TABLE `platform_likes` (
  `like_id` int(11) NOT NULL,
  `platform_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `position_id` int(11) NOT NULL,
  `position_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`position_id`, `position_name`) VALUES
(1, 'PRESIDENT'),
(2, 'VICE-PRESIDENT INTERNAL'),
(3, 'VICE-PRESIDENT EXTERNAL'),
(4, 'SECRETARY'),
(5, 'ASSISTANT SECRETARY'),
(6, 'TREASURER'),
(7, 'AUDITOR'),
(8, 'PUBLIC INFORMATION OFFICER'),
(9, 'BUSINESS MANAGER'),
(10, 'BUSINESS MANAGER'),
(11, 'SERGEANT-AT-ARMS'),
(12, 'SERGEANT-AT-ARMS'),
(13, 'SERGEANT-AT-ARMS');

-- --------------------------------------------------------

--
-- Table structure for table `role_changes`
--

CREATE TABLE `role_changes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `old_role` enum('super_admin','admin','user') NOT NULL,
  `new_role` enum('super_admin','admin','user') NOT NULL,
  `changed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(9) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `suffix_name` varchar(255) NOT NULL,
  `course` int(10) NOT NULL,
  `year_level` varchar(10) NOT NULL,
  `department` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `first_name`, `middle_name`, `last_name`, `suffix_name`, `course`, `year_level`, `department`) VALUES
(1, 'E21-00193', 'Cyanne Justin', 'Labitoria', 'Vega', '', 1, '4', 1),
(2, 'E21-00731', 'fgfd', 'fgd', 'dfgdfg', '', 1, '4', 1),
(3, 'E21-00732', 'fgfd', 'fgd', 'dfgdfg', '', 4, '2', 1),
(4, 'E21-00733', 'fgfd', 'fgd', 'dfgdfg', '', 13, '4', 2),
(5, 'E24-00115', 'fhd', 'fdgh', 'fdgh', '', 13, '4', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `confirmation_key` varchar(120) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `tfa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `tfa_secret` varchar(255) DEFAULT NULL,
  `role` enum('super_admin','admin','user') NOT NULL DEFAULT 'user',
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `confirmation_key`, `status`, `date_created`, `tfa_enabled`, `tfa_secret`, `role`, `remember_token`) VALUES
(1, 'qoshima', 'florescalvs@gmail.com', '$2y$10$HPZvkd8VtukeUijpKMchZukOtLER.CbB9Ttu1CYEy8QKj5o/UhOFm', 'e366acd3386965cfe2106b23587933a3', '1', '2024-08-17 16:43:50', 0, NULL, 'super_admin', NULL),
(2, 'bitress', 'bytress@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$QVNMZnRhL1Z2aUExY0tVbw$v+RTRtvGwNm6t++rnEM8n1XmNY8evlSU4OeXAUqPE2o', 'e01b607dffad74f35022f1badfee73f3', '1', '2024-08-17 17:21:20', 0, NULL, 'admin', NULL),
(5, 'admin', 'admin@123', '$2y$10$SqEQK9aU5Ymh6boNq0XMI.trJjfP2EcQXhqgdgsIyWG2z7KF6wYOS', '6bfc2ef55ab83c0bfafca03a8f5b0256', '1', '2025-02-15 21:05:34', 0, NULL, 'user', NULL),
(6, 'admin1', 'admin@1234', '$2y$10$7/W4edaFUHkFQfqYRfyWUOVwSJjx/9z/XGQ9ZUi.TGXQwZokBmqru', 'f51ac591fceb80428773c739237768d6', '1', '2025-02-15 21:05:53', 0, NULL, 'user', NULL),
(7, 'Marvin', 'marvinragunton2001@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$aGY1clJNVUw3SzFkdVk1TQ$UKROjLlWG0sWl8WCD9S69DBZBDwHtPBJeG+t6lvZ6Vs', '578284d75e498af92bdb5d368e4aa383', '1', '2025-02-15 22:29:31', 0, NULL, 'user', NULL),
(8, 'admins', 'marvinragunton01@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$NUVVSXFVc0ttR29VeDRYTQ$C6sa7qgQ8ZPfLiU/F/R4rJi1pV3S5Zd1Kvm1wWJdnwU', 'e3e97d73ef3c1af1be8c3b7f2e829807', '1', '2025-02-15 22:33:00', 0, NULL, 'user', NULL),
(10, 'ren', 'ad@gmail.com', '$2y$10$VbooyGQ0mQCPx1x/IpUtQevvUQo5snKq6ZMm8kvtl3AMaQNu.FI8u', '8bc13bda1ae7d66b5662150ffca63e29', '1', '2025-02-15 23:21:35', 0, NULL, 'user', NULL),
(11, 'renald', 'ads@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$Zi9kYThuVXFTMS5qbWNrbA$YraYpI+QXfRKWvUYe5jzwzPVX6IsM6DJ2J0omBak87Y', '491b0469663b3095dfb6954f7376d322', '1', '2025-02-15 23:25:26', 0, NULL, 'super_admin', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_activity`
--

CREATE TABLE `user_activity` (
  `user_activity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_activity` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activity`
--

INSERT INTO `user_activity` (`user_activity_id`, `user_id`, `last_activity`) VALUES
(1, 1, '2024-09-29 12:15:17'),
(2, 2, '2024-08-17 05:24:55'),
(3, 7, '2025-02-16 02:29:31'),
(4, 8, '2025-02-16 02:33:00'),
(5, 9, '2025-02-16 02:49:58'),
(6, 10, '2025-02-16 03:21:35'),
(7, 11, '2025-02-16 03:25:26');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT 'assets/images/default/default.png',
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`id`, `user_id`, `avatar`, `first_name`, `last_name`, `birthdate`) VALUES
(1, 1, 'uploads/avatars/4080221d39566f0e01ccccba0b54ff44.jpg', '', '', '2024-01-01'),
(2, 2, 'assets/images/default/default.png', NULL, NULL, NULL),
(3, 4, 'assets/images/default/default.png', 'ad', 'ads', NULL),
(4, 5, 'assets/images/default/default.png', 'ad', 'ads', NULL),
(5, 6, 'assets/images/default/default.png', 'ad', 'ads', NULL),
(6, 7, 'assets/images/default/default.png', NULL, NULL, NULL),
(7, 8, 'assets/images/default/default.png', NULL, NULL, NULL),
(8, 9, 'assets/images/default/default.png', NULL, NULL, NULL),
(9, 10, 'assets/images/default/default.png', NULL, NULL, NULL),
(10, 11, 'assets/images/default/default.png', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `vote_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `partylist_id` int(11) DEFAULT NULL,
  `voted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `election_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`vote_id`, `student_id`, `candidate_id`, `position_id`, `partylist_id`, `voted_at`, `election_id`) VALUES
(1, 2, 1, 1, 1, '2025-02-15 03:09:58', NULL),
(2, 3, 1, 1, 1, '2025-02-15 06:00:25', NULL),
(4, 2, 1, 1, 1, '2025-02-15 17:29:05', 4),
(5, 2, 4, 2, 1, '2025-02-15 17:29:05', 4),
(6, 3, 3, 1, 2, '2025-02-15 17:36:32', 4),
(7, 3, 4, 2, 1, '2025-02-15 17:36:32', 4),
(8, 5, 1, 1, 1, '2025-02-15 19:07:04', 4),
(9, 5, 4, 2, 1, '2025-02-15 19:07:04', 4),
(10, 2, 1, 1, 1, '2024-12-01 09:00:00', 5),
(11, 3, 1, 1, 1, '2024-12-01 10:00:00', 5),
(12, 5, 3, 1, 2, '2024-12-01 11:00:00', 5),
(13, 2, 4, 2, 1, '2024-12-01 09:00:00', 5),
(14, 3, 4, 2, 1, '2024-12-01 10:00:00', 5),
(15, 5, 4, 2, 1, '2024-12-01 11:00:00', 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`candidate_id`);

--
-- Indexes for table `candidate_accounts`
--
ALTER TABLE `candidate_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `candidate_id` (`candidate_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `course_department` (`course_department`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `election_results`
--
ALTER TABLE `election_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `election_id` (`election_id`),
  ADD KEY `position_id` (`position_id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- Indexes for table `election_result_signatures`
--
ALTER TABLE `election_result_signatures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `election_id` (`election_id`);

--
-- Indexes for table `election_settings`
--
ALTER TABLE `election_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`login_attempts_id`);

--
-- Indexes for table `login_sessions`
--
ALTER TABLE `login_sessions`
  ADD PRIMARY KEY (`login_sessions_id`);

--
-- Indexes for table `partylists`
--
ALTER TABLE `partylists`
  ADD PRIMARY KEY (`partylist_id`);

--
-- Indexes for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `platforms`
--
ALTER TABLE `platforms`
  ADD PRIMARY KEY (`platform_id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- Indexes for table `platform_comments`
--
ALTER TABLE `platform_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `platform_id` (`platform_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `platform_likes`
--
ALTER TABLE `platform_likes`
  ADD PRIMARY KEY (`like_id`),
  ADD UNIQUE KEY `unique_like` (`platform_id`,`student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`position_id`);

--
-- Indexes for table `role_changes`
--
ALTER TABLE `role_changes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course` (`course`),
  ADD KEY `department` (`department`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD PRIMARY KEY (`user_activity_id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`vote_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `partylist_id` (`partylist_id`),
  ADD KEY `votes_ibfk_3` (`position_id`),
  ADD KEY `election_id` (`election_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `candidate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `candidate_accounts`
--
ALTER TABLE `candidate_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `election_results`
--
ALTER TABLE `election_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `election_result_signatures`
--
ALTER TABLE `election_result_signatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `election_settings`
--
ALTER TABLE `election_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `login_attempts_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `login_sessions`
--
ALTER TABLE `login_sessions`
  MODIFY `login_sessions_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `partylists`
--
ALTER TABLE `partylists`
  MODIFY `partylist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `platforms`
--
ALTER TABLE `platforms`
  MODIFY `platform_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `platform_comments`
--
ALTER TABLE `platform_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `platform_likes`
--
ALTER TABLE `platform_likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `role_changes`
--
ALTER TABLE `role_changes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_activity`
--
ALTER TABLE `user_activity`
  MODIFY `user_activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidate_accounts`
--
ALTER TABLE `candidate_accounts`
  ADD CONSTRAINT `candidate_accounts_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidate_id`) ON DELETE CASCADE;

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_1` FOREIGN KEY (`course_department`) REFERENCES `department` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `election_results`
--
ALTER TABLE `election_results`
  ADD CONSTRAINT `election_results_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `election_settings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `election_results_ibfk_2` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `election_results_ibfk_3` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidate_id`) ON DELETE CASCADE;

--
-- Constraints for table `election_result_signatures`
--
ALTER TABLE `election_result_signatures`
  ADD CONSTRAINT `election_result_signatures_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `election_settings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `platforms`
--
ALTER TABLE `platforms`
  ADD CONSTRAINT `platforms_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidate_id`) ON DELETE CASCADE;

--
-- Constraints for table `platform_comments`
--
ALTER TABLE `platform_comments`
  ADD CONSTRAINT `platform_comments_ibfk_1` FOREIGN KEY (`platform_id`) REFERENCES `platforms` (`platform_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `platform_comments_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `platform_likes`
--
ALTER TABLE `platform_likes`
  ADD CONSTRAINT `platform_likes_ibfk_1` FOREIGN KEY (`platform_id`) REFERENCES `platforms` (`platform_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `platform_likes_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_changes`
--
ALTER TABLE `role_changes`
  ADD CONSTRAINT `role_changes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_changes_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`course`) REFERENCES `course` (`course_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`department`) REFERENCES `department` (`department_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidate_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_3` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_4` FOREIGN KEY (`partylist_id`) REFERENCES `partylists` (`partylist_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `votes_ibfk_5` FOREIGN KEY (`election_id`) REFERENCES `election_settings` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
