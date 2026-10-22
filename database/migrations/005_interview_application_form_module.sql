-- Migration: 005_interview_application_form_module.sql
-- Description: Interview Application Form Module tables (applications, employment rows, field versions, email logs, QR settings, and automation settings)

-- 1. Create interview_applications table
CREATE TABLE IF NOT EXISTS `interview_applications` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `hr_name` VARCHAR(100) NOT NULL,
    `post_applied` VARCHAR(150) NOT NULL,
    `full_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `mobile` VARCHAR(20) NOT NULL,
    `experience_type` ENUM('experience', 'fresher') NOT NULL DEFAULT 'fresher',
    `years_in_real_estate` DECIMAL(4,1) NULL,
    `vehicle_available` ENUM('yes', 'no') NOT NULL DEFAULT 'no',
    `current_location` VARCHAR(255) NOT NULL,
    `higher_education` VARCHAR(255) NOT NULL,
    `has_references` ENUM('yes', 'no') NOT NULL DEFAULT 'no',
    `reference_details` TEXT NULL,
    `last_company_hr_tl` VARCHAR(255) NULL,
    `reason_to_leave` TEXT NULL,
    `immediate_joiner` ENUM('yes', 'no') NOT NULL DEFAULT 'yes',
    `consent_at` DATETIME NOT NULL,
    `submitted_at` DATETIME NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    
    -- HR Department Editable Section
    `source_by` VARCHAR(150) NULL,
    `feedback_tl_sm` TEXT NULL,
    `feedback_hr` TEXT NULL,
    `feedback_final` TEXT NULL,
    `module_discussed` TEXT NULL,
    `position_offered` VARCHAR(150) NULL,
    `decision` ENUM('pending', 'selected', 'rejected') NOT NULL DEFAULT 'pending',
    `decision_at` DATETIME NULL,
    `decision_by` INT UNSIGNED NULL,
    `candidate_interested` ENUM('yes', 'no') NULL,
    `offered_salary` DECIMAL(12,2) NULL,
    `joining_date` DATE NULL,
    `rejection_reason` TEXT NULL,
    
    -- Versioning, audit & status flags
    `version` INT UNSIGNED NOT NULL DEFAULT 1,
    `first_viewed_at` DATETIME NULL,
    `first_viewed_by` INT UNSIGNED NULL,
    `updated_by` INT UNSIGNED NULL,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
    `archived_at` DATETIME NULL,
    
    INDEX `idx_ia_decision` (`decision`),
    INDEX `idx_ia_submitted_at` (`submitted_at`),
    INDEX `idx_ia_updated_at` (`updated_at`),
    INDEX `idx_ia_mobile` (`mobile`),
    INDEX `idx_ia_email` (`email`),
    INDEX `idx_ia_first_viewed` (`first_viewed_at`),
    INDEX `idx_ia_archived` (`is_archived`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create interview_employment_rows table
CREATE TABLE IF NOT EXISTS `interview_employment_rows` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `application_id` BIGINT UNSIGNED NOT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `organisation` VARCHAR(255) NULL,
    `designation` VARCHAR(150) NULL,
    `salary` VARCHAR(100) NULL,
    `hr_name_number` VARCHAR(255) NULL,
    `duration` VARCHAR(100) NULL,
    INDEX `idx_ier_app_id` (`application_id`),
    CONSTRAINT `fk_ier_app_id` FOREIGN KEY (`application_id`) REFERENCES `interview_applications`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create interview_field_versions table (for concurrency merge & audit history)
CREATE TABLE IF NOT EXISTS `interview_field_versions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `application_id` BIGINT UNSIGNED NOT NULL,
    `field` VARCHAR(64) NOT NULL,
    `value` LONGTEXT NULL,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_by` INT UNSIGNED NULL,
    INDEX `idx_ifv_app_field` (`application_id`, `field`),
    INDEX `idx_ifv_updated_at` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create interview_email_log table
CREATE TABLE IF NOT EXISTS `interview_email_log` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `application_id` BIGINT UNSIGNED NOT NULL,
    `type` VARCHAR(64) NOT NULL,
    `to_email` VARCHAR(150) NOT NULL,
    `status` ENUM('pending', 'sent', 'failed') NOT NULL DEFAULT 'pending',
    `error` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `sent_at` DATETIME NULL,
    INDEX `idx_iel_app_type` (`application_id`, `type`),
    INDEX `idx_iel_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Create qr_settings table
CREATE TABLE IF NOT EXISTS `qr_settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(64) NOT NULL UNIQUE,
    `slug` VARCHAR(64) NOT NULL UNIQUE,
    `accepting_responses` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_qrs_type` (`type`),
    INDEX `idx_qrs_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Insert initial QR slug for Interview Form if not present (at least 16 chars)
INSERT INTO `qr_settings` (`type`, `slug`, `accepting_responses`, `created_at`)
VALUES ('interview_form', 'rsp_interview_7f9c2d1b4e8a', 1, NOW())
ON DUPLICATE KEY UPDATE `type` = VALUES(`type`);

-- 7. Seed default Interview Automation Settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `updated_at`)
VALUES 
('interview_email_hr_new_form', '1', 'interview', NOW()),
('interview_email_hr_recipients', 'hr@realtysmartzpathshala.in', 'interview', NOW()),
('interview_email_candidate_selected', '0', 'interview', NOW()),
('interview_email_selected_subject', 'Offer of Employment | Realty Smartz Pathshala', 'interview', NOW()),
('interview_email_selected_body', 'Dear {name},\n\nWe are pleased to inform you that following your interview, you have been selected for the position of {position} at Realty Smartz Pathshala.\n\nYour tentative date of joining is {joining_date}.\n\nOur HR department will contact you shortly with further details and joining documentation.\n\nWarm regards,\nHR Department\nRealty Smartz Pathshala', 'interview', NOW()),
('interview_email_candidate_rejected', '0', 'interview', NOW()),
('interview_email_rejected_subject', 'Application Update | Realty Smartz Pathshala', 'interview', NOW()),
('interview_email_rejected_body', 'Dear {name},\n\nThank you for taking the time to attend the interview with Realty Smartz Pathshala.\n\nWhile your background and qualifications are commendable, we have decided to move forward with other candidates whose profiles more closely match our current operational requirements.\n\nWe genuinely appreciate your interest in our organization and wish you every success in your future career endeavors.\n\nWarm regards,\nHR Department\nRealty Smartz Pathshala', 'interview', NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();
