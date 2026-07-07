-- ============================================================
-- QMGames Store — Step 11 Migration: Download Events Analytics
-- Database:  qmgames_store
-- Run this AFTER importing database/database.sql (which has the
-- base 16-table schema).
--
-- HOW TO IMPORT:
--   1. Open phpMyAdmin → select qmgames_store database.
--   2. Click Import → choose this file → Go.
--   3. Confirm download_events table appears in the table list.
--
-- PRIVACY NOTE:
--   This table stores HASHED identifiers only.
--   Raw IP addresses and user agents are never stored.
-- ============================================================

USE `qmgames_store`;

-- ============================================================
-- TABLE: download_events
-- Purpose: Lightweight analytics log for successful download
--          redirects. Used to detect abuse patterns and
--          provide future admin analytics.
-- Privacy: Only sha256-hashed identifiers stored — no raw IP.
-- ============================================================
CREATE TABLE IF NOT EXISTS `download_events` (
  `id`               INT          NOT NULL AUTO_INCREMENT,
  `game_id`          INT          NOT NULL   COMMENT 'FK → games.id',
  `download_link_id` INT          NOT NULL   COMMENT 'FK → download_links.id',
  `event_type`       ENUM('download_redirect')
                     NOT NULL DEFAULT 'download_redirect',
  `ip_hash`          VARCHAR(128) NULL       COMMENT 'sha256(ip + APP_SALT) — no raw IP stored',
  `user_agent_hash`  VARCHAR(128) NULL       COMMENT 'sha256(ua + APP_SALT) — no raw UA stored',
  `created_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_de_game`       (`game_id`),
  KEY `idx_de_link`       (`download_link_id`),
  KEY `idx_de_created_at` (`created_at`),
  CONSTRAINT `fk_de_game`
    FOREIGN KEY (`game_id`)
    REFERENCES `games` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_de_link`
    FOREIGN KEY (`download_link_id`)
    REFERENCES `download_links` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Privacy-safe download event log — hashed identifiers only';

-- Migration complete.
-- Reminder: Change APP_SALT in includes/config.php before production.
