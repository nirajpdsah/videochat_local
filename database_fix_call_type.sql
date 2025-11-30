-- Fix: Add missing call_type column to signals table
-- Run this in Railway MySQL console

-- Check if column exists first (optional, but helpful)
-- If you get an error that column already exists, that's fine - just means it's already there

ALTER TABLE signals 
ADD COLUMN call_type ENUM('video', 'audio') DEFAULT 'video' AFTER signal_data;

-- Verify it was added
SHOW COLUMNS FROM signals;

