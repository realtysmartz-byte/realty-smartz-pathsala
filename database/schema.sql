-- ================================================================
-- Realty Smartz Pathshala - Complete Database Schema
-- Charset: utf8mb4 | Engine: InnoDB
-- Strict Data Safety: No hard deletes on core candidate data
-- ================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------
-- 1. Users Table (Candidates & Admins)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `mobile` VARCHAR(20) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('candidate', 'super_admin', 'hr_manager', 'reviewer') NOT NULL DEFAULT 'candidate',
  `status` ENUM(
      'registered',
      'email_verified',
      'profile_completed',
      'documents_submitted',
      'test_pending',
      'test_completed',
      'under_review',
      'approved',
      'changes_requested',
      'rejected'
  ) NOT NULL DEFAULT 'registered',
  `email_verified_at` DATETIME NULL,
  `remember_token` VARCHAR(100) NULL,
  `is_locked` TINYINT(1) NOT NULL DEFAULT 0,
  `locked_until` DATETIME NULL,
  `failed_login_attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
  `archived_at` DATETIME NULL,
  `archived_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_users_email` (`email`),
  INDEX `idx_users_role` (`role`),
  INDEX `idx_users_status` (`status`),
  INDEX `idx_users_archived` (`is_archived`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 2. Email Verifications Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `email_verifications` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `otp` VARCHAR(6) NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `expires_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_ev_user` (`user_id`),
  INDEX `idx_ev_token` (`token`),
  INDEX `idx_ev_otp` (`otp`),
  CONSTRAINT `fk_ev_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 3. Password Resets Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(191) NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_pr_email` (`email`),
  INDEX `idx_pr_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 4. Login Attempts Table (Rate Limiting)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ip_address` VARCHAR(45) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `last_attempt` DATETIME NOT NULL,
  `is_locked` TINYINT(1) NOT NULL DEFAULT 0,
  `locked_until` DATETIME NULL,
  INDEX `idx_la_ip_email` (`ip_address`, `email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 5. Profiles Table (Section A & B: Personal & Identity)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `profiles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL UNIQUE,
  `father_name` VARCHAR(150) NULL,
  `mother_name` VARCHAR(150) NULL,
  `dob` DATE NULL,
  `gender` ENUM('male', 'female', 'other') NULL,
  `marital_status` ENUM('single', 'married', 'divorced', 'widowed') NULL,
  `blood_group` VARCHAR(10) NULL,
  `nationality` VARCHAR(50) NOT NULL DEFAULT 'Indian',
  `photo_path` VARCHAR(255) NULL,
  `permanent_address` TEXT NULL,
  `current_address` TEXT NULL,
  `same_as_permanent` TINYINT(1) NOT NULL DEFAULT 0,
  `city` VARCHAR(100) NULL,
  `state` VARCHAR(100) NULL,
  `pincode` VARCHAR(20) NULL,
  -- Encrypted at rest (AES-256-CBC)
  `aadhaar_encrypted` TEXT NULL,
  `aadhaar_last4` VARCHAR(4) NULL,
  `pan_number` VARCHAR(20) NULL,
  `passport_number` VARCHAR(30) NULL,
  -- Section G: Consent & Declaration
  `declaration_accepted` TINYINT(1) NOT NULL DEFAULT 0,
  `declaration_date` DATE NULL,
  `signature_type` ENUM('typed', 'drawn') NULL,
  `signature_data` MEDIUMTEXT NULL,
  `completion_percentage` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `is_locked` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 6. Education Table (Section C)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `education` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `highest_qualification` VARCHAR(100) NOT NULL,
  `institute` VARCHAR(255) NOT NULL,
  `year_of_passing` INT NOT NULL,
  `percentage_cgpa` VARCHAR(20) NOT NULL,
  `document_id` INT UNSIGNED NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_edu_user` (`user_id`),
  CONSTRAINT `fk_edu_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 7. Experience Table (Section D)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `experience` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `is_fresher` TINYINT(1) NOT NULL DEFAULT 0,
  `company` VARCHAR(255) NULL,
  `designation` VARCHAR(150) NULL,
  `joining_date` DATE NULL,
  `last_working_date` DATE NULL,
  `last_salary` VARCHAR(50) NULL,
  `offer_letter_doc_id` INT UNSIGNED NULL,
  `relieving_letter_doc_id` INT UNSIGNED NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_exp_user` (`user_id`),
  CONSTRAINT `fk_exp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 8. Bank Details Table (Section E)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bank_details` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL UNIQUE,
  `account_holder_name` VARCHAR(150) NOT NULL,
  `bank_name` VARCHAR(150) NOT NULL,
  -- Encrypted at rest (AES-256-CBC)
  `account_number_encrypted` TEXT NOT NULL,
  `account_number_last4` VARCHAR(4) NOT NULL,
  `ifsc_code` VARCHAR(20) NOT NULL,
  `cheque_doc_id` INT UNSIGNED NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_bank_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 9. Emergency Contacts Table (Section F)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `emergency_contacts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL UNIQUE,
  `contact_name` VARCHAR(150) NOT NULL,
  `relationship` VARCHAR(50) NOT NULL,
  `mobile` VARCHAR(20) NOT NULL,
  `alternate_mobile` VARCHAR(20) NULL,
  `address` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_emg_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 10. Documents Table (Active Documents)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `documents` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `document_type` VARCHAR(50) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `stored_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(100) NOT NULL,
  `file_size_before` INT UNSIGNED NOT NULL,
  `file_size_after` INT UNSIGNED NOT NULL,
  `version` INT UNSIGNED NOT NULL DEFAULT 1,
  `status` ENUM('pending', 'approved', 'rejected', 'reupload_requested') NOT NULL DEFAULT 'pending',
  `rejection_reason` TEXT NULL,
  `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
  `archived_at` DATETIME NULL,
  `uploaded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_docs_user` (`user_id`),
  INDEX `idx_docs_type` (`document_type`),
  INDEX `idx_docs_status` (`status`),
  CONSTRAINT `fk_docs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 11. Document Versions Table (Version History)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `document_versions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `document_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `document_type` VARCHAR(50) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `stored_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(100) NOT NULL,
  `file_size` INT UNSIGNED NOT NULL,
  `version` INT UNSIGNED NOT NULL,
  `replaced_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `replaced_by` INT UNSIGNED NULL,
  INDEX `idx_doc_ver_doc` (`document_id`),
  INDEX `idx_doc_ver_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 12. Aptitude Test Categories
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `test_categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 13. Questions Bank
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `questions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NOT NULL,
  `question_text` TEXT NOT NULL,
  `explanation` TEXT NULL,
  `marks` INT UNSIGNED NOT NULL DEFAULT 1,
  `negative_marks` DECIMAL(3,2) NOT NULL DEFAULT 0.25,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_q_cat` (`category_id`),
  INDEX `idx_q_active` (`is_active`, `is_archived`),
  CONSTRAINT `fk_q_cat` FOREIGN KEY (`category_id`) REFERENCES `test_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 14. Question Options
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `options` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `question_id` INT UNSIGNED NOT NULL,
  `option_text` TEXT NOT NULL,
  `is_correct` TINYINT(1) NOT NULL DEFAULT 0,
  `option_order` INT NOT NULL DEFAULT 1,
  INDEX `idx_opt_q` (`question_id`),
  CONSTRAINT `fk_opt_q` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 15. Tests Configuration Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tests` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `total_questions` INT UNSIGNED NOT NULL DEFAULT 25,
  `time_limit_minutes` INT UNSIGNED NOT NULL DEFAULT 30,
  `passing_percentage` DECIMAL(5,2) NOT NULL DEFAULT 60.00,
  `negative_marking_per_question` DECIMAL(3,2) NOT NULL DEFAULT 0.25,
  `randomize_questions` TINYINT(1) NOT NULL DEFAULT 1,
  `randomize_options` TINYINT(1) NOT NULL DEFAULT 1,
  `test_order` ENUM('before_documents', 'after_documents') NOT NULL DEFAULT 'after_documents',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 16. Test Attempts Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `test_attempts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `test_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `started_at` DATETIME NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `completed_at` DATETIME NULL,
  `total_questions` INT UNSIGNED NOT NULL,
  `total_answered` INT UNSIGNED NOT NULL DEFAULT 0,
  `total_correct` INT UNSIGNED NOT NULL DEFAULT 0,
  `total_wrong` INT UNSIGNED NOT NULL DEFAULT 0,
  `score` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `passed` TINYINT(1) NOT NULL DEFAULT 0,
  `tab_switch_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `fullscreen_exit_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `status` ENUM('in_progress', 'completed', 'timed_out', 'cancelled') NOT NULL DEFAULT 'in_progress',
  `allow_reattempt` TINYINT(1) NOT NULL DEFAULT 0,
  `reattempt_allowed_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_ta_user` (`user_id`),
  INDEX `idx_ta_test` (`test_id`),
  CONSTRAINT `fk_ta_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ta_test` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 17. Attempt Answers Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `attempt_answers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `attempt_id` INT UNSIGNED NOT NULL,
  `question_id` INT UNSIGNED NOT NULL,
  `selected_option_id` INT UNSIGNED NULL,
  `is_marked_for_review` TINYINT(1) NOT NULL DEFAULT 0,
  `is_correct` TINYINT(1) NOT NULL DEFAULT 0,
  `marks_awarded` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_attempt_question` (`attempt_id`, `question_id`),
  CONSTRAINT `fk_aa_attempt` FOREIGN KEY (`attempt_id`) REFERENCES `test_attempts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_aa_q` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 18. Status History Table (Audit of Candidate Status)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `status_history` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `old_status` VARCHAR(50) NULL,
  `new_status` VARCHAR(50) NOT NULL,
  `comments` TEXT NULL,
  `changed_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_sh_user` (`user_id`),
  CONSTRAINT `fk_sh_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 19. Admin Comments Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_comments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `admin_id` INT UNSIGNED NOT NULL,
  `comment_type` ENUM('internal', 'public_to_candidate') NOT NULL DEFAULT 'public_to_candidate',
  `comment_text` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_ac_user` (`user_id`),
  CONSTRAINT `fk_ac_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ac_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 20. Notifications Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `type` VARCHAR(50) NOT NULL DEFAULT 'info',
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `link` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_notif_user` (`user_id`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 21. Audit Logs Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(100) NOT NULL,
  `entity_id` INT UNSIGNED NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `old_values` JSON NULL,
  `new_values` JSON NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_audit_user` (`user_id`),
  INDEX `idx_audit_action` (`action`),
  INDEX `idx_audit_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 22. Settings Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 23. Pages Content Table (CMS for Public Website)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pages_content` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `page_slug` VARCHAR(100) NOT NULL,
  `section_key` VARCHAR(100) NOT NULL,
  `title` VARCHAR(255) NULL,
  `subtitle` VARCHAR(255) NULL,
  `body_content` MEDIUMTEXT NULL,
  `image_url` VARCHAR(255) NULL,
  `extra_data` JSON NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_page_section` (`page_slug`, `section_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 24. Projects Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) NOT NULL UNIQUE,
  `category` ENUM('featured', 'luxury', 'new', 'intl') NOT NULL DEFAULT 'featured',
  `location` VARCHAR(150) NOT NULL,
  `price_range` VARCHAR(100) NOT NULL,
  `type_config` VARCHAR(100) NOT NULL,
  `description` MEDIUMTEXT NULL,
  `highlights` TEXT NULL,
  `cover_image` VARCHAR(255) NOT NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
  `order_num` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_proj_cat` (`category`),
  INDEX `idx_proj_published` (`is_published`, `is_archived`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 25. Project Images Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_images` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `project_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `caption` VARCHAR(255) NULL,
  `order_num` INT NOT NULL DEFAULT 0,
  INDEX `idx_pi_proj` (`project_id`),
  CONSTRAINT `fk_pi_proj` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 26. Blog Categories Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `blog_categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 27. Blog Posts Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `excerpt` TEXT NULL,
  `content` MEDIUMTEXT NOT NULL,
  `cover_image` VARCHAR(255) NULL,
  `author_name` VARCHAR(100) NOT NULL DEFAULT 'Realty Smartz',
  `reading_time_minutes` INT UNSIGNED NOT NULL DEFAULT 4,
  `meta_title` VARCHAR(255) NULL,
  `meta_description` TEXT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
  `published_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_bp_cat` (`category_id`),
  INDEX `idx_bp_published` (`is_published`, `is_archived`),
  CONSTRAINT `fk_bp_cat` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 28. Videos Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `videos` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `youtube_url` VARCHAR(255) NOT NULL,
  `youtube_id` VARCHAR(50) NOT NULL,
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `order_num` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 29. Enquiries Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `enquiries` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `subject` VARCHAR(200) NULL,
  `message` TEXT NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('new', 'contacted', 'closed') NOT NULL DEFAULT 'new',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 30. FAQs Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `faqs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `question` VARCHAR(255) NOT NULL,
  `answer` TEXT NOT NULL,
  `category` VARCHAR(50) NOT NULL DEFAULT 'general',
  `order_num` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 31. Migrations Table
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `migration_name` VARCHAR(255) NOT NULL UNIQUE,
  `executed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- DATABASE SAFETY TRIGGERS
-- Protects users and documents from accidental hard DELETE
-- ================================================================
DROP TRIGGER IF EXISTS `trg_prevent_user_delete`;
DELIMITER //
CREATE TRIGGER `trg_prevent_user_delete`
BEFORE DELETE ON `users`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Data Safety Policy: Hard DELETE is prohibited on users. Use is_archived = 1 instead.';
END //
DELIMITER ;

DROP TRIGGER IF EXISTS `trg_prevent_document_delete`;
DELIMITER //
CREATE TRIGGER `trg_prevent_document_delete`
BEFORE DELETE ON `documents`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Data Safety Policy: Hard DELETE is prohibited on documents. Use is_archived = 1 instead.';
END //
DELIMITER ;

SET FOREIGN_KEY_CHECKS = 1;
