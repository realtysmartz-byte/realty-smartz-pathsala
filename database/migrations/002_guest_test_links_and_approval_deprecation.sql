-- Migration: 002_guest_test_links_and_approval_deprecation.sql
-- Description: Creates guest_test_links table, adds guest link references, and deprecates approval workflow fields while preserving all columns and data.

-- 1. Create guest_test_links table if not exists
CREATE TABLE IF NOT EXISTS `guest_test_links` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `token_hash` VARCHAR(64) NOT NULL UNIQUE,
    `user_id` INT NULL,
    `guest_name` VARCHAR(150) NULL,
    `guest_mobile` VARCHAR(20) NULL,
    `guest_email` VARCHAR(150) NULL,
    `set_id` INT NULL,
    `expires_at` DATETIME NOT NULL,
    `max_attempts` INT NOT NULL DEFAULT 1,
    `attempts_used` INT NOT NULL DEFAULT 0,
    `status` ENUM('active', 'in_progress', 'submitted', 'expired', 'revoked') NOT NULL DEFAULT 'active',
    `created_by` INT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `first_used_at` DATETIME NULL,
    `submitted_at` DATETIME NULL,
    `revoked_at` DATETIME NULL,
    INDEX `idx_guest_token` (`token_hash`),
    INDEX `idx_guest_user` (`user_id`),
    INDEX `idx_guest_status` (`status`),
    INDEX `idx_guest_email` (`guest_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Deprecation notice for approval fields:
-- The approval workflow is removed from application logic.
-- The existing columns `users.status` (containing legacy 'under_review', 'approved', 'rejected', 'changes_requested'),
-- `profiles.rejection_reason`, `documents.status` ('approved', 'rejected') are intentionally KEPT intact.
-- No columns or rows are dropped. Candidate status is now derived dynamically from real progress data:
-- - Registered
-- - Email verified
-- - Onboarding submitted (all required documents uploaded and profile completed)
-- - Test completed
