-- ============================================================
-- QMGames Store — Step 19 Migration: First Admin Account
-- Database: qmgames_store
--
-- !! IMPORTANT SECURITY INSTRUCTIONS !!
--
-- Step 1: Generate a secure password hash using PHP.
--         Run this in a local PHP file or XAMPP console:
--
--           echo password_hash('Admin@12345', PASSWORD_DEFAULT);
--
--         Copy the output (starts with $2y$...) into this file
--         where it says PASTE_GENERATED_HASH_HERE below.
--
-- Step 2: Import this file in phpMyAdmin AFTER generating the hash.
--
-- Step 3: Log in and IMMEDIATELY change the password via admin
--         settings before sharing the project or going live.
--
-- Default credentials (CHANGE BEFORE PRODUCTION):
--   Email   : admin@qmgames.local
--   Password: Admin@12345
--
-- !! NEVER USE DEFAULT CREDENTIALS IN PRODUCTION !!
-- ============================================================

USE `qmgames_store`;

-- Only insert if an admin with this email does not already exist
INSERT INTO `admins`
  (`name`, `email`, `password_hash`, `role`, `status`)
SELECT
  'Super Admin',
  'admin@qmgames.local',
  'PASTE_GENERATED_HASH_HERE',
  'super_admin',
  'active'
WHERE NOT EXISTS (
  SELECT 1 FROM `admins` WHERE `email` = 'admin@qmgames.local'
);

-- ============================================================
-- How to generate a hash for Admin@12345 using PHP:
-- ============================================================
-- Create a temporary file: tools/create_hash.php
-- Content:
--   <?php echo password_hash('Admin@12345', PASSWORD_DEFAULT); ?>
-- Open: http://localhost/quantum-mentor-games-store/tools/create_hash.php
-- Copy the output, paste it above, then DELETE create_hash.php
-- ============================================================
