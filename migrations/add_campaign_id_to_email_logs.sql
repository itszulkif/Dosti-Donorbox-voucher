-- Add campaign_id column to email_logs table if it doesn't exist
ALTER TABLE email_logs ADD COLUMN campaign_id INT NULL DEFAULT NULL AFTER email_type;
