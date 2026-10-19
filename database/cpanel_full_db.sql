-- Realty Smartz Pathshala Complete Production Database Dump
-- Generated: 2026-10-19 13:33:08
-- Compatible with cPanel MySQL / MariaDB and phpMyAdmin

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

-- --------------------------------------------------------
-- Table structure for `admin_comments`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `admin_comments`;
CREATE TABLE `admin_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `admin_id` int(10) unsigned NOT NULL,
  `comment_type` enum('internal','public_to_candidate') NOT NULL DEFAULT 'public_to_candidate',
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ac_user` (`user_id`),
  KEY `fk_ac_admin` (`admin_id`),
  CONSTRAINT `fk_ac_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ac_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `admin_comments`
INSERT INTO `admin_comments` (`id`, `user_id`, `admin_id`, `comment_type`, `comment_text`, `created_at`) VALUES
  ('1', '20', '20', 'public_to_candidate', 'Candidate completed 4-step onboarding wizard and submitted documents.', '2026-10-17 14:52:01'),
  ('2', '27', '27', 'public_to_candidate', 'Aptitude Assessment completed with score 10/15 (66.67%) - PASSED', '2026-10-17 15:31:06'),
  ('3', '28', '28', 'public_to_candidate', 'Aptitude Assessment completed with score 10/15 (66.67%) - PASSED', '2026-10-17 15:31:32'),
  ('4', '29', '29', 'public_to_candidate', 'Aptitude Assessment completed with score 10/15 (66.67%) - PASSED', '2026-10-17 16:00:14'),
  ('5', '30', '30', 'public_to_candidate', 'Candidate completed 4-step onboarding wizard and submitted documents.', '2026-10-17 16:42:08'),
  ('6', '30', '30', 'public_to_candidate', 'Aptitude Assessment completed with score 0/15 (0%) - FAILED', '2026-10-17 16:43:01'),
  ('7', '4', '4', 'public_to_candidate', 'Aptitude Assessment completed with score 0/15 (0%) - FAILED', '2026-10-18 13:30:21'),
  ('8', '33', '33', 'public_to_candidate', 'Aptitude Assessment completed with score 0/15 (0%) - FAILED', '2026-10-18 18:31:57'),
  ('9', '33', '33', 'public_to_candidate', 'Aptitude Assessment completed with score 0/15 (0%) - FAILED', '2026-10-18 18:32:43'),
  ('10', '33', '33', 'public_to_candidate', 'Aptitude Assessment completed with score 0/15 (0%) - FAILED', '2026-10-18 18:34:20');

-- --------------------------------------------------------
-- Table structure for `aptitude_sets`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `aptitude_sets`;
CREATE TABLE `aptitude_sets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `set_code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `times_assigned` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `set_code` (`set_code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `aptitude_sets`
INSERT INTO `aptitude_sets` (`id`, `set_code`, `name`, `description`, `times_assigned`, `is_active`, `created_at`) VALUES
  ('1', 'Set A', 'Assessment Set A - Aptitude & Real Estate', 'Logical, Quantitative, English, Real Estate Basics, Sales & Negotiation', '12', '1', '2026-10-17 14:03:11'),
  ('2', 'Set B', 'Assessment Set B - Aptitude & Real Estate', 'Logical, Quantitative, English, Real Estate Basics, Sales & Negotiation', '11', '1', '2026-10-17 14:03:11'),
  ('3', 'Set C', 'Assessment Set C - Aptitude & Real Estate', 'Logical, Quantitative, English, Real Estate Basics, Sales & Negotiation', '11', '1', '2026-10-17 14:03:11'),
  ('4', 'Set D', 'Assessment Set D - Aptitude & Real Estate', 'Logical, Quantitative, English, Real Estate Basics, Sales & Negotiation', '11', '1', '2026-10-17 14:03:11'),
  ('5', 'Set E', 'Assessment Set E - Aptitude & Real Estate', 'Logical, Quantitative, English, Real Estate Basics, Sales & Negotiation', '11', '1', '2026-10-17 14:03:11');

-- --------------------------------------------------------
-- Table structure for `attempt_answers`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `attempt_answers`;
CREATE TABLE `attempt_answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attempt_id` int(10) unsigned NOT NULL,
  `question_id` int(10) unsigned NOT NULL,
  `selected_option_id` int(10) unsigned DEFAULT NULL,
  `is_marked_for_review` tinyint(1) NOT NULL DEFAULT 0,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `marks_awarded` decimal(5,2) NOT NULL DEFAULT 0.00,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_attempt_question` (`attempt_id`,`question_id`),
  KEY `fk_aa_q` (`question_id`),
  CONSTRAINT `fk_aa_attempt` FOREIGN KEY (`attempt_id`) REFERENCES `test_attempts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_aa_q` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `attempt_answers`
INSERT INTO `attempt_answers` (`id`, `attempt_id`, `question_id`, `selected_option_id`, `is_marked_for_review`, `is_correct`, `marks_awarded`, `updated_at`) VALUES
  ('1', '10', '61', '242', '0', '1', '1.00', '2026-10-17 15:31:05'),
  ('2', '10', '62', '246', '0', '1', '1.00', '2026-10-17 15:31:05'),
  ('3', '10', '63', '250', '0', '1', '1.00', '2026-10-17 15:31:05'),
  ('4', '10', '64', '254', '0', '1', '1.00', '2026-10-17 15:31:05'),
  ('5', '10', '65', '258', '0', '1', '1.00', '2026-10-17 15:31:05'),
  ('6', '10', '66', '262', '0', '1', '1.00', '2026-10-17 15:31:05'),
  ('7', '10', '67', '266', '0', '1', '1.00', '2026-10-17 15:31:05'),
  ('8', '10', '68', '270', '0', '1', '1.00', '2026-10-17 15:31:05'),
  ('9', '10', '69', '274', '0', '1', '1.00', '2026-10-17 15:31:05'),
  ('10', '10', '70', '278', '0', '1', '1.00', '2026-10-17 15:31:05'),
  ('11', '10', '71', '284', '0', '0', '0.00', '2026-10-17 15:31:04'),
  ('12', '10', '72', '288', '0', '0', '0.00', '2026-10-17 15:31:04'),
  ('13', '10', '73', '292', '0', '0', '0.00', '2026-10-17 15:31:05'),
  ('14', '10', '74', '296', '0', '0', '0.00', '2026-10-17 15:31:05'),
  ('15', '10', '75', '300', '0', '0', '0.00', '2026-10-17 15:31:05'),
  ('16', '11', '61', '242', '0', '1', '1.00', '2026-10-17 15:31:31'),
  ('17', '11', '62', '246', '0', '1', '1.00', '2026-10-17 15:31:31'),
  ('18', '11', '63', '250', '0', '1', '1.00', '2026-10-17 15:31:31'),
  ('19', '11', '64', '254', '0', '1', '1.00', '2026-10-17 15:31:31'),
  ('20', '11', '65', '258', '0', '1', '1.00', '2026-10-17 15:31:31'),
  ('21', '11', '66', '262', '0', '1', '1.00', '2026-10-17 15:31:32'),
  ('22', '11', '67', '266', '0', '1', '1.00', '2026-10-17 15:31:32'),
  ('23', '11', '68', '270', '0', '1', '1.00', '2026-10-17 15:31:32'),
  ('24', '11', '69', '274', '0', '1', '1.00', '2026-10-17 15:31:32'),
  ('25', '11', '70', '278', '0', '1', '1.00', '2026-10-17 15:31:32'),
  ('26', '11', '71', '284', '0', '0', '0.00', '2026-10-17 15:31:30'),
  ('27', '11', '72', '288', '0', '0', '0.00', '2026-10-17 15:31:31'),
  ('28', '11', '73', '292', '0', '0', '0.00', '2026-10-17 15:31:31'),
  ('29', '11', '74', '296', '0', '0', '0.00', '2026-10-17 15:31:31'),
  ('30', '11', '75', '300', '0', '0', '0.00', '2026-10-17 15:31:31'),
  ('31', '12', '46', '182', '0', '1', '1.00', '2026-10-17 16:00:14'),
  ('32', '12', '47', '186', '0', '1', '1.00', '2026-10-17 16:00:14'),
  ('33', '12', '48', '190', '0', '1', '1.00', '2026-10-17 16:00:14'),
  ('34', '12', '49', '194', '0', '1', '1.00', '2026-10-17 16:00:14'),
  ('35', '12', '50', '198', '0', '1', '1.00', '2026-10-17 16:00:14'),
  ('36', '12', '51', '202', '0', '1', '1.00', '2026-10-17 16:00:14'),
  ('37', '12', '52', '206', '0', '1', '1.00', '2026-10-17 16:00:14'),
  ('38', '12', '53', '210', '0', '1', '1.00', '2026-10-17 16:00:14'),
  ('39', '12', '54', '214', '0', '1', '1.00', '2026-10-17 16:00:14'),
  ('40', '12', '55', '218', '0', '1', '1.00', '2026-10-17 16:00:14'),
  ('41', '12', '56', '224', '0', '0', '0.00', '2026-10-17 16:00:13'),
  ('42', '12', '57', '228', '0', '0', '0.00', '2026-10-17 16:00:13'),
  ('43', '12', '58', '232', '0', '0', '0.00', '2026-10-17 16:00:13'),
  ('44', '12', '59', '236', '0', '0', '0.00', '2026-10-17 16:00:13'),
  ('45', '12', '60', '240', '0', '0', '0.00', '2026-10-17 16:00:13'),
  ('46', '13', '61', NULL, '0', '0', '0.00', '2026-10-17 16:42:19'),
  ('47', '13', '62', NULL, '0', '0', '0.00', '2026-10-17 16:42:19'),
  ('48', '13', '63', NULL, '0', '0', '0.00', '2026-10-17 16:42:19'),
  ('49', '13', '64', NULL, '0', '0', '0.00', '2026-10-17 16:42:19'),
  ('50', '13', '65', NULL, '0', '0', '0.00', '2026-10-17 16:42:19'),
  ('51', '13', '66', NULL, '0', '0', '0.00', '2026-10-17 16:42:19'),
  ('52', '13', '67', NULL, '0', '0', '0.00', '2026-10-17 16:42:19'),
  ('53', '13', '68', NULL, '0', '0', '0.00', '2026-10-17 16:42:54'),
  ('54', '13', '69', NULL, '0', '0', '0.00', '2026-10-17 16:42:19'),
  ('55', '13', '70', NULL, '0', '0', '0.00', '2026-10-17 16:42:19'),
  ('56', '13', '71', NULL, '0', '0', '0.00', '2026-10-17 16:42:19'),
  ('57', '13', '72', NULL, '0', '0', '0.00', '2026-10-17 16:42:19'),
  ('58', '13', '73', NULL, '0', '0', '0.00', '2026-10-17 16:42:19'),
  ('59', '13', '74', NULL, '0', '0', '0.00', '2026-10-17 16:42:19'),
  ('60', '13', '75', NULL, '0', '0', '0.00', '2026-10-17 16:42:19'),
  ('61', '14', '31', NULL, '0', '0', '0.00', '2026-10-17 17:09:33'),
  ('62', '14', '32', NULL, '0', '0', '0.00', '2026-10-17 17:09:33'),
  ('63', '14', '33', NULL, '0', '0', '0.00', '2026-10-17 17:09:33'),
  ('64', '14', '34', NULL, '0', '0', '0.00', '2026-10-17 17:09:33'),
  ('65', '14', '35', NULL, '0', '0', '0.00', '2026-10-17 17:09:33'),
  ('66', '14', '36', NULL, '0', '0', '0.00', '2026-10-17 17:09:33'),
  ('67', '14', '37', NULL, '0', '0', '0.00', '2026-10-17 17:09:33'),
  ('68', '14', '38', NULL, '0', '0', '0.00', '2026-10-17 17:09:33'),
  ('69', '14', '39', NULL, '0', '0', '0.00', '2026-10-17 17:09:33'),
  ('70', '14', '40', NULL, '0', '0', '0.00', '2026-10-17 17:09:33'),
  ('71', '14', '41', NULL, '0', '0', '0.00', '2026-10-17 17:09:33'),
  ('72', '14', '42', NULL, '0', '0', '0.00', '2026-10-17 17:09:33'),
  ('73', '14', '43', NULL, '0', '0', '0.00', '2026-10-17 17:09:33'),
  ('74', '14', '44', NULL, '0', '0', '0.00', '2026-10-17 17:09:33'),
  ('75', '14', '45', NULL, '0', '0', '0.00', '2026-10-17 17:09:33'),
  ('76', '15', '16', NULL, '0', '0', '0.00', '2026-10-18 18:30:54'),
  ('77', '15', '17', NULL, '0', '0', '0.00', '2026-10-18 18:30:54'),
  ('78', '15', '18', NULL, '0', '0', '0.00', '2026-10-18 18:30:54'),
  ('79', '15', '19', NULL, '0', '0', '0.00', '2026-10-18 18:30:55'),
  ('80', '15', '20', NULL, '0', '0', '0.00', '2026-10-18 18:30:55'),
  ('81', '15', '21', NULL, '0', '0', '0.00', '2026-10-18 18:30:55'),
  ('82', '15', '22', NULL, '0', '0', '0.00', '2026-10-18 18:30:55'),
  ('83', '15', '23', NULL, '0', '0', '0.00', '2026-10-18 18:30:55'),
  ('84', '15', '24', NULL, '0', '0', '0.00', '2026-10-18 18:30:55'),
  ('85', '15', '25', NULL, '0', '0', '0.00', '2026-10-18 18:30:55'),
  ('86', '15', '26', NULL, '0', '0', '0.00', '2026-10-18 18:30:55'),
  ('87', '15', '27', NULL, '0', '0', '0.00', '2026-10-18 18:30:55'),
  ('88', '15', '28', NULL, '0', '0', '0.00', '2026-10-18 18:30:55'),
  ('89', '15', '29', NULL, '0', '0', '0.00', '2026-10-18 18:30:55'),
  ('90', '15', '30', NULL, '0', '0', '0.00', '2026-10-18 18:30:55'),
  ('91', '16', '61', '241', '0', '0', '0.00', '2026-10-18 18:31:54'),
  ('92', '16', '62', NULL, '0', '0', '0.00', '2026-10-18 18:31:52'),
  ('93', '16', '63', NULL, '0', '0', '0.00', '2026-10-18 18:31:52'),
  ('94', '16', '64', NULL, '0', '0', '0.00', '2026-10-18 18:31:52'),
  ('95', '16', '65', NULL, '0', '0', '0.00', '2026-10-18 18:31:52'),
  ('96', '16', '66', NULL, '0', '0', '0.00', '2026-10-18 18:31:52'),
  ('97', '16', '67', NULL, '0', '0', '0.00', '2026-10-18 18:31:52'),
  ('98', '16', '68', NULL, '0', '0', '0.00', '2026-10-18 18:31:52'),
  ('99', '16', '69', NULL, '0', '0', '0.00', '2026-10-18 18:31:52'),
  ('100', '16', '70', NULL, '0', '0', '0.00', '2026-10-18 18:31:52'),
  ('101', '16', '71', NULL, '0', '0', '0.00', '2026-10-18 18:31:52'),
  ('102', '16', '72', NULL, '0', '0', '0.00', '2026-10-18 18:31:52'),
  ('103', '16', '73', NULL, '0', '0', '0.00', '2026-10-18 18:31:52'),
  ('104', '16', '74', NULL, '0', '0', '0.00', '2026-10-18 18:31:52'),
  ('105', '16', '75', NULL, '0', '0', '0.00', '2026-10-18 18:31:52'),
  ('106', '17', '46', '181', '0', '0', '0.00', '2026-10-18 18:32:40'),
  ('107', '17', '47', NULL, '0', '0', '0.00', '2026-10-18 18:32:38'),
  ('108', '17', '48', NULL, '0', '0', '0.00', '2026-10-18 18:32:38'),
  ('109', '17', '49', NULL, '0', '0', '0.00', '2026-10-18 18:32:38'),
  ('110', '17', '50', NULL, '0', '0', '0.00', '2026-10-18 18:32:38'),
  ('111', '17', '51', NULL, '0', '0', '0.00', '2026-10-18 18:32:38'),
  ('112', '17', '52', NULL, '0', '0', '0.00', '2026-10-18 18:32:38'),
  ('113', '17', '53', NULL, '0', '0', '0.00', '2026-10-18 18:32:38'),
  ('114', '17', '54', NULL, '0', '0', '0.00', '2026-10-18 18:32:38'),
  ('115', '17', '55', NULL, '0', '0', '0.00', '2026-10-18 18:32:38'),
  ('116', '17', '56', NULL, '0', '0', '0.00', '2026-10-18 18:32:38'),
  ('117', '17', '57', NULL, '0', '0', '0.00', '2026-10-18 18:32:38'),
  ('118', '17', '58', NULL, '0', '0', '0.00', '2026-10-18 18:32:38'),
  ('119', '17', '59', NULL, '0', '0', '0.00', '2026-10-18 18:32:38'),
  ('120', '17', '60', NULL, '0', '0', '0.00', '2026-10-18 18:32:38'),
  ('121', '18', '31', '121', '0', '0', '0.00', '2026-10-18 18:34:16'),
  ('122', '18', '32', NULL, '0', '0', '0.00', '2026-10-18 18:34:14'),
  ('123', '18', '33', NULL, '0', '0', '0.00', '2026-10-18 18:34:14'),
  ('124', '18', '34', NULL, '0', '0', '0.00', '2026-10-18 18:34:14'),
  ('125', '18', '35', NULL, '0', '0', '0.00', '2026-10-18 18:34:14'),
  ('126', '18', '36', NULL, '0', '0', '0.00', '2026-10-18 18:34:14'),
  ('127', '18', '37', NULL, '0', '0', '0.00', '2026-10-18 18:34:14'),
  ('128', '18', '38', NULL, '0', '0', '0.00', '2026-10-18 18:34:14'),
  ('129', '18', '39', NULL, '0', '0', '0.00', '2026-10-18 18:34:14'),
  ('130', '18', '40', NULL, '0', '0', '0.00', '2026-10-18 18:34:14'),
  ('131', '18', '41', NULL, '0', '0', '0.00', '2026-10-18 18:34:14'),
  ('132', '18', '42', NULL, '0', '0', '0.00', '2026-10-18 18:34:14'),
  ('133', '18', '43', NULL, '0', '0', '0.00', '2026-10-18 18:34:14'),
  ('134', '18', '44', NULL, '0', '0', '0.00', '2026-10-18 18:34:14'),
  ('135', '18', '45', NULL, '0', '0', '0.00', '2026-10-18 18:34:14');

-- --------------------------------------------------------
-- Table structure for `audit_logs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(100) NOT NULL,
  `entity_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_audit_user` (`user_id`),
  KEY `idx_audit_action` (`action`),
  KEY `idx_audit_entity` (`entity_type`,`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=274 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `audit_logs`
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `ip_address`, `user_agent`, `old_values`, `new_values`, `created_at`) VALUES
  ('1', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9711', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-17 12:26:31'),
  ('2', '4', 'candidate_registered', 'users', '4', '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9711', NULL, '{\"email\":\"abhishek.sharma@test.com\",\"name\":\"Abhishek Sharma\"}', '2026-10-17 12:26:58'),
  ('3', '5', 'candidate_registered', 'users', '5', '127.0.0.1', '', NULL, '{\"email\":\"kunal.1792220474@example.com\",\"name\":\"Kunal Sharma\"}', '2026-10-17 12:31:15'),
  ('4', '5', 'email_verified', 'users', '5', '127.0.0.1', '', NULL, '{\"method\":\"otp\"}', '2026-10-17 12:31:16'),
  ('5', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-17 12:52:57'),
  ('6', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-17 12:53:21'),
  ('7', NULL, 'login_failed', 'users', '4', '127.0.0.1', '', NULL, '{\"email\":\"abhishek.sharma@test.com\"}', '2026-10-17 12:53:43'),
  ('8', NULL, 'login_failed', 'users', '4', '127.0.0.1', '', NULL, '{\"email\":\"abhishek.sharma@test.com\"}', '2026-10-17 12:54:07'),
  ('9', NULL, 'login_failed', 'users', '4', '127.0.0.1', '', NULL, '{\"email\":\"abhishek.sharma@test.com\"}', '2026-10-17 12:54:26'),
  ('10', '5', 'login_success', 'users', '5', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-17 12:55:25'),
  ('11', NULL, 'admin_login_failed', 'users', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"admin@realtypathsala.in\"}', '2026-10-17 13:00:33'),
  ('12', NULL, 'admin_login_failed', 'users', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"admin@realtypathsala.in\"}', '2026-10-17 13:00:54'),
  ('13', NULL, 'admin_login_failed', 'users', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"admin@realtypathsala.in\"}', '2026-10-17 13:01:11'),
  ('14', NULL, 'admin_login_failed', 'users', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"admin@realtysmartzpathsala.in\",\"ip\":\"127.0.0.1\"}', '2026-10-17 13:20:02'),
  ('15', NULL, 'admin_login_failed', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9711', NULL, '{\"email\":\"admin@realtysmartzpathshala.in\",\"ip\":\"127.0.0.1\"}', '2026-10-17 13:20:41'),
  ('16', NULL, 'admin_login_failed', 'users', '1', '127.0.0.1', '', NULL, '{\"email\":\"admin@realtysmartzpathshala.in\",\"ip\":\"127.0.0.1\"}', '2026-10-17 13:21:38'),
  ('17', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-17 13:21:38'),
  ('18', NULL, 'admin_login_failed', 'users', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"admin@realtysmartzpathsala.in\",\"ip\":\"127.0.0.1\"}', '2026-10-17 13:22:29'),
  ('19', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-17 13:51:04'),
  ('20', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-17 14:15:24'),
  ('21', '1', 'status_updated', 'users', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"status\":\"registered\"}', '{\"status\":\"under_review\",\"comments\":\"\"}', '2026-10-17 14:37:19'),
  ('22', '1', 'status_updated', 'users', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"status\":\"under_review\"}', '{\"status\":\"approved\",\"comments\":\"\"}', '2026-10-17 14:37:32'),
  ('23', '18', 'candidate_registered', 'users', '18', '127.0.0.1', '', NULL, '{\"email\":\"phase2_test_candidate@realtysmartzpathshala.in\",\"name\":\"Aditya Sharma\",\"mobile\":\"9876543299\"}', '2026-10-17 14:50:42'),
  ('24', '18', 'email_verified', 'users', '18', '127.0.0.1', '', NULL, '{\"method\":\"otp\"}', '2026-10-17 14:50:43'),
  ('25', '19', 'candidate_registered', 'users', '19', '127.0.0.1', '', NULL, '{\"email\":\"phase2_candidate_1792228894@realtysmartzpathshala.in\",\"name\":\"Aditya Sharma\",\"mobile\":\"9876115041\"}', '2026-10-17 14:51:35'),
  ('26', '19', 'email_verified', 'users', '19', '127.0.0.1', '', NULL, '{\"method\":\"otp\"}', '2026-10-17 14:51:35'),
  ('27', '20', 'candidate_registered', 'users', '20', '127.0.0.1', '', NULL, '{\"email\":\"phase2_candidate_1792228915@realtysmartzpathshala.in\",\"name\":\"Aditya Sharma\",\"mobile\":\"9876284391\"}', '2026-10-17 14:51:56'),
  ('28', '20', 'email_verified', 'users', '20', '127.0.0.1', '', NULL, '{\"method\":\"otp\"}', '2026-10-17 14:51:56'),
  ('29', '20', 'status_updated', 'users', '20', '127.0.0.1', '', '{\"status\":\"email_verified\"}', '{\"status\":\"documents_submitted\",\"comments\":\"Candidate completed 4-step onboarding wizard and submitted documents.\"}', '2026-10-17 14:52:01'),
  ('30', '20', 'onboarding_submitted', 'profiles', '18', '127.0.0.1', '', NULL, '{\"signature_type\":\"typed\",\"timestamp\":\"2026-10-17 14:52:00\"}', '2026-10-17 14:52:01'),
  ('31', '21', 'candidate_registered', 'users', '21', '127.0.0.1', '', NULL, '{\"email\":\"phase3_tester_1792230113@realtysmartzpathshala.in\",\"name\":\"Rohan Varma\",\"mobile\":\"9879975969\"}', '2026-10-17 15:11:54'),
  ('32', '22', 'candidate_registered', 'users', '22', '127.0.0.1', '', NULL, '{\"email\":\"phase3_tester_1792230253@realtysmartzpathshala.in\",\"name\":\"Rohan Varma\",\"mobile\":\"9876767486\"}', '2026-10-17 15:14:14'),
  ('33', '23', 'candidate_registered', 'users', '23', '127.0.0.1', '', NULL, '{\"email\":\"phase3_tester_1792230311@realtysmartzpathshala.in\",\"name\":\"Rohan Varma\",\"mobile\":\"9876615250\"}', '2026-10-17 15:15:12'),
  ('34', '1', 'identity_revealed', 'profiles', '23', '127.0.0.1', '', NULL, '{\"target_candidate_id\":23,\"action\":\"reveal_sensitive_identity\",\"revealed_fields\":[\"aadhaar\",\"pan\"]}', '2026-10-17 15:15:15'),
  ('35', '24', 'candidate_registered', 'users', '24', '127.0.0.1', '', NULL, '{\"email\":\"phase3_tester_1792230330@realtysmartzpathshala.in\",\"name\":\"Rohan Varma\",\"mobile\":\"9874188408\"}', '2026-10-17 15:15:30'),
  ('36', '1', 'identity_revealed', 'profiles', '24', '127.0.0.1', '', NULL, '{\"target_candidate_id\":24,\"action\":\"reveal_sensitive_identity\",\"revealed_fields\":[\"aadhaar\",\"pan\"]}', '2026-10-17 15:15:32'),
  ('37', '25', 'candidate_registered', 'users', '25', '127.0.0.1', '', NULL, '{\"email\":\"phase3_tester_1792230371@realtysmartzpathshala.in\",\"name\":\"Rohan Varma\",\"mobile\":\"9878183149\"}', '2026-10-17 15:16:12'),
  ('38', '1', 'identity_revealed', 'profiles', '25', '127.0.0.1', '', NULL, '{\"target_candidate_id\":25,\"action\":\"reveal_sensitive_identity\",\"revealed_fields\":[\"aadhaar\",\"pan\"]}', '2026-10-17 15:16:14'),
  ('39', '26', 'candidate_registered', 'users', '26', '127.0.0.1', '', NULL, '{\"email\":\"phase3_tester_1792230403@realtysmartzpathshala.in\",\"name\":\"Rohan Varma\",\"mobile\":\"9878777193\"}', '2026-10-17 15:16:44'),
  ('40', '1', 'identity_revealed', 'profiles', '26', '127.0.0.1', '', NULL, '{\"target_candidate_id\":26,\"action\":\"reveal_sensitive_identity\",\"revealed_fields\":[\"aadhaar\",\"pan\"]}', '2026-10-17 15:16:46'),
  ('41', '27', 'candidate_registered', 'users', '27', '127.0.0.1', '', NULL, '{\"email\":\"phase4_candidate_1792231259@realtysmartz.in\",\"name\":\"Aptitude Test Candidate\",\"mobile\":\"9876543210\"}', '2026-10-17 15:31:00'),
  ('42', '27', 'test_started', 'test_attempts', '10', '127.0.0.1', '', NULL, '{\"user_id\":27,\"test_id\":1,\"set_id\":5,\"set_code\":\"Set E\"}', '2026-10-17 15:31:01'),
  ('43', '27', 'anti_cheat_warning', 'test_attempts', '10', '127.0.0.1', '', NULL, '{\"type\":\"tab_switch\",\"counts\":{\"tab_switch_count\":1,\"fullscreen_exit_count\":0}}', '2026-10-17 15:31:02'),
  ('44', '27', 'anti_cheat_warning', 'test_attempts', '10', '127.0.0.1', '', NULL, '{\"type\":\"tab_switch\",\"counts\":{\"tab_switch_count\":2,\"fullscreen_exit_count\":0}}', '2026-10-17 15:31:02'),
  ('45', '27', 'status_updated', 'users', '27', '127.0.0.1', '', '{\"status\":\"documents_submitted\"}', '{\"status\":\"test_completed\",\"comments\":\"Aptitude Assessment completed with score 10\\/15 (66.67%) - PASSED\"}', '2026-10-17 15:31:06'),
  ('46', '27', 'test_finalized', 'test_attempts', '10', '127.0.0.1', '', NULL, '{\"score\":10,\"percentage\":66.67,\"passed\":1}', '2026-10-17 15:31:06'),
  ('47', '28', 'candidate_registered', 'users', '28', '127.0.0.1', '', NULL, '{\"email\":\"phase4_candidate_1792231284@realtysmartz.in\",\"name\":\"Aptitude Test Candidate\",\"mobile\":\"9876543210\"}', '2026-10-17 15:31:24'),
  ('48', '28', 'test_started', 'test_attempts', '11', '127.0.0.1', '', NULL, '{\"user_id\":28,\"test_id\":1,\"set_id\":5,\"set_code\":\"Set E\"}', '2026-10-17 15:31:26'),
  ('49', '28', 'anti_cheat_warning', 'test_attempts', '11', '127.0.0.1', '', NULL, '{\"type\":\"tab_switch\",\"counts\":{\"tab_switch_count\":1,\"fullscreen_exit_count\":0}}', '2026-10-17 15:31:28'),
  ('50', '28', 'anti_cheat_warning', 'test_attempts', '11', '127.0.0.1', '', NULL, '{\"type\":\"tab_switch\",\"counts\":{\"tab_switch_count\":2,\"fullscreen_exit_count\":0}}', '2026-10-17 15:31:28'),
  ('51', '28', 'status_updated', 'users', '28', '127.0.0.1', '', '{\"status\":\"documents_submitted\"}', '{\"status\":\"test_completed\",\"comments\":\"Aptitude Assessment completed with score 10\\/15 (66.67%) - PASSED\"}', '2026-10-17 15:31:32'),
  ('52', '28', 'test_finalized', 'test_attempts', '11', '127.0.0.1', '', NULL, '{\"score\":10,\"percentage\":66.67,\"passed\":1}', '2026-10-17 15:31:32'),
  ('53', '29', 'candidate_registered', 'users', '29', '127.0.0.1', '', NULL, '{\"email\":\"phase4_candidate_1792233007@realtysmartz.in\",\"name\":\"Aptitude Test Candidate\",\"mobile\":\"9876543210\"}', '2026-10-17 16:00:08'),
  ('54', '29', 'test_started', 'test_attempts', '12', '127.0.0.1', '', NULL, '{\"user_id\":29,\"test_id\":1,\"set_id\":4,\"set_code\":\"Set D\"}', '2026-10-17 16:00:09'),
  ('55', '29', 'anti_cheat_warning', 'test_attempts', '12', '127.0.0.1', '', NULL, '{\"type\":\"tab_switch\",\"counts\":{\"tab_switch_count\":1,\"fullscreen_exit_count\":0}}', '2026-10-17 16:00:10'),
  ('56', '29', 'anti_cheat_warning', 'test_attempts', '12', '127.0.0.1', '', NULL, '{\"type\":\"tab_switch\",\"counts\":{\"tab_switch_count\":2,\"fullscreen_exit_count\":0}}', '2026-10-17 16:00:11'),
  ('57', '29', 'status_updated', 'users', '29', '127.0.0.1', '', '{\"status\":\"documents_submitted\"}', '{\"status\":\"test_completed\",\"comments\":\"Aptitude Assessment completed with score 10\\/15 (66.67%) - PASSED\"}', '2026-10-17 16:00:14'),
  ('58', '29', 'test_finalized', 'test_attempts', '12', '127.0.0.1', '', NULL, '{\"score\":10,\"percentage\":66.67,\"passed\":1}', '2026-10-17 16:00:14'),
  ('59', '30', 'candidate_registered', 'users', '30', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"abhishekab16390@gmail.com\",\"name\":\"Abhishek\",\"mobile\":\"9306098396\"}', '2026-10-17 16:17:00'),
  ('60', '30', 'email_verified', 'users', '30', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"method\":\"otp\"}', '2026-10-17 16:27:04'),
  ('61', '30', 'document_uploaded', 'documents', '31', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"type\":\"profile_photo\",\"version\":1,\"size\":89248}', '2026-10-17 16:28:49'),
  ('62', '30', 'document_uploaded', 'documents', '32', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"type\":\"aadhaar_front\",\"version\":1,\"size\":410700}', '2026-10-17 16:29:18'),
  ('63', '30', 'document_uploaded', 'documents', '33', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1', NULL, '{\"type\":\"aadhaar_back\",\"version\":1,\"size\":410700}', '2026-10-17 16:39:36'),
  ('64', '30', 'document_uploaded', 'documents', '34', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1', NULL, '{\"type\":\"pan_card\",\"version\":1,\"size\":373905}', '2026-10-17 16:40:22'),
  ('65', '30', 'status_updated', 'users', '30', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1', '{\"status\":\"email_verified\"}', '{\"status\":\"documents_submitted\",\"comments\":\"Candidate completed 4-step onboarding wizard and submitted documents.\"}', '2026-10-17 16:42:08'),
  ('66', '30', 'onboarding_submitted', 'profiles', '28', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1', NULL, '{\"signature_type\":\"typed\",\"timestamp\":\"2026-10-17 16:42:07\"}', '2026-10-17 16:42:08'),
  ('67', '30', 'test_started', 'test_attempts', '13', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1', NULL, '{\"user_id\":30,\"test_id\":1,\"set_id\":5,\"set_code\":\"Set E\"}', '2026-10-17 16:42:19'),
  ('68', '30', 'anti_cheat_warning', 'test_attempts', '13', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1', NULL, '{\"type\":\"window_blur\",\"counts\":{\"tab_switch_count\":1,\"fullscreen_exit_count\":0}}', '2026-10-17 16:42:28'),
  ('69', '30', 'status_updated', 'users', '30', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1', '{\"status\":\"documents_submitted\"}', '{\"status\":\"test_completed\",\"comments\":\"Aptitude Assessment completed with score 0\\/15 (0%) - FAILED\"}', '2026-10-17 16:43:01'),
  ('70', '30', 'test_finalized', 'test_attempts', '13', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1', NULL, '{\"score\":0,\"percentage\":0,\"passed\":0}', '2026-10-17 16:43:01'),
  ('71', '30', 'logout', 'users', '30', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1', NULL, NULL, '2026-10-17 17:05:51'),
  ('72', '1', 'login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-17 17:09:18'),
  ('73', '1', 'logout', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, NULL, '2026-10-17 17:09:23'),
  ('74', '4', 'login_success', 'users', '4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-17 17:09:28'),
  ('75', '4', 'test_started', 'test_attempts', '14', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":4,\"test_id\":1,\"set_id\":3,\"set_code\":\"Set C\"}', '2026-10-17 17:09:33'),
  ('76', '1', 'login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-17 17:10:49'),
  ('77', '1', 'logout', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, NULL, '2026-10-17 17:10:56'),
  ('78', '4', 'login_success', 'users', '4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-17 17:11:00'),
  ('79', '1', 'login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-17 17:12:49'),
  ('80', '1', 'logout', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, NULL, '2026-10-17 17:12:56'),
  ('81', '4', 'login_success', 'users', '4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-17 17:13:00'),
  ('82', '4', 'anti_cheat_warning', 'test_attempts', '14', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"type\":\"tab_switch\",\"counts\":{\"tab_switch_count\":1,\"fullscreen_exit_count\":0}}', '2026-10-17 17:13:03'),
  ('83', NULL, 'admin_login_failed', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"admin@realtysmartzpathshala.in\",\"ip\":\"127.0.0.1\"}', '2026-10-17 17:29:57'),
  ('84', '30', 'login_success', 'users', '30', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-17 17:32:23'),
  ('85', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 13:20:42'),
  ('86', '31', 'login_success', 'users', '31', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 13:20:49'),
  ('87', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 13:26:01'),
  ('88', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 13:30:10'),
  ('89', '4', 'login_success', 'users', '4', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 13:30:18'),
  ('90', '4', 'status_updated', 'users', '4', '127.0.0.1', '', '{\"status\":\"documents_submitted\"}', '{\"status\":\"test_completed\",\"comments\":\"Aptitude Assessment completed with score 0\\/15 (0%) - FAILED\"}', '2026-10-18 13:30:21'),
  ('91', '4', 'test_finalized', 'test_attempts', '14', '127.0.0.1', '', NULL, '{\"score\":0,\"percentage\":0,\"passed\":0}', '2026-10-18 13:30:22'),
  ('92', '31', 'login_success', 'users', '31', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 13:46:31'),
  ('93', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 13:46:32'),
  ('94', '31', 'login_success', 'users', '31', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 14:18:17'),
  ('95', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 14:18:18'),
  ('96', '31', 'login_success', 'users', '31', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 14:22:16'),
  ('97', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 14:22:17'),
  ('98', '31', 'login_success', 'users', '31', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 14:24:23'),
  ('99', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 14:24:24'),
  ('100', '31', 'login_success', 'users', '31', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 14:26:21'),
  ('101', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 14:26:22'),
  ('102', '32', 'candidate_registered', 'users', '32', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"amitrawat7867@gmail.com\",\"name\":\"Amit\",\"mobile\":\"8178635472\"}', '2026-10-18 14:46:50'),
  ('103', '32', 'email_verified', 'users', '32', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"method\":\"otp\"}', '2026-10-18 14:47:25'),
  ('104', '32', 'login_success', 'users', '32', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 15:17:39'),
  ('105', '32', 'login_success', 'users', '32', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 15:19:04'),
  ('106', '32', 'login_success', 'users', '32', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 15:21:00'),
  ('107', '32', 'login_success', 'users', '32', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 15:22:30'),
  ('108', '31', 'login_success', 'users', '31', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 15:48:37'),
  ('109', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 15:48:38'),
  ('110', '32', 'login_success', 'users', '32', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 15:50:37'),
  ('111', '32', 'login_success', 'users', '32', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 15:51:05'),
  ('112', '32', 'login_success', 'users', '32', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 15:52:29'),
  ('113', '32', 'login_success', 'users', '32', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 15:53:06'),
  ('114', '32', 'login_success', 'users', '32', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 15:53:37'),
  ('115', '32', 'login_success', 'users', '32', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 17:26:12'),
  ('116', '32', 'document_uploaded', 'documents', '35', '127.0.0.1', '', NULL, '{\"type\":\"aadhaar_front\",\"version\":1,\"size\":1364}', '2026-10-18 17:30:29'),
  ('117', NULL, 'admin_login_failed', 'users', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"admin@realtysmartzpathsala.in\",\"ip\":\"127.0.0.1\"}', '2026-10-18 17:31:09'),
  ('118', NULL, 'admin_login_failed', 'users', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"admin@realtysmartzpathsala.in\",\"ip\":\"127.0.0.1\"}', '2026-10-18 17:31:22'),
  ('119', NULL, 'admin_login_failed', 'users', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"admin@realtysmartzpathsala.in\",\"ip\":\"127.0.0.1\"}', '2026-10-18 17:31:45'),
  ('120', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 17:37:20'),
  ('121', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 17:37:21'),
  ('122', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 17:37:21'),
  ('123', '1', 'admin_login_success', 'users', '1', '127.0.0.1', '', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 17:37:22'),
  ('124', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 17:37:44'),
  ('125', '1', 'status_updated', 'users', '20', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"status\":\"documents_submitted\"}', '{\"status\":\"approved\",\"comments\":\"\"}', '2026-10-18 17:39:54'),
  ('126', '1', 'status_updated', 'users', '20', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"status\":\"approved\"}', '{\"status\":\"changes_requested\",\"comments\":\"\"}', '2026-10-18 17:40:08'),
  ('127', '1', 'status_updated', 'users', '20', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"status\":\"changes_requested\"}', '{\"status\":\"approved\",\"comments\":\"\"}', '2026-10-18 17:40:13'),
  ('128', '1', 'settings_updated', 'settings', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"keys\":[\"site_name\",\"site_tagline\",\"contact_phone\",\"contact_email\",\"contact_address\",\"stat_professionals_trained\",\"stat_live_projects\",\"stat_success_rate\",\"stat_placement_rate\",\"stat_years_active\",\"stat_avg_deal\",\"preloader_text\"]}', '2026-10-18 18:18:51'),
  ('129', '1', 'settings_updated', 'settings', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"keys\":[\"site_name\",\"site_tagline\",\"contact_phone\",\"contact_email\",\"contact_address\",\"stat_professionals_trained\",\"stat_live_projects\",\"stat_success_rate\",\"stat_placement_rate\",\"stat_years_active\",\"stat_avg_deal\",\"preloader_text\"]}', '2026-10-18 18:18:53'),
  ('130', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:21:55'),
  ('131', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:21:58'),
  ('132', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:22:02'),
  ('133', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:22:06'),
  ('134', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:22:10'),
  ('135', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:22:13'),
  ('136', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:22:16'),
  ('137', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:22:18'),
  ('138', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:22:21'),
  ('139', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:22:24'),
  ('140', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:22:26'),
  ('141', '1', 'identity_revealed', 'users', '20', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"field\":\"aadhaar\",\"revealed_by\":\"admin@realtysmartzpathshala.in\",\"ip\":\"127.0.0.1\"}', '2026-10-18 18:22:29'),
  ('142', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:22:31'),
  ('143', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:23:03'),
  ('144', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:24:27'),
  ('145', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:24:30'),
  ('146', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:24:33'),
  ('147', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:24:36'),
  ('148', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:24:39'),
  ('149', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:24:53'),
  ('150', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:25:20'),
  ('151', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:25:36'),
  ('152', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:26:05'),
  ('153', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:26:30'),
  ('154', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:26:32'),
  ('155', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:26:35'),
  ('156', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:26:39'),
  ('157', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:26:42'),
  ('158', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:27:04'),
  ('159', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:27:06'),
  ('160', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:27:09'),
  ('161', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:27:12'),
  ('162', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:27:16'),
  ('163', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:27:34'),
  ('164', '1', 'identity_revealed', 'users', '20', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"field\":\"aadhaar\",\"revealed_by\":\"admin@realtysmartzpathshala.in\",\"ip\":\"127.0.0.1\"}', '2026-10-18 18:27:37'),
  ('165', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:27:38'),
  ('166', '1', 'download_full_file_pdf', 'users', '20', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Aditya_Sharma-20-file.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-18 18:27:41'),
  ('167', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:29:12'),
  ('168', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:30:02'),
  ('169', '1', 'create_guest_test_link', 'guest_test_links', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":null,\"guest_email\":\"\",\"expires_at\":\"2026-10-20 18:30:03\",\"ip\":\"127.0.0.1\"}', '2026-10-18 18:30:03'),
  ('170', '33', 'guest_candidate_identified', 'guest_test_links', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":33,\"name\":\"Vikram Malhotra\",\"email\":\"vikram.guest@example.com\",\"ip\":\"127.0.0.1\"}', '2026-10-18 18:30:05'),
  ('171', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:30:49'),
  ('172', '1', 'create_guest_test_link', 'guest_test_links', '2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":null,\"guest_email\":\"\",\"expires_at\":\"2026-10-20 18:30:50\",\"ip\":\"127.0.0.1\"}', '2026-10-18 18:30:51'),
  ('173', '33', 'guest_candidate_identified', 'guest_test_links', '2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":33,\"name\":\"Vikram Malhotra\",\"email\":\"vikram.guest@example.com\",\"ip\":\"127.0.0.1\"}', '2026-10-18 18:30:53'),
  ('174', '33', 'guest_test_started', 'test_attempts', '15', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"guest_test_link_id\":2,\"user_id\":33,\"set_id\":2,\"ip\":\"127.0.0.1\"}', '2026-10-18 18:30:55'),
  ('175', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:31:47'),
  ('176', '1', 'create_guest_test_link', 'guest_test_links', '3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":null,\"guest_email\":\"\",\"expires_at\":\"2026-10-20 18:31:48\",\"ip\":\"127.0.0.1\"}', '2026-10-18 18:31:49'),
  ('177', '33', 'guest_candidate_identified', 'guest_test_links', '3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":33,\"name\":\"Vikram Malhotra\",\"email\":\"vikram.guest@example.com\",\"ip\":\"127.0.0.1\"}', '2026-10-18 18:31:51'),
  ('178', '33', 'guest_test_started', 'test_attempts', '16', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"guest_test_link_id\":3,\"user_id\":33,\"set_id\":5,\"ip\":\"127.0.0.1\"}', '2026-10-18 18:31:53'),
  ('179', '33', 'status_updated', 'users', '33', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"status\":\"registered\"}', '{\"status\":\"test_completed\",\"comments\":\"Aptitude Assessment completed with score 0\\/15 (0%) - FAILED\"}', '2026-10-18 18:31:57'),
  ('180', '33', 'test_finalized', 'test_attempts', '16', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"score\":0,\"percentage\":0,\"passed\":0}', '2026-10-18 18:31:57'),
  ('181', '33', 'guest_test_submitted', 'test_attempts', '16', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"guest_test_link_id\":3,\"ip\":\"127.0.0.1\"}', '2026-10-18 18:31:57'),
  ('182', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:32:32'),
  ('183', '1', 'create_guest_test_link', 'guest_test_links', '4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":null,\"guest_email\":\"\",\"expires_at\":\"2026-10-20 18:32:33\",\"ip\":\"127.0.0.1\"}', '2026-10-18 18:32:34'),
  ('184', '33', 'guest_candidate_identified', 'guest_test_links', '4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":33,\"name\":\"Vikram Malhotra\",\"email\":\"vikram.guest@example.com\",\"ip\":\"127.0.0.1\"}', '2026-10-18 18:32:36'),
  ('185', '33', 'guest_test_started', 'test_attempts', '17', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"guest_test_link_id\":4,\"user_id\":33,\"set_id\":4,\"ip\":\"127.0.0.1\"}', '2026-10-18 18:32:38'),
  ('186', '33', 'status_updated', 'users', '33', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"status\":\"test_completed\"}', '{\"status\":\"test_completed\",\"comments\":\"Aptitude Assessment completed with score 0\\/15 (0%) - FAILED\"}', '2026-10-18 18:32:43'),
  ('187', '33', 'test_finalized', 'test_attempts', '17', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"score\":0,\"percentage\":0,\"passed\":0}', '2026-10-18 18:32:43'),
  ('188', '33', 'guest_test_submitted', 'test_attempts', '17', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"guest_test_link_id\":4,\"ip\":\"127.0.0.1\"}', '2026-10-18 18:32:43'),
  ('189', '1', 'revoke_guest_test_link', 'guest_test_links', '4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:32:46'),
  ('190', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:32:54'),
  ('191', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:32:57'),
  ('192', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:32:59'),
  ('193', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:02'),
  ('194', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:05'),
  ('195', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:14'),
  ('196', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:16'),
  ('197', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:19'),
  ('198', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:23'),
  ('199', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:26'),
  ('200', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:29'),
  ('201', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:32'),
  ('202', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:34'),
  ('203', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:37'),
  ('204', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:40'),
  ('205', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:42'),
  ('206', '1', 'identity_revealed', 'users', '20', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"field\":\"aadhaar\",\"revealed_by\":\"admin@realtysmartzpathshala.in\",\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:45'),
  ('207', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:46'),
  ('208', '1', 'download_full_file_pdf', 'users', '20', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Aditya_Sharma-20-file.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:49'),
  ('209', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:50'),
  ('210', '1', 'create_guest_test_link', 'guest_test_links', '5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":null,\"guest_email\":\"\",\"expires_at\":\"2026-10-20 18:33:51\",\"ip\":\"127.0.0.1\"}', '2026-10-18 18:33:52'),
  ('211', '33', 'guest_candidate_identified', 'guest_test_links', '5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":33,\"name\":\"Vikram Malhotra\",\"email\":\"vikram.guest@example.com\",\"ip\":\"127.0.0.1\"}', '2026-10-18 18:34:12'),
  ('212', '33', 'guest_test_started', 'test_attempts', '18', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"guest_test_link_id\":5,\"user_id\":33,\"set_id\":3,\"ip\":\"127.0.0.1\"}', '2026-10-18 18:34:14'),
  ('213', '33', 'status_updated', 'users', '33', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"status\":\"test_completed\"}', '{\"status\":\"test_completed\",\"comments\":\"Aptitude Assessment completed with score 0\\/15 (0%) - FAILED\"}', '2026-10-18 18:34:20'),
  ('214', '33', 'test_finalized', 'test_attempts', '18', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"score\":0,\"percentage\":0,\"passed\":0}', '2026-10-18 18:34:20'),
  ('215', '33', 'guest_test_submitted', 'test_attempts', '18', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"guest_test_link_id\":5,\"ip\":\"127.0.0.1\"}', '2026-10-18 18:34:20'),
  ('216', '1', 'revoke_guest_test_link', 'guest_test_links', '5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-18 18:34:23'),
  ('217', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:33:08'),
  ('218', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:34:09'),
  ('219', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:35:36'),
  ('220', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:37:56'),
  ('221', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:39:15'),
  ('222', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:40:29'),
  ('223', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:42:14'),
  ('224', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:42:45'),
  ('225', '1', 'update_candidate', 'users', '33', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Vikram Malhotra\",\"joining_date\":\"2026-11-01\",\"ip\":\"127.0.0.1\"}', '2026-10-19 10:42:47'),
  ('226', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:44:26'),
  ('227', '1', 'update_candidate', 'users', '33', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Vikram Malhotra\",\"joining_date\":\"2026-11-01\",\"ip\":\"127.0.0.1\"}', '2026-10-19 10:44:28'),
  ('228', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:44:36'),
  ('229', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:44:45'),
  ('230', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:44:55'),
  ('231', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:49:34'),
  ('232', '1', 'update_candidate', 'users', '33', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Vikram Malhotra\",\"joining_date\":\"2026-11-01\",\"ip\":\"127.0.0.1\"}', '2026-10-19 10:49:36'),
  ('233', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:50:37'),
  ('234', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:51:40'),
  ('235', '1', 'update_candidate', 'users', '33', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Vikram Malhotra\",\"joining_date\":\"2026-12-15\",\"ip\":\"127.0.0.1\"}', '2026-10-19 10:51:44'),
  ('236', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:51:45'),
  ('237', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:53:28'),
  ('238', '1', 'update_candidate', 'users', '33', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Vikram Malhotra\",\"joining_date\":\"2026-11-01\",\"ip\":\"127.0.0.1\"}', '2026-10-19 10:53:31'),
  ('239', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:54:33'),
  ('240', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:55:31'),
  ('241', '1', 'update_candidate', 'users', '33', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Vikram Malhotra\",\"joining_date\":\"2026-11-01\",\"ip\":\"127.0.0.1\"}', '2026-10-19 10:55:33'),
  ('242', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:55:35'),
  ('243', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:55:38'),
  ('244', '1', 'update_candidate', 'users', '33', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Vikram Malhotra\",\"joining_date\":\"2026-12-15\",\"ip\":\"127.0.0.1\"}', '2026-10-19 10:55:42'),
  ('245', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:55:43'),
  ('246', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:56:55'),
  ('247', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:56:59'),
  ('248', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:57:03'),
  ('249', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:57:08'),
  ('250', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:57:12'),
  ('251', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:57:16'),
  ('252', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:57:19'),
  ('253', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:57:23'),
  ('254', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:57:28'),
  ('255', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 10:57:31'),
  ('256', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 11:31:48'),
  ('257', '1', 'logout', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, NULL, '2026-10-19 11:34:08'),
  ('258', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 11:54:41'),
  ('259', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 11:54:44'),
  ('260', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 11:54:47'),
  ('261', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 11:54:52'),
  ('262', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 11:54:56'),
  ('263', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 11:55:00'),
  ('264', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 11:55:04'),
  ('265', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 11:55:07'),
  ('266', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 11:55:10'),
  ('267', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 11:55:13'),
  ('268', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 11:55:17'),
  ('269', '1', 'update_candidate', 'users', '33', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Vikram Malhotra\",\"joining_date\":\"2026-11-01\",\"ip\":\"127.0.0.1\"}', '2026-10-19 11:55:19'),
  ('270', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 11:55:22'),
  ('271', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 11:55:25'),
  ('272', '1', 'update_candidate', 'users', '33', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Vikram Malhotra\",\"joining_date\":\"2026-12-15\",\"ip\":\"127.0.0.1\"}', '2026-10-19 11:55:29'),
  ('273', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 11:55:30');

-- --------------------------------------------------------
-- Table structure for `bank_details`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `bank_details`;
CREATE TABLE `bank_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `account_holder_name` varchar(150) NOT NULL,
  `bank_name` varchar(150) NOT NULL,
  `account_number_encrypted` text NOT NULL,
  `account_number_last4` varchar(4) NOT NULL,
  `ifsc_code` varchar(20) NOT NULL,
  `cheque_doc_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `fk_bank_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `blog_categories`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `blog_categories`;
CREATE TABLE `blog_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `blog_categories`
INSERT INTO `blog_categories` (`id`, `name`, `slug`, `created_at`) VALUES
  ('1', 'Career & Training', 'career-training', '2026-10-17 12:24:20'),
  ('2', 'Market Trends', 'market-trends', '2026-10-17 12:24:20'),
  ('3', 'Sales Mastery', 'sales-mastery', '2026-10-17 12:24:20'),
  ('4', 'Legal & Regulations', 'legal-regulations', '2026-10-17 12:24:20');

-- --------------------------------------------------------
-- Table structure for `blog_posts`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `blog_posts`;
CREATE TABLE `blog_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` mediumtext NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `author_name` varchar(100) NOT NULL DEFAULT 'Realty Smartz',
  `reading_time_minutes` int(10) unsigned NOT NULL DEFAULT 4,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_bp_cat` (`category_id`),
  KEY `idx_bp_published` (`is_published`,`is_archived`),
  CONSTRAINT `fk_bp_cat` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `blog_posts`
INSERT INTO `blog_posts` (`id`, `category_id`, `title`, `slug`, `excerpt`, `content`, `cover_image`, `author_name`, `reading_time_minutes`, `meta_title`, `meta_description`, `is_published`, `is_archived`, `published_at`, `created_at`) VALUES
  ('1', '1', 'Why Real Estate Sales is India\'s Highest Earning Career in 2026', 'why-real-estate-sales-is-indias-highest-earning-career', 'Discover why real estate consultation in Gurgaon and NCR is generating record seven-figure compensation packages for trained young professionals.', '<p>The Indian real estate sector is undergoing an unprecedented institutional revolution. With Gurgaon remaining the powerhouse of luxury housing and commercial absorption, skilled advisors are closing multi-crore transactions monthly.</p><p>At <strong>Realty Smartz Pathshala</strong>, we bridge the critical gap between academic education and battlefield sales mastery. Our candidates learn live market dynamics, investor psychographics, and relationship-driven closing techniques that guarantee exceptional career trajectories.</p>', NULL, 'Realty Smartz Editorial', '5', NULL, NULL, '1', '0', '2026-10-17 12:24:20', '2026-10-17 12:24:20'),
  ('2', '3', '5 Core Closing Skills Every Luxury Property Consultant Needs', '5-core-closing-skills-every-luxury-property-consultant-needs', 'Explore the psychological and consultative strategies that turn casual inquiries into multi-crore asset purchases.', '<p>Closing ultra-luxury real estate deals requires far more than reciting floor plans and price lists. High Net Worth Individuals (HNWIs) purchase based on confidentiality, prestige, lifestyle enhancement, and wealth preservation.</p><p>Key skills include consultative listening, deep micro-market price knowledge, objection de-escalation, and creating authentic urgency without sounding desperate.</p>', NULL, 'Chief Sales Mentor', '4', NULL, NULL, '1', '0', '2026-10-17 12:24:20', '2026-10-17 12:24:20'),
  ('3', '4', 'Understanding Circle Rates, RERA, and Carpet Area Calculations', 'understanding-circle-rates-rera-carpet-area-calculations', 'A definitive guide for real estate candidates to master property documentation and legal transparency.', '<p>A credible real estate advisor is first and foremost a trusted legal advisor. Understanding the fine difference between carpet area, plinth area, and super built-up area ensures you protect your clients from misguidance and build lifelong goodwill.</p>', NULL, 'Legal Compliance Team', '6', NULL, NULL, '1', '0', '2026-10-17 12:24:20', '2026-10-17 12:24:20');

-- --------------------------------------------------------
-- Table structure for `document_versions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `document_versions`;
CREATE TABLE `document_versions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `document_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_size` int(10) unsigned NOT NULL,
  `version` int(10) unsigned NOT NULL,
  `replaced_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `replaced_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_doc_ver_doc` (`document_id`),
  KEY `idx_doc_ver_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `document_versions`
INSERT INTO `document_versions` (`id`, `document_id`, `user_id`, `document_type`, `original_name`, `stored_name`, `file_path`, `mime_type`, `file_size`, `version`, `replaced_at`, `replaced_by`) VALUES
  ('1', '25', '21', 'pan_card', 'pan_card_original_scan.jpg', '21_pan_card_153b2d2198facdfdf12f.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/21_pan_card_153b2d2198facdfdf12f.jpg', 'image/jpeg', '32305', '1', '2026-10-17 15:11:54', '21'),
  ('2', '26', '22', 'pan_card', 'pan_card_original_scan.jpg', '22_pan_card_ec89aa8335c6ed6de8c0.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/22_pan_card_ec89aa8335c6ed6de8c0.jpg', 'image/jpeg', '32305', '1', '2026-10-17 15:14:15', '22'),
  ('3', '27', '23', 'pan_card', 'pan_card_original_scan.jpg', '23_pan_card_ce59f6d5165d9e363e4e.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/23_pan_card_ce59f6d5165d9e363e4e.jpg', 'image/jpeg', '32305', '1', '2026-10-17 15:15:13', '23'),
  ('4', '28', '24', 'pan_card', 'pan_card_original_scan.jpg', '24_pan_card_6d84fcf7e7e8b97a7ea1.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/24_pan_card_6d84fcf7e7e8b97a7ea1.jpg', 'image/jpeg', '32305', '1', '2026-10-17 15:15:31', '24'),
  ('5', '29', '25', 'pan_card', 'pan_card_original_scan.jpg', '25_pan_card_7ad7f3c4c0d68940bb74.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/25_pan_card_7ad7f3c4c0d68940bb74.jpg', 'image/jpeg', '32305', '1', '2026-10-17 15:16:13', '25'),
  ('6', '30', '26', 'pan_card', 'pan_card_original_scan.jpg', '26_pan_card_87a4811dad79ca70796e.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/26_pan_card_87a4811dad79ca70796e.jpg', 'image/jpeg', '32305', '1', '2026-10-17 15:16:45', '26');

-- --------------------------------------------------------
-- Table structure for `documents`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_size_before` int(10) unsigned NOT NULL,
  `file_size_after` int(10) unsigned NOT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT 1,
  `status` enum('pending','approved','rejected','reupload_requested') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` datetime DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_docs_user` (`user_id`),
  KEY `idx_docs_type` (`document_type`),
  KEY `idx_docs_status` (`status`),
  CONSTRAINT `fk_docs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `documents`
INSERT INTO `documents` (`id`, `user_id`, `document_type`, `original_name`, `stored_name`, `file_path`, `mime_type`, `file_size_before`, `file_size_after`, `version`, `status`, `rejection_reason`, `is_archived`, `archived_at`, `uploaded_at`) VALUES
  ('1', '6', 'aadhaar', 'Aadhaar_AaravMehta.pdf', 'fb44b327595b0383a2fe08d73f942a3d.pdf', 'storage/documents/6/fb44b327595b0383a2fe08d73f942a3d.pdf', 'application/pdf', '1845200', '620400', '1', 'approved', NULL, '0', NULL, '2026-09-23 14:04:01'),
  ('2', '6', 'pan', 'Pan_AaravMehta.pdf', 'b616a0ddf673fd6b653b8e450f7af573.pdf', 'storage/documents/6/b616a0ddf673fd6b653b8e450f7af573.pdf', 'application/pdf', '1845200', '620400', '1', 'approved', NULL, '0', NULL, '2026-09-23 14:04:01'),
  ('3', '6', 'photo', 'Photo_AaravMehta.pdf', '107bd8e9eb5fa91d41a0e069b6e1a34b.pdf', 'storage/documents/6/107bd8e9eb5fa91d41a0e069b6e1a34b.pdf', 'application/pdf', '1845200', '620400', '1', 'approved', NULL, '0', NULL, '2026-09-23 14:04:01'),
  ('4', '6', 'degree', 'Degree_AaravMehta.pdf', '4a249b4e37b002660ba825ad02929eb4.pdf', 'storage/documents/6/4a249b4e37b002660ba825ad02929eb4.pdf', 'application/pdf', '1845200', '620400', '1', 'approved', NULL, '0', NULL, '2026-09-23 14:04:01'),
  ('5', '7', 'aadhaar', 'Aadhaar_PriyaSharma.pdf', 'b40c6664967cdb377acc26ab6b40c7ed.pdf', 'storage/documents/7/b40c6664967cdb377acc26ab6b40c7ed.pdf', 'application/pdf', '1845200', '620400', '1', 'pending', NULL, '0', NULL, '2026-10-15 14:04:33'),
  ('6', '7', 'pan', 'Pan_PriyaSharma.pdf', 'b0346dc8b4a9b2a195a228a5186445b5.pdf', 'storage/documents/7/b0346dc8b4a9b2a195a228a5186445b5.pdf', 'application/pdf', '1845200', '620400', '1', 'pending', NULL, '0', NULL, '2026-10-15 14:04:33'),
  ('7', '8', 'aadhaar', 'Aadhaar_RohanVarma.pdf', 'fd2eb3cee702749a0963cfcb9722eb0d.pdf', 'storage/documents/8/fd2eb3cee702749a0963cfcb9722eb0d.pdf', 'application/pdf', '1845200', '620400', '1', 'pending', NULL, '0', NULL, '2026-10-16 14:04:33'),
  ('8', '8', 'pan', 'Pan_RohanVarma.pdf', 'faf1238ab46842ad57c52bbd6ab7e7da.pdf', 'storage/documents/8/faf1238ab46842ad57c52bbd6ab7e7da.pdf', 'application/pdf', '1845200', '620400', '1', 'pending', NULL, '0', NULL, '2026-10-16 14:04:33'),
  ('9', '9', 'aadhaar', 'Aadhaar_AnanyaIyer.pdf', '799916515f11169361586c1b4ec1f29b.pdf', 'storage/documents/9/799916515f11169361586c1b4ec1f29b.pdf', 'application/pdf', '1845200', '620400', '1', 'reupload_requested', NULL, '0', NULL, '2026-10-12 14:04:34'),
  ('10', '9', 'pan', 'Pan_AnanyaIyer.pdf', '654a3b70ec4b56b7014e4a481829c063.pdf', 'storage/documents/9/654a3b70ec4b56b7014e4a481829c063.pdf', 'application/pdf', '1845200', '620400', '1', 'approved', NULL, '0', NULL, '2026-10-12 14:04:34'),
  ('11', '10', 'aadhaar', 'Aadhaar_VikramSinghal.pdf', 'b47818663982f4b1f5e2edd5bbcf935a.pdf', 'storage/documents/10/b47818663982f4b1f5e2edd5bbcf935a.pdf', 'application/pdf', '1845200', '620400', '1', 'pending', NULL, '0', NULL, '2026-10-14 14:04:35'),
  ('12', '10', 'pan', 'Pan_VikramSinghal.pdf', '7df4e16d23a96b323d498f78ae17fce6.pdf', 'storage/documents/10/7df4e16d23a96b323d498f78ae17fce6.pdf', 'application/pdf', '1845200', '620400', '1', 'pending', NULL, '0', NULL, '2026-10-14 14:04:35'),
  ('13', '11', 'aadhaar', 'Aadhaar_SnehaPatil.pdf', 'eedc9b8721d3d7e32f0f9a6417f62ced.pdf', 'storage/documents/11/eedc9b8721d3d7e32f0f9a6417f62ced.pdf', 'application/pdf', '1845200', '620400', '1', 'approved', NULL, '0', NULL, '2026-09-29 14:04:35'),
  ('14', '11', 'pan', 'Pan_SnehaPatil.pdf', 'dfd491336a9c809b8ad003671d564ed5.pdf', 'storage/documents/11/dfd491336a9c809b8ad003671d564ed5.pdf', 'application/pdf', '1845200', '620400', '1', 'approved', NULL, '0', NULL, '2026-09-29 14:04:35'),
  ('15', '11', 'offer_letter', 'Offer_letter_SnehaPatil.pdf', 'df7547fc63fd0a658f01e861e2c972b9.pdf', 'storage/documents/11/df7547fc63fd0a658f01e861e2c972b9.pdf', 'application/pdf', '1845200', '620400', '1', 'approved', NULL, '0', NULL, '2026-09-29 14:04:35'),
  ('16', '14', 'aadhaar', 'Aadhaar_AdityaKashyap.pdf', '1fe8a70345bf26a12862486dfa020dce.pdf', 'storage/documents/14/1fe8a70345bf26a12862486dfa020dce.pdf', 'application/pdf', '1845200', '620400', '1', 'pending', NULL, '0', NULL, '2026-10-13 14:04:36'),
  ('17', '15', 'aadhaar', 'Aadhaar_DivyaNair.pdf', '31627c2fc7fbc06be605af88366022e9.pdf', 'storage/documents/15/31627c2fc7fbc06be605af88366022e9.pdf', 'application/pdf', '1845200', '620400', '1', 'pending', NULL, '0', NULL, '2026-10-11 14:04:37'),
  ('18', '15', 'pan', 'Pan_DivyaNair.pdf', 'c9389c02fe328762bc92f3146180d2a0.pdf', 'storage/documents/15/c9389c02fe328762bc92f3146180d2a0.pdf', 'application/pdf', '1845200', '620400', '1', 'pending', NULL, '0', NULL, '2026-10-11 14:04:37'),
  ('19', '16', 'aadhaar', 'Aadhaar_MohitAgarwal.pdf', '0be4be67a2d4724fbc61a6c3cc683e9f.pdf', 'storage/documents/16/0be4be67a2d4724fbc61a6c3cc683e9f.pdf', 'application/pdf', '1845200', '620400', '1', 'pending', NULL, '0', NULL, '2026-10-15 14:04:37'),
  ('20', '16', 'pan', 'Pan_MohitAgarwal.pdf', 'fe6ac9118ba7a4ceb1fe65dbd569e6f9.pdf', 'storage/documents/16/fe6ac9118ba7a4ceb1fe65dbd569e6f9.pdf', 'application/pdf', '1845200', '620400', '1', 'pending', NULL, '0', NULL, '2026-10-15 14:04:37'),
  ('21', '17', 'aadhaar', 'Aadhaar_PoojaDeshmukh.pdf', '2ac7bc0101630000a0e270c885b6be32.pdf', 'storage/documents/17/2ac7bc0101630000a0e270c885b6be32.pdf', 'application/pdf', '1845200', '620400', '1', 'approved', NULL, '0', NULL, '2026-09-27 14:04:38'),
  ('22', '17', 'pan', 'Pan_PoojaDeshmukh.pdf', '36b5e1ff6d65a27a85b7df42a95fb56e.pdf', 'storage/documents/17/36b5e1ff6d65a27a85b7df42a95fb56e.pdf', 'application/pdf', '1845200', '620400', '1', 'approved', NULL, '0', NULL, '2026-09-27 14:04:38'),
  ('23', '20', 'aadhaar_front', 'aditya_aadhaar.pdf', 'aditya_aadhaar.pdf', 'storage/uploads/sample.pdf', 'application/pdf', '1048576', '524288', '1', 'pending', NULL, '0', NULL, '2026-10-17 14:52:00'),
  ('24', '20', 'pan_card', 'aditya_pan.jpg', 'aditya_pan.jpg', 'storage/uploads/sample.jpg', 'image/jpeg', '819200', '409600', '1', 'pending', NULL, '0', NULL, '2026-10-17 14:52:00'),
  ('25', '21', 'pan_card', 'pan_card_clear_version2.jpg', '21_pan_card_afd49aba4f485ce0e345.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/21_pan_card_afd49aba4f485ce0e345.jpg', 'image/jpeg', '30690', '30690', '2', 'pending', NULL, '0', NULL, '2026-10-17 15:11:55'),
  ('26', '22', 'pan_card', 'pan_card_clear_version2.jpg', '22_pan_card_c61255468454a5772919.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/22_pan_card_c61255468454a5772919.jpg', 'image/jpeg', '30690', '30690', '2', 'approved', NULL, '0', NULL, '2026-10-17 15:14:15'),
  ('27', '23', 'pan_card', 'pan_card_clear_version2.jpg', '23_pan_card_3643e205b73ed30b2c49.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/23_pan_card_3643e205b73ed30b2c49.jpg', 'image/jpeg', '30690', '30690', '2', 'approved', NULL, '0', NULL, '2026-10-17 15:15:14'),
  ('28', '24', 'pan_card', 'pan_card_clear_version2.jpg', '24_pan_card_8146471add277320f2c1.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/24_pan_card_8146471add277320f2c1.jpg', 'image/jpeg', '30690', '30690', '2', 'approved', NULL, '0', NULL, '2026-10-17 15:15:31'),
  ('29', '25', 'pan_card', 'pan_card_clear_version2.jpg', '25_pan_card_9c5e1995d55bfd84f928.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/25_pan_card_9c5e1995d55bfd84f928.jpg', 'image/jpeg', '30690', '30690', '2', 'approved', NULL, '0', NULL, '2026-10-17 15:16:13'),
  ('30', '26', 'pan_card', 'pan_card_clear_version2.jpg', '26_pan_card_51d3eff0aab24dd2c268.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/26_pan_card_51d3eff0aab24dd2c268.jpg', 'image/jpeg', '30690', '30690', '2', 'approved', NULL, '0', NULL, '2026-10-17 15:16:45'),
  ('31', '30', 'profile_photo', 'aadharcard.jpeg', '30_profile_photo_bd18636e8ee76f3e3eee.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/30_profile_photo_bd18636e8ee76f3e3eee.jpg', 'image/jpeg', '67071', '89248', '1', 'pending', NULL, '0', NULL, '2026-10-17 16:28:49'),
  ('32', '30', 'aadhaar_front', '10TH DMC MOSHAM.jfif', '30_aadhaar_front_4faf18df2aea0d8decb4.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/30_aadhaar_front_4faf18df2aea0d8decb4.jpg', 'image/jpeg', '330030', '410700', '1', 'pending', NULL, '0', NULL, '2026-10-17 16:29:17'),
  ('33', '30', 'aadhaar_back', '10TH DMC MOSHAM.jfif', '30_aadhaar_back_41044e40468c693c9a02.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/30_aadhaar_back_41044e40468c693c9a02.jpg', 'image/jpeg', '330030', '410700', '1', 'pending', NULL, '0', NULL, '2026-10-17 16:39:36'),
  ('34', '30', 'pan_card', '10TH DMC MOSHAM.jpg', '30_pan_card_0d7be476fe6b9596a4b5.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/30_pan_card_0d7be476fe6b9596a4b5.jpg', 'image/jpeg', '240859', '373905', '1', 'pending', NULL, '0', NULL, '2026-10-17 16:40:22'),
  ('35', '32', 'aadhaar_front', 'aadhaar_front.jpg', '32_aadhaar_front_a10a59f329ee2464642a.jpg', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/32_aadhaar_front_a10a59f329ee2464642a.jpg', 'image/jpeg', '1367', '1364', '1', 'pending', NULL, '0', NULL, '2026-10-18 17:30:29');

-- --------------------------------------------------------
-- Table structure for `education`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `education`;
CREATE TABLE `education` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `highest_qualification` varchar(100) NOT NULL,
  `institute` varchar(255) NOT NULL,
  `year_of_passing` int(11) NOT NULL,
  `percentage_cgpa` varchar(20) NOT NULL,
  `document_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_edu_user` (`user_id`),
  CONSTRAINT `fk_edu_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `email_verifications`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `email_verifications`;
CREATE TABLE `email_verifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `otp` varchar(6) NOT NULL,
  `token` varchar(64) NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ev_user` (`user_id`),
  KEY `idx_ev_token` (`token`),
  KEY `idx_ev_otp` (`otp`),
  CONSTRAINT `fk_ev_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `email_verifications`
INSERT INTO `email_verifications` (`id`, `user_id`, `otp`, `token`, `attempts`, `expires_at`, `created_at`) VALUES
  ('1', '4', '694018', '73adc4c1fd1bbee6788718b347cca8f00c9519a20faea5ce25b41d0ff8ad184e', '0', '2026-10-17 09:06:57', '2026-10-17 12:26:58');

-- --------------------------------------------------------
-- Table structure for `emergency_contacts`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `emergency_contacts`;
CREATE TABLE `emergency_contacts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `contact_name` varchar(150) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `alternate_mobile` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `fk_emg_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `emergency_contacts`
INSERT INTO `emergency_contacts` (`id`, `user_id`, `contact_name`, `relationship`, `mobile`, `alternate_mobile`, `address`, `created_at`, `updated_at`) VALUES
  ('1', '19', 'Surender Sharma', 'Father', '9876500001', '9876500002 (Geeta Sh', NULL, '2026-10-17 14:51:38', '2026-10-17 14:51:38'),
  ('2', '20', 'Surender Sharma', 'Father', '9876500001', '9876500002 (Geeta Sh', NULL, '2026-10-17 14:51:59', '2026-10-17 14:51:59'),
  ('3', '30', 'dsdfds', 'Father', '9323242111', NULL, NULL, '2026-10-17 16:41:31', '2026-10-17 16:41:43'),
  ('6', '32', 'Ram Kumar', 'Brother', '9876543210', NULL, NULL, '2026-10-18 17:26:16', '2026-10-18 17:26:16');

-- --------------------------------------------------------
-- Table structure for `enquiries`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `enquiries`;
CREATE TABLE `enquiries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('new','contacted','closed') NOT NULL DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `experience`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `experience`;
CREATE TABLE `experience` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `is_fresher` tinyint(1) NOT NULL DEFAULT 0,
  `company` varchar(255) DEFAULT NULL,
  `designation` varchar(150) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `last_working_date` date DEFAULT NULL,
  `last_salary` varchar(50) DEFAULT NULL,
  `offer_letter_doc_id` int(10) unsigned DEFAULT NULL,
  `relieving_letter_doc_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_exp_user` (`user_id`),
  CONSTRAINT `fk_exp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `faqs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `faqs`;
CREATE TABLE `faqs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'general',
  `order_num` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `faqs`
INSERT INTO `faqs` (`id`, `question`, `answer`, `category`, `order_num`, `is_active`, `created_at`) VALUES
  ('1', 'Who is eligible to enroll in Realty Smartz Pathshala?', 'Any enthusiastic graduate, final-year student, or working professional passionate about high-earning sales careers in real estate can register. Freshers are warmly welcome with our tailored foundation modules.', 'onboarding', '1', '1', '2026-10-17 12:24:20'),
  ('2', 'What documents do I need to complete my onboarding profile?', 'You will need: a passport size photograph, Aadhaar card, PAN card, highest educational degree/mark sheet, cancelled cheque or bank passbook copy, and (for experienced candidates) your previous offer letter and relieving letter.', 'onboarding', '2', '1', '2026-10-17 12:24:20'),
  ('3', 'What happens if my document upload fails or is rejected?', 'If a document is unclear or incorrect, our review team will notify you with specific guidance. You can simply log into your portal and re-upload only that requested document without re-doing the whole profile.', 'documents', '3', '1', '2026-10-17 12:24:20'),
  ('4', 'How does the Aptitude Test work?', 'The test consists of 25 multiple-choice questions over 30 minutes, covering logic, quant, communication, and real estate awareness. It includes anti-cheat protection. You can review your scorecard immediately upon submission.', 'aptitude', '4', '1', '2026-10-17 12:24:20'),
  ('5', 'Is placement assistance provided after clearing onboarding?', 'Yes! 98% of candidates who complete the onboarding and Pathshala training program are placed directly in high-performing sales teams with leading developers and consultancy firms in Gurgaon.', 'placement', '5', '1', '2026-10-17 12:24:20');

-- --------------------------------------------------------
-- Table structure for `guest_test_links`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `guest_test_links`;
CREATE TABLE `guest_test_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token_hash` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_name` varchar(150) DEFAULT NULL,
  `guest_mobile` varchar(20) DEFAULT NULL,
  `guest_email` varchar(150) DEFAULT NULL,
  `set_id` int(11) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `max_attempts` int(11) NOT NULL DEFAULT 1,
  `attempts_used` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','in_progress','submitted','expired','revoked') NOT NULL DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `first_used_at` datetime DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_hash` (`token_hash`),
  KEY `idx_guest_token` (`token_hash`),
  KEY `idx_guest_user` (`user_id`),
  KEY `idx_guest_status` (`status`),
  KEY `idx_guest_email` (`guest_email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `guest_test_links`
INSERT INTO `guest_test_links` (`id`, `token_hash`, `user_id`, `guest_name`, `guest_mobile`, `guest_email`, `set_id`, `expires_at`, `max_attempts`, `attempts_used`, `status`, `created_by`, `created_at`, `first_used_at`, `submitted_at`, `revoked_at`) VALUES
  ('1', '86a957c2df7c3a5bd11b0bfa7c5894c6605c175c15e16ab0ba56ccbae75977e3', '33', 'Vikram Malhotra', '9876500011', 'vikram.guest@example.com', NULL, '2026-10-20 18:30:03', '1', '0', 'active', '1', '2026-10-18 18:30:03', NULL, NULL, NULL),
  ('2', '7fa701cc6382a18fb512e49f5257aa1c088e4b2370742014efa8bd30b81534df', '33', 'Vikram Malhotra', '9876500011', 'vikram.guest@example.com', '2', '2026-10-20 18:30:50', '1', '1', 'in_progress', '1', '2026-10-18 18:30:51', '2026-10-18 18:30:55', NULL, NULL),
  ('3', '76df6ac1864cae9fbcc54574d0c9e08f32b31bf11bd2e0eda4f49d0365f674bf', '33', 'Vikram Malhotra', '9876500011', 'vikram.guest@example.com', '5', '2026-10-20 18:31:48', '1', '1', 'submitted', '1', '2026-10-18 18:31:49', '2026-10-18 18:31:52', '2026-10-18 18:31:57', NULL),
  ('4', '2311b74d40c1a03c972d8d57ecc26689b0d49c7c02ec3f0fa5f1c98d3be135db', '33', 'Vikram Malhotra', '9876500011', 'vikram.guest@example.com', '4', '2026-10-20 18:32:33', '1', '1', 'revoked', '1', '2026-10-18 18:32:34', '2026-10-18 18:32:38', '2026-10-18 18:32:43', '2026-10-18 18:32:46'),
  ('5', '37eb3f3eb62a6f51a725be83f2f35afddaa78ce50df306ca55f8cff47b4ead8f', '33', 'Vikram Malhotra', '9876500011', 'vikram.guest@example.com', '3', '2026-10-20 18:33:51', '1', '1', 'revoked', '1', '2026-10-18 18:33:52', '2026-10-18 18:34:14', '2026-10-18 18:34:20', '2026-10-18 18:34:23');

-- --------------------------------------------------------
-- Table structure for `login_attempts`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `email` varchar(191) NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `last_attempt` datetime NOT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_la_ip_email` (`ip_address`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `migrations`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration_name` varchar(255) NOT NULL,
  `executed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `migration_name` (`migration_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `migrations`
INSERT INTO `migrations` (`id`, `migration_name`, `executed_at`) VALUES
  ('1', '001_create_initial_schema.sql', '2026-10-17 12:24:20');

-- --------------------------------------------------------
-- Table structure for `notifications`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_notif_user` (`user_id`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `options`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(10) unsigned NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `option_order` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_opt_q` (`question_id`),
  CONSTRAINT `fk_opt_q` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=301 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `options`
INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`, `option_order`) VALUES
  ('1', '1', '149', '0', '1'),
  ('2', '1', '159', '1', '2'),
  ('3', '1', '169', '0', '3'),
  ('4', '1', '158', '0', '4'),
  ('5', '2', 'Brother', '0', '1'),
  ('6', '2', 'Father', '0', '2'),
  ('7', '2', 'Maternal Uncle', '1', '3'),
  ('8', '2', 'Grandfather', '0', '4'),
  ('9', '3', '5279431', '0', '1'),
  ('10', '3', '8251896', '1', '2'),
  ('11', '3', '8543691', '0', '3'),
  ('12', '3', '8251869', '0', '4'),
  ('13', '4', '₹1,80,000', '0', '1'),
  ('14', '4', '₹2,40,000', '1', '2'),
  ('15', '4', '₹3,20,000', '0', '3'),
  ('16', '4', '₹2,00,000', '0', '4'),
  ('17', '5', '540 sq yards', '0', '1'),
  ('18', '5', '600 sq yards', '1', '2'),
  ('19', '5', '450 sq yards', '0', '3'),
  ('20', '5', '720 sq yards', '0', '4'),
  ('21', '6', '16.67%', '0', '1'),
  ('22', '6', '20.00%', '1', '2'),
  ('23', '6', '25.00%', '0', '3'),
  ('24', '6', '18.50%', '0', '4'),
  ('25', '7', 'Kindly reverts back with your feedback asap.', '0', '1'),
  ('26', '7', 'Please let us know your convenient time for the site inspection.', '1', '2'),
  ('27', '7', 'Can you came tomorrow morning at 10 AM to property site?', '0', '3'),
  ('28', '7', 'We is waiting for your confirmation regarding the unit booking.', '0', '4'),
  ('29', '8', 'Careless', '0', '1'),
  ('30', '8', 'Wise', '1', '2'),
  ('31', '8', 'Aggressive', '0', '3'),
  ('32', '8', 'Hesitant', '0', '4'),
  ('33', '9', 'in order to', '0', '1'),
  ('34', '9', 'due to', '1', '2'),
  ('35', '9', 'even though', '0', '3'),
  ('36', '9', 'despite of', '0', '4'),
  ('37', '10', '50%', '0', '1'),
  ('38', '10', '70%', '1', '2'),
  ('39', '10', '80%', '0', '3'),
  ('40', '10', '100%', '0', '4'),
  ('41', '11', 'Total built area including balcony, common corridors and clubhouse share', '0', '1'),
  ('42', '11', 'Net usable floor area of an apartment excluding external walls and balcony, but including internal walls', '1', '2'),
  ('43', '11', 'Plinth area plus super area loading percentage', '0', '3'),
  ('44', '11', 'Total boundary area enclosed by the structural pillars only', '0', '4'),
  ('45', '12', 'The highest auction price recorded in a municipal ward', '0', '1'),
  ('46', '12', 'The minimum threshold rate fixed by government for property stamp duty & registration', '1', '2'),
  ('47', '12', 'The developer\'s pre-launch introductory rate for investors', '0', '3'),
  ('48', '12', 'The bank\'s distressed liquidation evaluation rate', '0', '4'),
  ('49', '13', 'Immediately agree to slash the price by ₹15 Lakh to close on the spot', '0', '1'),
  ('50', '13', 'Acknowledge budget, understand payment milestones, and showcase the flexible construction-linked payment plan & long-term appreciation', '1', '2'),
  ('51', '13', 'Tell the client they cannot afford luxury properties in this area', '0', '3'),
  ('52', '13', 'Walk away and refuse to show further units to this prospect', '0', '4'),
  ('53', '14', 'To immediately pressure the buyer to swipe a booking token', '0', '1'),
  ('54', '14', 'To uncover the buyer\'s genuine requirements, budget, purpose (end-use vs investment), and decision timeline', '1', '2'),
  ('55', '14', 'To talk non-stop about all 20 projects your agency represents', '0', '3'),
  ('56', '14', 'To find out the client\'s credit card limit for token swipe', '0', '4'),
  ('57', '15', 'Sharp Angle Close', '0', '1'),
  ('58', '15', 'Alternative Choice Close', '1', '2'),
  ('59', '15', 'High-Pressure Takeaway Close', '0', '3'),
  ('60', '15', 'Ben Franklin Balance Sheet Close', '0', '4'),
  ('61', '16', 'Only Conclusion I follows', '0', '1'),
  ('62', '16', 'Neither Conclusion I nor II follows', '1', '2'),
  ('63', '16', 'Only Conclusion II follows', '0', '3'),
  ('64', '16', 'Both Conclusions follow', '0', '4'),
  ('65', '17', '15 meters West', '0', '1'),
  ('66', '17', '15 meters East', '1', '2'),
  ('67', '17', '10 meters East', '0', '3'),
  ('68', '17', '25 meters North-East', '0', '4'),
  ('69', '18', 'Freehold', '0', '1'),
  ('70', '18', 'Leasehold', '0', '2'),
  ('71', '18', 'Subprime', '1', '3'),
  ('72', '18', 'Conveyance', '0', '4'),
  ('73', '19', '1,650 sq ft', '0', '1'),
  ('74', '19', '1,760 sq ft', '1', '2'),
  ('75', '19', '1,820 sq ft', '0', '3'),
  ('76', '19', '1,950 sq ft', '0', '4'),
  ('77', '20', '₹11,25,000', '0', '1'),
  ('78', '20', '₹13,50,000', '1', '2'),
  ('79', '20', '₹15,00,000', '0', '3'),
  ('80', '20', '₹12,75,000', '0', '4'),
  ('81', '21', '80 site tours', '0', '1'),
  ('82', '21', '96 site tours', '1', '2'),
  ('83', '21', '112 site tours', '0', '3'),
  ('84', '21', '88 site tours', '0', '4'),
  ('85', '22', 'we sent you the brochure check it and tell us fast.', '0', '1'),
  ('86', '22', 'As requested, we have enclosed the master layout and payment plan for your review.', '1', '2'),
  ('87', '22', 'As requested we have enclosed, the layout plan and brochure for your review;', '0', '3'),
  ('88', '22', 'Brochure is attached please see it today only without fail.', '0', '4'),
  ('89', '23', 'Candid', '0', '1'),
  ('90', '23', 'Opaque', '1', '2'),
  ('91', '23', 'Explicit', '0', '3'),
  ('92', '23', 'Lucid', '0', '4'),
  ('93', '24', 'to', '0', '1'),
  ('94', '24', 'with', '1', '2'),
  ('95', '24', 'against', '0', '3'),
  ('96', '24', 'by', '0', '4'),
  ('97', '25', 'Ratio of carpet area to parking slots', '0', '1'),
  ('98', '25', 'Ratio of total covered floor area to the total plot area', '1', '2'),
  ('99', '25', 'Ratio of open green lawn area to concrete footprint', '0', '3'),
  ('100', '25', 'Ratio of residential towers to commercial retail shops', '0', '4'),
  ('101', '26', 'A certificate showing electricity and water bill clearance only', '0', '1'),
  ('102', '26', 'A document proving the property is free from mortgages, legal disputes, and financial liabilities', '1', '2'),
  ('103', '26', 'A certificate issued by an architect confirming seismic resistance', '0', '3'),
  ('104', '26', 'An environmental green-building rating score', '0', '4'),
  ('105', '27', 'DDA (Delhi Development Authority)', '0', '1'),
  ('106', '27', 'HRERA (Haryana Real Estate Regulatory Authority)', '1', '2'),
  ('107', '27', 'HUDA / HSVP only', '0', '3'),
  ('108', '27', 'RERA Mumbai Bench', '0', '4'),
  ('109', '28', 'Mock the buyer\'s economic understanding', '0', '1'),
  ('110', '28', 'Explain that property prices usually rise faster than interest savings, and home loans can always be refinanced when rates soften', '1', '2'),
  ('111', '28', 'Promise the buyer a personal cash kickback on the EMI', '0', '3'),
  ('112', '28', 'Immediately agree and advise the buyer not to purchase real estate for 2 years', '0', '4'),
  ('113', '29', 'Nodding constantly while checking WhatsApp messages on your mobile', '0', '1'),
  ('114', '29', 'Focusing completely on the client\'s words, noting priorities, clarifying pain points, and validating needs before proposing units', '1', '2'),
  ('115', '29', 'Interrupting the client every 30 seconds to quote developer awards', '0', '3'),
  ('116', '29', 'Memorizing the client\'s family members\' names to flatter them', '0', '4'),
  ('117', '30', 'Send 50 uncompressed PDF files and never follow up', '0', '1'),
  ('118', '30', 'Share the curated digital dossier immediately on WhatsApp, and schedule a specific 10-minute follow-up slot the next afternoon', '1', '2'),
  ('119', '30', 'Tell the client you only communicate via registered postal mail', '0', '3'),
  ('120', '30', 'Repeatedly call them every hour until they answer', '0', '4'),
  ('121', '31', 'SUW', '0', '1'),
  ('122', '31', 'TVX', '1', '2'),
  ('123', '31', 'TWY', '0', '3'),
  ('124', '31', 'UWZ', '0', '4'),
  ('125', '32', '26th', '0', '1'),
  ('126', '32', '27th', '1', '2'),
  ('127', '32', '28th', '0', '3'),
  ('128', '32', '25th', '0', '4'),
  ('129', '33', '19-12-1-17-20', '0', '1'),
  ('130', '33', '19-13-1-18-20', '1', '2'),
  ('131', '33', '18-13-1-18-19', '0', '3'),
  ('132', '33', '20-14-2-19-21', '0', '4'),
  ('133', '34', '6.0%', '0', '1'),
  ('134', '34', '7.2%', '1', '2'),
  ('135', '34', '8.5%', '0', '3'),
  ('136', '34', '9.0%', '0', '4'),
  ('137', '35', '₹36,00,000', '0', '1'),
  ('138', '35', '₹40,00,000', '1', '2'),
  ('139', '35', '₹44,00,000', '0', '3'),
  ('140', '35', '₹48,00,000', '0', '4'),
  ('141', '36', '₹48,500', '0', '1'),
  ('142', '36', '₹56,667', '1', '2'),
  ('143', '36', '₹62,000', '0', '3'),
  ('144', '36', '₹70,000', '0', '4'),
  ('145', '37', 'Hope you buy this flat today.', '0', '1'),
  ('146', '37', 'I look forward to assisting you in finding your ideal property investment.', '1', '2'),
  ('147', '37', 'Call me immediately if you have money ready.', '0', '3'),
  ('148', '37', 'Thanks for taking my email and revert fast.', '0', '4'),
  ('149', '38', 'Acommodation', '0', '1'),
  ('150', '38', 'Accommodation', '1', '2'),
  ('151', '38', 'Accomodation', '0', '3'),
  ('152', '38', 'Acomodation', '0', '4'),
  ('153', '39', 'Timid', '0', '1'),
  ('154', '39', 'Persistent', '1', '2'),
  ('155', '39', 'Casual', '0', '3'),
  ('156', '39', 'Passive', '0', '4'),
  ('157', '40', 'Permission to start excavation and foundation laying', '0', '1'),
  ('158', '40', 'Official certification that the building is compliant with building codes and fit for residential occupation', '1', '2'),
  ('159', '40', 'Approval for commercial signboards and hoarding displays', '0', '3'),
  ('160', '40', 'A deed transferring land ownership between family members', '0', '4'),
  ('161', '41', 'A fee paid directly to the private developer for clubhouse maintenance', '0', '1'),
  ('162', '41', 'A state tax levied on property documents to establish legal ownership and validity in court', '1', '2'),
  ('163', '41', 'A refundable deposit kept with the central Reserve Bank of India', '0', '3'),
  ('164', '41', 'An optional insurance premium covering interior painting', '0', '4'),
  ('165', '42', 'The property can only be rented to relatives without charge', '0', '1'),
  ('166', '42', 'Outright perpetual ownership of both the land and building with full transferability', '1', '2'),
  ('167', '42', 'A government lease that automatically expires every 99 years', '0', '3'),
  ('168', '42', 'An agricultural land parcel that cannot have masonry structures', '0', '4'),
  ('169', '43', 'Tell the client Developer X uses inferior materials and will fail', '0', '1'),
  ('170', '43', 'Highlight your project\'s superior construction quality, RERA milestones, brand track record, and deliverable inclusions that more than offset the price gap', '1', '2'),
  ('171', '43', 'Immediately give up and advise the customer to book with Developer X', '0', '3'),
  ('172', '43', 'Argue aggressively with the client and demand proof of quotes', '0', '4'),
  ('173', '44', 'Falsely claiming a fake buyer is sitting outside with cash to force a panic booking', '0', '1'),
  ('174', '44', 'Highlighting authentic, factual constraints like upcoming price escalations or limited inventory of premium unit layouts', '1', '2'),
  ('175', '44', 'Locking the client in the conference room until they sign the application form', '0', '3'),
  ('176', '44', 'Promising unrealistic 100% price appreciation within 30 days', '0', '4'),
  ('177', '45', 'Updating leads once every 3 months only', '0', '1'),
  ('178', '45', 'Disciplined daily logging of client conversation notes, preference changes, and punctual follow-up scheduling', '1', '2'),
  ('179', '45', 'Marking every uninterested lead as booked to win internal awards', '0', '3'),
  ('180', '45', 'Deleting customer phone numbers after one unanswered call', '0', '4'),
  ('181', '46', '54', '0', '1'),
  ('182', '46', '56', '1', '2'),
  ('183', '46', '58', '0', '3'),
  ('184', '46', '60', '0', '4'),
  ('185', '47', 'ECNEDISRE', '0', '1'),
  ('186', '47', 'ECNEDISER', '1', '2'),
  ('187', '47', 'ENCSEDERI', '0', '3'),
  ('188', '47', 'ECNEDSIRR', '0', '4'),
  ('189', '48', 'North-West', '0', '1'),
  ('190', '48', 'East', '1', '2'),
  ('191', '48', 'South-West', '0', '3'),
  ('192', '48', 'North', '0', '4'),
  ('193', '49', '₹108.0 Lakh', '0', '1'),
  ('194', '49', '₹110.4 Lakh', '1', '2'),
  ('195', '49', '₹115.0 Lakh', '0', '3'),
  ('196', '49', '₹104.2 Lakh', '0', '4'),
  ('197', '50', '₹78,000', '0', '1'),
  ('198', '50', '₹93,600', '1', '2'),
  ('199', '50', '₹86,400', '0', '3'),
  ('200', '50', '₹1,02,000', '0', '4'),
  ('201', '51', '45 units', '0', '1'),
  ('202', '51', '60 units', '1', '2'),
  ('203', '51', '72 units', '0', '3'),
  ('204', '51', '80 units', '0', '4'),
  ('205', '52', 'casually', '0', '1'),
  ('206', '52', 'meticulously', '1', '2'),
  ('207', '52', 'hastily', '0', '3'),
  ('208', '52', 'tentatively', '0', '4'),
  ('209', '53', 'The team of senior real estate consultants are inspecting the clubhouse.', '0', '1'),
  ('210', '53', 'The team of senior real estate consultants is inspecting the clubhouse.', '1', '2'),
  ('211', '53', 'The team of senior real estate consultants have inspect the clubhouse.', '0', '3'),
  ('212', '53', 'The team of senior real estate consultants were inspecting since morning.', '0', '4'),
  ('213', '54', 'To cancel a scheduled meeting', '0', '1'),
  ('214', '54', 'To finalize and formally close a transaction', '1', '2'),
  ('215', '54', 'To print physical stickers on paper', '0', '3'),
  ('216', '54', 'To compromise on company policy', '0', '4'),
  ('217', '55', 'There is no legal difference between them', '0', '1'),
  ('218', '55', 'Agreement for Sale promises future terms, while the Sale Deed legally transfers ownership upon registration', '1', '2'),
  ('219', '55', 'Agreement for Sale is only for agricultural land, while Sale Deed is for apartments', '0', '3'),
  ('220', '55', 'Sale Deed requires no stamp duty while Agreement for Sale requires full stamp duty', '0', '4'),
  ('221', '56', 'The ratio of land tax to valuation', '0', '1'),
  ('222', '56', 'The proportion of property value that a bank can finance as a loan', '1', '2'),
  ('223', '56', 'The ratio of lease tenure to villa size', '0', '3'),
  ('224', '56', 'The ratio of live leads to total visitors', '0', '4'),
  ('225', '57', 'A receipt for the initial booking token only', '0', '1'),
  ('226', '57', 'An official document issued by the developer handing over physical keys and occupancy rights to the buyer', '1', '2'),
  ('227', '57', 'A bank notice demanding early mortgage repayment', '0', '3'),
  ('228', '57', 'A police verification certificate for tenant leasing', '0', '4'),
  ('229', '58', 'Talk continuously without letting any family member speak', '0', '1'),
  ('230', '58', 'Ask open-ended questions inviting the spouse and family members to share their preferences and daily routines', '1', '2'),
  ('231', '58', 'Rush them through the show flat in 5 minutes to create artificial urgency', '0', '3'),
  ('232', '58', 'Leave the family alone in the room and walk away completely', '0', '4'),
  ('233', '59', 'Make up a plausible-sounding answer on the spot to avoid appearing uninformed', '0', '1'),
  ('234', '59', 'Honestly acknowledge it, consult the in-house legal team, and provide verified documentation within a committed timeframe', '1', '2'),
  ('235', '59', 'Tell the client technical legal questions are irrelevant for homebuyers', '0', '3'),
  ('236', '59', 'Ignore the question and redirect to clubhouse amenities', '0', '4'),
  ('237', '60', 'Buying scraped phone databases from third-party call centers', '0', '1'),
  ('238', '60', 'Generating warm prospective buyers through satisfied clients who advocate your service to peers and relatives', '1', '2'),
  ('239', '60', 'Posting identical unit ads on 50 free classified websites', '0', '3'),
  ('240', '60', 'Handing out paper flyers at traffic intersections', '0', '4'),
  ('241', '61', 'ROKHC', '0', '1'),
  ('242', '61', 'TPMJE', '1', '2'),
  ('243', '61', 'SPMKE', '0', '3'),
  ('244', '61', 'TQMKF', '0', '4'),
  ('245', '62', '60 degrees', '0', '1'),
  ('246', '62', '90 degrees', '1', '2'),
  ('247', '62', '75 degrees', '0', '3'),
  ('248', '62', '105 degrees', '0', '4'),
  ('249', '63', '64', '0', '1'),
  ('250', '63', '100', '1', '2'),
  ('251', '63', '125', '0', '3'),
  ('252', '63', '216', '0', '4'),
  ('253', '64', '6.5%', '0', '1'),
  ('254', '64', '8.0%', '1', '2'),
  ('255', '64', '10.0%', '0', '3'),
  ('256', '64', '7.5%', '0', '4'),
  ('257', '65', '₹96,000', '0', '1'),
  ('258', '65', '₹1,08,000', '1', '2'),
  ('259', '65', '₹1,18,000', '0', '3'),
  ('260', '65', '₹1,25,000', '0', '4'),
  ('261', '66', '20 units', '0', '1'),
  ('262', '66', '22.5 units', '1', '2'),
  ('263', '66', '25 units', '0', '3'),
  ('264', '66', '24 units', '0', '4'),
  ('265', '67', 'fragmented', '0', '1'),
  ('266', '67', 'seamless', '1', '2'),
  ('267', '67', 'tangled', '0', '3'),
  ('268', '67', 'cluttered', '0', '4'),
  ('269', '68', 'Please revert back again when you are free.', '0', '1'),
  ('270', '68', 'Please let us know if you require any additional floor plans or documentation.', '1', '2'),
  ('271', '68', 'We will return back your call as early as feasible.', '0', '3'),
  ('272', '68', 'Kindly discuss together amongst yourselves and reply.', '0', '4'),
  ('273', '69', 'Spurious', '0', '1'),
  ('274', '69', 'Genuine', '1', '2'),
  ('275', '69', 'Artificial', '0', '3'),
  ('276', '69', 'Hypothetical', '0', '4'),
  ('277', '70', 'To earn maximum interest in speculative stock markets', '0', '1'),
  ('278', '70', 'To safeguard homebuyer funds and guarantee they are used solely for the specific project\'s construction and land expenses', '1', '2'),
  ('279', '70', 'To pay administrative fines to the municipal council', '0', '3'),
  ('280', '70', 'To distribute employee festival bonuses', '0', '4'),
  ('281', '71', 'Buyer must deduct 5% TDS on all properties', '0', '1'),
  ('282', '71', 'Buyer must deduct 1% TDS on property transactions valued at ₹50 Lakh or above', '1', '2'),
  ('283', '71', 'Seller pays 2% extra tax to the sub-registrar office', '0', '3'),
  ('284', '71', 'Only commercial banks are permitted to deduct property TDS', '0', '4'),
  ('285', '72', 'The area of the swimming pool and children\'s playground', '0', '1'),
  ('286', '72', 'The total covered built-up area measured up to the external face of the perimeter walls', '1', '2'),
  ('287', '72', 'The carpet area excluding internal bathrooms', '0', '3'),
  ('288', '72', 'The total area of the open terrace roof', '0', '4'),
  ('289', '73', 'Tell the client their family\'s opinion does not matter in financial deals', '0', '1'),
  ('290', '73', 'Support the decision wholeheartedly, share a clean comparison summary for their family discussion, and offer to host a brief walkthrough or call with the family', '1', '2'),
  ('291', '73', 'Refuse to let the client leave until they sign a binding undertaking', '0', '3'),
  ('292', '73', 'Immediately mark the client as lost and discard the lead', '0', '4'),
  ('293', '74', 'A physical handshake technique to assess client confidence', '0', '1'),
  ('294', '74', 'An empathy-driven objection handling model that validates concern, shares peer experience, and showcases proven positive outcome', '1', '2'),
  ('295', '74', 'An aggressive discounting scheme designed to panic buyers', '0', '3'),
  ('296', '74', 'A legal contract clause governing structural defects', '0', '4'),
  ('297', '75', 'The consultative advisor charges double brokerage upfront', '0', '1'),
  ('298', '75', 'The consultative advisor focuses on holistic needs, risk mitigation, and long-term client value rather than rapid one-time sales', '1', '2'),
  ('299', '75', 'Transactional brokers always wear suits while advisors do not', '0', '3'),
  ('300', '75', 'There is no practical difference between them', '0', '4');

-- --------------------------------------------------------
-- Table structure for `pages_content`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pages_content`;
CREATE TABLE `pages_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_slug` varchar(100) NOT NULL,
  `section_key` varchar(100) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `body_content` mediumtext DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `extra_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`extra_data`)),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_page_section` (`page_slug`,`section_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `password_resets`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pr_email` (`email`),
  KEY `idx_pr_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `profiles`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `profiles`;
CREATE TABLE `profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `father_name` varchar(150) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `mother_name` varchar(150) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `marital_status` enum('single','married','divorced','widowed') DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `nationality` varchar(50) NOT NULL DEFAULT 'Indian',
  `photo_path` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `same_as_permanent` tinyint(1) NOT NULL DEFAULT 0,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(20) DEFAULT NULL,
  `aadhaar_enc` text DEFAULT NULL,
  `aadhaar_encrypted` text DEFAULT NULL,
  `aadhaar_last4` varchar(4) DEFAULT NULL,
  `pan_enc` text DEFAULT NULL,
  `pan_last4` varchar(4) DEFAULT NULL,
  `pan_number` varchar(20) DEFAULT NULL,
  `passport_number` varchar(30) DEFAULT NULL,
  `social_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_links`)),
  `declaration_accepted` tinyint(1) NOT NULL DEFAULT 0,
  `declaration_date` date DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `signature_type` enum('typed','drawn') DEFAULT NULL,
  `signature_data` mediumtext DEFAULT NULL,
  `completion_percentage` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `fk_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `profiles`
INSERT INTO `profiles` (`id`, `user_id`, `father_name`, `whatsapp`, `mother_name`, `dob`, `gender`, `marital_status`, `blood_group`, `nationality`, `photo_path`, `address`, `permanent_address`, `current_address`, `same_as_permanent`, `city`, `state`, `pincode`, `aadhaar_enc`, `aadhaar_encrypted`, `aadhaar_last4`, `pan_enc`, `pan_last4`, `pan_number`, `passport_number`, `social_links`, `declaration_accepted`, `declaration_date`, `joining_date`, `signature_type`, `signature_data`, `completion_percentage`, `is_locked`, `created_at`, `updated_at`) VALUES
  ('1', '4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Indian', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '0', '0', '2026-10-17 12:26:58', '2026-10-17 12:26:58'),
  ('2', '5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Indian', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '0', '0', '2026-10-17 12:31:15', '2026-10-17 12:31:15'),
  ('3', '6', 'Rajesh Mehta', NULL, NULL, '1998-05-15', 'male', NULL, NULL, 'Indian', NULL, 'Flat 402, Green Glen Layout, Sector 48', NULL, NULL, '0', 'Gurugram', 'Haryana', '122001', 'gZ5I9aovKz0hXZ5aaxJmjnV3WlpXbnN6Z0FGQnd5ZlNJaHdEVmc9PQ==', NULL, '0006', '3S7GcdhugWFDjr2DmGqGWUl4Tm9GcWJteUNOQzh0UDEwSjI1cUE9PQ==', '116Z', NULL, NULL, '{\"linkedin\":\"https:\\/\\/linkedin.com\\/in\\/aaravmehta\",\"instagram\":\"https:\\/\\/instagram.com\\/aaravmehta\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '1', NULL, NULL, NULL, NULL, '100', '0', '2026-09-23 14:04:01', '2026-10-17 14:04:33'),
  ('5', '7', 'Suresh Sharma', NULL, NULL, '1998-05-15', 'male', NULL, NULL, 'Indian', NULL, 'Flat 402, Green Glen Layout, Sector 48', NULL, NULL, '0', 'New Delhi', 'Delhi', '122001', 'VKwCoKi/iGDcq90JKpIMiDd5d0EvVVRnRkovYVZaV1o4b2ZJMUE9PQ==', NULL, '0007', 'Mt8LK81VIoORZoxTKpEQT0pYdjBrbGJNTG1BbkdXaHo1T0Rqd2c9PQ==', '117Z', NULL, NULL, '{\"linkedin\":\"https:\\/\\/linkedin.com\\/in\\/priyasharma\",\"instagram\":\"https:\\/\\/instagram.com\\/priyasharma\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '1', NULL, NULL, NULL, NULL, '100', '0', '2026-10-15 14:04:33', '2026-10-15 14:04:33'),
  ('6', '8', 'Anil Varma', NULL, NULL, '1998-05-15', 'male', NULL, NULL, 'Indian', NULL, 'Flat 402, Green Glen Layout, Sector 48', NULL, NULL, '0', 'Noida', 'Uttar Pradesh', '122001', 'Lwe02kCqnMrBX4tYQegjflRjNVc0bzc0N3FQRWZTSEE0UGloMXc9PQ==', NULL, '0008', 'dQ9VgJDtYu0+AzPFZF7N+jZ6eG0xYXFBZXMxSWZWNTdxaExBQkE9PQ==', '118Z', NULL, NULL, '{\"linkedin\":\"https:\\/\\/linkedin.com\\/in\\/rohanvarma\",\"instagram\":\"https:\\/\\/instagram.com\\/rohanvarma\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '1', NULL, NULL, NULL, NULL, '100', '0', '2026-10-16 14:04:33', '2026-10-16 14:04:33'),
  ('7', '9', 'Ramanathan Iyer', NULL, NULL, '1998-05-15', 'male', NULL, NULL, 'Indian', NULL, 'Flat 402, Green Glen Layout, Sector 48', NULL, NULL, '0', 'Bengaluru', 'Karnataka', '122001', 'tfgbiSA+yRwEMHCTUr3xLnU4MG1aK3YvYUdoVGdKSDZ0bFlVNEE9PQ==', NULL, '0009', '5OdHAcnqjKEVXCzVyyWQQTIycmtqK3EzWmErL2NqdDNzUFo3TUE9PQ==', '119Z', NULL, NULL, '{\"linkedin\":\"https:\\/\\/linkedin.com\\/in\\/ananyaiyer\",\"instagram\":\"https:\\/\\/instagram.com\\/ananyaiyer\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '1', NULL, NULL, NULL, NULL, '85', '0', '2026-10-12 14:04:34', '2026-10-12 14:04:34'),
  ('8', '10', 'Mahesh Singhal', NULL, NULL, '1998-05-15', 'male', NULL, NULL, 'Indian', NULL, 'Flat 402, Green Glen Layout, Sector 48', NULL, NULL, '0', 'Faridabad', 'Haryana', '122001', 'YI5eEuCM3ja0gOgT0h/O9G81dElpdDQydkQyTjMvclhIUGYxN2c9PQ==', NULL, '0010', 'E4Dog5XBxzGRE0eMqsLHxTJrWjNjNXVYUGw5QTExSGhkanRaM3c9PQ==', '110Z', NULL, NULL, '{\"linkedin\":\"https:\\/\\/linkedin.com\\/in\\/vikramsinghal\",\"instagram\":\"https:\\/\\/instagram.com\\/vikramsinghal\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '1', NULL, NULL, NULL, NULL, '100', '0', '2026-10-14 14:04:35', '2026-10-14 14:04:35'),
  ('9', '11', 'Deepak Patil', NULL, NULL, '1998-05-15', 'male', NULL, NULL, 'Indian', NULL, 'Flat 402, Green Glen Layout, Sector 48', NULL, NULL, '0', 'Pune', 'Maharashtra', '122001', 'eHzaeVdrQIYQPfm4OCMc3DdzdnB6aEFtNlNUemlaYWtSdGJJcEE9PQ==', NULL, '0011', 'XXi34FfFx6B8UiCjEkeiqVBLZkY0SS9yR01ueTl1ZlRsTDYyYnc9PQ==', '111Z', NULL, NULL, '{\"linkedin\":\"https:\\/\\/linkedin.com\\/in\\/snehapatil\",\"instagram\":\"https:\\/\\/instagram.com\\/snehapatil\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '1', NULL, NULL, NULL, NULL, '100', '0', '2026-09-29 14:04:35', '2026-09-29 14:04:35'),
  ('10', '12', 'Sunil Malhotra', NULL, NULL, '1998-05-15', 'male', NULL, NULL, 'Indian', NULL, 'Flat 402, Green Glen Layout, Sector 48', NULL, NULL, '0', 'Gurugram', 'Haryana', '122001', 'cN/TcE/bVRJEFYwIXPdjXmxPSitBZlF1WUZzcW95RlVBWmpwbGc9PQ==', NULL, '0012', 'Fo3XXMpkTLfw2qggYQOcw1ZTbUU3dTBNK3pjem1wTGxMbUFjVVE9PQ==', '112Z', NULL, NULL, '{\"linkedin\":\"https:\\/\\/linkedin.com\\/in\\/karanmalhotra\",\"instagram\":\"https:\\/\\/instagram.com\\/karanmalhotra\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '1', NULL, NULL, NULL, NULL, '25', '0', '2026-10-16 14:04:36', '2026-10-16 14:04:36'),
  ('11', '13', 'Harish Chawla', NULL, NULL, '1998-05-15', 'male', NULL, NULL, 'Indian', NULL, 'Flat 402, Green Glen Layout, Sector 48', NULL, NULL, '0', 'Chandigarh', 'Punjab', '122001', '51SnmqqeQCbb6x04YoGBBVV6Y0JZdk5KWVhBYkpJb1VMQTNwcGc9PQ==', NULL, '0013', 'sac9ZiMNYGC08/czKsgmE2JENm1SRTdhMUZhM2x3L1EvRmpidEE9PQ==', '113Z', NULL, NULL, '{\"linkedin\":\"https:\\/\\/linkedin.com\\/in\\/tanvichawla\",\"instagram\":\"https:\\/\\/instagram.com\\/tanvichawla\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '1', NULL, NULL, NULL, NULL, '0', '0', '2026-10-17 14:04:36', '2026-10-17 14:04:36'),
  ('12', '14', 'Vivek Kashyap', NULL, NULL, '1998-05-15', 'male', NULL, NULL, 'Indian', NULL, 'Flat 402, Green Glen Layout, Sector 48', NULL, NULL, '0', 'Patna', 'Bihar', '122001', 'SfsqhsO/U8vdQhGU9+3ufitYUHdIRkZFL0IzVERkZ3M0YVBUZ0E9PQ==', NULL, '0014', 'qsLCsRlBeLtLucqyyFqJDzQ3Mi9sU1hqQURUYVdQckNyNVh4L2c9PQ==', '114Z', NULL, NULL, '{\"linkedin\":\"https:\\/\\/linkedin.com\\/in\\/adityakashyap\",\"instagram\":\"https:\\/\\/instagram.com\\/adityakashyap\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '1', NULL, NULL, NULL, NULL, '75', '0', '2026-10-13 14:04:36', '2026-10-13 14:04:36'),
  ('13', '15', 'Gopal Nair', NULL, NULL, '1998-05-15', 'male', NULL, NULL, 'Indian', NULL, 'Flat 402, Green Glen Layout, Sector 48', NULL, NULL, '0', 'Kochi', 'Kerala', '122001', '5PnJh4Y+bgxIxtHQQKOBQlh5RndXejZvdHdqb0RjeUg4a0ZhUGc9PQ==', NULL, '0015', 'w5N+i4lcufSRK5ELv5Pe33pOWXpUbkFvSStrbjd4V0s4Zm5jWUE9PQ==', '115Z', NULL, NULL, '{\"linkedin\":\"https:\\/\\/linkedin.com\\/in\\/divyanair\",\"instagram\":\"https:\\/\\/instagram.com\\/divyanair\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '1', NULL, NULL, NULL, NULL, '100', '0', '2026-10-11 14:04:37', '2026-10-11 14:04:37'),
  ('14', '16', 'Pawan Agarwal', NULL, NULL, '1998-05-15', 'male', NULL, NULL, 'Indian', NULL, 'Flat 402, Green Glen Layout, Sector 48', NULL, NULL, '0', 'Lucknow', 'Uttar Pradesh', '122001', '3NgBpLFZnU+2KhCB+ey/NTRUcmZNcVdaSk1jVmhRdW0yNWphclE9PQ==', NULL, '0016', 'yrdeV7HuOEkOYcQY1pj3FFVMbnV0OGljQ3NOTnFwa2VsN2hEVGc9PQ==', '116Z', NULL, NULL, '{\"linkedin\":\"https:\\/\\/linkedin.com\\/in\\/mohitagarwal\",\"instagram\":\"https:\\/\\/instagram.com\\/mohitagarwal\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '1', NULL, NULL, NULL, NULL, '100', '0', '2026-10-15 14:04:37', '2026-10-15 14:04:37'),
  ('15', '17', 'Sanjay Deshmukh', NULL, NULL, '1998-05-15', 'male', NULL, NULL, 'Indian', NULL, 'Flat 402, Green Glen Layout, Sector 48', NULL, NULL, '0', 'Nagpur', 'Maharashtra', '122001', 'yo9K06dost+TCP2jj49FszdhM2JQVXNhWWFCcGo5cStBTkhHM2c9PQ==', NULL, '0017', 'lXnq8d16RkPAZ1nkigQFv3VrZVBsM0V0NGdjU09BTFpXUXZTQ1E9PQ==', '117Z', NULL, NULL, '{\"linkedin\":\"https:\\/\\/linkedin.com\\/in\\/poojadeshmukh\",\"instagram\":\"https:\\/\\/instagram.com\\/poojadeshmukh\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '1', NULL, NULL, NULL, NULL, '100', '0', '2026-09-27 14:04:38', '2026-09-27 14:04:38'),
  ('16', '18', 'Surender Sharma', NULL, NULL, NULL, NULL, NULL, NULL, 'Indian', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '0', '0', '2026-10-17 14:50:42', '2026-10-17 14:50:42'),
  ('17', '19', 'Surender Sharma', '9876543299', NULL, '1998-05-14', 'male', NULL, NULL, 'Indian', NULL, 'Flat 402, DLF Phase 5, Golf Course Road', 'Flat 402, DLF Phase 5, Golf Course Road', 'Flat 402, DLF Phase 5, Golf Course Road', '0', 'Gurugram', 'Haryana', '122009', 'QTlmsNlCjTMNmrdobNdu7m+GKMBrX6RCucWwWs5TlzspimdZ1aVfkjNihKVsavgc4luUZjWemPQ4nAp9dS8XfA==', 'QTlmsNlCjTMNmrdobNdu7m+GKMBrX6RCucWwWs5TlzspimdZ1aVfkjNihKVsavgc4luUZjWemPQ4nAp9dS8XfA==', '1100', 'OJenFxuCaWiCKQ6EiNIhSG/eBxKndzCghI/RDP1H/oUSAQYbtLnFoGRp8SL9h0G8GkLSj7Bz9CXiVZYOp92yfw==', '234F', 'ABCDE1234F', NULL, '{\"linkedin\":\"https://linkedin.com/in/adityasharma-realestate\",\"instagram\":\"@aditya_realty\",\"facebook\":\"https://facebook.com/adityasharma\",\"twitter\":\"https://x.com/aditya_realty\",\"youtube\":\"https://youtube.com/@adityarealtysmartz\",\"portfolio\":\"https://adityarealtyportfolio.in\"}', '0', NULL, NULL, NULL, NULL, '75', '0', '2026-10-17 14:51:35', '2026-10-17 14:51:38'),
  ('18', '20', 'Surender Sharma', '9876543299', NULL, '1998-05-14', 'male', NULL, NULL, 'Indian', NULL, 'Flat 402, DLF Phase 5, Golf Course Road', 'Flat 402, DLF Phase 5, Golf Course Road', 'Flat 402, DLF Phase 5, Golf Course Road', '0', 'Gurugram', 'Haryana', '122009', 'epf7RXfhUTImcnaD6HFWdx3zOENjExlSmsCCdeMJaJ60J+G0QGb7ouEhqV5WQt7eb3Q/7kSjg/338hjWlptWbA==', 'epf7RXfhUTImcnaD6HFWdx3zOENjExlSmsCCdeMJaJ60J+G0QGb7ouEhqV5WQt7eb3Q/7kSjg/338hjWlptWbA==', '1100', 'I9mW/HEouPIVlNYy0dLyrQAjijAEPZZmXtL+xIukWXtoeQEP1+8kTR9yw1vQqsCwxvCplw/MumtN7bb5ZbWjew==', '234F', 'ABCDE1234F', NULL, '{\"linkedin\":\"https://linkedin.com/in/adityasharma-realestate\",\"instagram\":\"@aditya_realty\",\"facebook\":\"https://facebook.com/adityasharma\",\"twitter\":\"https://x.com/aditya_realty\",\"youtube\":\"https://youtube.com/@adityarealtysmartz\",\"portfolio\":\"https://adityarealtyportfolio.in\"}', '1', '2026-10-17', NULL, 'typed', 'Aditya Sharma', '100', '0', '2026-10-17 14:51:56', '2026-10-18 17:40:08'),
  ('19', '21', 'Kailash Varma', NULL, NULL, NULL, NULL, NULL, NULL, 'Indian', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '0', '0', '2026-10-17 15:11:54', '2026-10-17 15:11:54'),
  ('20', '22', 'Kailash Varma', NULL, NULL, NULL, NULL, NULL, NULL, 'Indian', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '0', '0', '2026-10-17 15:14:14', '2026-10-17 15:14:14'),
  ('21', '23', 'Kailash Varma', NULL, NULL, NULL, NULL, NULL, NULL, 'Indian', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, 'qlZ5dibmODwIXQfbLsYoHHed/xhr4C6egSCETX5czsnvS5NEkKVfIg1hSdpb8SNr66/F2X/MgwydnzAvRWbbuQ==', '7654', '/yVwabH08dH10PMY49gLDuj7wujtvdT0GDS+hZeBqPvUFVymJUcs/E11LCIh96UwlLRkt/28hpHXDqaPNFkZHQ==', '9876', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '0', '0', '2026-10-17 15:15:12', '2026-10-17 15:15:15'),
  ('22', '24', 'Kailash Varma', NULL, NULL, NULL, NULL, NULL, NULL, 'Indian', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, 'VBnDAJMeCrqgqFrjST9dVOmNCAZOvw2x9pQ6RKdZnKYdCLXxqn+Zw9uKnMTker5MN0QNL+dlG+Vir1eHo5Cz9g==', '7654', 'TJyQozXZqOdZWY0GBAzUgqrrSC/plOCPXv/1KHuE54DJMD435u7K+zSPzKwlexID1efwElNjqQmrCUemrOvaVQ==', '9876', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '0', '0', '2026-10-17 15:15:30', '2026-10-17 15:15:32'),
  ('23', '25', 'Kailash Varma', NULL, NULL, NULL, NULL, NULL, NULL, 'Indian', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, 'Te3vsnH8isTAsV8ZFJiVBJHlJXUAxXNFzjiYP4ZLi5EigFy5Y3v3lOX8v+WBt2CKVZG91xFKcyVHySu3BO+gZQ==', '7654', 'wWfNbGTuLhuDRmzE0MLJgn/Zkz7MxwXB9OkeCXC2I/DFxzWfrxqlkGTPeH5lu0rEsr5fJP9RZTHHPFg9GSxoMw==', '9876', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '0', '0', '2026-10-17 15:16:12', '2026-10-17 15:16:14'),
  ('24', '26', 'Kailash Varma', NULL, NULL, NULL, NULL, NULL, NULL, 'Indian', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, 'V5T5RU44KIEAQxrBgJLOwJcXHtTQ+5yKRdsBnPpgfyariAwpTSChT95snhlbTQZwj9EoYmibqEz4S4YnNdWE8g==', '7654', 'RcBgIYYHcdYM3E7hqy+wD6H7FvIkm2rpLQEoMY7DwMakcjwqDLFklSKuSEd8hOWgnUzpoUxByVj5TejDQcCt5Q==', '9876', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '0', '0', '2026-10-17 15:16:44', '2026-10-17 15:16:46'),
  ('25', '27', 'Test Father', NULL, NULL, NULL, NULL, NULL, NULL, 'Indian', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '0', '0', '2026-10-17 15:31:00', '2026-10-17 15:31:00'),
  ('26', '28', 'Test Father', NULL, NULL, NULL, NULL, NULL, NULL, 'Indian', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '0', '0', '2026-10-17 15:31:24', '2026-10-17 15:31:24'),
  ('27', '29', 'Test Father', NULL, NULL, NULL, NULL, NULL, NULL, 'Indian', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '0', '0', '2026-10-17 16:00:08', '2026-10-17 16:00:08'),
  ('28', '30', 'Rajesh', '9306098396', NULL, '2026-10-17', 'male', NULL, NULL, 'Indian', 'D:\\Realty Smartz\\Realty Pathsala/storage/uploads/30_profile_photo_bd18636e8ee76f3e3eee.jpg', 'jind', 'jind', 'jind', '0', 'Gurugram', 'Haryana', '122001', 'mc9PPyEedFEqy9tg4y1UPyTolXO4Ks87QJ4wVIofIPetdrrk+f2EB0PrHBcTH/dvAobkshRIiMSW9vTEPEJ/wQ==', 'mc9PPyEedFEqy9tg4y1UPyTolXO4Ks87QJ4wVIofIPetdrrk+f2EB0PrHBcTH/dvAobkshRIiMSW9vTEPEJ/wQ==', '5839', 'NhhMolSZHsp3YkANkI38zVVuWsNqRbBTwv36fIt+kfJnbp+b65cg8cw609Xm3PLqTkURrQyeaPbj7fTqmHfNYQ==', '887F', 'ADAAG3887F', NULL, '{\"linkedin\":\"\",\"instagram\":\"\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '1', '2026-10-17', NULL, 'typed', 'Abhishek', '100', '1', '2026-10-17 16:17:00', '2026-10-17 16:42:08'),
  ('29', '32', 'Rawat', '8178635472', NULL, '2026-10-18', 'male', NULL, NULL, 'Indian', NULL, 'fasgeadsfbdsvesvsdvbfdbsdvfc', 'fasgeadsfbdsvesvsdvbfdbsdvfc', 'fasgeadsfbdsvesvsdvbfdbsdvfc', '0', 'Gurugram', 'Haryana', '122001', 'rYWMgL2zLxHF0DrdT9bze+mjnXg4kM3Lr6xhvYt/Pe9w+voKPo3V+G3EzPcAzram+opSHLftjkyWe2cIAcM/mw==', 'rYWMgL2zLxHF0DrdT9bze+mjnXg4kM3Lr6xhvYt/Pe9w+voKPo3V+G3EzPcAzram+opSHLftjkyWe2cIAcM/mw==', '9012', 't2N8AV/DNh5Tn1faAY33u7D5gKpULNtkySZUTWrVqbvjedkmgZ01IyeJ4l70al31OdjiCJOid4iNwy5ZzKN8lw==', '234F', 'ABCDE1234F', NULL, '{\"linkedin\":\"\",\"instagram\":\"\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '0', NULL, NULL, NULL, NULL, '75', '0', '2026-10-18 14:46:50', '2026-10-18 17:26:16'),
  ('30', '33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Indian', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, '2026-12-15', NULL, NULL, '0', '0', '2026-10-18 18:30:05', '2026-10-19 11:55:29');

-- --------------------------------------------------------
-- Table structure for `project_images`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `project_images`;
CREATE TABLE `project_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `order_num` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_pi_proj` (`project_id`),
  CONSTRAINT `fk_pi_proj` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `projects`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `category` enum('featured','luxury','new','intl') NOT NULL DEFAULT 'featured',
  `location` varchar(150) NOT NULL,
  `price_range` varchar(100) NOT NULL,
  `type_config` varchar(100) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `highlights` text DEFAULT NULL,
  `cover_image` varchar(255) NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `order_num` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_proj_cat` (`category`),
  KEY `idx_proj_published` (`is_published`,`is_archived`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `projects`
INSERT INTO `projects` (`id`, `title`, `slug`, `category`, `location`, `price_range`, `type_config`, `description`, `highlights`, `cover_image`, `is_featured`, `is_published`, `is_archived`, `order_num`, `created_at`) VALUES
  ('1', 'Shapoorji Pallonji', 'shapoorji-pallonji-gurgaon', 'featured', 'Gurgaon, Sector 102', '₹3.20 Cr+', '3 BHK & 4 BHK Luxury Residences', 'An ultra-luxurious residential landmark featuring signature architecture, expansive green views, and world-class sports amenities on the Dwarka Expressway.', '[\"Ultra-luxury clubhouse\", \"Olympic size swimming pool\", \"Air purification system\", \"Direct Dwarka Expressway connectivity\"]', '/assets/img/projects/shapoorji.jpg', '1', '1', '0', '1', '2026-10-17 12:24:20'),
  ('2', 'DLF Privana', 'dlf-privana-sector-76', 'luxury', 'Sector 76, Gurgaon', '₹6.50 Cr+', '4 BHK Ultra Luxury High-Rise', 'DLF Privana represents the pinnacle of luxury living in Gurgaon, nested amidst the scenic Aravalli hills with expansive low-density planning and bespoke master craftsmanship.', '[\"Nestled near Aravalli green corridor\", \"Grand triple-height lobbies\", \"Low density 4-to-a-core layout\", \"Private elevators\"]', '/assets/img/projects/dlf-privana.jpg', '1', '1', '0', '2', '2026-10-17 12:24:20'),
  ('3', 'Signature Global Daxin', 'signature-global-daxin-sohna', 'new', 'Sohna, South Gurgaon', '₹1.15 Cr+', 'Premium Independent Floors & 3 BHK', 'Strategically situated on the Sohna Elevated Corridor, offering serene foothill tranquility with immediate access to Cyber City and Rajiv Chowk.', '[\"Sohna elevated corridor 15 mins to Rajiv Chowk\", \"Zero pollution living\", \"Exclusive basement & terrace rights\", \"Dedicated car parking\"]', '/assets/img/projects/signature-global.jpg', '1', '1', '0', '3', '2026-10-17 12:24:20'),
  ('4', 'Heritage Plots Pataudi', 'heritage-plots-pataudi', 'new', 'Pataudi Road, Gurgaon', '₹65 Lakh+', 'Freehold Residential Plotted Development', 'Gated township with wide internal boulevards, underground cabling, lush fruit orchards, and lucrative appreciation along the growth corridor.', '[\"100% Freehold RERA approved plots\", \"Gated security with CCTV surveillance\", \"Clubhouse & jogging tracks\", \"Proximity to upcoming orbital rail corridor\"]', '/assets/img/projects/heritage-plots.jpg', '0', '1', '0', '4', '2026-10-17 12:24:20'),
  ('5', 'Omaxe The State', 'omaxe-the-state-dwarka', 'featured', 'Sector 19B, Dwarka, Delhi', '₹85 Lakh+', 'Integrated Commercial, Sports Arena & Retail', 'North India\'s largest commercial-cum-sports destination adjacent to Yashobhoomi Convention Center and Bharat Vandana Park.', '[\"Adjacent to Asia\'s largest convention center\", \"ICC standard cricket stadium\", \"High footfall retail high street\", \"International food court & 5-star club\"]', '/assets/img/projects/omaxe.jpg', '1', '1', '0', '5', '2026-10-17 12:24:20'),
  ('6', 'Ganga Realty Fusion 85', 'ganga-realty-sector-85', 'luxury', 'Sector 85, Gurgaon', '₹1.80 Cr+', '3 BHK & 4 BHK Pure Luxury Towers', 'Architectural marvel combining futuristic smart-home automation with AI-powered security, private deck gardens, and infinity sky lounges.', '[\"AI-powered smart residential towers\", \"Infinity sky pool overlooking skyline\", \"Zero vehicular movement on ground level\", \"Near proposed metro station\"]', '/assets/img/projects/ganga-realty.jpg', '1', '1', '0', '6', '2026-10-17 12:24:20'),
  ('7', 'Trehan Iris Broadway', 'trehan-sector-35-gurgaon', 'featured', 'Sector 35, Sohna Road', '₹95 Lakh+', 'Commercial High Street & Office Spaces', 'High-street retail destination capturing over 50,000 captive families with prime road frontage and double-height showroom units.', '[\"3-side open corner plot\", \"Anchored by national hypermarket\", \"Dedicated entertainment zone with 6-screen multiplex\", \"Assured high rental yield\"]', '/assets/img/projects/trehan.jpg', '0', '1', '0', '7', '2026-10-17 12:24:20'),
  ('8', 'Dubai Luxury Projects', 'dubai-luxury-residences-uae', 'intl', 'Downtown & Business Bay, UAE', 'AED 1.2M+ (₹2.7 Cr+)', 'Waterfront Apartments & Sky Villas', 'High-ROI offshore investment portfolio with 0% income tax, 10-year Golden Visa eligibility, and rental yields averaging 8-10% tax-free.', '[\"10-Year UAE Golden Visa eligibility\", \"Tax-free rental yields up to 10%\", \"Private beach & marina promenade\", \"Flexible 80/20 post-handover payment plan\"]', '/assets/img/projects/dubai.jpg', '1', '1', '0', '8', '2026-10-17 12:24:20'),
  ('9', 'M3M Capital', 'm3m-capital-dwarka-expressway', 'luxury', 'Sector 113, Dwarka Expressway', '₹2.80 Cr+', 'Golf-Themed Luxury 3.5 & 4.5 BHK', '0 km from Delhi border, overlooking 60-meter wide arterial boulevards with private golf putting greens, sky club, and valet concierge.', '[\"Zero km from Delhi border\", \"Golf-inspired landscaped gardens\", \"Huge 75,000 sq.ft luxury clubhouse\", \"Multi-tier security protocol\"]', '/assets/img/projects/m3m.jpg', '0', '1', '0', '9', '2026-10-17 12:24:20'),
  ('10', 'Danube Properties Dubai', 'danube-properties-maritime-city', 'intl', 'Maritime City & JLT, Dubai', 'AED 950K+ (₹2.1 Cr+)', 'Fully Furnished Designer Waterfront Suites', 'Fully furnished luxury studio to 3 BHK suites with over 40 resort-grade amenities and Danube\'s hallmark 1% per month payment plan.', '[\"Famous 1% per month payment plan\", \"Doctor on call & private infinity pool in apartment\", \"100% freehold foreign ownership\", \"Handover with Italian designer furnishings\"]', '/assets/img/projects/danube.jpg', '0', '1', '0', '10', '2026-10-17 12:24:20');

-- --------------------------------------------------------
-- Table structure for `questions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `questions`;
CREATE TABLE `questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `set_id` int(10) unsigned DEFAULT NULL,
  `question_text` text NOT NULL,
  `explanation` text DEFAULT NULL,
  `marks` int(10) unsigned NOT NULL DEFAULT 1,
  `difficulty` enum('easy','medium') NOT NULL DEFAULT 'medium',
  `negative_marks` decimal(3,2) NOT NULL DEFAULT 0.25,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_q_cat` (`category_id`),
  KEY `idx_q_active` (`is_active`,`is_archived`),
  CONSTRAINT `fk_q_cat` FOREIGN KEY (`category_id`) REFERENCES `test_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `questions`
INSERT INTO `questions` (`id`, `category_id`, `set_id`, `question_text`, `explanation`, `marks`, `difficulty`, `negative_marks`, `is_active`, `is_archived`, `created_at`) VALUES
  ('1', '1', '1', 'Find the next number in the sequence: 4, 9, 19, 39, 79, ?', 'The pattern is: (Previous number * 2) + 1. So 79 * 2 + 1 = 159.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:26'),
  ('2', '1', '1', 'Pointing to a photograph of a man, Vikas said, \"His mother is the only daughter of my mother.\" How is Vikas related to the man?', 'Only daughter of Vikas\'s mother is Vikas\'s sister. Her son is the man in the photo. Hence, Vikas is the maternal uncle (Mama).', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:26'),
  ('3', '1', '1', 'If DELHI is coded as 73541 and CALCUTTA as 82589662, how is CALICUT coded?', 'Direct letter-to-digit substitution: C=8, A=2, L=5, I=1, C=8, U=9, T=6. Result is 8251896.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:26'),
  ('4', '2', '1', 'A real estate broker sells a 3 BHK luxury apartment for ₹1.60 Crore. If the agreed brokerage is 1.5%, what is the broker\'s commission?', '1.5% of 1,60,00,000 = (1.5 / 100) * 1,60,00,000 = ₹2,40,000.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:26'),
  ('5', '2', '1', 'An office plot measures 60 feet by 90 feet. What is the area of this plot in square yards (Gaj)?', 'Area in sq ft = 60 * 90 = 5400 sq ft. Since 1 sq yard = 9 sq ft, Area in sq yards = 5400 / 9 = 600 sq yards.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:26'),
  ('6', '2', '1', 'A commercial shop bought for ₹40 Lakh is sold for ₹48 Lakh. What is the percentage profit earned on this investment?', 'Profit = 48 - 40 = 8 Lakh. Percentage profit = (8 / 40) * 100 = 20%.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:26'),
  ('7', '3', '1', 'Choose the grammatically correct sentence for formal business communication with an HNI client:', 'Option B maintains formal, courteous tone, proper conditional agreement, and professional vocabulary.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:27'),
  ('8', '3', '1', 'Select the word most similar in meaning to \"PRUDENT\":', 'Prudent means acting with or showing care, caution and good judgement for the future. \"Wise / Cautious\" is the exact synonym.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:27'),
  ('9', '3', '1', 'Fill in the blank: \"The high-rise tower project was delayed _______ unseasonal monsoon rains and material shortages.\"', '\"due to\" correctly introduces the causative noun phrase explaining the delay.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:27'),
  ('10', '4', '1', 'Under RERA (Real Estate Regulation and Development Act), what minimum percentage of the funds collected from homebuyers must be deposited into a dedicated escrow account?', 'Under Section 4(2)(l)(D) of RERA, 70% of the amounts realized for the real estate project from allottees must be deposited into a separate scheduled bank account exclusively to cover construction and land cost.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:27'),
  ('11', '4', '1', 'What is the precise definition of \"Carpet Area\" as mandated by RERA?', 'RERA mandates carpet area as the net usable floor area of an apartment, excluding the area covered by external walls, service shafts, and exclusive balcony/verandah, but including the area covered by internal partition walls.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:27'),
  ('12', '4', '1', 'What is \"Circle Rate\" (also called Collector Rate / Guidance Value) in Indian property transactions?', 'The Circle Rate is the minimum government-mandated price per unit area below which a property transaction cannot be registered in the sub-registrar office for stamp duty calculation.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:27'),
  ('13', '5', '1', 'A prospective buyer says during a site visit: \"The property is great, but the price is ₹15 Lakh above my budget.\" What is the best initial response?', 'Acknowledging the budget, validating priorities, and exploring payment plans or value breakdown before immediately discounting preserves both credibility and developer margin.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:27'),
  ('14', '5', '1', 'What is the primary objective of a \"Discovery Call\" with an inbound real estate lead?', 'A discovery call identifies BANT (Budget, Authority, Need/Preferences, and Timeline) so the property consultant presents only relevant, high-probability options.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:28'),
  ('15', '5', '1', 'Which closing technique is demonstrated when a sales executive asks: \"Would you prefer the corner park-facing unit on the 8th floor or the courtyard view on the 12th floor for your family?\"', 'This is the \"Alternative Choice Close\" (assumptive close), which shifts the client\'s decision from \"whether to buy\" to \"which unit suits them best\".', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:28'),
  ('16', '1', '2', 'Statements: All builders are licensed. Some licensed individuals are architects. Conclusion I: Some architects are builders. Conclusion II: All architects are licensed.', 'From the premises, we know builders are subset of licensed, and some licensed are architects. We cannot conclude that architects are builders, nor that all architects are licensed. Neither follows.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:28'),
  ('17', '1', '2', 'Anurag walks 10 meters North, turns Right and walks 15 meters, then turns Right again and walks 10 meters. In which direction and how far is he from his starting point?', 'Moving 10m N, then 15m E, then 10m S puts Anurag directly 15 meters East of his starting position.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:28'),
  ('18', '1', '2', 'Which word does NOT belong with the others in a commercial real estate context?', 'Leasehold, Freehold, and Registry pertain to legal land ownership titles. Subprime relates to high-risk credit lending.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:28'),
  ('19', '2', '2', 'A residential floor has a super built-up area of 2,200 sq ft and a loading factor of 25% over the carpet area. What is the approximate carpet area of the unit?', 'Super Built-up = Carpet Area * (1 + 0.25). So Carpet Area = 2200 / 1.25 = 1,760 sq ft.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:28'),
  ('20', '2', '2', 'An investor deposits ₹50,00,000 in a fixed real estate yield bond offering 9% simple interest per annum. What total interest will be earned over 3 years?', 'SI = (P * R * T) / 100 = (50,00,000 * 9 * 3) / 100 = ₹13,50,000.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:28'),
  ('21', '2', '2', 'If 5 sales associates can complete 40 physical site tours in 4 days, how many site tours can 8 sales associates complete in 6 days at the same pace?', 'Work per person per day = 40 / (5 * 4) = 2 tours/day. For 8 associates in 6 days = 8 * 6 * 2 = 96 site tours.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:29'),
  ('22', '3', '2', 'Identify the sentence with correct punctuation and professional tone:', 'Option B uses correct capitalization, comma placement, and precise business English.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:29'),
  ('23', '3', '2', 'Choose the antonym for the word \"TRANSPARENT\" in property deals:', 'Transparent means open, clear and candid. \"Opaque\" means obscure, cloudy and concealed.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:29'),
  ('24', '3', '2', 'Fill in the blank with the appropriate preposition: \"The developer has complied _______ all statutory fire safety norms.\"', 'The verb \"comply\" is standardly paired with the preposition \"with\" (complied with).', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:29'),
  ('25', '4', '2', 'What does FAR (Floor Area Ratio) or FSI (Floor Space Index) represent in urban planning?', 'FAR is the ratio of a building\'s total gross floor area (built-up area) to the total area of the land parcel upon which it stands.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:29'),
  ('26', '4', '2', 'What is an \"Encumbrance Certificate\" (EC) in property legal due diligence?', 'An Encumbrance Certificate verifies whether a property is free from any legal claims, existing mortgages, bank liens, or legal encumbrances during a specified duration.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:29'),
  ('27', '4', '2', 'In Haryana (Gurgaon/Faridabad), who is the primary statutory regulator for real estate projects and agent registration?', 'HRERA (Haryana Real Estate Regulatory Authority), with benches in Gurugram and Panchkula, regulates and registers projects and brokers in Haryana.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:30'),
  ('28', '5', '2', 'A buyer is hesitant, stating: \"I want to wait for interest rates to drop before making a purchase.\" How should you address this?', 'Explaining that property capital appreciation typically outpaces minor interest rate cuts—and that loans can be refinanced later while unit selection cannot—creates balanced urgency.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:30'),
  ('29', '5', '2', 'What does \"Active Listening\" signify during a face-to-face consultation with property investors?', 'Active listening involves concentrating fully, clarifying underlying motivations (e.g. child\'s schooling, rental yield), and summarizing before pitching solutions.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:30'),
  ('30', '5', '2', 'When a client says \"Send me the details on WhatsApp, I will review later\", what is the most effective proactive response?', 'Sending the brief while anchoring a specific follow-up time prevents the lead from going cold while maintaining professional discipline.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:30'),
  ('31', '1', '3', 'Complete the letter series: BDF, HJL, NPR, ?', 'Letters skip 1 letter within groups (B, D, F) and start the next group 2 letters after the end (F + 2 = H, L + 2 = N, R + 2 = T). The next group is TVX.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:30'),
  ('32', '1', '3', 'In a row of 40 luxury villa buyers, Manish is 14th from the left end. What is his rank from the right end?', 'Rank from other end = Total - Given Rank + 1 = 40 - 14 + 1 = 27th.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:30'),
  ('33', '1', '3', 'If REALTY is coded as 18-5-1-12-20-25, what is the code for SMART?', 'Letters correspond directly to their alphabetical positions: S=19, M=13, A=1, R=18, T=20.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:30'),
  ('34', '2', '3', 'A commercial floor generates a monthly rental income of ₹1,50,000. If the total acquisition cost was ₹2.50 Crore, what is the Gross Rental Yield?', 'Annual Rent = 1,50,000 * 12 = ₹18,00,000. Gross Yield = (18,00,000 / 2,50,00,000) * 100 = 7.2%.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:31'),
  ('35', '2', '3', 'The ratio of investment between two partners in a land deal is 3:5. If the total annual capital gain is ₹64,00,000, what is the larger share?', 'Total parts = 3 + 5 = 8 parts. 1 part = 64,00,000 / 8 = 8,00,000. Larger share (5 parts) = 5 * 8,00,000 = ₹40,00,000.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:31'),
  ('36', '2', '3', 'A home loan of ₹80 Lakh is sanctioned at 8.5% annual interest. What is the approximate interest payable in the first month alone?', 'Monthly interest = (80,00,000 * 0.085) / 12 = 6,80,000 / 12 ≈ ₹56,667.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:31'),
  ('37', '3', '3', 'Select the most professional phrase to conclude a client email pitch:', '\"I look forward to assisting you in finding your ideal property\" is courteous, client-centric, and professional.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:31'),
  ('38', '3', '3', 'Choose the correctly spelled word:', '\"ACCOMMODATION\" is spelled with two \'c\'s and two \'m\'s.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:31'),
  ('39', '3', '3', 'Identify the synonym for \"TENACIOUS\" in sales:', 'Tenacious means persistent, determined, and holding firm. \"Persistent\" is the direct synonym.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:31'),
  ('40', '4', '3', 'What is the standard purpose of an \"Occupancy Certificate\" (OC) issued by civic authorities?', 'An OC certifies that a building has been constructed strictly in accordance with approved municipal plans and is safe for human habitation.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:31'),
  ('41', '4', '3', 'In property transactions in India, what is the role of Stamp Duty?', 'Stamp duty is a state government tax levied on legal property transfer documents to validate and legally record the transaction.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:32'),
  ('42', '4', '3', 'What does \"Freehold Property\" mean in contrast to \"Leasehold Property\"?', 'Freehold ownership grants complete, indefinite title to the land and structure with full transfer rights and no expiration period.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:32'),
  ('43', '5', '3', 'A customer says: \"Developer X across the road is offering units ₹500/sq ft cheaper than your project.\" How should a professional sales consultant respond?', 'Selling value over raw price by highlighting superior specifications, clubhouse amenities, construction speed, and track record justifies price differentiation without attacking rivals.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:32'),
  ('44', '5', '3', 'What is the \"Urgency Technique\" in high-demand real estate launches, and how must it be ethically applied?', 'Ethical urgency communicates verified facts (e.g. only 4 garden-facing units remaining at current launch pricing) to prompt timely action without fraudulent claims.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:32'),
  ('45', '5', '3', 'Which CRM habit is most vital for high-performing real estate sales professionals?', 'Accurate, timely logging of lead interactions, next follow-up dates, and personal preferences ensures consistent nurturing and high pipeline velocity.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:32'),
  ('46', '1', '4', 'Look at the series: 2, 6, 12, 20, 30, 42, ? What number comes next?', 'Differences are increasing by 2: +4, +6, +8, +10, +12, +14. So 42 + 14 = 56 (or n^2 + n: 7^2 + 7 = 56).', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:32'),
  ('47', '1', '4', 'If CLOCK is written as KCOLC, how is RESIDENCE written in that code?', 'The word is spelled backwards in reverse order: E-C-N-E-D-I-S-E-R.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:32'),
  ('48', '1', '4', 'Five commercial towers A, B, C, D, and E are situated in a tech park. B is North of A. C is East of B. D is South of C. What is the direction of D from A?', 'A to B is North. B to C is East. C to D is South (equal to B to A). Therefore, D is directly East of A.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:33'),
  ('49', '2', '4', 'An investor purchases a plot of land for ₹80 Lakh. Over 2 years, the plot appreciates by 15% in year 1 and 20% in year 2. What is its value at the end of 2 years?', 'Year 1: 80 Lakh * 1.15 = 92 Lakh. Year 2: 92 Lakh * 1.20 = ₹110.4 Lakh (₹1.104 Crore).', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:33'),
  ('50', '2', '4', 'A showroom with a carpet area of 1,200 sq ft has a monthly maintenance fee of ₹6.50 per sq ft. What is the total maintenance cost per annum?', 'Monthly charge = 1200 * 6.50 = ₹7,800. Annual charge = 7,800 * 12 = ₹93,600.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:33'),
  ('51', '2', '4', 'In a residential project of 240 units, 75% of units have been booked in Phase 1. How many units remain unsold?', 'Unsold percentage = 25%. Unsold units = 0.25 * 240 = 60 units.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:33'),
  ('52', '3', '4', 'Select the most appropriate word to fill in the blank: \"All land acquisition documents have been _______ verified by our legal advocates.\"', '\"meticulously\" (meaning with great attention to detail) fits best in legal scrutiny context.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:33'),
  ('53', '3', '4', 'Choose the sentence with the correct subject-verb agreement:', '\"The team of senior real estate consultants is\" takes singular verb because subject is \"team\".', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:33'),
  ('54', '3', '4', 'What is the meaning of the idiom \"to seal the deal\"?', '\"To seal the deal\" means to finalize or close a business agreement or contract successfully.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:33'),
  ('55', '4', '4', 'What is the key difference between an \"Agreement for Sale\" and a \"Sale Deed\" (Conveyance Deed)?', 'An Agreement for Sale is a promise to transfer property in the future upon conditions being met, whereas a registered Sale Deed actually executes the immediate legal transfer of ownership.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:34'),
  ('56', '4', '4', 'Under Indian banking practice, what does LTV (Loan-to-Value) ratio designate for home buyers?', 'LTV is the percentage of property value that a financial institution or bank will lend as a home loan (e.g. 80% LTV means the bank finances 80% and the buyer provides 20% down payment).', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:34'),
  ('57', '4', '4', 'What is a \"Possession Letter\" in the real estate handover process?', 'A Possession Letter is issued by the developer confirming that the buyer has fulfilled all financial obligations and is granted physical possession and keys of the property unit.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:34'),
  ('58', '5', '4', 'A prospective homebuyer brings their family for a second site visit and seems interested, but stays quiet. What should you do?', 'Encouraging family members to share their thoughts and vision for how they will use the space uncovers hidden objections and emotional buy-in.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:34'),
  ('59', '5', '4', 'How should a real estate sales executive handle a situation where a client asks a technical legal question they do not know the answer to?', 'Admitting you will verify with the legal/technical department and committing to a concrete callback time builds trust and prevents inaccurate commitments.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:34'),
  ('60', '5', '4', 'What is \"Referral Selling\" and why is it considered the highest-converting channel in premium real estate?', 'Referral selling leverages the trust of satisfied existing homeowners who recommend friends and family, resulting in higher conversion and shorter sales cycles.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:34'),
  ('61', '1', '5', 'In a letter-shifting pattern, each letter in a word is moved forward by one position in the alphabet (for example, \"CAT\" becomes \"DBU\" as C→D, A→B, T→U). Following this rule, what does the word \"SOLID\" become?', 'Shifting each letter of SOLID forward by one position (S→T, O→P, L→M, I→J, D→E) gives TPMJE.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:34'),
  ('62', '1', '5', 'A clock shows 3:00. What is the angle between the hour hand and the minute hand?', 'At 3:00, the hour hand points to 3 and the minute hand points to 12. Each hour mark is 360 / 12 = 30 degrees. 3 * 30 = 90 degrees.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:35'),
  ('63', '1', '5', 'Which of the following numbers does NOT fit the pattern: 8, 27, 64, 100, 125, 216?', 'The pattern consists of perfect cubes: 2^3=8, 3^3=27, 4^3=64, 5^3=125, 6^3=216. 100 is 10^2, not a perfect cube.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:35'),
  ('64', '2', '5', 'An apartment was listed for ₹1.25 Crore. After negotiations, it was closed for ₹1.15 Crore. What percentage discount was conceded by the seller?', 'Discount = 1.25 - 1.15 = 0.10 Crore. Percentage = (0.10 / 1.25) * 100 = 8.0%.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:35'),
  ('65', '2', '5', 'A rectangular living hall is 24 feet long and 18 feet wide. What is the cost of Italian marble flooring at ₹250 per sq ft?', 'Floor Area = 24 * 18 = 432 sq ft. Total Cost = 432 * 250 = ₹1,08,000.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:35'),
  ('66', '2', '5', 'An agency sold 15 units in Q1, 20 units in Q2, 25 units in Q3, and 30 units in Q4. What was the average number of units sold per quarter?', 'Average = (15 + 20 + 25 + 30) / 4 = 90 / 4 = 22.5 units.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:35'),
  ('67', '3', '5', 'Choose the word that best fits the blank: \"The location offers _______ connectivity to Golf Course Road and the IGI Airport expressway.\"', '\"seamless\" (smooth, continuous, without friction) is the standard commercial real estate term for connectivity.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:35'),
  ('68', '3', '5', 'Identify the sentence free of redundancy and errors:', 'Option B avoids redundant expressions like \"revert back\" and maintains clean, executive phrasing.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:35'),
  ('69', '3', '5', 'What is the closest synonym for the word \"AUTHENTIC\"?', '\"Genuine\" means authentic, real, and verified.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:36'),
  ('70', '4', '5', 'What is the primary role of an Escrow Account in real estate project governance under RERA?', 'An escrow account ensures that allottee funds are ring-fenced and disbursed only strictly against certified stages of construction and land liabilities, preventing diversion to other ventures.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:36'),
  ('71', '4', '5', 'What does \"TDS under Section 194-IA\" mandate when purchasing immovable property in India worth ₹50 Lakh or more?', 'Under Section 194-IA of the Income Tax Act, the buyer must deduct 1% TDS from the total sale consideration when purchasing immovable property exceeding ₹50 Lakh and deposit it with the government.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:36'),
  ('72', '4', '5', 'What constitutes a \"Plinth Area\" of a building?', 'The plinth area is the built-up covered area measured at the floor level of the basement or of any story, taken from the outer dimensions of the walls.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:36'),
  ('73', '5', '5', 'A client expresses interest but states: \"I want to consult my family before making a final commitment.\" What is the best closing response?', 'Respecting the decision while facilitating the family review (e.g. providing a comprehensive summary or offering a virtual walkthrough for out-of-town members) maintains momentum without being overbearing.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:36'),
  ('74', '5', '5', 'What is the \"Feel, Felt, Found\" method in overcoming property client objections?', 'It empathizes with the client (\"I understand how you feel\"), normalizes the concern (\"other investors felt the same\"), and presents evidence (\"they found the return on investment exceeded expectations\").', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:36'),
  ('75', '5', '5', 'What distinguishes a consultative real estate advisor from a transactional broker?', 'A consultative advisor prioritizes the client\'s long-term wealth, lifestyle goals, and legal security rather than merely chasing quick one-off commission checks.', '1', 'medium', '0.00', '1', '0', '2026-10-17 16:53:36');

-- --------------------------------------------------------
-- Table structure for `settings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) NOT NULL DEFAULT 'general',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `settings`
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `updated_at`) VALUES
  ('1', 'site_name', 'Realty Smartz Pathshala', 'general', '2026-10-18 18:18:52'),
  ('2', 'site_tagline', 'Master the Art of Real Estate | India\'s #1 Real Estate Training', 'general', '2026-10-18 18:18:52'),
  ('3', 'contact_email', 'info@realtysmartzpathshala.in', 'contact', '2026-10-18 18:18:52'),
  ('4', 'hr_email', 'hr@realtysmartzpathshala.in', 'contact', '2026-10-17 12:24:19'),
  ('5', 'contact_phone', '+91 9999 888 777', 'contact', '2026-10-18 18:18:52'),
  ('6', 'contact_address', 'Spaze Plazo, Sector 69, Gurugram, Haryana 122018, India', 'contact', '2026-10-18 18:18:52'),
  ('7', 'working_hours', 'Mon - Sat: 9:30 AM - 6:30 PM (Sunday Closed)', 'contact', '2026-10-17 12:24:19'),
  ('8', 'instagram_url', 'https://www.instagram.com/realtysmartz/', 'social', '2026-10-17 12:24:19'),
  ('9', 'linkedin_url', 'https://www.linkedin.com/company/realtysmartz/', 'social', '2026-10-17 12:24:19'),
  ('10', 'youtube_url', 'https://www.youtube.com/@realtysmartz', 'social', '2026-10-17 12:24:19'),
  ('11', 'smtp_host', 'mail.realtysmartzpathshala.in', 'email', '2026-10-17 16:23:57'),
  ('12', 'smtp_port', '465', 'email', '2026-10-17 16:23:57'),
  ('13', 'smtp_user', 'info@realtysmartzpathshala.in', 'email', '2026-10-17 16:23:57'),
  ('14', 'smtp_pass', '@Realtypathsala2233', 'email', '2026-10-17 16:23:57'),
  ('15', 'smtp_secure', 'ssl', 'email', '2026-10-17 16:23:58'),
  ('16', 'smtp_from_name', 'Realty Smartz Pathshala', 'email', '2026-10-17 12:24:19'),
  ('17', 'smtp_from_email', 'info@realtysmartzpathshala.in', 'email', '2026-10-17 16:23:58'),
  ('18', 'stats_trained', '500+', 'stats', '2026-10-17 12:24:19'),
  ('19', 'stats_projects', '50+', 'stats', '2026-10-17 12:24:19'),
  ('20', 'stats_success_rate', '4.9/5', 'stats', '2026-10-17 12:24:19'),
  ('21', 'stats_placement_rate', '98%', 'stats', '2026-10-17 12:24:19'),
  ('22', 'stats_years_active', '10+ Years', 'stats', '2026-10-17 12:24:19'),
  ('23', 'stats_avg_deal', '₹2.4 Cr', 'stats', '2026-10-17 12:24:19'),
  ('24', 'doc_mandatory_photo', '1', 'documents', '2026-10-17 12:24:19'),
  ('25', 'doc_mandatory_study', '1', 'documents', '2026-10-17 12:24:19'),
  ('26', 'doc_mandatory_offer', '0', 'documents', '2026-10-17 12:24:19'),
  ('27', 'doc_mandatory_relieving', '0', 'documents', '2026-10-17 12:24:19'),
  ('28', 'doc_mandatory_cheque', '1', 'documents', '2026-10-17 12:24:19'),
  ('29', 'doc_mandatory_aadhaar', '1', 'documents', '2026-10-17 12:24:19'),
  ('30', 'doc_mandatory_pan', '1', 'documents', '2026-10-17 12:24:19'),
  ('31', 'test_order', 'after_documents', 'aptitude', '2026-10-17 12:24:19'),
  ('32', 'app_version', '1.0.0', 'system', '2026-10-17 12:24:19'),
  ('33', 'last_deployed_at', '2026-10-17 12:24:19', 'system', '2026-10-17 12:24:19'),
  ('39', 'stat_professionals_trained', '500+', 'general', '2026-10-18 18:18:52'),
  ('40', 'stat_live_projects', '50+', 'general', '2026-10-18 18:18:53'),
  ('41', 'stat_success_rate', '4.9', 'general', '2026-10-18 18:18:53'),
  ('42', 'stat_placement_rate', '98%', 'general', '2026-10-18 18:18:53'),
  ('43', 'stat_years_active', '10+ Years', 'general', '2026-10-18 18:18:53'),
  ('44', 'stat_avg_deal', '2.4 Cr', 'general', '2026-10-18 18:18:53'),
  ('45', 'preloader_text', 'Preparing Your Success Path...', 'general', '2026-10-18 18:18:53');

-- --------------------------------------------------------
-- Table structure for `status_history`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `status_history`;
CREATE TABLE `status_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `comments` text DEFAULT NULL,
  `changed_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_sh_user` (`user_id`),
  CONSTRAINT `fk_sh_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `status_history`
INSERT INTO `status_history` (`id`, `user_id`, `old_status`, `new_status`, `comments`, `changed_by`, `created_at`) VALUES
  ('1', '13', 'registered', 'under_review', '', '1', '2026-10-17 14:37:19'),
  ('2', '13', 'under_review', 'approved', '', '1', '2026-10-17 14:37:32'),
  ('3', '20', 'email_verified', 'documents_submitted', 'Candidate completed 4-step onboarding wizard and submitted documents.', '20', '2026-10-17 14:52:01'),
  ('4', '27', 'documents_submitted', 'test_completed', 'Aptitude Assessment completed with score 10/15 (66.67%) - PASSED', '27', '2026-10-17 15:31:06'),
  ('5', '28', 'documents_submitted', 'test_completed', 'Aptitude Assessment completed with score 10/15 (66.67%) - PASSED', '28', '2026-10-17 15:31:32'),
  ('6', '29', 'documents_submitted', 'test_completed', 'Aptitude Assessment completed with score 10/15 (66.67%) - PASSED', '29', '2026-10-17 16:00:14'),
  ('7', '30', 'email_verified', 'documents_submitted', 'Candidate completed 4-step onboarding wizard and submitted documents.', '30', '2026-10-17 16:42:08'),
  ('8', '30', 'documents_submitted', 'test_completed', 'Aptitude Assessment completed with score 0/15 (0%) - FAILED', '30', '2026-10-17 16:43:01'),
  ('9', '4', 'documents_submitted', 'test_completed', 'Aptitude Assessment completed with score 0/15 (0%) - FAILED', '4', '2026-10-18 13:30:21'),
  ('10', '20', 'documents_submitted', 'approved', '', '1', '2026-10-18 17:39:54'),
  ('11', '20', 'approved', 'changes_requested', '', '1', '2026-10-18 17:40:08'),
  ('12', '20', 'changes_requested', 'approved', '', '1', '2026-10-18 17:40:13'),
  ('13', '33', 'registered', 'test_completed', 'Aptitude Assessment completed with score 0/15 (0%) - FAILED', '33', '2026-10-18 18:31:57'),
  ('14', '33', 'test_completed', 'test_completed', 'Aptitude Assessment completed with score 0/15 (0%) - FAILED', '33', '2026-10-18 18:32:43'),
  ('15', '33', 'test_completed', 'test_completed', 'Aptitude Assessment completed with score 0/15 (0%) - FAILED', '33', '2026-10-18 18:34:20');

-- --------------------------------------------------------
-- Table structure for `test_attempts`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `test_attempts`;
CREATE TABLE `test_attempts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `test_id` int(10) unsigned NOT NULL,
  `set_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `guest_test_link_id` int(11) DEFAULT NULL,
  `attempt_no` int(10) unsigned NOT NULL DEFAULT 1,
  `started_at` datetime NOT NULL,
  `deadline_at` datetime DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `total_questions` int(10) unsigned NOT NULL,
  `total_answered` int(10) unsigned NOT NULL DEFAULT 0,
  `total_correct` int(10) unsigned NOT NULL DEFAULT 0,
  `total_wrong` int(10) unsigned NOT NULL DEFAULT 0,
  `score` decimal(6,2) NOT NULL DEFAULT 0.00,
  `percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `passed` tinyint(1) NOT NULL DEFAULT 0,
  `tab_switch_count` int(10) unsigned NOT NULL DEFAULT 0,
  `fullscreen_exit_count` int(10) unsigned NOT NULL DEFAULT 0,
  `status` enum('in_progress','completed','timed_out','cancelled') NOT NULL DEFAULT 'in_progress',
  `allow_reattempt` tinyint(1) NOT NULL DEFAULT 0,
  `reattempt_allowed_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ta_user` (`user_id`),
  KEY `idx_ta_test` (`test_id`),
  KEY `idx_guest_link` (`guest_test_link_id`),
  CONSTRAINT `fk_ta_test` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ta_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `test_attempts`
INSERT INTO `test_attempts` (`id`, `test_id`, `set_id`, `user_id`, `guest_test_link_id`, `attempt_no`, `started_at`, `deadline_at`, `submitted_at`, `expires_at`, `completed_at`, `total_questions`, `total_answered`, `total_correct`, `total_wrong`, `score`, `percentage`, `passed`, `tab_switch_count`, `fullscreen_exit_count`, `status`, `allow_reattempt`, `reattempt_allowed_by`, `created_at`) VALUES
  ('1', '1', '1', '6', NULL, '1', '2026-09-23 15:04:32', '2026-09-23 15:14:32', '2026-09-23 15:12:32', '2026-09-23 15:14:32', '2026-09-23 15:12:32', '15', '15', '14', '1', '14.00', '93.33', '1', '0', '0', 'completed', '0', NULL, '2026-09-23 15:12:32'),
  ('2', '1', '2', '7', NULL, '1', '2026-10-15 15:04:33', '2026-10-15 15:14:33', '2026-10-15 15:12:33', '2026-10-15 15:14:33', '2026-10-15 15:12:33', '15', '15', '12', '3', '12.00', '80.00', '1', '0', '0', 'completed', '0', NULL, '2026-10-15 15:12:33'),
  ('3', '1', '3', '8', NULL, '1', '2026-10-16 15:04:33', '2026-10-16 15:14:33', '2026-10-16 15:12:33', '2026-10-16 15:14:33', '2026-10-16 15:12:33', '15', '15', '11', '4', '11.00', '73.33', '1', '0', '0', 'completed', '0', NULL, '2026-10-16 15:12:33'),
  ('4', '1', '1', '9', NULL, '1', '2026-10-12 15:04:34', '2026-10-12 15:14:34', '2026-10-12 15:12:34', '2026-10-12 15:14:34', '2026-10-12 15:12:34', '15', '15', '13', '2', '13.00', '86.67', '1', '0', '0', 'completed', '0', NULL, '2026-10-12 15:12:34'),
  ('5', '1', '4', '10', NULL, '1', '2026-10-14 15:04:35', '2026-10-14 15:14:35', '2026-10-14 15:12:35', '2026-10-14 15:14:35', '2026-10-14 15:12:35', '15', '15', '10', '5', '10.00', '66.67', '1', '0', '0', 'completed', '0', NULL, '2026-10-14 15:12:35'),
  ('6', '1', '2', '11', NULL, '1', '2026-09-29 15:04:35', '2026-09-29 15:14:35', '2026-09-29 15:12:35', '2026-09-29 15:14:35', '2026-09-29 15:12:35', '15', '15', '15', '0', '15.00', '100.00', '1', '0', '0', 'completed', '0', NULL, '2026-09-29 15:12:35'),
  ('7', '1', '5', '15', NULL, '1', '2026-10-11 15:04:37', '2026-10-11 15:14:37', '2026-10-11 15:12:37', '2026-10-11 15:14:37', '2026-10-11 15:12:37', '15', '15', '7', '8', '7.00', '46.67', '0', '0', '0', 'completed', '0', NULL, '2026-10-11 15:12:37'),
  ('8', '1', '1', '16', NULL, '1', '2026-10-15 15:04:37', '2026-10-15 15:14:37', '2026-10-15 15:12:37', '2026-10-15 15:14:37', '2026-10-15 15:12:37', '15', '15', '13', '2', '13.00', '86.67', '1', '0', '0', 'completed', '0', NULL, '2026-10-15 15:12:37'),
  ('9', '1', '3', '17', NULL, '1', '2026-09-27 15:04:38', '2026-09-27 15:14:38', '2026-09-27 15:12:38', '2026-09-27 15:14:38', '2026-09-27 15:12:38', '15', '15', '14', '1', '14.00', '93.33', '1', '0', '0', 'completed', '0', NULL, '2026-09-27 15:12:38'),
  ('10', '1', '5', '27', NULL, '1', '2026-10-17 15:30:59', '2026-10-17 15:40:59', '2026-10-17 15:31:06', '2026-10-17 15:40:59', '2026-10-17 15:31:06', '15', '15', '10', '5', '10.00', '66.67', '1', '2', '0', 'completed', '0', NULL, '2026-10-17 15:31:00'),
  ('11', '1', '5', '28', NULL, '1', '2026-10-17 15:31:24', '2026-10-17 15:41:24', '2026-10-17 15:31:32', '2026-10-17 15:41:24', '2026-10-17 15:31:32', '15', '15', '10', '5', '10.00', '66.67', '1', '2', '0', 'completed', '0', NULL, '2026-10-17 15:31:25'),
  ('12', '1', '4', '29', NULL, '1', '2026-10-17 16:00:08', '2026-10-17 16:10:08', '2026-10-17 16:00:14', '2026-10-17 16:10:08', '2026-10-17 16:00:14', '15', '15', '10', '5', '10.00', '66.67', '1', '2', '0', 'completed', '0', NULL, '2026-10-17 16:00:08'),
  ('13', '1', '5', '30', NULL, '1', '2026-10-17 16:42:18', '2026-10-17 16:52:18', '2026-10-17 16:43:01', '2026-10-17 16:52:18', '2026-10-17 16:43:01', '15', '0', '0', '0', '0.00', '0.00', '0', '1', '0', 'completed', '0', NULL, '2026-10-17 16:42:19'),
  ('14', '1', '3', '4', NULL, '1', '2026-10-17 17:09:32', '2026-10-17 17:19:32', '2026-10-18 13:30:21', '2026-10-17 17:19:32', '2026-10-18 13:30:21', '15', '0', '0', '0', '0.00', '0.00', '0', '1', '0', 'timed_out', '0', NULL, '2026-10-17 17:09:33'),
  ('15', '1', '2', '33', '2', '1', '2026-10-18 18:30:54', '2026-10-18 18:40:54', NULL, '2026-10-18 18:40:54', NULL, '15', '0', '0', '0', '0.00', '0.00', '0', '0', '0', 'in_progress', '0', NULL, '2026-10-18 18:30:54'),
  ('16', '1', '5', '33', '3', '1', '2026-10-18 18:31:51', '2026-10-18 18:41:51', '2026-10-18 18:31:57', '2026-10-18 18:41:51', '2026-10-18 18:31:57', '15', '1', '0', '1', '0.00', '0.00', '0', '0', '0', 'completed', '0', NULL, '2026-10-18 18:31:52'),
  ('17', '1', '4', '33', '4', '1', '2026-10-18 18:32:37', '2026-10-18 18:42:37', '2026-10-18 18:32:42', '2026-10-18 18:42:37', '2026-10-18 18:32:42', '15', '1', '0', '1', '0.00', '0.00', '0', '0', '0', 'completed', '0', NULL, '2026-10-18 18:32:38'),
  ('18', '1', '3', '33', '5', '1', '2026-10-18 18:34:13', '2026-10-18 18:44:13', '2026-10-18 18:34:19', '2026-10-18 18:44:13', '2026-10-18 18:34:19', '15', '1', '0', '1', '0.00', '0.00', '0', '0', '0', 'completed', '0', NULL, '2026-10-18 18:34:13');

-- --------------------------------------------------------
-- Table structure for `test_categories`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `test_categories`;
CREATE TABLE `test_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `test_categories`
INSERT INTO `test_categories` (`id`, `name`, `slug`, `description`, `is_active`, `created_at`) VALUES
  ('1', 'Logical Reasoning', 'logical-reasoning', 'Puzzles, sequences, analogies and analytical deductions', '1', '2026-10-17 12:24:20'),
  ('2', 'Quantitative Aptitude', 'quantitative-aptitude', 'Percentages, ratios, profit & loss, speed & area estimations', '1', '2026-10-17 12:24:20'),
  ('3', 'English & Communication', 'english-verbal', 'Grammar, vocabulary, comprehension and professional email etiquette', '1', '2026-10-17 12:24:20'),
  ('4', 'Real Estate Basics', 'real-estate-basics', 'Terminology, RERA guidelines, carpet vs super area, property documentation', '1', '2026-10-17 12:24:20'),
  ('5', 'Sales & Client Negotiation', 'sales-negotiation', 'Client objection handling, lead qualification, closing psychology', '1', '2026-10-17 12:24:20');

-- --------------------------------------------------------
-- Table structure for `tests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tests`;
CREATE TABLE `tests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `total_questions` int(10) unsigned NOT NULL DEFAULT 25,
  `time_limit_minutes` int(10) unsigned NOT NULL DEFAULT 30,
  `passing_percentage` decimal(5,2) NOT NULL DEFAULT 60.00,
  `negative_marking_per_question` decimal(3,2) NOT NULL DEFAULT 0.25,
  `randomize_questions` tinyint(1) NOT NULL DEFAULT 1,
  `randomize_options` tinyint(1) NOT NULL DEFAULT 1,
  `test_order` enum('before_documents','after_documents') NOT NULL DEFAULT 'after_documents',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `tests`
INSERT INTO `tests` (`id`, `title`, `description`, `total_questions`, `time_limit_minutes`, `passing_percentage`, `negative_marking_per_question`, `randomize_questions`, `randomize_options`, `test_order`, `is_active`, `is_archived`, `created_at`) VALUES
  ('1', 'Realty Smartz Pathshala Aptitude Assessment', 'Comprehensive 25-question evaluation covering logical deductions, quantitative calculations, verbal communication, real estate regulations, and sales closing techniques.', '15', '10', '50.00', '0.00', '1', '1', 'after_documents', '1', '0', '2026-10-17 12:24:20');

-- --------------------------------------------------------
-- Table structure for `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) NOT NULL,
  `father_name` varchar(150) DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('candidate','super_admin','hr_manager','reviewer') NOT NULL DEFAULT 'candidate',
  `is_guest` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','registered','email_verified','profile_completed','documents_submitted','test_pending','test_completed','under_review','approved','changes_requested','rejected') NOT NULL DEFAULT 'registered',
  `email_verified` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` datetime DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `failed_login_attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` datetime DEFAULT NULL,
  `archived_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_email` (`email`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_status` (`status`),
  KEY `idx_users_archived` (`is_archived`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `users`
INSERT INTO `users` (`id`, `full_name`, `father_name`, `email`, `mobile`, `whatsapp`, `joining_date`, `password_hash`, `role`, `is_guest`, `status`, `email_verified`, `email_verified_at`, `remember_token`, `is_locked`, `locked_until`, `failed_login_attempts`, `is_archived`, `archived_at`, `archived_by`, `created_at`, `updated_at`) VALUES
  ('1', 'Super Admin', NULL, 'admin@realtysmartzpathshala.in', '+919876543210', NULL, NULL, '$2y$10$.dwZJA0wNa3zvJXnawqZROtJa7inJKguyA7d6c9bKaM0U64Spumaa', 'super_admin', '0', 'active', '1', '2026-10-17 12:24:19', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 12:24:19', '2026-10-18 18:21:55'),
  ('2', 'HR Manager', NULL, 'hr@realtysmartzpathshala.in', '+919876543211', NULL, NULL, '$2y$10$7in11AzJaJ35YMziftsFZugUW8ELNs5ev1Zr7dHEfAHSoKlPJex8u', 'hr_manager', '0', 'approved', '1', '2026-10-17 12:24:19', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 12:24:19', '2026-10-17 12:24:19'),
  ('3', 'Reviewer Officer', NULL, 'reviewer@realtysmartzpathshala.in', '+919876543212', NULL, NULL, '$2y$10$7in11AzJaJ35YMziftsFZugUW8ELNs5ev1Zr7dHEfAHSoKlPJex8u', 'reviewer', '0', 'approved', '1', '2026-10-17 12:24:19', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 12:24:19', '2026-10-17 12:24:19'),
  ('4', 'Abhishek Sharma', NULL, 'abhishek.sharma@test.com', '9812345678', NULL, NULL, '$2y$11$wh5q96n1RNI5qWs/BeuaGu.5PVpmsETjXRDrI4jTlfb69VqM4HhD2', 'candidate', '0', 'test_completed', '1', '2026-10-18 13:30:17', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 12:26:58', '2026-10-18 13:30:21'),
  ('5', 'Kunal Sharma', NULL, 'kunal.1792220474@example.com', '9876543210', NULL, NULL, '$2y$11$LOk1pyLu9nUK3du3gZnteePIWlXKgZ1wBsi8ceSzGEaL6zD2WULbW', 'candidate', '0', 'email_verified', '1', '2026-10-17 12:31:16', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 12:31:15', '2026-10-17 12:31:16'),
  ('6', 'Aarav Mehta', 'Rajesh Mehta', 'aarav.mehta@example.com', '9820123456', '9820123456', NULL, '$2y$11$DPzMoeM7IK50xO8YE7h4fuwWLRttlZRV/YCURLoNM3CI11oZMqgCy', 'candidate', '0', 'approved', '1', '2026-09-23 14:04:01', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-09-23 14:04:01', '2026-09-23 14:04:01'),
  ('7', 'Priya Sharma', 'Suresh Sharma', 'priya.sharma99@example.com', '9811234567', '9811234567', NULL, '$2y$11$8cxKeZWYZQS.wAwUYd5AAudgh8X6txauTbD/NkHXbiqZ4QfPSjHhO', 'candidate', '0', 'under_review', '1', '2026-10-15 14:04:33', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-15 14:04:33', '2026-10-15 14:04:33'),
  ('8', 'Rohan Varma', 'Anil Varma', 'rohan.varma@example.com', '9876501234', '9876501234', NULL, '$2y$11$BL9WDiBIPjrEniH39eb2t.iX4rYGVGmETd5PjbkKdpjrmbCeKjd9O', 'candidate', '0', 'test_completed', '1', '2026-10-16 14:04:33', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-16 14:04:33', '2026-10-16 14:04:33'),
  ('9', 'Ananya Iyer', 'Ramanathan Iyer', 'ananya.iyer@example.com', '9845012345', '9845012345', NULL, '$2y$11$FfCthfV1cWuj73DjGeXZquYqDTVwBRtbUrFH29y2Mz9TwUiI4u2.i', 'candidate', '0', 'changes_requested', '1', '2026-10-12 14:04:34', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-12 14:04:34', '2026-10-12 14:04:34'),
  ('10', 'Vikram Singhal', 'Mahesh Singhal', 'vikram.singhal@example.com', '9910012345', '9910012345', NULL, '$2y$11$8cccBROwMMbrECPSkGKnguctkKd7f4N.TDlfb59XJtylA0Rm9/Jmy', 'candidate', '0', 'under_review', '1', '2026-10-14 14:04:35', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-14 14:04:35', '2026-10-14 14:04:35'),
  ('11', 'Sneha Patil', 'Deepak Patil', 'sneha.patil@example.com', '9822012345', '9822012345', NULL, '$2y$11$D2DRgL9UBwikUiObvTMQLO0N6br9ac64exylU/sJSINLRyXUUHRhS', 'candidate', '0', 'approved', '1', '2026-09-29 14:04:35', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-09-29 14:04:35', '2026-09-29 14:04:35'),
  ('12', 'Karan Malhotra', 'Sunil Malhotra', 'karan.malhotra@example.com', '9810098765', '9810098765', NULL, '$2y$11$lC..PG/QreCEtgJT/ccYr.YYg35/a8r3wQn.CQDEgiJ56rdh3xOum', 'candidate', '0', 'email_verified', '1', '2026-10-16 14:04:36', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-16 14:04:36', '2026-10-16 14:04:36'),
  ('13', 'Tanvi Chawla', 'Harish Chawla', 'tanvi.chawla@example.com', '9871122334', '9871122334', NULL, '$2y$11$icqTHJ/GjaWaVi3qryxb..ASrdw28X0QuCdnJtUDniIxWtowmylne', 'candidate', '0', 'approved', '1', NULL, NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 14:04:36', '2026-10-17 14:37:32'),
  ('14', 'Aditya Kashyap', 'Vivek Kashyap', 'aditya.kashyap@example.com', '9934012345', '9934012345', NULL, '$2y$11$hRrmr5zKjyEdg1JC4GUpf.9TEqKpUsvbWq.5NNKCJhdqLTLmE2nO6', 'candidate', '0', 'profile_completed', '1', '2026-10-13 14:04:36', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-13 14:04:36', '2026-10-13 14:04:36'),
  ('15', 'Divya Nair', 'Gopal Nair', 'divya.nair@example.com', '9847012345', '9847012345', NULL, '$2y$11$Z120IC547QzXv1rifBsMn.uiIJZgGuOyJMmrql.DBB29y351hQkIG', 'candidate', '0', 'test_completed', '1', '2026-10-11 14:04:37', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-11 14:04:37', '2026-10-11 14:04:37'),
  ('16', 'Mohit Agarwal', 'Pawan Agarwal', 'mohit.agarwal@example.com', '9839012345', '9839012345', NULL, '$2y$11$rewSsvHusxlFR.TpG5LDF.EF85janHEOQSHeYRHXhvN0uhKC20byC', 'candidate', '0', 'under_review', '1', '2026-10-15 14:04:37', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-15 14:04:37', '2026-10-15 14:04:37'),
  ('17', 'Pooja Deshmukh', 'Sanjay Deshmukh', 'pooja.deshmukh@example.com', '9823012345', '9823012345', NULL, '$2y$11$E3AXWaJwWTskTrsmWKIqP.Ctroh3/Shfwj4he5qQpvEQfMKbUbM6u', 'candidate', '0', 'approved', '1', '2026-09-27 14:04:38', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-09-27 14:04:38', '2026-09-27 14:04:38'),
  ('18', 'Aditya Sharma', 'Surender Sharma', 'phase2_test_candidate@realtysmartzpathshala.in', '9876543299', '9876543299', NULL, '$2y$11$R6StbOAZ6D1LpL5ZHl0YIeCH9tOSf2rwzv2rpqEppMiJBkf/p7XGe', 'candidate', '0', 'email_verified', '1', '2026-10-17 14:50:43', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 14:50:42', '2026-10-17 14:50:43'),
  ('19', 'Aditya Sharma', 'Surender Sharma', 'phase2_candidate_1792228894@realtysmartzpathshala.in', '9876115041', '9876543299', NULL, '$2y$11$WHzJvPvW4o42nEb9rK58beuPeMNvwpt.wwY5fVod.SUrA6LYNHPQu', 'candidate', '0', 'email_verified', '1', '2026-10-17 14:51:35', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 14:51:35', '2026-10-17 14:51:35'),
  ('20', 'Aditya Sharma', 'Surender Sharma', 'phase2_candidate_1792228915@realtysmartzpathshala.in', '9876284391', '9876543299', NULL, '$2y$11$/7w2XYcVudse/9t6ETi.q.yAemldxf4FTC.Md3m49/1/fUKCAxHQO', 'candidate', '0', 'approved', '1', '2026-10-17 14:51:56', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 14:51:56', '2026-10-18 17:40:12'),
  ('21', 'Rohan Varma', 'Kailash Varma', 'phase3_tester_1792230113@realtysmartzpathshala.in', '9879975969', NULL, NULL, '$2y$11$WPjD7IKsAcKgfny0BxaiBev/5UedCH5rM7JS/6yjq3kRYmMy1v8xq', 'candidate', '0', 'registered', '1', NULL, NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 15:11:54', '2026-10-17 15:11:54'),
  ('22', 'Rohan Varma', 'Kailash Varma', 'phase3_tester_1792230253@realtysmartzpathshala.in', '9876767486', NULL, NULL, '$2y$11$aOUd473luNqpUBPY2xqZr.cd5K7jVSxgJRV1XqTVZKIkSRg/ES0Iy', 'candidate', '0', 'changes_requested', '1', NULL, NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 15:14:14', '2026-10-17 15:14:16'),
  ('23', 'Rohan Varma', 'Kailash Varma', 'phase3_tester_1792230311@realtysmartzpathshala.in', '9876615250', NULL, NULL, '$2y$11$9FeVAH0OnVft0yxtH8X9.Obsk3Y5kRqD8hV8C6jO4v30ZOcbOORBe', 'candidate', '0', 'changes_requested', '1', NULL, NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 15:15:12', '2026-10-17 15:15:14'),
  ('24', 'Rohan Varma', 'Kailash Varma', 'phase3_tester_1792230330@realtysmartzpathshala.in', '9874188408', NULL, NULL, '$2y$11$zZhh3K.Ln65PcK5bLHncSOTdKHcqZvQIbSbxFKTdrt7clxJoXYqsi', 'candidate', '0', 'changes_requested', '1', NULL, NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 15:15:30', '2026-10-17 15:15:32'),
  ('25', 'Rohan Varma', 'Kailash Varma', 'phase3_tester_1792230371@realtysmartzpathshala.in', '9878183149', NULL, NULL, '$2y$11$slD3RT7et/JeCEpmmu2e1.a4OB.ZXS/WbS.dIZfo/TQu/oMyvBYIy', 'candidate', '0', 'changes_requested', '1', NULL, NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 15:16:12', '2026-10-17 15:16:14'),
  ('26', 'Rohan Varma', 'Kailash Varma', 'phase3_tester_1792230403@realtysmartzpathshala.in', '9878777193', NULL, NULL, '$2y$11$WqF.TyueBoOKAFFCYe8ayekHpcvfu1AewRMNQV0goWrYpkMSMrtve', 'candidate', '0', 'changes_requested', '1', NULL, NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 15:16:44', '2026-10-17 15:16:45'),
  ('27', 'Aptitude Test Candidate', 'Test Father', 'phase4_candidate_1792231259@realtysmartz.in', '9876543210', NULL, NULL, '$2y$11$ilnG3uifOEW6JY.fE0q/PeSab80OQcf08iUKBBuLyJIypB7FgnJw6', 'candidate', '0', 'test_completed', '1', '2026-10-17 15:31:00', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 15:31:00', '2026-10-17 15:31:06'),
  ('28', 'Aptitude Test Candidate', 'Test Father', 'phase4_candidate_1792231284@realtysmartz.in', '9876543210', NULL, NULL, '$2y$11$Sy2.FHbjuE1bh4JJuNl7Ze17JdfDN0hJqLs/nEHymFn9cd0ZLnAXO', 'candidate', '0', 'test_completed', '1', '2026-10-17 15:31:24', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 15:31:24', '2026-10-17 15:31:32'),
  ('29', 'Aptitude Test Candidate', 'Test Father', 'phase4_candidate_1792233007@realtysmartz.in', '9876543210', NULL, NULL, '$2y$11$To8kQRA7LlVnXCsk1oUm.OHo1FvnyJaSe2I/3BaaJmMirxSrTIhNS', 'candidate', '0', 'test_completed', '1', '2026-10-17 16:00:08', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 16:00:08', '2026-10-17 16:00:14'),
  ('30', 'Abhishek', 'Rajesh', 'abhishekab16390@gmail.com', '9306098396', '9306098396', NULL, '$2y$11$umDGlx1trAqc/YZAS3UoY.BPfciGTgto4Jfbir.At4f/NRFcw2U0y', 'candidate', '0', 'test_completed', '1', '2026-10-17 16:27:04', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 16:17:00', '2026-10-17 16:43:01'),
  ('31', 'Test Candidate', NULL, 'testcandidate@realtysmartzpathshala.in', '+919999900001', NULL, NULL, '$2y$11$FfLDlt.WIOJSx8Tte3DFRu.oHZoPoCidQETYwuKfkx0YThDoSHinK', 'candidate', '0', 'approved', '1', '2026-10-18 13:20:48', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-18 13:20:48', '2026-10-18 13:20:48'),
  ('32', 'Amit Rawat', 'Rawat', 'amitrawat7867@gmail.com', '8178635472', '8178635472', NULL, '$2y$10$1smWIcd2WW7LKmJh27doQemAJ47CHjgqXYvvcsa5UmRgf9V1ks9OW', 'candidate', '0', 'email_verified', '1', '2026-10-18 14:47:25', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-18 14:46:50', '2026-10-18 15:24:42'),
  ('33', 'Vikram Malhotra', NULL, 'vikram.guest@example.com', '9876500011', NULL, '2026-12-15', '$2y$10$jOWymKuF5yVrFu.NBhG1mOq7txG/WS4i4SyC0D3sRNJwsxQUo7QSW', 'candidate', '1', 'test_completed', '1', NULL, NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-18 18:30:05', '2026-10-19 11:55:29');

-- --------------------------------------------------------
-- Table structure for `videos`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `videos`;
CREATE TABLE `videos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `youtube_url` varchar(255) NOT NULL,
  `youtube_id` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `order_num` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `videos`
INSERT INTO `videos` (`id`, `title`, `youtube_url`, `youtube_id`, `description`, `is_active`, `order_num`, `created_at`) VALUES
  ('1', 'Inside Realty Smartz Pathshala: Transforming Careers in Real Estate', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ', 'Experience how our classroom training, live site exposures, and senior mentorship empower fresh graduates and professionals.', '1', '1', '2026-10-17 12:24:20'),
  ('2', 'Gurgaon Real Estate Market Masterclass 2026', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ', 'Deep dive into micro-market trends along Dwarka Expressway, Golf Course Extension, and Southern Peripheral Road.', '1', '2', '2026-10-17 12:24:20');

SET FOREIGN_KEY_CHECKS = 1;
