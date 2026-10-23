-- Realty Smartz Pathshala Complete Production Database Dump
-- Generated: 2026-10-23 15:57:04
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `admin_comments`
INSERT INTO `admin_comments` (`id`, `user_id`, `admin_id`, `comment_type`, `comment_text`, `created_at`) VALUES
  ('4', '34', '34', 'public_to_candidate', 'Candidate completed 4-step onboarding wizard and submitted documents.', '2026-10-19 17:17:19'),
  ('5', '34', '34', 'public_to_candidate', 'Aptitude Assessment completed with score 2/15 (13.33%) - FAILED', '2026-10-19 17:17:49'),
  ('12', '54', '54', 'public_to_candidate', 'Candidate completed 4-step onboarding wizard and submitted documents.', '2026-10-22 12:18:39'),
  ('13', '54', '54', 'public_to_candidate', 'Aptitude Assessment completed with score 0/15 (0%) - FAILED', '2026-10-22 12:19:04'),
  ('25', '62', '62', 'public_to_candidate', 'Aptitude Assessment completed with score 11/15 (73.33%) - PASSED', '2026-10-22 16:10:57');

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
  ('1', 'Set A', 'Assessment Set A - Aptitude & Real Estate', 'Logical, Quantitative, English, Real Estate Basics, Sales & Negotiation', '14', '1', '2026-10-17 14:03:11'),
  ('2', 'Set B', 'Assessment Set B - Aptitude & Real Estate', 'Logical, Quantitative, English, Real Estate Basics, Sales & Negotiation', '15', '1', '2026-10-17 14:03:11'),
  ('3', 'Set C', 'Assessment Set C - Aptitude & Real Estate', 'Logical, Quantitative, English, Real Estate Basics, Sales & Negotiation', '15', '1', '2026-10-17 14:03:11'),
  ('4', 'Set D', 'Assessment Set D - Aptitude & Real Estate', 'Logical, Quantitative, English, Real Estate Basics, Sales & Negotiation', '15', '1', '2026-10-17 14:03:11'),
  ('5', 'Set E', 'Assessment Set E - Aptitude & Real Estate', 'Logical, Quantitative, English, Real Estate Basics, Sales & Negotiation', '15', '1', '2026-10-17 14:03:11');

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
) ENGINE=InnoDB AUTO_INCREMENT=272 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `attempt_answers`
INSERT INTO `attempt_answers` (`id`, `attempt_id`, `question_id`, `selected_option_id`, `is_marked_for_review`, `is_correct`, `marks_awarded`, `updated_at`) VALUES
  ('61', '5', '1', NULL, '0', '0', '0.00', '2026-10-22 12:18:43'),
  ('62', '5', '2', NULL, '0', '0', '0.00', '2026-10-22 12:18:43'),
  ('63', '5', '3', NULL, '0', '0', '0.00', '2026-10-22 12:18:43'),
  ('64', '5', '4', NULL, '0', '0', '0.00', '2026-10-22 12:18:43'),
  ('65', '5', '5', NULL, '0', '0', '0.00', '2026-10-22 12:18:43'),
  ('66', '5', '6', NULL, '0', '0', '0.00', '2026-10-22 12:18:43'),
  ('67', '5', '7', NULL, '0', '0', '0.00', '2026-10-22 12:18:43'),
  ('68', '5', '8', NULL, '0', '0', '0.00', '2026-10-22 12:18:43'),
  ('69', '5', '9', NULL, '0', '0', '0.00', '2026-10-22 12:18:43'),
  ('70', '5', '10', NULL, '0', '0', '0.00', '2026-10-22 12:18:43'),
  ('71', '5', '11', NULL, '0', '0', '0.00', '2026-10-22 12:18:43'),
  ('72', '5', '12', NULL, '0', '0', '0.00', '2026-10-22 12:18:43'),
  ('73', '5', '13', NULL, '0', '0', '0.00', '2026-10-22 12:18:43'),
  ('74', '5', '14', NULL, '0', '0', '0.00', '2026-10-22 12:18:43'),
  ('75', '5', '15', NULL, '0', '0', '0.00', '2026-10-22 12:18:43'),
  ('257', '19', '46', '182', '1', '1', '1.00', '2026-10-22 16:10:57'),
  ('258', '19', '47', '186', '1', '1', '1.00', '2026-10-22 16:10:57'),
  ('259', '19', '48', '192', '1', '0', '0.00', '2026-10-22 16:02:22'),
  ('260', '19', '49', '194', '1', '1', '1.00', '2026-10-22 16:10:57'),
  ('261', '19', '50', '198', '1', '1', '1.00', '2026-10-22 16:10:57'),
  ('262', '19', '51', '202', '1', '1', '1.00', '2026-10-22 16:10:57'),
  ('263', '19', '52', '207', '1', '0', '0.00', '2026-10-22 16:06:56'),
  ('264', '19', '53', '212', '1', '0', '0.00', '2026-10-22 16:07:54'),
  ('265', '19', '54', '214', '1', '1', '1.00', '2026-10-22 16:10:57'),
  ('266', '19', '55', '218', '1', '1', '1.00', '2026-10-22 16:10:57'),
  ('267', '19', '56', '222', '1', '1', '1.00', '2026-10-22 16:10:57'),
  ('268', '19', '57', '226', '1', '1', '1.00', '2026-10-22 16:10:57'),
  ('269', '19', '58', '232', '1', '0', '0.00', '2026-10-22 16:10:10'),
  ('270', '19', '59', '234', '1', '1', '1.00', '2026-10-22 16:10:57'),
  ('271', '19', '60', '238', '1', '1', '1.00', '2026-10-22 16:10:57');

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
) ENGINE=InnoDB AUTO_INCREMENT=647 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `audit_logs`
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `ip_address`, `user_agent`, `old_values`, `new_values`, `created_at`) VALUES
  ('1', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:07:37'),
  ('2', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:07:54'),
  ('3', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:07:56'),
  ('4', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:07:59'),
  ('5', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:08:01'),
  ('6', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:08:04'),
  ('7', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:08:07'),
  ('8', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:08:09'),
  ('9', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:08:11'),
  ('10', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:08:14'),
  ('11', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:08:17'),
  ('12', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:08:21'),
  ('13', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:08:24'),
  ('14', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:09:40'),
  ('15', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:11:09'),
  ('16', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:11:12'),
  ('17', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:11:15'),
  ('18', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:11:19'),
  ('19', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:11:24'),
  ('20', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:11:28'),
  ('21', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:11:32'),
  ('22', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:11:35'),
  ('23', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:11:38'),
  ('24', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:11:41'),
  ('25', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:11:43'),
  ('26', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:11:46'),
  ('27', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:11:56'),
  ('28', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:12:05'),
  ('29', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:12:16'),
  ('30', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:13:10'),
  ('31', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:13:12'),
  ('32', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:13:15'),
  ('33', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:13:17'),
  ('34', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:13:20'),
  ('35', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:13:22'),
  ('36', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:13:25'),
  ('37', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:13:27'),
  ('38', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:13:29'),
  ('39', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:13:35'),
  ('40', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:13:40'),
  ('41', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:13:42'),
  ('42', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:13:45'),
  ('43', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:13:47'),
  ('44', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:18:17'),
  ('45', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:18:20'),
  ('46', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:18:22'),
  ('47', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:18:25'),
  ('48', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:18:28'),
  ('49', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:18:31'),
  ('50', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:18:33'),
  ('51', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:18:36'),
  ('52', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:18:38'),
  ('53', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:18:41'),
  ('54', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:18:43'),
  ('55', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:18:47'),
  ('56', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:18:50'),
  ('57', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:18:52'),
  ('58', '34', 'candidate_registered', 'users', '34', '150.242.64.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"abhishekboora2001@gmail.com\",\"name\":\"Abhishek\",\"mobile\":\"9306098396\"}', '2026-10-19 16:34:35'),
  ('59', '34', 'email_verified', 'users', '34', '150.242.64.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"method\":\"otp\"}', '2026-10-19 16:35:17'),
  ('60', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:22'),
  ('61', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:24'),
  ('62', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:26'),
  ('63', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:29'),
  ('64', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:32'),
  ('65', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:34'),
  ('66', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:37'),
  ('67', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:39'),
  ('68', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:42'),
  ('69', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:44'),
  ('70', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:47'),
  ('71', '1', 'update_candidate', 'users', '34', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Abhishek\",\"joining_date\":\"2026-11-01\",\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:49'),
  ('72', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:50'),
  ('73', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:52'),
  ('74', '1', 'update_candidate', 'users', '34', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Abhishek\",\"joining_date\":\"2026-12-15\",\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:55'),
  ('75', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:49:56'),
  ('76', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:50:05'),
  ('77', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:50:13'),
  ('78', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:51:14'),
  ('79', '1', 'create_guest_test_link', 'guest_test_links', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":null,\"guest_email\":\"\",\"expires_at\":\"2026-10-21 16:51:15\",\"ip\":\"127.0.0.1\"}', '2026-10-19 16:51:16'),
  ('85', '1', 'revoke_guest_test_link', 'guest_test_links', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:51:27'),
  ('86', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:52:18'),
  ('87', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:52:23'),
  ('88', '1', 'download_full_file_pdf', 'users', '35', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Vikram_Malhotra-35-file.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-19 16:52:27'),
  ('89', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:52:52'),
  ('90', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:52:55'),
  ('91', '1', 'download_full_file_pdf', 'users', '35', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Vikram_Malhotra-35-file.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-19 16:52:59'),
  ('92', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:11'),
  ('93', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:13'),
  ('94', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:15'),
  ('95', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:18'),
  ('96', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:21'),
  ('97', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:23'),
  ('98', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:26'),
  ('99', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:28'),
  ('100', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:30'),
  ('101', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:33'),
  ('102', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:35'),
  ('103', '1', 'update_candidate', 'users', '35', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Vikram Malhotra\",\"joining_date\":\"2026-11-01\",\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:38'),
  ('104', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:39'),
  ('105', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:41'),
  ('106', '1', 'update_candidate', 'users', '35', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Vikram Malhotra\",\"joining_date\":\"2026-12-15\",\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:44'),
  ('107', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:45'),
  ('108', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:54'),
  ('109', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:53:58'),
  ('110', '1', 'download_full_file_pdf', 'users', '35', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Vikram_Malhotra-35-file.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-19 16:54:01'),
  ('111', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:54:02'),
  ('112', '1', 'create_guest_test_link', 'guest_test_links', '2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":null,\"guest_email\":\"\",\"expires_at\":\"2026-10-21 16:54:03\",\"ip\":\"127.0.0.1\"}', '2026-10-19 16:54:04'),
  ('118', '1', 'revoke_guest_test_link', 'guest_test_links', '2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 16:54:15'),
  ('119', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:00:47'),
  ('120', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:00:49'),
  ('121', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:00:52'),
  ('122', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:00:54'),
  ('123', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:00:57'),
  ('124', NULL, 'login_failed', 'users', '35', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"vikram.guest@example.com\"}', '2026-10-19 17:05:47'),
  ('125', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:12'),
  ('126', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:15'),
  ('127', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:17'),
  ('128', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:20'),
  ('129', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:23'),
  ('130', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:25'),
  ('131', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:28'),
  ('132', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:31'),
  ('133', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:33'),
  ('134', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:36'),
  ('135', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:38'),
  ('136', '1', 'update_candidate', 'users', '35', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Vikram Malhotra\",\"joining_date\":\"2026-11-01\",\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:40'),
  ('137', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:41'),
  ('138', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:44'),
  ('139', '1', 'update_candidate', 'users', '35', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Vikram Malhotra\",\"joining_date\":\"2026-12-15\",\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:47'),
  ('140', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:48'),
  ('141', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:07:56'),
  ('142', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:08:00'),
  ('143', '1', 'download_full_file_pdf', 'users', '35', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Vikram_Malhotra-35-file.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-19 17:08:04'),
  ('144', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:08:05'),
  ('145', '1', 'create_guest_test_link', 'guest_test_links', '3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":null,\"guest_email\":\"\",\"expires_at\":\"2026-10-21 17:08:06\",\"ip\":\"127.0.0.1\"}', '2026-10-19 17:08:07'),
  ('151', '1', 'revoke_guest_test_link', 'guest_test_links', '3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:08:18'),
  ('152', '34', 'document_uploaded', 'documents', '1', '150.242.64.103', 'Mozilla/5.0 (Linux; Android 17; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.8078.39 Mobile Safari/537.36', NULL, '{\"type\":\"aadhaar_front\",\"version\":1,\"size\":257455}', '2026-10-19 17:15:56'),
  ('153', '34', 'document_uploaded', 'documents', '2', '150.242.64.103', 'Mozilla/5.0 (Linux; Android 17; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.8078.39 Mobile Safari/537.36', NULL, '{\"type\":\"aadhaar_back\",\"version\":1,\"size\":257455}', '2026-10-19 17:16:00'),
  ('154', '34', 'document_uploaded', 'documents', '3', '150.242.64.103', 'Mozilla/5.0 (Linux; Android 17; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.8078.39 Mobile Safari/537.36', NULL, '{\"type\":\"pan_card\",\"version\":1,\"size\":257455}', '2026-10-19 17:16:44'),
  ('155', '34', 'status_updated', 'users', '34', '150.242.64.103', 'Mozilla/5.0 (Linux; Android 17; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.8078.39 Mobile Safari/537.36', '{\"status\":\"email_verified\"}', '{\"status\":\"documents_submitted\",\"comments\":\"Candidate completed 4-step onboarding wizard and submitted documents.\"}', '2026-10-19 17:17:19'),
  ('156', '34', 'onboarding_submitted', 'profiles', '1', '150.242.64.103', 'Mozilla/5.0 (Linux; Android 17; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.8078.39 Mobile Safari/537.36', NULL, '{\"signature_type\":\"typed\",\"timestamp\":\"2026-10-19 17:17:19\"}', '2026-10-19 17:17:19'),
  ('157', '34', 'test_started', 'test_attempts', '4', '150.242.64.103', 'Mozilla/5.0 (Linux; Android 17; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.8078.39 Mobile Safari/537.36', NULL, '{\"user_id\":34,\"test_id\":1,\"set_id\":2,\"set_code\":\"Set B\"}', '2026-10-19 17:17:22'),
  ('158', '34', 'status_updated', 'users', '34', '150.242.64.103', 'Mozilla/5.0 (Linux; Android 17; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.8078.39 Mobile Safari/537.36', '{\"status\":\"documents_submitted\"}', '{\"status\":\"test_completed\",\"comments\":\"Aptitude Assessment completed with score 2\\/15 (13.33%) - FAILED\"}', '2026-10-19 17:17:49'),
  ('159', '34', 'test_finalized', 'test_attempts', '4', '150.242.64.103', 'Mozilla/5.0 (Linux; Android 17; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.8078.39 Mobile Safari/537.36', NULL, '{\"score\":2,\"percentage\":13.33,\"passed\":0}', '2026-10-19 17:17:49'),
  ('160', '34', 'admin_login_failed', 'users', '1', '150.242.64.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"admin@realtysmartzpathshala.in\",\"ip\":\"150.242.64.103\"}', '2026-10-19 17:22:22'),
  ('161', '1', 'admin_login_success', 'users', '1', '150.242.64.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"150.242.64.103\"}', '2026-10-19 17:22:30'),
  ('162', '1', 'logout', 'users', '1', '150.242.64.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, NULL, '2026-10-19 17:27:26'),
  ('163', '1', 'admin_login_success', 'users', '1', '150.242.64.103', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.6.1 Mobile/15E148 Safari/604.1', NULL, '{\"ip\":\"150.242.64.103\"}', '2026-10-19 17:31:11'),
  ('164', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:32:33'),
  ('165', '1', 'update_candidate', 'users', '35', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Vikram Malhotra\",\"joining_date\":\"2026-11-01\",\"ip\":\"127.0.0.1\"}', '2026-10-19 17:32:35'),
  ('166', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:32:36'),
  ('167', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:32:38'),
  ('168', '1', 'update_candidate', 'users', '35', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"full_name\":\"Vikram Malhotra\",\"joining_date\":\"2026-12-15\",\"ip\":\"127.0.0.1\"}', '2026-10-19 17:32:41'),
  ('169', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:32:42'),
  ('170', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:34:07'),
  ('171', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:35:46'),
  ('172', '1', 'candidate_archived', 'users', '35', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"archived\":1}', '2026-10-19 17:35:48'),
  ('173', '1', 'archive_candidate', 'users', '35', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:35:48'),
  ('174', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:36:17'),
  ('175', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:36:39'),
  ('176', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:36:59'),
  ('177', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:42:37'),
  ('178', '1', 'candidate_archived', 'users', '34', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"archived\":1}', '2026-10-19 17:42:39'),
  ('179', '1', 'archive_candidate', 'users', '34', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 17:42:39'),
  ('246', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 18:22:39'),
  ('247', '1', 'candidate_archived', 'users', '53', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"archived\":1}', '2026-10-19 18:22:42'),
  ('248', '1', 'archive_candidate', 'users', '53', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-19 18:22:42'),
  ('249', '54', 'candidate_registered', 'users', '54', '103.240.194.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"yogatatvam@gmail.com\",\"name\":\"Smart Finance Chhavi Mittal\",\"mobile\":\"8800992566\"}', '2026-10-22 12:14:52'),
  ('250', '54', 'email_verified', 'users', '54', '103.240.194.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"method\":\"otp\"}', '2026-10-22 12:15:37'),
  ('251', '54', 'document_uploaded', 'documents', '24', '103.240.194.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"type\":\"aadhaar_front\",\"version\":1,\"size\":293524}', '2026-10-22 12:17:41'),
  ('252', '54', 'document_uploaded', 'documents', '25', '103.240.194.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"type\":\"aadhaar_back\",\"version\":1,\"size\":293524}', '2026-10-22 12:17:50'),
  ('253', '54', 'document_uploaded', 'documents', '26', '103.240.194.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"type\":\"pan_card\",\"version\":1,\"size\":167556}', '2026-10-22 12:18:01'),
  ('254', '54', 'status_updated', 'users', '54', '103.240.194.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"status\":\"email_verified\"}', '{\"status\":\"documents_submitted\",\"comments\":\"Candidate completed 4-step onboarding wizard and submitted documents.\"}', '2026-10-22 12:18:39'),
  ('255', '54', 'onboarding_submitted', 'profiles', '21', '103.240.194.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"signature_type\":\"typed\",\"timestamp\":\"2026-10-22 12:18:39\"}', '2026-10-22 12:18:39'),
  ('256', '54', 'test_started', 'test_attempts', '5', '103.240.194.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":54,\"test_id\":1,\"set_id\":1,\"set_code\":\"Set A\"}', '2026-10-22 12:18:43'),
  ('257', '54', 'status_updated', 'users', '54', '103.240.194.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"status\":\"documents_submitted\"}', '{\"status\":\"test_completed\",\"comments\":\"Aptitude Assessment completed with score 0\\/15 (0%) - FAILED\"}', '2026-10-22 12:19:04'),
  ('258', '54', 'test_finalized', 'test_attempts', '5', '103.240.194.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"score\":0,\"percentage\":0,\"passed\":0}', '2026-10-22 12:19:04'),
  ('259', NULL, 'admin_login_failed', 'users', '1', '103.240.194.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"admin@realtysmartzpathshala.in\",\"ip\":\"103.240.194.107\"}', '2026-10-22 12:26:02'),
  ('260', '1', 'admin_login_success', 'users', '1', '103.240.194.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"103.240.194.107\"}', '2026-10-22 12:26:15'),
  ('261', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:16:43'),
  ('262', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:16:46'),
  ('263', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:16:49'),
  ('264', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:16:51'),
  ('265', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:16:54'),
  ('266', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:16:57'),
  ('267', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:17:00'),
  ('268', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:17:02'),
  ('269', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:17:04'),
  ('270', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:17:07'),
  ('271', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:17:10'),
  ('272', '1', 'identity_revealed', 'users', '54', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"field\":\"aadhaar\",\"revealed_by\":\"admin@realtysmartzpathshala.in\",\"ip\":\"127.0.0.1\"}', '2026-10-22 13:17:13'),
  ('273', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:17:18'),
  ('274', '1', 'download_full_file_pdf', 'users', '54', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Smart_Finance_Chhavi_Mittal-54-file.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-22 13:17:23'),
  ('275', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:17:24'),
  ('276', '1', 'create_guest_test_link', 'guest_test_links', '4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":null,\"guest_email\":\"\",\"expires_at\":\"2026-10-24 13:17:26\",\"ip\":\"127.0.0.1\"}', '2026-10-22 13:17:26'),
  ('279', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:23:25'),
  ('280', '1', 'create_guest_test_link', 'guest_test_links', '6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":null,\"guest_email\":\"\",\"expires_at\":\"2026-10-24 13:23:27\",\"ip\":\"127.0.0.1\"}', '2026-10-22 13:23:27'),
  ('286', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:28:44'),
  ('287', '1', 'create_guest_test_link', 'guest_test_links', '7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":null,\"guest_email\":\"\",\"expires_at\":\"2026-10-24 13:28:47\",\"ip\":\"127.0.0.1\"}', '2026-10-22 13:28:47'),
  ('293', '1', 'revoke_guest_test_link', 'guest_test_links', '7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:29:01'),
  ('294', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:29:38'),
  ('295', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:29:41'),
  ('296', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:29:44'),
  ('297', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:29:48'),
  ('298', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:29:52'),
  ('299', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:29:56'),
  ('300', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:29:59'),
  ('301', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:30:02'),
  ('302', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:30:06'),
  ('303', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:30:09'),
  ('304', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:30:12'),
  ('305', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:30:16'),
  ('306', '1', 'download_full_file_pdf', 'users', '35', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Vikram_Malhotra-35-file.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-22 13:30:21'),
  ('307', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:30:23'),
  ('308', '1', 'create_guest_test_link', 'guest_test_links', '8', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":null,\"guest_email\":\"\",\"expires_at\":\"2026-10-24 13:30:25\",\"ip\":\"127.0.0.1\"}', '2026-10-22 13:30:25'),
  ('314', '1', 'revoke_guest_test_link', 'guest_test_links', '8', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:30:38'),
  ('319', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:43:31'),
  ('324', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:44:48'),
  ('325', '1', 'converted_to_onboarding', 'users', '56', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"admin_id\":1,\"converted_at\":\"2026-10-22 13:44:50\"}', '2026-10-22 13:44:49'),
  ('330', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:47:07'),
  ('331', '1', 'converted_to_onboarding', 'users', '57', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"admin_id\":1,\"converted_at\":\"2026-10-22 13:47:09\"}', '2026-10-22 13:47:09'),
  ('336', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 13:49:55'),
  ('337', '1', 'converted_to_onboarding', 'users', '58', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"admin_id\":1,\"converted_at\":\"2026-10-22 13:49:57\"}', '2026-10-22 13:49:57'),
  ('343', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:44:38'),
  ('344', '1', 'converted_to_onboarding', 'users', '59', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"admin_id\":1,\"converted_at\":\"2026-10-22 14:44:41\"}', '2026-10-22 14:44:40'),
  ('345', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:45:06'),
  ('346', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:45:10'),
  ('347', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:45:15'),
  ('348', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:45:20'),
  ('349', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:45:24'),
  ('350', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:45:28'),
  ('351', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:45:31'),
  ('352', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:45:34'),
  ('353', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:45:37'),
  ('354', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:45:41'),
  ('359', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:45:53'),
  ('360', '1', 'converted_to_onboarding', 'users', '60', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"admin_id\":1,\"converted_at\":\"2026-10-22 14:45:55\"}', '2026-10-22 14:45:55'),
  ('361', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:45:58'),
  ('362', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:46:03'),
  ('363', '1', 'download_full_file_pdf', 'users', '60', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Rohan_Test_Verma-60-file.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-22 14:46:07'),
  ('364', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:46:08'),
  ('365', '1', 'create_guest_test_link', 'guest_test_links', '9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"user_id\":null,\"guest_email\":\"\",\"expires_at\":\"2026-10-24 14:46:10\",\"ip\":\"127.0.0.1\"}', '2026-10-22 14:46:10'),
  ('371', '1', 'revoke_guest_test_link', 'guest_test_links', '9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 14:46:24'),
  ('372', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 15:09:22'),
  ('373', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 15:09:27'),
  ('374', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 15:09:32'),
  ('375', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 15:09:38'),
  ('376', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 15:09:43'),
  ('382', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 15:10:15'),
  ('383', '1', 'converted_to_onboarding', 'users', '61', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"admin_id\":1,\"converted_at\":\"2026-10-22 15:10:22\"}', '2026-10-22 15:10:21'),
  ('384', NULL, 'admin_login_failed', 'users', '1', '103.240.195.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"admin@realtysmartzpathshala.in\",\"ip\":\"103.240.195.117\"}', '2026-10-22 15:17:45'),
  ('385', '1', 'admin_login_success', 'users', '1', '103.240.195.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"103.240.195.117\"}', '2026-10-22 15:17:54'),
  ('386', '62', 'candidate_registered', 'users', '62', '103.240.195.117', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Mobile Safari/537.36', NULL, '{\"email\":\"ai.mittal51@gmail.com\",\"name\":\"ANKITA MITTAL\",\"mobile\":\"9266826371\"}', '2026-10-22 16:00:50'),
  ('387', '62', 'test_started', 'test_attempts', '19', '103.240.195.117', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Mobile Safari/537.36', NULL, '{\"user_id\":62,\"test_id\":1,\"set_id\":4,\"set_code\":\"Set D\"}', '2026-10-22 16:00:56'),
  ('388', '1', 'admin_login_success', 'users', '1', '103.240.194.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"103.240.194.107\"}', '2026-10-22 16:06:44'),
  ('389', '62', 'anti_cheat_warning', 'test_attempts', '19', '103.240.195.117', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Mobile Safari/537.36', NULL, '{\"type\":\"tab_switch\",\"counts\":{\"tab_switch_count\":\"1\",\"fullscreen_exit_count\":\"0\"}}', '2026-10-22 16:10:37'),
  ('390', '62', 'status_updated', 'users', '62', '103.240.195.117', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Mobile Safari/537.36', '{\"status\":\"registered\"}', '{\"status\":\"test_completed\",\"comments\":\"Aptitude Assessment completed with score 11\\/15 (73.33%) - PASSED\"}', '2026-10-22 16:10:57'),
  ('391', '62', 'test_finalized', 'test_attempts', '19', '103.240.195.117', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Mobile Safari/537.36', NULL, '{\"score\":11,\"percentage\":73.33,\"passed\":1}', '2026-10-22 16:10:58'),
  ('392', '1', 'interview_field_update', 'interview_applications', '1', '127.0.0.1', '', NULL, '{\"field\":\"source_by\",\"new_value\":\"Direct Walk-in\",\"version\":2}', '2026-10-22 17:17:10'),
  ('393', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:18:59'),
  ('394', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:19:08'),
  ('395', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:19:56'),
  ('396', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:20:10'),
  ('397', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-22 17:20:11'),
  ('398', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:20:20'),
  ('399', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:23:44'),
  ('400', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:23:53'),
  ('401', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:24:55'),
  ('402', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:25:57'),
  ('403', '1', 'admin_login_success', 'users', '1', '103.240.194.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"103.240.194.107\"}', '2026-10-22 17:27:36'),
  ('404', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:32:30'),
  ('405', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:32:34'),
  ('406', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:32:39'),
  ('407', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-22 17:32:40'),
  ('408', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-22 17:32:41'),
  ('409', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:32:43'),
  ('410', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:35:17'),
  ('411', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:35:21'),
  ('412', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:35:28'),
  ('413', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-22 17:35:30'),
  ('414', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-22 17:35:31'),
  ('415', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:35:33'),
  ('416', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:42:50'),
  ('417', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:42:55'),
  ('418', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:43:58'),
  ('419', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-22 17:43:59'),
  ('420', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-22 17:44:00'),
  ('421', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 17:44:03'),
  ('422', '1', 'admin_login_success', 'users', '1', '103.240.194.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"103.240.194.107\"}', '2026-10-22 18:18:21'),
  ('423', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:34:50'),
  ('424', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:34:56'),
  ('425', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:35:59'),
  ('426', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-22 18:36:00'),
  ('427', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-22 18:36:01'),
  ('428', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:36:03'),
  ('429', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:41:21'),
  ('430', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:42:22'),
  ('431', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:44:46'),
  ('432', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:45:39'),
  ('433', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:46:23'),
  ('434', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:51:42'),
  ('435', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:52:07'),
  ('436', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:52:09'),
  ('437', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:52:14'),
  ('438', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-22 18:52:15'),
  ('439', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-22 18:52:16'),
  ('440', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:52:17'),
  ('441', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:55:03'),
  ('442', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:55:06'),
  ('443', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:55:10'),
  ('444', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-22 18:55:11'),
  ('445', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-22 18:55:12'),
  ('446', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:55:13'),
  ('447', '1', 'interview_field_update', 'interview_applications', '5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"field\":\"feedback_hr\",\"new_value\":\"Excellent communication skills and strong presentation.\",\"version\":2}', '2026-10-22 18:55:28'),
  ('448', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:59:55'),
  ('449', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 18:59:58'),
  ('450', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 19:00:04'),
  ('451', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-22 19:00:05'),
  ('452', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-22 19:00:06'),
  ('453', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-22 19:00:08'),
  ('454', '1', 'interview_field_update', 'interview_applications', '7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"feedback_hr\",\"old_value\":null}', '{\"field\":\"feedback_hr\",\"new_value\":\"Excellent communication skills and strong presentation.\",\"version\":2}', '2026-10-22 19:00:23'),
  ('455', '1', 'interview_field_update', 'interview_applications', '7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":3}', '2026-10-22 19:00:24'),
  ('456', '1', 'interview_field_update', 'interview_applications', '7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"candidate_interested\",\"old_value\":null}', '{\"field\":\"candidate_interested\",\"new_value\":\"yes\",\"version\":4}', '2026-10-22 19:00:26'),
  ('457', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 10:26:42'),
  ('458', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 10:26:49'),
  ('459', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 10:27:03'),
  ('460', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 10:27:58'),
  ('461', '1', 'interview_field_update', 'interview_applications', '9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"feedback_hr\",\"old_value\":null}', '{\"field\":\"feedback_hr\",\"new_value\":\"Excellent communication skills and strong presentation.\",\"version\":2}', '2026-10-23 10:28:16'),
  ('462', '1', 'interview_field_update', 'interview_applications', '9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":3}', '2026-10-23 10:28:17'),
  ('463', '1', 'interview_field_update', 'interview_applications', '9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"candidate_interested\",\"old_value\":null}', '{\"field\":\"candidate_interested\",\"new_value\":\"yes\",\"version\":4}', '2026-10-23 10:28:19'),
  ('464', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 10:32:39'),
  ('465', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-23 10:32:41'),
  ('466', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-23 10:32:43'),
  ('467', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 10:33:10'),
  ('468', '1', 'interview_field_update', 'interview_applications', '10', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"feedback_hr\",\"old_value\":null}', '{\"field\":\"feedback_hr\",\"new_value\":\"Excellent communication skills and strong presentation.\",\"version\":2}', '2026-10-23 10:33:27'),
  ('469', '1', 'interview_field_update', 'interview_applications', '10', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":3}', '2026-10-23 10:33:27'),
  ('470', '1', 'interview_field_update', 'interview_applications', '10', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"candidate_interested\",\"old_value\":null}', '{\"field\":\"candidate_interested\",\"new_value\":\"yes\",\"version\":4}', '2026-10-23 10:33:29'),
  ('471', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 10:38:14'),
  ('472', '1', 'interview_field_update', 'interview_applications', '11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"feedback_hr\",\"old_value\":null}', '{\"field\":\"feedback_hr\",\"new_value\":\"Excellent communication skills and strong presentation.\",\"version\":2}', '2026-10-23 10:38:38'),
  ('473', '1', 'interview_field_update', 'interview_applications', '11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":3}', '2026-10-23 10:38:39'),
  ('474', '1', 'interview_field_update', 'interview_applications', '11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"candidate_interested\",\"old_value\":null}', '{\"field\":\"candidate_interested\",\"new_value\":\"yes\",\"version\":4}', '2026-10-23 10:38:40'),
  ('475', '1', 'interview_field_update', 'interview_applications', '11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"position_offered\",\"old_value\":null}', '{\"field\":\"position_offered\",\"new_value\":\"Senior Relationship Associate\",\"version\":5}', '2026-10-23 10:38:41'),
  ('476', '1', 'interview_field_update', 'interview_applications', '11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"offered_salary\",\"old_value\":null}', '{\"field\":\"offered_salary\",\"new_value\":450000,\"version\":6}', '2026-10-23 10:38:41'),
  ('477', '1', 'interview_field_update', 'interview_applications', '11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"joining_date\",\"old_value\":null}', '{\"field\":\"joining_date\",\"new_value\":\"2026-11-15\",\"version\":7}', '2026-10-23 10:38:42'),
  ('478', '1', 'interview_field_update', 'interview_applications', '11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"selected\"}', '{\"field\":\"decision\",\"new_value\":\"rejected\",\"version\":8}', '2026-10-23 10:38:43'),
  ('479', '1', 'interview_field_update', 'interview_applications', '11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"rejection_reason\",\"old_value\":null}', '{\"field\":\"rejection_reason\",\"new_value\":\"Not willing to relocate to Gurgaon\",\"version\":9}', '2026-10-23 10:38:44'),
  ('480', '1', 'interview_field_update', 'interview_applications', '11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"rejected\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":10}', '2026-10-23 10:38:45'),
  ('481', '1', 'download_interview_pdf', 'interview_applications', '11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Interview_Form_Kavita_Live_Test_390_11.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-23 10:38:50'),
  ('482', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 10:42:17'),
  ('483', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 10:42:27'),
  ('484', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 10:42:54'),
  ('485', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-23 10:42:57'),
  ('486', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-23 10:42:59'),
  ('487', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 10:43:02'),
  ('488', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"feedback_hr\",\"old_value\":null}', '{\"field\":\"feedback_hr\",\"new_value\":\"Excellent communication skills and strong presentation.\",\"version\":2}', '2026-10-23 10:43:22'),
  ('489', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":3}', '2026-10-23 10:43:24'),
  ('490', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"candidate_interested\",\"old_value\":null}', '{\"field\":\"candidate_interested\",\"new_value\":\"yes\",\"version\":4}', '2026-10-23 10:43:26'),
  ('491', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"position_offered\",\"old_value\":null}', '{\"field\":\"position_offered\",\"new_value\":\"Senior Relationship Associate\",\"version\":5}', '2026-10-23 10:43:27'),
  ('492', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"offered_salary\",\"old_value\":null}', '{\"field\":\"offered_salary\",\"new_value\":450000,\"version\":6}', '2026-10-23 10:43:28'),
  ('493', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"joining_date\",\"old_value\":null}', '{\"field\":\"joining_date\",\"new_value\":\"2026-11-15\",\"version\":7}', '2026-10-23 10:43:28'),
  ('494', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"selected\"}', '{\"field\":\"decision\",\"new_value\":\"rejected\",\"version\":8}', '2026-10-23 10:43:31'),
  ('495', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"rejection_reason\",\"old_value\":null}', '{\"field\":\"rejection_reason\",\"new_value\":\"Not willing to relocate to Gurgaon\",\"version\":9}', '2026-10-23 10:43:32'),
  ('496', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"rejected\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":10}', '2026-10-23 10:43:34'),
  ('497', '1', 'download_interview_pdf', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Interview_Form_Kavita_Live_Test_416_13.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-23 10:43:38'),
  ('498', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 11:48:46'),
  ('499', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 11:48:49'),
  ('500', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 11:48:55'),
  ('501', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-23 11:48:56'),
  ('502', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-23 11:48:57'),
  ('503', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 11:48:59'),
  ('504', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 12:15:47'),
  ('505', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 12:15:50'),
  ('506', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 12:15:55'),
  ('507', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-23 12:15:57'),
  ('508', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-23 12:15:58'),
  ('509', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 12:16:00'),
  ('510', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 12:24:25'),
  ('511', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 12:24:28'),
  ('512', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 12:24:55'),
  ('513', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-23 12:24:56'),
  ('514', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-23 12:24:57'),
  ('515', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 12:24:58'),
  ('516', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 12:27:06'),
  ('517', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 12:32:13'),
  ('518', '1', 'interview_field_update', 'interview_applications', '20', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"feedback_hr\",\"old_value\":null}', '{\"field\":\"feedback_hr\",\"new_value\":\"Excellent communication skills and strong presentation.\",\"version\":2}', '2026-10-23 12:32:22'),
  ('519', '1', 'interview_field_update', 'interview_applications', '19', '127.0.0.1', '', '{\"field\":\"feedback_hr\",\"old_value\":null}', '{\"field\":\"feedback_hr\",\"new_value\":\"Test feedback from scratch\",\"version\":2}', '2026-10-23 12:35:10'),
  ('520', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 12:39:09'),
  ('521', '1', 'interview_field_update', 'interview_applications', '21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"feedback_hr\",\"old_value\":null}', '{\"field\":\"feedback_hr\",\"new_value\":\"Excellent communication skills and strong presentation.\",\"version\":2}', '2026-10-23 12:39:19'),
  ('522', '1', 'interview_field_update', 'interview_applications', '21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":3}', '2026-10-23 12:39:20'),
  ('523', '1', 'interview_field_update', 'interview_applications', '21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"candidate_interested\",\"old_value\":null}', '{\"field\":\"candidate_interested\",\"new_value\":\"yes\",\"version\":4}', '2026-10-23 12:39:21'),
  ('524', '1', 'interview_field_update', 'interview_applications', '21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"position_offered\",\"old_value\":null}', '{\"field\":\"position_offered\",\"new_value\":\"Senior Relationship Associate\",\"version\":5}', '2026-10-23 12:39:22'),
  ('525', '1', 'interview_field_update', 'interview_applications', '21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"offered_salary\",\"old_value\":null}', '{\"field\":\"offered_salary\",\"new_value\":450000,\"version\":6}', '2026-10-23 12:39:22'),
  ('526', '1', 'interview_field_update', 'interview_applications', '21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"joining_date\",\"old_value\":null}', '{\"field\":\"joining_date\",\"new_value\":\"2026-11-15\",\"version\":7}', '2026-10-23 12:39:23'),
  ('527', '1', 'interview_field_update', 'interview_applications', '21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"selected\"}', '{\"field\":\"decision\",\"new_value\":\"rejected\",\"version\":8}', '2026-10-23 12:39:25'),
  ('528', '1', 'interview_field_update', 'interview_applications', '21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"rejection_reason\",\"old_value\":null}', '{\"field\":\"rejection_reason\",\"new_value\":\"Not willing to relocate to Gurgaon\",\"version\":9}', '2026-10-23 12:39:26'),
  ('529', '1', 'interview_field_update', 'interview_applications', '21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"rejected\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":10}', '2026-10-23 12:39:27'),
  ('530', '1', 'download_interview_pdf', 'interview_applications', '21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Interview_Form_Kavita_Live_Test_354_21.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-23 12:39:29'),
  ('531', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 12:39:47'),
  ('532', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 12:39:53'),
  ('533', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 12:39:57'),
  ('534', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-23 12:39:58'),
  ('535', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-23 12:39:59'),
  ('536', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 12:40:01'),
  ('537', '1', 'interview_field_update', 'interview_applications', '23', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"feedback_hr\",\"old_value\":null}', '{\"field\":\"feedback_hr\",\"new_value\":\"Excellent communication skills and strong presentation.\",\"version\":2}', '2026-10-23 12:40:10'),
  ('538', '1', 'interview_field_update', 'interview_applications', '23', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":3}', '2026-10-23 12:40:11'),
  ('539', '1', 'interview_field_update', 'interview_applications', '23', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"candidate_interested\",\"old_value\":null}', '{\"field\":\"candidate_interested\",\"new_value\":\"yes\",\"version\":4}', '2026-10-23 12:40:12'),
  ('540', '1', 'interview_field_update', 'interview_applications', '23', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"position_offered\",\"old_value\":null}', '{\"field\":\"position_offered\",\"new_value\":\"Senior Relationship Associate\",\"version\":5}', '2026-10-23 12:40:13'),
  ('541', '1', 'interview_field_update', 'interview_applications', '23', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"offered_salary\",\"old_value\":null}', '{\"field\":\"offered_salary\",\"new_value\":450000,\"version\":6}', '2026-10-23 12:40:13'),
  ('542', '1', 'interview_field_update', 'interview_applications', '23', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"joining_date\",\"old_value\":null}', '{\"field\":\"joining_date\",\"new_value\":\"2026-11-15\",\"version\":7}', '2026-10-23 12:40:14'),
  ('543', '1', 'interview_field_update', 'interview_applications', '23', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"selected\"}', '{\"field\":\"decision\",\"new_value\":\"rejected\",\"version\":8}', '2026-10-23 12:40:15'),
  ('544', '1', 'interview_field_update', 'interview_applications', '23', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"rejection_reason\",\"old_value\":null}', '{\"field\":\"rejection_reason\",\"new_value\":\"Not willing to relocate to Gurgaon\",\"version\":9}', '2026-10-23 12:40:16'),
  ('545', '1', 'interview_field_update', 'interview_applications', '23', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"rejected\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":10}', '2026-10-23 12:40:16'),
  ('546', '1', 'download_interview_pdf', 'interview_applications', '23', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Interview_Form_Kavita_Live_Test_268_23.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-23 12:40:17'),
  ('547', NULL, 'admin_login_failed', 'users', '1', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"email\":\"admin@realtysmartzpathshala.in\",\"ip\":\"103.240.236.244\"}', '2026-10-23 12:47:33'),
  ('548', '1', 'admin_login_success', 'users', '1', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"103.240.236.244\"}', '2026-10-23 12:47:46'),
  ('549', '1', 'interview_field_update', 'interview_applications', '25', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":2}', '2026-10-23 12:55:22'),
  ('550', '1', 'interview_field_update', 'interview_applications', '25', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"selected\"}', '{\"field\":\"decision\",\"new_value\":\"rejected\",\"version\":3}', '2026-10-23 12:55:32'),
  ('551', '1', 'interview_field_update', 'interview_applications', '25', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"rejected\"}', '{\"field\":\"decision\",\"new_value\":\"pending\",\"version\":4}', '2026-10-23 12:55:36'),
  ('552', '1', 'interview_field_update', 'interview_applications', '24', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":2}', '2026-10-23 12:56:29'),
  ('553', '1', 'interview_field_update', 'interview_applications', '24', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"field\":\"candidate_interested\",\"old_value\":null}', '{\"field\":\"candidate_interested\",\"new_value\":\"yes\",\"version\":3}', '2026-10-23 12:56:34'),
  ('554', '1', 'interview_field_update', 'interview_applications', '24', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"field\":\"position_offered\",\"old_value\":null}', '{\"field\":\"position_offered\",\"new_value\":\"fdfsccff\",\"version\":4}', '2026-10-23 12:56:37'),
  ('555', '1', 'interview_field_update', 'interview_applications', '24', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"field\":\"offered_salary\",\"old_value\":null}', '{\"field\":\"offered_salary\",\"new_value\":222222,\"version\":5}', '2026-10-23 12:56:43'),
  ('556', '1', 'interview_field_update', 'interview_applications', '24', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"field\":\"offered_salary\",\"old_value\":\"222222.00\"}', '{\"field\":\"offered_salary\",\"new_value\":222222,\"version\":6}', '2026-10-23 12:56:45'),
  ('557', '1', 'interview_field_update', 'interview_applications', '24', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"field\":\"joining_date\",\"old_value\":null}', '{\"field\":\"joining_date\",\"new_value\":\"2026-10-27\",\"version\":7}', '2026-10-23 12:56:49'),
  ('558', '1', 'interview_field_update', 'interview_applications', '24', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"selected\"}', '{\"field\":\"decision\",\"new_value\":\"pending\",\"version\":8}', '2026-10-23 12:57:17'),
  ('559', '1', 'interview_field_update', 'interview_applications', '24', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":9}', '2026-10-23 12:57:19'),
  ('560', '1', 'interview_field_update', 'interview_applications', '24', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"selected\"}', '{\"field\":\"decision\",\"new_value\":\"rejected\",\"version\":10}', '2026-10-23 12:57:21'),
  ('561', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 13:22:50'),
  ('562', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 13:22:55'),
  ('563', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 13:23:02'),
  ('564', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-23 13:23:03'),
  ('565', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-23 13:23:04'),
  ('566', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 13:23:07'),
  ('567', '1', 'interview_field_update', 'interview_applications', '27', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"feedback_hr\",\"old_value\":null}', '{\"field\":\"feedback_hr\",\"new_value\":\"Excellent communication skills and strong presentation.\",\"version\":2}', '2026-10-23 13:23:17'),
  ('568', '1', 'interview_field_update', 'interview_applications', '27', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":3}', '2026-10-23 13:23:19'),
  ('569', '1', 'interview_field_update', 'interview_applications', '27', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"candidate_interested\",\"old_value\":null}', '{\"field\":\"candidate_interested\",\"new_value\":\"yes\",\"version\":4}', '2026-10-23 13:23:20'),
  ('570', '1', 'interview_field_update', 'interview_applications', '27', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"position_offered\",\"old_value\":null}', '{\"field\":\"position_offered\",\"new_value\":\"Senior Relationship Associate\",\"version\":5}', '2026-10-23 13:23:20'),
  ('571', '1', 'interview_field_update', 'interview_applications', '27', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"offered_salary\",\"old_value\":null}', '{\"field\":\"offered_salary\",\"new_value\":450000,\"version\":6}', '2026-10-23 13:23:21'),
  ('572', '1', 'interview_field_update', 'interview_applications', '27', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"joining_date\",\"old_value\":null}', '{\"field\":\"joining_date\",\"new_value\":\"2026-11-15\",\"version\":7}', '2026-10-23 13:23:22'),
  ('573', '1', 'interview_field_update', 'interview_applications', '27', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"selected\"}', '{\"field\":\"decision\",\"new_value\":\"rejected\",\"version\":8}', '2026-10-23 13:23:23'),
  ('574', '1', 'interview_field_update', 'interview_applications', '27', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"rejection_reason\",\"old_value\":null}', '{\"field\":\"rejection_reason\",\"new_value\":\"Not willing to relocate to Gurgaon\",\"version\":9}', '2026-10-23 13:23:24'),
  ('575', '1', 'interview_field_update', 'interview_applications', '27', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"rejected\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":10}', '2026-10-23 13:23:25'),
  ('576', '1', 'download_interview_pdf', 'interview_applications', '27', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Interview_Form_Kavita_Live_Test_318_27.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-23 13:23:26'),
  ('577', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 13:38:54'),
  ('578', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 13:38:59'),
  ('579', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 13:39:05'),
  ('580', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-23 13:39:07'),
  ('581', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-23 13:39:08'),
  ('582', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 13:39:11'),
  ('583', '1', 'interview_field_update', 'interview_applications', '29', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"feedback_hr\",\"old_value\":null}', '{\"field\":\"feedback_hr\",\"new_value\":\"Excellent communication skills and strong presentation.\",\"version\":2}', '2026-10-23 13:39:21'),
  ('584', '1', 'interview_field_update', 'interview_applications', '29', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":3}', '2026-10-23 13:39:22'),
  ('585', '1', 'interview_field_update', 'interview_applications', '29', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"candidate_interested\",\"old_value\":null}', '{\"field\":\"candidate_interested\",\"new_value\":\"yes\",\"version\":4}', '2026-10-23 13:39:23'),
  ('586', '1', 'interview_field_update', 'interview_applications', '29', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"position_offered\",\"old_value\":null}', '{\"field\":\"position_offered\",\"new_value\":\"Senior Relationship Associate\",\"version\":5}', '2026-10-23 13:39:24'),
  ('587', '1', 'interview_field_update', 'interview_applications', '29', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"offered_salary\",\"old_value\":null}', '{\"field\":\"offered_salary\",\"new_value\":450000,\"version\":6}', '2026-10-23 13:39:24'),
  ('588', '1', 'interview_field_update', 'interview_applications', '29', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"joining_date\",\"old_value\":null}', '{\"field\":\"joining_date\",\"new_value\":\"2026-11-15\",\"version\":7}', '2026-10-23 13:39:25'),
  ('589', '1', 'interview_field_update', 'interview_applications', '29', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"selected\"}', '{\"field\":\"decision\",\"new_value\":\"rejected\",\"version\":8}', '2026-10-23 13:39:27'),
  ('590', '1', 'interview_field_update', 'interview_applications', '29', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"rejection_reason\",\"old_value\":null}', '{\"field\":\"rejection_reason\",\"new_value\":\"Not willing to relocate to Gurgaon\",\"version\":9}', '2026-10-23 13:39:28'),
  ('591', '1', 'interview_field_update', 'interview_applications', '29', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"rejected\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":10}', '2026-10-23 13:39:29'),
  ('592', '1', 'download_interview_pdf', 'interview_applications', '29', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Interview_Form_Kavita_Live_Test_913_29.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-23 13:39:30'),
  ('593', '1', 'admin_login_success', 'users', '1', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"103.240.236.244\"}', '2026-10-23 13:56:48'),
  ('594', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 14:05:20'),
  ('595', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 14:05:23'),
  ('596', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 14:05:35'),
  ('597', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-23 14:05:37'),
  ('598', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-23 14:05:38'),
  ('599', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 14:05:40'),
  ('600', '1', 'interview_field_update', 'interview_applications', '5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"feedback_hr\",\"old_value\":null}', '{\"field\":\"feedback_hr\",\"new_value\":\"Excellent communication skills and strong presentation.\",\"version\":2}', '2026-10-23 14:05:51'),
  ('601', '1', 'interview_field_update', 'interview_applications', '5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":3}', '2026-10-23 14:05:55'),
  ('602', '1', 'interview_field_update', 'interview_applications', '5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"candidate_interested\",\"old_value\":null}', '{\"field\":\"candidate_interested\",\"new_value\":\"yes\",\"version\":4}', '2026-10-23 14:05:59'),
  ('603', '1', 'interview_field_update', 'interview_applications', '5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"position_offered\",\"old_value\":null}', '{\"field\":\"position_offered\",\"new_value\":\"Senior Relationship Associate\",\"version\":5}', '2026-10-23 14:06:03'),
  ('604', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 14:08:34'),
  ('605', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 14:08:37'),
  ('606', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 14:08:47'),
  ('607', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-23 14:08:49'),
  ('608', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-23 14:08:50'),
  ('609', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 14:08:51'),
  ('610', '1', 'interview_field_update', 'interview_applications', '7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"feedback_hr\",\"old_value\":null}', '{\"field\":\"feedback_hr\",\"new_value\":\"Excellent communication skills and strong presentation.\",\"version\":2}', '2026-10-23 14:09:02'),
  ('611', '1', 'interview_field_update', 'interview_applications', '7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":3}', '2026-10-23 14:09:06'),
  ('612', '1', 'interview_field_update', 'interview_applications', '7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"candidate_interested\",\"old_value\":null}', '{\"field\":\"candidate_interested\",\"new_value\":\"yes\",\"version\":4}', '2026-10-23 14:09:11'),
  ('613', '1', 'interview_field_update', 'interview_applications', '7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"position_offered\",\"old_value\":null}', '{\"field\":\"position_offered\",\"new_value\":\"Senior Relationship Associate\",\"version\":5}', '2026-10-23 14:09:14'),
  ('614', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 14:11:49'),
  ('615', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 14:11:53'),
  ('616', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 14:12:03'),
  ('617', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-23 14:12:04'),
  ('618', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-23 14:12:05'),
  ('619', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 14:12:07'),
  ('620', '1', 'interview_field_update', 'interview_applications', '9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"feedback_hr\",\"old_value\":null}', '{\"field\":\"feedback_hr\",\"new_value\":\"Excellent communication skills and strong presentation.\",\"version\":2}', '2026-10-23 14:12:18'),
  ('621', '1', 'interview_field_update', 'interview_applications', '9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":3}', '2026-10-23 14:12:19'),
  ('622', '1', 'interview_field_update', 'interview_applications', '9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"candidate_interested\",\"old_value\":null}', '{\"field\":\"candidate_interested\",\"new_value\":\"yes\",\"version\":4}', '2026-10-23 14:12:20'),
  ('623', '1', 'interview_field_update', 'interview_applications', '9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"position_offered\",\"old_value\":null}', '{\"field\":\"position_offered\",\"new_value\":\"Senior Relationship Associate\",\"version\":5}', '2026-10-23 14:12:20'),
  ('624', '1', 'interview_field_update', 'interview_applications', '9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"offered_salary\",\"old_value\":null}', '{\"field\":\"offered_salary\",\"new_value\":450000,\"version\":6}', '2026-10-23 14:12:21'),
  ('625', '1', 'interview_field_update', 'interview_applications', '9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"joining_date\",\"old_value\":null}', '{\"field\":\"joining_date\",\"new_value\":\"2026-11-15\",\"version\":7}', '2026-10-23 14:12:21'),
  ('626', '1', 'interview_field_update', 'interview_applications', '9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"selected\"}', '{\"field\":\"decision\",\"new_value\":\"rejected\",\"version\":8}', '2026-10-23 14:12:23'),
  ('627', '1', 'interview_field_update', 'interview_applications', '9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"rejection_reason\",\"old_value\":null}', '{\"field\":\"rejection_reason\",\"new_value\":\"Not willing to relocate to Gurgaon\",\"version\":9}', '2026-10-23 14:12:24'),
  ('628', '1', 'interview_field_update', 'interview_applications', '9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"rejected\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":10}', '2026-10-23 14:12:24'),
  ('629', '1', 'download_interview_pdf', 'interview_applications', '9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Interview_Form_Kavita_Live_Test_24_9.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-23 14:12:25'),
  ('630', '1', 'admin_login_success', 'users', '1', '103.240.236.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"103.240.236.244\"}', '2026-10-23 14:43:37'),
  ('631', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 15:55:57'),
  ('632', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 15:56:02'),
  ('633', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 15:56:14'),
  ('634', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":false}', '2026-10-23 15:56:16'),
  ('635', '1', 'qr_toggle_accepting', 'qr_settings', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"accepting\":true}', '2026-10-23 15:56:17'),
  ('636', '1', 'admin_login_success', 'users', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"ip\":\"127.0.0.1\"}', '2026-10-23 15:56:20'),
  ('637', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"feedback_hr\",\"old_value\":null}', '{\"field\":\"feedback_hr\",\"new_value\":\"Excellent communication skills and strong presentation.\",\"version\":2}', '2026-10-23 15:56:44'),
  ('638', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"pending\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":3}', '2026-10-23 15:56:45'),
  ('639', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"candidate_interested\",\"old_value\":null}', '{\"field\":\"candidate_interested\",\"new_value\":\"yes\",\"version\":4}', '2026-10-23 15:56:46'),
  ('640', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"position_offered\",\"old_value\":null}', '{\"field\":\"position_offered\",\"new_value\":\"Senior Relationship Associate\",\"version\":5}', '2026-10-23 15:56:47'),
  ('641', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"offered_salary\",\"old_value\":null}', '{\"field\":\"offered_salary\",\"new_value\":450000,\"version\":6}', '2026-10-23 15:56:47'),
  ('642', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"joining_date\",\"old_value\":null}', '{\"field\":\"joining_date\",\"new_value\":\"2026-11-15\",\"version\":7}', '2026-10-23 15:56:48'),
  ('643', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"selected\"}', '{\"field\":\"decision\",\"new_value\":\"rejected\",\"version\":8}', '2026-10-23 15:56:49'),
  ('644', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"rejection_reason\",\"old_value\":null}', '{\"field\":\"rejection_reason\",\"new_value\":\"Not willing to relocate to Gurgaon\",\"version\":9}', '2026-10-23 15:56:50'),
  ('645', '1', 'interview_field_update', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', '{\"field\":\"decision\",\"old_value\":\"rejected\"}', '{\"field\":\"decision\",\"new_value\":\"selected\",\"version\":10}', '2026-10-23 15:56:51'),
  ('646', '1', 'download_interview_pdf', 'interview_applications', '13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, '{\"filename\":\"Interview_Form_Kavita_Live_Test_412_13.pdf\",\"ip\":\"127.0.0.1\"}', '2026-10-23 15:56:52');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `documents`
INSERT INTO `documents` (`id`, `user_id`, `document_type`, `original_name`, `stored_name`, `file_path`, `mime_type`, `file_size_before`, `file_size_after`, `version`, `status`, `rejection_reason`, `is_archived`, `archived_at`, `uploaded_at`) VALUES
  ('1', '34', 'aadhaar_front', 'CI_CD Pipeline_ Faster, Safer Delivery.png', '34_aadhaar_front_9d9c608e915d56d48869.jpg', '/home/realtys1/pathshala_app/storage/uploads/34_aadhaar_front_9d9c608e915d56d48869.jpg', 'image/jpeg', '1807458', '257455', '1', 'pending', NULL, '0', NULL, '2026-10-19 17:15:56'),
  ('2', '34', 'aadhaar_back', 'CI_CD Pipeline_ Faster, Safer Delivery.png', '34_aadhaar_back_e140d1fa3b23cdc36ac2.jpg', '/home/realtys1/pathshala_app/storage/uploads/34_aadhaar_back_e140d1fa3b23cdc36ac2.jpg', 'image/jpeg', '1807458', '257455', '1', 'pending', NULL, '0', NULL, '2026-10-19 17:16:00'),
  ('3', '34', 'pan_card', 'CI_CD Pipeline_ Faster, Safer Delivery.png', '34_pan_card_8adf8f399a2dcb49fc5a.jpg', '/home/realtys1/pathshala_app/storage/uploads/34_pan_card_8adf8f399a2dcb49fc5a.jpg', 'image/jpeg', '1807458', '257455', '1', 'pending', NULL, '0', NULL, '2026-10-19 17:16:44'),
  ('24', '54', 'aadhaar_front', 'Adhar card Chhavi Mittal.jpeg', '54_aadhaar_front_0ff68da892f356212b3d.jpg', '/home/realtys1/pathshala_app/storage/uploads/54_aadhaar_front_0ff68da892f356212b3d.jpg', 'image/jpeg', '353187', '293524', '1', 'pending', NULL, '0', NULL, '2026-10-22 12:17:41'),
  ('25', '54', 'aadhaar_back', 'Adhar card Chhavi Mittal.jpeg', '54_aadhaar_back_c9eb968fa96ec5b02efb.jpg', '/home/realtys1/pathshala_app/storage/uploads/54_aadhaar_back_c9eb968fa96ec5b02efb.jpg', 'image/jpeg', '353187', '293524', '1', 'pending', NULL, '0', NULL, '2026-10-22 12:17:50'),
  ('26', '54', 'pan_card', 'Adhar Amit Arora.pdf', '54_pan_card_d1c09655a3fd29621b27.pdf', '/home/realtys1/pathshala_app/storage/uploads/54_pan_card_d1c09655a3fd29621b27.pdf', 'application/pdf', '167556', '167556', '1', 'pending', NULL, '0', NULL, '2026-10-22 12:18:01');

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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `emergency_contacts`
INSERT INTO `emergency_contacts` (`id`, `user_id`, `contact_name`, `relationship`, `mobile`, `alternate_mobile`, `address`, `created_at`, `updated_at`) VALUES
  ('1', '34', 'Geeta Singh', 'Father', '9876966879', NULL, NULL, '2026-10-19 17:17:01', '2026-10-19 17:17:05'),
  ('18', '54', 'rames', 'Father', '8908864334', NULL, NULL, '2026-10-22 12:18:29', '2026-10-22 12:18:30');

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `interview_applications`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `interview_applications`;
CREATE TABLE `interview_applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hr_name` varchar(100) NOT NULL,
  `post_applied` varchar(150) NOT NULL,
  `applied_for_type` varchar(50) NOT NULL DEFAULT 'Commercial',
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `experience_type` enum('experience','fresher') NOT NULL DEFAULT 'fresher',
  `years_in_real_estate` decimal(4,1) DEFAULT NULL,
  `vehicle_available` enum('yes','no') NOT NULL DEFAULT 'no',
  `current_location` varchar(255) NOT NULL,
  `higher_education` varchar(255) NOT NULL,
  `has_references` enum('yes','no') NOT NULL DEFAULT 'no',
  `reference_details` text DEFAULT NULL,
  `last_company_hr_tl` varchar(255) DEFAULT NULL,
  `reason_to_leave` text DEFAULT NULL,
  `immediate_joiner` enum('yes','no') NOT NULL DEFAULT 'yes',
  `consent_at` datetime NOT NULL,
  `submitted_at` datetime NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `source_by` varchar(150) DEFAULT NULL,
  `feedback_tl_sm` text DEFAULT NULL,
  `feedback_hr` text DEFAULT NULL,
  `feedback_final` text DEFAULT NULL,
  `module_discussed` text DEFAULT NULL,
  `position_offered` varchar(150) DEFAULT NULL,
  `decision` enum('pending','selected','rejected') NOT NULL DEFAULT 'pending',
  `decision_at` datetime DEFAULT NULL,
  `decision_by` int(10) unsigned DEFAULT NULL,
  `candidate_interested` enum('yes','no') DEFAULT NULL,
  `offered_salary` decimal(12,2) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT 1,
  `first_viewed_at` datetime DEFAULT NULL,
  `first_viewed_by` int(10) unsigned DEFAULT NULL,
  `updated_by` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ia_decision` (`decision`),
  KEY `idx_ia_submitted_at` (`submitted_at`),
  KEY `idx_ia_updated_at` (`updated_at`),
  KEY `idx_ia_mobile` (`mobile`),
  KEY `idx_ia_email` (`email`),
  KEY `idx_ia_first_viewed` (`first_viewed_at`),
  KEY `idx_ia_archived` (`is_archived`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `interview_applications`
INSERT INTO `interview_applications` (`id`, `hr_name`, `post_applied`, `applied_for_type`, `full_name`, `email`, `mobile`, `experience_type`, `years_in_real_estate`, `vehicle_available`, `current_location`, `higher_education`, `has_references`, `reference_details`, `last_company_hr_tl`, `reason_to_leave`, `immediate_joiner`, `consent_at`, `submitted_at`, `ip_address`, `user_agent`, `source_by`, `feedback_tl_sm`, `feedback_hr`, `feedback_final`, `module_discussed`, `position_offered`, `decision`, `decision_at`, `decision_by`, `candidate_interested`, `offered_salary`, `joining_date`, `rejection_reason`, `version`, `first_viewed_at`, `first_viewed_by`, `updated_by`, `updated_at`, `is_archived`, `archived_at`) VALUES
  ('1', 'Ankita Mittal', 'Test', 'Commercial', 'Abhishek Boora', 'abhishekboora2001@gmail.com', '9306098396', 'fresher', NULL, 'no', 'Gurgaon, IN', 'Mcaaaa', 'no', NULL, NULL, NULL, 'no', '2026-10-23 13:57:55', '2026-10-23 13:57:55', '27.59.70.157', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '2026-10-23 13:57:55', '0', NULL),
  ('2', 'Ankita Mittal', 'Test', 'Commercial', 'Abhishek Boora', 'abhishekboora2001@gmail.com', '9306098396', 'fresher', NULL, 'no', 'Gurgaon, IN', 'Mcaaaa', 'no', NULL, NULL, NULL, 'no', '2026-10-23 13:59:06', '2026-10-23 13:59:06', '27.59.70.157', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '2026-10-23 13:59:06', '0', NULL),
  ('3', 'Ankita Mittal', 'Hhhj', 'Commercial', 'Tyhy', 'abhishekbooa2001@gmail.com', '9306098396', 'fresher', NULL, 'no', 'Gurgaon', 'Mcaaaa', 'no', NULL, NULL, NULL, 'no', '2026-10-23 13:59:33', '2026-10-23 13:59:33', '27.59.70.157', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '2026-10-23 13:59:33', '0', NULL),
  ('4', 'Unanti Bhatia', 'Associate Director - Luxury Sales', 'Commercial', 'Aaditya Vikram Singh', 'aaditya.test.1792744526115@realtysmartzpathshala.in', '9829107752', 'experience', '4.5', 'yes', 'Golf Course Road, Gurugram', 'B.Tech + MBA', 'yes', 'Direct Referral from Director of Sales', 'Siddharth Roy', 'Opportunity in ultra-luxury vertical', 'yes', '2026-10-23 14:05:26', '2026-10-23 14:05:26', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '2026-10-23 14:05:26', '0', NULL),
  ('5', 'Unanti Bhatia', 'Client Relationship Executive', 'Commercial', 'Kavita Live Test 167', 'kavita.1792744544245@example.com', '9911624196', 'fresher', NULL, 'no', 'Indirapuram, Ghaziabad', 'B.Com', 'no', NULL, NULL, NULL, 'yes', '2026-10-23 14:05:44', '2026-10-23 14:05:44', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, NULL, 'Excellent communication skills and strong presentation.', NULL, NULL, 'Senior Relationship Associate', 'selected', '2026-10-23 14:05:55', '1', 'yes', NULL, NULL, NULL, '5', '2026-10-23 14:05:49', '1', '1', '2026-10-23 14:06:03', '0', NULL),
  ('6', 'Unanti Bhatia', 'Associate Director - Luxury Sales', 'Commercial', 'Aaditya Vikram Singh', 'aaditya.test.1792744720516@realtysmartzpathshala.in', '9886791884', 'experience', '4.5', 'yes', 'Golf Course Road, Gurugram', 'B.Tech + MBA', 'yes', 'Direct Referral from Director of Sales', 'Siddharth Roy', 'Opportunity in ultra-luxury vertical', 'yes', '2026-10-23 14:08:40', '2026-10-23 14:08:40', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '2026-10-23 14:08:40', '0', NULL),
  ('7', 'Unanti Bhatia', 'Client Relationship Executive', 'Commercial', 'Kavita Live Test 365', 'kavita.1792744735939@example.com', '9981452012', 'fresher', NULL, 'no', 'Indirapuram, Ghaziabad', 'B.Com', 'no', NULL, NULL, NULL, 'yes', '2026-10-23 14:08:55', '2026-10-23 14:08:55', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, NULL, 'Excellent communication skills and strong presentation.', NULL, NULL, 'Senior Relationship Associate', 'selected', '2026-10-23 14:09:06', '1', 'yes', NULL, NULL, NULL, '5', '2026-10-23 14:09:01', '1', '1', '2026-10-23 14:09:14', '0', NULL),
  ('8', 'Unanti Bhatia', 'Associate Director - Luxury Sales', 'Commercial', 'Aaditya Vikram Singh', 'aaditya.test.1792744916201@realtysmartzpathshala.in', '9891007665', 'experience', '4.5', 'yes', 'Golf Course Road, Gurugram', 'B.Tech + MBA', 'yes', 'Direct Referral from Director of Sales', 'Siddharth Roy', 'Opportunity in ultra-luxury vertical', 'yes', '2026-10-23 14:11:56', '2026-10-23 14:11:56', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '2026-10-23 14:11:56', '0', NULL),
  ('9', 'Unanti Bhatia', 'Client Relationship Executive', 'Commercial', 'Kavita Live Test 24', 'kavita.1792744931579@example.com', '9964296176', 'fresher', NULL, 'no', 'Indirapuram, Ghaziabad', 'B.Com', 'no', NULL, NULL, NULL, 'yes', '2026-10-23 14:12:11', '2026-10-23 14:12:11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, NULL, 'Excellent communication skills and strong presentation.', NULL, NULL, 'Senior Relationship Associate', 'selected', '2026-10-23 14:12:24', '1', 'yes', '450000.00', '2026-11-15', 'Not willing to relocate to Gurgaon', '10', '2026-10-23 14:12:17', '1', '1', '2026-10-23 14:12:24', '0', NULL),
  ('10', 'Priyanka Yadav', 'Test', 'Commercial', 'Demo', 'abhishekboora2001@gmail.com', '9306098395', 'fresher', NULL, 'no', 'Gurgaon', 'Mcaaaa', 'no', NULL, NULL, NULL, 'no', '2026-10-23 14:45:09', '2026-10-23 14:45:09', '27.59.70.157', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '2026-10-23 14:45:09', '0', NULL),
  ('11', 'Ankita Mittal', 'Sales executive', 'Commercial', 'Ravi kumar', 'ai.mittal51@gmail.com', '9532145698', 'fresher', NULL, 'no', 'Gugi', 'Vivi', 'no', NULL, NULL, NULL, 'no', '2026-10-23 14:50:44', '2026-10-23 14:50:44', '150.242.87.176', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Mobile Safari/537.36', NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '2026-10-23 14:50:44', '0', NULL),
  ('12', 'Unanti Bhatia', 'Associate Director - Luxury Sales', 'Commercial', 'Aaditya Vikram Singh', 'aaditya.test.1792751164998@realtysmartzpathshala.in', '9869913078', 'experience', '4.5', 'yes', 'Golf Course Road, Gurugram', 'B.Tech + MBA', 'yes', 'Direct Referral from Director of Sales', 'Siddharth Roy', 'Opportunity in ultra-luxury vertical', 'yes', '2026-10-23 15:56:05', '2026-10-23 15:56:05', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '2026-10-23 15:56:05', '0', NULL),
  ('13', 'Unanti Bhatia', 'Client Relationship Executive', 'Residential', 'Kavita Live Test 412', 'kavita.1792751183135@example.com', '9952313163', 'fresher', NULL, 'no', 'Indirapuram, Ghaziabad', 'B.Com', 'no', NULL, NULL, NULL, 'yes', '2026-10-23 15:56:23', '2026-10-23 15:56:23', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36', NULL, NULL, 'Excellent communication skills and strong presentation.', NULL, NULL, 'Senior Relationship Associate', 'selected', '2026-10-23 15:56:51', '1', 'yes', '450000.00', '2026-11-15', 'Not willing to relocate to Gurgaon', '10', '2026-10-23 15:56:41', '1', '1', '2026-10-23 15:56:51', '0', NULL);

-- --------------------------------------------------------
-- Table structure for `interview_email_log`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `interview_email_log`;
CREATE TABLE `interview_email_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `application_id` bigint(20) unsigned NOT NULL,
  `type` varchar(64) NOT NULL,
  `to_email` varchar(150) NOT NULL,
  `status` enum('pending','sent','failed') NOT NULL DEFAULT 'pending',
  `error` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `sent_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_iel_app_type` (`application_id`,`type`),
  KEY `idx_iel_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `interview_email_log`
INSERT INTO `interview_email_log` (`id`, `application_id`, `type`, `to_email`, `status`, `error`, `created_at`, `sent_at`) VALUES
  ('1', '4', 'hr_new_form', 'hr@realtysmartzpathshala.in', 'failed', NULL, '2026-10-23 14:05:26', '2026-10-23 14:05:27'),
  ('2', '5', 'hr_new_form', 'hr@realtysmartzpathshala.in', 'failed', NULL, '2026-10-23 14:05:44', '2026-10-23 14:05:45'),
  ('3', '6', 'hr_new_form', 'hr@realtysmartzpathshala.in', 'failed', NULL, '2026-10-23 14:08:40', '2026-10-23 14:08:41'),
  ('4', '7', 'hr_new_form', 'hr@realtysmartzpathshala.in', 'failed', NULL, '2026-10-23 14:08:56', '2026-10-23 14:08:57'),
  ('5', '8', 'hr_new_form', 'hr@realtysmartzpathshala.in', 'failed', NULL, '2026-10-23 14:11:56', '2026-10-23 14:11:57'),
  ('6', '9', 'hr_new_form', 'hr@realtysmartzpathshala.in', 'failed', NULL, '2026-10-23 14:12:11', '2026-10-23 14:12:12'),
  ('7', '10', 'hr_new_form', 'hr@realtysmartzpathshala.in', 'failed', NULL, '2026-10-23 14:45:09', '2026-10-23 14:45:09'),
  ('8', '11', 'hr_new_form', 'hr@realtysmartzpathshala.in', 'failed', NULL, '2026-10-23 14:50:44', '2026-10-23 14:50:44'),
  ('9', '12', 'hr_new_form', 'hr@realtysmartzpathshala.in', 'failed', NULL, '2026-10-23 15:56:05', '2026-10-23 15:56:07'),
  ('10', '13', 'hr_new_form', 'hr@realtysmartzpathshala.in', 'failed', NULL, '2026-10-23 15:56:23', '2026-10-23 15:56:24');

-- --------------------------------------------------------
-- Table structure for `interview_employment_rows`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `interview_employment_rows`;
CREATE TABLE `interview_employment_rows` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `application_id` bigint(20) unsigned NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `organisation` varchar(255) DEFAULT NULL,
  `designation` varchar(150) DEFAULT NULL,
  `salary` varchar(100) DEFAULT NULL,
  `hr_name_number` varchar(255) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ier_app_id` (`application_id`),
  CONSTRAINT `fk_ier_app_id` FOREIGN KEY (`application_id`) REFERENCES `interview_applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `interview_employment_rows`
INSERT INTO `interview_employment_rows` (`id`, `application_id`, `sort_order`, `organisation`, `designation`, `salary`, `hr_name_number`, `duration`) VALUES
  ('1', '4', '1', 'Emaar India', 'Senior Manager - Sales', '12,00,000 PA', 'Kavita Mehta 9871122334', '3.5 Years'),
  ('2', '6', '1', 'Emaar India', 'Senior Manager - Sales', '12,00,000 PA', 'Kavita Mehta 9871122334', '3.5 Years'),
  ('3', '8', '1', 'Emaar India', 'Senior Manager - Sales', '12,00,000 PA', 'Kavita Mehta 9871122334', '3.5 Years'),
  ('4', '12', '1', 'Emaar India', 'Senior Manager - Sales', '12,00,000 PA', 'Kavita Mehta 9871122334', '3.5 Years');

-- --------------------------------------------------------
-- Table structure for `interview_field_versions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `interview_field_versions`;
CREATE TABLE `interview_field_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `application_id` bigint(20) unsigned NOT NULL,
  `field` varchar(64) NOT NULL,
  `value` longtext DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ifv_app_field` (`application_id`,`field`),
  KEY `idx_ifv_updated_at` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `interview_field_versions`
INSERT INTO `interview_field_versions` (`id`, `application_id`, `field`, `value`, `updated_at`, `updated_by`) VALUES
  ('1', '5', 'feedback_hr', 'Excellent communication skills and strong presentation.', '2026-10-23 14:05:50', '1'),
  ('2', '5', 'decision', 'selected', '2026-10-23 14:05:55', '1'),
  ('3', '5', 'candidate_interested', 'yes', '2026-10-23 14:05:59', '1'),
  ('4', '5', 'position_offered', 'Senior Relationship Associate', '2026-10-23 14:06:03', '1'),
  ('5', '7', 'feedback_hr', 'Excellent communication skills and strong presentation.', '2026-10-23 14:09:02', '1'),
  ('6', '7', 'decision', 'selected', '2026-10-23 14:09:06', '1'),
  ('7', '7', 'candidate_interested', 'yes', '2026-10-23 14:09:11', '1'),
  ('8', '7', 'position_offered', 'Senior Relationship Associate', '2026-10-23 14:09:14', '1'),
  ('9', '9', 'feedback_hr', 'Excellent communication skills and strong presentation.', '2026-10-23 14:12:18', '1'),
  ('10', '9', 'decision', 'selected', '2026-10-23 14:12:19', '1'),
  ('11', '9', 'candidate_interested', 'yes', '2026-10-23 14:12:20', '1'),
  ('12', '9', 'position_offered', 'Senior Relationship Associate', '2026-10-23 14:12:20', '1'),
  ('13', '9', 'offered_salary', '450000', '2026-10-23 14:12:21', '1'),
  ('14', '9', 'joining_date', '2026-11-15', '2026-10-23 14:12:21', '1'),
  ('15', '9', 'decision', 'rejected', '2026-10-23 14:12:23', '1'),
  ('16', '9', 'rejection_reason', 'Not willing to relocate to Gurgaon', '2026-10-23 14:12:24', '1'),
  ('17', '9', 'decision', 'selected', '2026-10-23 14:12:24', '1'),
  ('18', '13', 'feedback_hr', 'Excellent communication skills and strong presentation.', '2026-10-23 15:56:43', '1'),
  ('19', '13', 'decision', 'selected', '2026-10-23 15:56:45', '1'),
  ('20', '13', 'candidate_interested', 'yes', '2026-10-23 15:56:46', '1'),
  ('21', '13', 'position_offered', 'Senior Relationship Associate', '2026-10-23 15:56:47', '1'),
  ('22', '13', 'offered_salary', '450000', '2026-10-23 15:56:47', '1'),
  ('23', '13', 'joining_date', '2026-11-15', '2026-10-23 15:56:48', '1'),
  ('24', '13', 'decision', 'rejected', '2026-10-23 15:56:49', '1'),
  ('25', '13', 'rejection_reason', 'Not willing to relocate to Gurgaon', '2026-10-23 15:56:50', '1'),
  ('26', '13', 'decision', 'selected', '2026-10-23 15:56:51', '1');

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `profiles`
INSERT INTO `profiles` (`id`, `user_id`, `father_name`, `whatsapp`, `mother_name`, `dob`, `gender`, `marital_status`, `blood_group`, `nationality`, `photo_path`, `address`, `permanent_address`, `current_address`, `same_as_permanent`, `city`, `state`, `pincode`, `aadhaar_enc`, `aadhaar_encrypted`, `aadhaar_last4`, `pan_enc`, `pan_last4`, `pan_number`, `passport_number`, `social_links`, `declaration_accepted`, `declaration_date`, `joining_date`, `signature_type`, `signature_data`, `completion_percentage`, `is_locked`, `created_at`, `updated_at`) VALUES
  ('1', '34', 'Boora', '9306098396', NULL, '2026-10-07', 'male', NULL, NULL, 'Indian', NULL, 'sector 688 gurguramn', 'sector 688 gurguramn', 'sector 688 gurguramn', '0', 'Gurugram', 'Haryana', '234213', 'Avuqm2e6XRzVaKZj0GAnl4zEKU9L9y9WFE8oaDrNAL7zCh5UcO9RErJL1iuVdpiG12lsHdtS1Qufnqw6KcPn4Q==', 'Avuqm2e6XRzVaKZj0GAnl4zEKU9L9y9WFE8oaDrNAL7zCh5UcO9RErJL1iuVdpiG12lsHdtS1Qufnqw6KcPn4Q==', '3882', 'HijXX7Fu0jhqEC1uAbdPVR9uASuHdW04g1JuVYVv/P7cOy9xBhy1/fCyuc3QmuyoMROITx6xvYkBmEffzvgRDw==', '234F', 'ABCDE1234F', NULL, '{\"linkedin\":\"\",\"instagram\":\"\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '1', '2026-10-19', '2026-12-15', 'typed', 'Abhishek', '100', '1', '2026-10-19 16:34:35', '2026-10-19 17:17:19'),
  ('21', '54', 'Smart Finance Chhavi Mittal', '8800992566', NULL, '2002-06-29', 'female', NULL, NULL, 'Indian', NULL, '11th Floor, Spaze Palazo, Sector 69\r\nGolf Course Extension Road', '11th Floor, Spaze Palazo, Sector 69\r\nGolf Course Extension Road', '11th Floor, Spaze Palazo, Sector 69\r\nGolf Course Extension Road', '0', 'GURUGRAM', 'Haryana', '122001', 'l8dDZTux2WuJLTykeh3sKryina/3KAFXTTa2R+HAzOlU77tTlgP/JQ8lNYWAsAfTBmiP9MIOzzj61DtfnRHzJQ==', 'l8dDZTux2WuJLTykeh3sKryina/3KAFXTTa2R+HAzOlU77tTlgP/JQ8lNYWAsAfTBmiP9MIOzzj61DtfnRHzJQ==', '8909', 'ykRpKt/Li+DuJLuzSQ5U0shPPIc1ART6k7ZR0DpiKahJ/Os/1zV0RcxL/7s2KOULR98LuuqAt6uWCX1Z5E1ElA==', '604R', 'ALOPM8604R', NULL, '{\"linkedin\":\"\",\"instagram\":\"\",\"facebook\":\"\",\"twitter\":\"\",\"youtube\":\"\",\"portfolio\":\"\"}', '1', '2026-10-22', NULL, 'typed', 'Smart Finance Chhavi Mittal', '100', '1', '2026-10-22 12:14:52', '2026-10-22 12:18:39'),
  ('29', '62', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Indian', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '0', '0', '2026-10-22 16:00:50', '2026-10-22 16:00:50');

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
-- Table structure for `qr_settings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `qr_settings`;
CREATE TABLE `qr_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(64) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `accepting_responses` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_qrs_type` (`type`),
  KEY `idx_qrs_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `qr_settings`
INSERT INTO `qr_settings` (`id`, `type`, `slug`, `accepting_responses`, `created_at`) VALUES
  ('1', 'interview_form', 'interview-candidate', '1', '2026-10-22 16:53:59');

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
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `settings`
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `updated_at`) VALUES
  ('1', 'site_name', 'Realty Smartz Pathshala', 'general', '2026-10-18 18:18:52'),
  ('2', 'site_tagline', 'Master the Art of Real Estate | India\'s #1 Real Estate Training', 'general', '2026-10-18 18:18:52'),
  ('3', 'contact_email', 'info@realtysmartzpathshala.in', 'contact', '2026-10-18 18:18:52'),
  ('4', 'hr_email', 'hr@realtysmartzpathshala.in', 'contact', '2026-10-17 12:24:19'),
  ('5', 'contact_phone', '+91 9999 888 777', 'contact', '2026-10-18 18:18:52'),
  ('6', 'contact_address', 'Golf Course Road, Sector 69, Gurgaon, Haryana 122011, India', 'contact', '2026-10-18 18:18:52'),
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
  ('45', 'preloader_text', 'Preparing Your Success Path...', 'general', '2026-10-18 18:18:53'),
  ('58', 'google_sheets_webhook_url', 'https://script.google.com/macros/s/AKfycbx2I952kgfzCJtftZIj8v64uk8wmzdiTlc5_Bk8QLAF7rMuYArpOKqaw8XdIEChEZ9fMQ/exec', 'general', '2026-10-22 15:07:21'),
  ('59', 'interview_email_hr_new_form', '1', 'interview', '2026-10-22 16:53:59'),
  ('60', 'interview_email_hr_recipients', 'hr@realtysmartzpathshala.in', 'interview', '2026-10-22 16:53:59'),
  ('61', 'interview_email_candidate_selected', '0', 'interview', '2026-10-22 16:53:59'),
  ('62', 'interview_email_selected_subject', 'Offer of Employment | Realty Smartz Pathshala', 'interview', '2026-10-22 16:53:59'),
  ('63', 'interview_email_selected_body', 'Dear {name},\n\nWe are pleased to inform you that following your interview, you have been selected for the position of {position} at Realty Smartz Pathshala.\n\nYour tentative date of joining is {joining_date}.\n\nOur HR department will contact you shortly with further details and joining documentation.\n\nWarm regards,\nHR Department\nRealty Smartz Pathshala', 'interview', '2026-10-22 16:53:59'),
  ('64', 'interview_email_candidate_rejected', '0', 'interview', '2026-10-22 16:53:59'),
  ('65', 'interview_email_rejected_subject', 'Application Update | Realty Smartz Pathshala', 'interview', '2026-10-22 16:53:59'),
  ('66', 'interview_email_rejected_body', 'Dear {name},\n\nThank you for taking the time to attend the interview with Realty Smartz Pathshala.\n\nWhile your background and qualifications are commendable, we have decided to move forward with other candidates whose profiles more closely match our current operational requirements.\n\nWe genuinely appreciate your interest in our organization and wish you every success in your future career endeavors.\n\nWarm regards,\nHR Department\nRealty Smartz Pathshala', 'interview', '2026-10-22 16:53:59'),
  ('67', 'google_sheets_sheet_name', 'candidates', 'general', '2026-10-23 13:20:03'),
  ('68', 'google_sheets_interview_tab', 'Interview Candidate', 'general', '2026-10-23 13:20:03');

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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `status_history`
INSERT INTO `status_history` (`id`, `user_id`, `old_status`, `new_status`, `comments`, `changed_by`, `created_at`) VALUES
  ('4', '34', 'email_verified', 'documents_submitted', 'Candidate completed 4-step onboarding wizard and submitted documents.', '34', '2026-10-19 17:17:19'),
  ('5', '34', 'documents_submitted', 'test_completed', 'Aptitude Assessment completed with score 2/15 (13.33%) - FAILED', '34', '2026-10-19 17:17:49'),
  ('12', '54', 'email_verified', 'documents_submitted', 'Candidate completed 4-step onboarding wizard and submitted documents.', '54', '2026-10-22 12:18:39'),
  ('13', '54', 'documents_submitted', 'test_completed', 'Aptitude Assessment completed with score 0/15 (0%) - FAILED', '54', '2026-10-22 12:19:04'),
  ('25', '62', 'registered', 'test_completed', 'Aptitude Assessment completed with score 11/15 (73.33%) - PASSED', '62', '2026-10-22 16:10:57');

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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `test_attempts`
INSERT INTO `test_attempts` (`id`, `test_id`, `set_id`, `user_id`, `guest_test_link_id`, `attempt_no`, `started_at`, `deadline_at`, `submitted_at`, `expires_at`, `completed_at`, `total_questions`, `total_answered`, `total_correct`, `total_wrong`, `score`, `percentage`, `passed`, `tab_switch_count`, `fullscreen_exit_count`, `status`, `allow_reattempt`, `reattempt_allowed_by`, `created_at`) VALUES
  ('5', '1', '1', '54', NULL, '1', '2026-10-22 12:18:43', '2026-10-22 12:28:43', '2026-10-22 12:19:04', '2026-10-22 12:28:43', '2026-10-22 12:19:04', '15', '0', '0', '0', '0.00', '0.00', '0', '0', '0', 'completed', '0', NULL, '2026-10-22 12:18:43'),
  ('19', '1', '4', '62', NULL, '1', '2026-10-22 16:00:56', '2026-10-22 16:10:56', '2026-10-22 16:10:57', '2026-10-22 16:10:56', '2026-10-22 16:10:57', '15', '15', '11', '4', '11.00', '73.33', '1', '1', '0', 'completed', '0', NULL, '2026-10-22 16:00:56');

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
  `candidate_type` enum('onboarding','aptitude_only') NOT NULL DEFAULT 'onboarding',
  `source` varchar(50) NOT NULL DEFAULT 'direct',
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
  KEY `idx_users_archived` (`is_archived`),
  KEY `idx_users_candidate_type` (`candidate_type`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `users`
INSERT INTO `users` (`id`, `full_name`, `father_name`, `email`, `mobile`, `whatsapp`, `joining_date`, `password_hash`, `role`, `candidate_type`, `source`, `is_guest`, `status`, `email_verified`, `email_verified_at`, `remember_token`, `is_locked`, `locked_until`, `failed_login_attempts`, `is_archived`, `archived_at`, `archived_by`, `created_at`, `updated_at`) VALUES
  ('1', 'Super Admin', NULL, 'admin@realtysmartzpathshala.in', '+919876543210', NULL, NULL, '$2y$10$r8j0nFMN8FTQVLUN2AKbg.giMKuQ1tYMo09bjRhHeFj3wNqynkhoa', 'super_admin', 'onboarding', 'direct', '0', 'active', '1', '2026-10-17 12:24:19', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 12:24:19', '2026-10-22 18:34:50'),
  ('2', 'HR Manager', NULL, 'hr@realtysmartzpathshala.in', '+919876543211', NULL, NULL, '$2y$10$7in11AzJaJ35YMziftsFZugUW8ELNs5ev1Zr7dHEfAHSoKlPJex8u', 'hr_manager', 'onboarding', 'direct', '0', 'approved', '1', '2026-10-17 12:24:19', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 12:24:19', '2026-10-17 12:24:19'),
  ('3', 'Reviewer Officer', NULL, 'reviewer@realtysmartzpathshala.in', '+919876543212', NULL, NULL, '$2y$10$7in11AzJaJ35YMziftsFZugUW8ELNs5ev1Zr7dHEfAHSoKlPJex8u', 'reviewer', 'onboarding', 'direct', '0', 'approved', '1', '2026-10-17 12:24:19', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 12:24:19', '2026-10-17 12:24:19'),
  ('34', 'Abhishek', 'Boora', 'abhishekboora2001@gmail.com', '9306098396', '9306098396', '2026-12-15', '$2y$11$99gkd1BJf/6i7uPrMNYtzOe8rmJCi5vo6HMTh1iXD6eTr1Ult14Ly', 'candidate', 'onboarding', 'direct', '0', 'registered', '1', '2026-10-19 16:35:17', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-22 15:21:01', '2026-10-22 15:57:49'),
  ('54', 'Smart Finance Chhavi Mittal', 'Smart Finance Chhavi Mittal', 'yogatatvam@gmail.com', '8800992566', '8800992566', NULL, '$2y$11$5nxXzjbLy3mgsGBq8mURKejmQIDYoioWdTOIsSKAzx8jz4Eon0ATO', 'candidate', 'onboarding', 'direct', '0', 'test_completed', '1', '2026-10-22 12:15:37', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-22 12:14:52', '2026-10-22 12:19:04'),
  ('62', 'ANKITA MITTAL', NULL, 'ai.mittal51@gmail.com', '9266826371', NULL, NULL, '$2y$11$aZXBqXtrZnlnkrA8dHjXGeQ2WVej3At/KLANJuhGE2vpwopQWCqD2', 'candidate', 'aptitude_only', 'qr_aptitude', '0', 'test_completed', '1', '2026-10-22 16:00:50', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-22 16:00:50', '2026-10-22 16:10:57');

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
