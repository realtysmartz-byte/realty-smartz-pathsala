-- 004_add_candidate_type_and_source_to_users.sql
-- Supports separate identification for onboarding candidates and aptitude-only test candidates

SET @dbname = DATABASE();
SET @tablename = "users";
SET @columnname = "candidate_type";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE `users` ADD COLUMN `candidate_type` ENUM('onboarding', 'aptitude_only') NOT NULL DEFAULT 'onboarding' AFTER `role`"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @columnname2 = "source";
SET @preparedStatement2 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname2)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE `users` ADD COLUMN `source` VARCHAR(50) NOT NULL DEFAULT 'direct' AFTER `candidate_type`"
));
PREPARE alterIfNotExists2 FROM @preparedStatement2;
EXECUTE alterIfNotExists2;
DEALLOCATE PREPARE alterIfNotExists2;
