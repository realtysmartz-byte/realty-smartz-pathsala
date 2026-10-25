-- Realty Smartz Pathshala Complete Production Database Dump
-- Generated: 2026-10-25 13:07:40
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  ('59', 'interview_email_hr_new_form', '1', 'interview', '2026-10-23 18:46:21'),
  ('60', 'interview_email_hr_recipients', 'hr@realtysmartzpathshala.in', 'interview', '2026-10-23 18:46:21'),
  ('61', 'interview_email_candidate_selected', '0', 'interview', '2026-10-23 18:46:21'),
  ('62', 'interview_email_selected_subject', 'Offer of Employment | Realty Smartz', 'interview', '2026-10-23 18:46:21'),
  ('63', 'interview_email_selected_body', 'Dear {name},\r\n\r\nWe are pleased to inform you that following your interview, you have been selected for the position of {position} at Realty Smartz Pathshala.\r\n\r\nYour tentative date of joining is {joining_date}.\r\n\r\nOur HR department will contact you shortly with further details and joining documentation.\r\n\r\nWarm regards,\r\nHR Department\r\nRealty Smartz Pvt Ltd.', 'interview', '2026-10-23 18:46:21'),
  ('64', 'interview_email_candidate_rejected', '1', 'interview', '2026-10-23 18:46:21'),
  ('65', 'interview_email_rejected_subject', 'Application Update | Realty Smartz', 'interview', '2026-10-23 18:46:21'),
  ('66', 'interview_email_rejected_body', 'Dear {name},\r\n\r\nThank you for taking the time to attend the interview with Realty Smartz Pathshala.\r\n\r\nWhile your background and qualifications are commendable, we have decided to move forward with other candidates whose profiles more closely match our current operational requirements.\r\n\r\nWe genuinely appreciate your interest in our organization and wish you every success in your future career endeavors.\r\n\r\nWarm regards,\r\nHR Department\r\nRealty Smartz Pvt Ltd.', 'interview', '2026-10-23 18:46:21'),
  ('67', 'google_sheets_sheet_name', 'candidates', 'general', '2026-10-23 13:20:03'),
  ('68', 'google_sheets_interview_tab', 'Interview Candidate', 'general', '2026-10-23 13:20:03'),
  ('70', 'telegram_bot_token', '8858296372:AAFB-XERTG6Ewpt4SJoXw9v0M-xCUSMvkOg', 'general', '2026-10-23 18:46:21'),
  ('71', 'telegram_chat_id', '-1004486003903', 'general', '2026-10-23 18:46:21'),
  ('72', 'telegram_notifications_enabled', '1', 'general', '2026-10-23 18:46:21');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  ('3', 'Reviewer Officer', NULL, 'reviewer@realtysmartzpathshala.in', '+919876543212', NULL, NULL, '$2y$10$7in11AzJaJ35YMziftsFZugUW8ELNs5ev1Zr7dHEfAHSoKlPJex8u', 'reviewer', 'onboarding', 'direct', '0', 'approved', '1', '2026-10-17 12:24:19', NULL, '0', NULL, '0', '0', NULL, NULL, '2026-10-17 12:24:19', '2026-10-17 12:24:19');

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
