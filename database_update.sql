-- Database Update: Add 'call-request' to signal_type ENUM and call_type column
-- Run this if you already created the database with the old schema

-- Update signals table to include 'call-request' in signal_type
ALTER TABLE signals 
MODIFY signal_type ENUM('offer', 'answer', 'ice-candidate', 'call-request') NOT NULL;

-- Add call_type column if it doesn't exist
ALTER TABLE signals 
ADD COLUMN IF NOT EXISTS call_type ENUM('video', 'audio') DEFAULT 'video' AFTER signal_data;

-- Verify the updates
SHOW COLUMNS FROM signals;

