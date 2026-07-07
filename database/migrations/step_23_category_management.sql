-- ============================================================
-- QMGames Store — Step 23 Migration: Category Management
-- Database: qmgames_store
--
-- Changes:
--   1. Add 'archived' to categories.status ENUM
--   2. Ensure icon column has correct length (255 → 80 not needed,
--      existing 255 is fine; no change)
--
-- HOW TO IMPORT:
--   1. Open phpMyAdmin → select qmgames_store.
--   2. Click Import → choose this file → Go.
--   3. Confirm categories table now allows status = 'archived'.
--
-- SAFETY: Uses MODIFY COLUMN — does NOT drop or delete any data.
-- Existing rows with status 'active' or 'inactive' are unaffected.
-- ============================================================

USE `qmgames_store`;

-- Add 'archived' option to categories.status ENUM safely.
-- This extends the existing ENUM without removing old values.
ALTER TABLE `categories`
  MODIFY COLUMN `status`
    ENUM('active','inactive','archived')
    NOT NULL
    DEFAULT 'active'
    COMMENT 'active=public, inactive=hidden, archived=soft-deleted';

-- Confirm the change.
-- Run: DESCRIBE categories; and verify status shows 'active','inactive','archived'
