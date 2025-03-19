/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 10.4.28-MariaDB : Database - evote
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`evote` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `evote`;

/*Table structure for table `candidates` */

DROP TABLE IF EXISTS `candidates`;

CREATE TABLE `candidates` (
  `candidate_id` int(11) NOT NULL AUTO_INCREMENT,
  `candidate_position` int(11) NOT NULL,
  `candidate_name` varchar(255) NOT NULL,
  `partylist_id` int(11) NOT NULL,
  `department` int(11) NOT NULL,
  `candidate_image_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`candidate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `candidates` */

insert  into `candidates`(`candidate_id`,`candidate_position`,`candidate_name`,`partylist_id`,`department`,`candidate_image_path`) values 
(1,1,'Romel Moster',1,1,NULL),
(2,1,'Abdul',2,2,NULL);

/*Table structure for table `course` */

DROP TABLE IF EXISTS `course`;

CREATE TABLE `course` (
  `course_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_department` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  PRIMARY KEY (`course_id`),
  KEY `course_department` (`course_department`),
  CONSTRAINT `course_ibfk_1` FOREIGN KEY (`course_department`) REFERENCES `department` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `course` */

insert  into `course`(`course_id`,`course_department`,`course_name`) values 
(1,1,'Bachelor of Science in Information Technology'),
(2,1,'Bachelor of Arts in English Language'),
(3,1,'Bachelor of Arts in Social Science'),
(4,1,'Bachelor of Science in Mathematics'),
(5,1,'Bachelor of Arts in Psychology'),
(6,1,'Bachelor of Science in Public Administration'),
(9,2,'Bachelor of Secondary Education Major in English'),
(10,2,'Bachelor of Secondary Education Major in Mathematics'),
(11,2,'Bachelor of Secondary Education Major in Science'),
(13,2,'Bachelor of Secondary Education Major in Filipino'),
(14,2,'Bachelor of Secondary Education Major in Social Studies'),
(15,2,'Bachelor of Elementary Education'),
(16,2,'Bachelor of Physical Education'),
(17,3,'Bachelor of Science in Business Administration Major in Human Resource Management'),
(18,3,'Bachelor of Science in Business Administration Major in Marketing Management'),
(19,3,'Bachelor of Science in Business Administration Major in Financial Management'),
(20,3,'Bachelor of Science in Entrepreneurship');

/*Table structure for table `department` */

DROP TABLE IF EXISTS `department`;

CREATE TABLE `department` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(255) NOT NULL,
  PRIMARY KEY (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `department` */

insert  into `department`(`department_id`,`department_name`) values 
(1,'College of Arts and Science'),
(2,'College of Teacher and Education'),
(3,'College of Business Management and Entrepreneurship');

/*Table structure for table `login_attempts` */

DROP TABLE IF EXISTS `login_attempts`;

CREATE TABLE `login_attempts` (
  `login_attempts_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `attempt` int(10) NOT NULL DEFAULT 1,
  `ip_address` varchar(24) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`login_attempts_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `login_attempts` */

insert  into `login_attempts`(`login_attempts_id`,`user_id`,`attempt`,`ip_address`,`date`) values 
(2,1,1,'::1','2024-08-17'),
(3,1,1,'::1','2024-08-27'),
(4,1,1,'::1','2024-09-03');

/*Table structure for table `login_sessions` */

DROP TABLE IF EXISTS `login_sessions`;

CREATE TABLE `login_sessions` (
  `login_sessions_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `fingerprint` varchar(255) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `ip_address` varchar(24) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`login_sessions_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `login_sessions` */

insert  into `login_sessions`(`login_sessions_id`,`user_id`,`fingerprint`,`user_agent`,`ip_address`,`datetime`) values 
(1,1,'1b3a35342f7756d39a7bc2a244ce9153cb59cb9c2cb74f131d3065136d18a07c','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36','::1','2024-10-14 15:33:32'),
(2,2,'1b3a35342f7756d39a7bc2a244ce9153cb59cb9c2cb74f131d3065136d18a07c','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36','::1','2024-08-17 17:23:31'),
(3,1,'b4c57aa41b7aa9c6952423c308c0141e4549c3ffe8d62cadd8277e2cf2e1d6c5','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36','::1','2024-08-27 01:40:16'),
(4,1,'fec84459f8d2acb8064fc3faada61513ba1a3f7d96699e765d2ace4330d6fde0','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36','::1','2024-09-28 22:53:06');

/*Table structure for table `partylists` */

DROP TABLE IF EXISTS `partylists`;

CREATE TABLE `partylists` (
  `partylist_id` int(11) NOT NULL AUTO_INCREMENT,
  `partylist_name` varchar(255) NOT NULL,
  `department` int(11) NOT NULL,
  PRIMARY KEY (`partylist_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `partylists` */

insert  into `partylists`(`partylist_id`,`partylist_name`,`department`) values 
(1,'Kahit tress lang',1),
(2,'Pasado',2);

/*Table structure for table `password_reset` */

DROP TABLE IF EXISTS `password_reset`;

CREATE TABLE `password_reset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `is_valid` enum('0','1') NOT NULL DEFAULT '1',
  `expire_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `password_reset` */

insert  into `password_reset`(`id`,`user_id`,`token`,`is_valid`,`expire_at`) values 
(1,1,'8a990b902372fc6eaf94d4a5495d1016','0','2024-08-17 16:51:21'),
(2,1,'f7990478f1e1618f8a4fa59bd6400e65','1','2024-08-18 01:09:29'),
(3,1,'41fb29703b69e81524c016e82d778cfc','0','2024-08-17 17:13:11');

/*Table structure for table `positions` */

DROP TABLE IF EXISTS `positions`;

CREATE TABLE `positions` (
  `position_id` int(11) NOT NULL AUTO_INCREMENT,
  `position_name` varchar(255) NOT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `positions` */

insert  into `positions`(`position_id`,`position_name`) values 
(1,'PRESIDENT'),
(2,'VICE-PRESIDENT INTERNAL'),
(3,'VICE-PRESIDENT EXTERNAL'),
(4,'SECRETARY'),
(5,'ASSISTANT SECRETARY'),
(6,'TREASURER'),
(7,'AUDITOR'),
(8,'PUBLIC INFORMATION OFFICER'),
(9,'BUSINESS MANAGER'),
(10,'BUSINESS MANAGER'),
(11,'SERGEANT-AT-ARMS'),
(12,'SERGEANT-AT-ARMS'),
(13,'SERGEANT-AT-ARMS');

/*Table structure for table `students` */

DROP TABLE IF EXISTS `students`;

CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(9) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `suffix_name` varchar(255) NOT NULL,
  `course` int(10) NOT NULL,
  `year_level` varchar(10) NOT NULL,
  `department` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `course` (`course`),
  KEY `department` (`department`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`course`) REFERENCES `course` (`course_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `students_ibfk_2` FOREIGN KEY (`department`) REFERENCES `department` (`department_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `students` */

insert  into `students`(`id`,`student_id`,`first_name`,`middle_name`,`last_name`,`suffix_name`,`course`,`year_level`,`department`) values 
(1,'E21-00193','Cyanne Justin','Labitoria','Vega','',1,'4',1);

/*Table structure for table `user_activity` */

DROP TABLE IF EXISTS `user_activity`;

CREATE TABLE `user_activity` (
  `user_activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `last_activity` datetime NOT NULL,
  PRIMARY KEY (`user_activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `user_activity` */

insert  into `user_activity`(`user_activity_id`,`user_id`,`last_activity`) values 
(1,1,'2024-09-29 12:15:17'),
(2,2,'2024-08-17 05:24:55');

/*Table structure for table `user_details` */

DROP TABLE IF EXISTS `user_details`;

CREATE TABLE `user_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT 'assets/images/default/default.png',
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `user_details` */

insert  into `user_details`(`id`,`user_id`,`avatar`,`first_name`,`last_name`,`birthdate`) values 
(1,1,'uploads/avatars/4080221d39566f0e01ccccba0b54ff44.jpg','','','2024-01-01'),
(2,2,'assets/images/default/default.png',NULL,NULL,NULL);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `confirmation_key` varchar(120) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `tfa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `tfa_secret` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `users` */

insert  into `users`(`user_id`,`username`,`email`,`password`,`confirmation_key`,`status`,`date_created`,`tfa_enabled`,`tfa_secret`) values 
(1,'qoshima','florescalvs@gmail.com','$2y$10$HPZvkd8VtukeUijpKMchZukOtLER.CbB9Ttu1CYEy8QKj5o/UhOFm','e366acd3386965cfe2106b23587933a3','1','2024-08-17 16:43:50',0,NULL),
(2,'bitress','bytress@gmail.com','$argon2id$v=19$m=65536,t=4,p=1$QVNMZnRhL1Z2aUExY0tVbw$v+RTRtvGwNm6t++rnEM8n1XmNY8evlSU4OeXAUqPE2o','e01b607dffad74f35022f1badfee73f3','1','2024-08-17 17:21:20',0,NULL);

/*Table structure for table `votes` */

DROP TABLE IF EXISTS `votes`;

CREATE TABLE `votes` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `partylist_id` int(11) DEFAULT NULL,
  `voted_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`vote_id`),
  KEY `student_id` (`student_id`),
  KEY `candidate_id` (`candidate_id`),
  KEY `partylist_id` (`partylist_id`),
  KEY `votes_ibfk_3` (`position_id`),
  CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidate_id`) ON DELETE CASCADE,
  CONSTRAINT `votes_ibfk_3` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`) ON DELETE CASCADE,
  CONSTRAINT `votes_ibfk_4` FOREIGN KEY (`partylist_id`) REFERENCES `partylists` (`partylist_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `votes` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
