-- Migration: 003_add_joining_date_to_profiles.sql
-- Description: Adds joining_date column to profiles and users tables to support tracking and editing candidate joining dates.

ALTER TABLE `profiles` ADD COLUMN `joining_date` DATE NULL AFTER `declaration_date`;
ALTER TABLE `users` ADD COLUMN `joining_date` DATE NULL AFTER `whatsapp`;
