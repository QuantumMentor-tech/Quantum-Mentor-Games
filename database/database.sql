-- ============================================================
-- QMGames Store - Full Database Schema
-- Project:   Quantum Mentor Games Store (QMGames Store)
-- Database:  qmgames_store
-- Engine:    InnoDB
-- Charset:   utf8mb4
-- Collation: utf8mb4_unicode_ci
-- Step:      2 — Database Planning and MySQL Setup
--
-- LEGAL NOTICE:
--   This database is designed exclusively for legal, authorized,
--   official, freeware, open-source, demo, or permission-based
--   game distribution. No fields, values, or structures support
--   piracy, cracked games, DRM bypassing, keygens, malware,
--   illegal torrents, or unauthorized game distribution.
--
-- HOW TO IMPORT:
--   1. Open XAMPP Control Panel — start Apache and MySQL.
--   2. Open phpMyAdmin: http://localhost/phpmyadmin
--   3. Click "Import" tab in the top navigation.
--   4. Choose this file: database/database.sql
--   5. Click "Go". The database and all tables will be created.
--   6. Then import database/seed.sql for sample data.
--
-- TABLE CREATION ORDER (respects foreign key dependencies):
--   1.  admins
--   2.  categories
--   3.  tags
--   4.  users
--   5.  games
--   6.  game_categories
--   7.  game_requirements
--   8.  game_screenshots
--   9.  download_links
--   10. download_reports
--   11. contact_messages
--   12. site_settings
--   13. game_tags
--   14. user_library
--   15. orders
--   16. order_items
-- ============================================================

-- ============================================================
-- Safety: Drop and recreate database (local development only)
-- WARNING: Remove or comment out DROP DATABASE for production!
-- ============================================================
DROP DATABASE IF EXISTS `qmgames_store`;
CREATE DATABASE `qmgames_store`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `qmgames_store`;

-- Disable foreign key checks during table creation
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

-- ============================================================
-- TABLE 1: admins
-- Purpose: Stores administrator accounts for the admin panel.
--          Role-based access: super_admin > admin > editor.
-- Security: Passwords are NEVER stored as plain text.
--           PHP password_hash() (PASSWORD_BCRYPT) must be used.
-- ============================================================
CREATE TABLE `admins` (
  `id`              INT          NOT NULL AUTO_INCREMENT,
  `name`            VARCHAR(100) NOT NULL                   COMMENT 'Display name of the admin',
  `email`           VARCHAR(150) NOT NULL                   COMMENT 'Login email — must be unique',
  `password_hash`   VARCHAR(255) NOT NULL                   COMMENT 'BCrypt hash via password_hash(). NEVER plain text.',
  `role`            ENUM('super_admin','admin','editor')
                    NOT NULL DEFAULT 'admin'                COMMENT 'Access level: super_admin has full access',
  `status`          ENUM('active','inactive')
                    NOT NULL DEFAULT 'active'               COMMENT 'Inactive admins cannot log in',
  `last_login_at`   DATETIME     NULL DEFAULT NULL          COMMENT 'Timestamp of last successful login',
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admins_email`  (`email`),
  KEY        `idx_admins_status` (`status`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Admin panel user accounts with role-based access';


-- ============================================================
-- TABLE 2: categories
-- Purpose: Game categories / genres used for browsing and filtering.
--          Examples: Action, RPG, Low-End PC Games, Open Source Games.
-- ============================================================
CREATE TABLE `categories` (
  `id`          INT          NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(120) NOT NULL                   COMMENT 'Display name, e.g. "Action"',
  `slug`        VARCHAR(150) NOT NULL                   COMMENT 'URL-safe slug, e.g. "action"',
  `description` TEXT         NULL                       COMMENT 'Optional category description',
  `icon`        VARCHAR(255) NULL                       COMMENT 'Icon filename or CSS class',
  `sort_order`  INT          NOT NULL DEFAULT 0         COMMENT 'Display order (ascending)',
  `status`      ENUM('active','inactive')
                NOT NULL DEFAULT 'active'               COMMENT 'Inactive categories are hidden from public',
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug`        (`slug`),
  KEY        `idx_categories_status`     (`status`),
  KEY        `idx_categories_sort_order` (`sort_order`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Game categories and genres for browsing and filtering';


-- ============================================================
-- TABLE 3: tags
-- Purpose: Flexible tags for enhanced search and filtering.
--          Examples: Windows, Offline, Low-End PC, Controller Supported.
-- ============================================================
CREATE TABLE `tags` (
  `id`         INT          NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100) NOT NULL                   COMMENT 'Tag display name, e.g. "Offline"',
  `slug`       VARCHAR(120) NOT NULL                   COMMENT 'URL-safe slug, e.g. "offline"',
  `status`     ENUM('active','inactive')
               NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tags_slug`   (`slug`),
  KEY        `idx_tags_status` (`status`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Tags for flexible game filtering and search';


-- ============================================================
-- TABLE 4: users
-- Purpose: Future public user account system.
--          Table is created now but frontend features are NOT
--          activated until a future step. Do not expose
--          registration/login UI yet.
-- ============================================================
CREATE TABLE `users` (
  `id`                  INT          NOT NULL AUTO_INCREMENT,
  `name`                VARCHAR(120) NOT NULL,
  `email`               VARCHAR(150) NOT NULL                   COMMENT 'Unique login email',
  `password_hash`       VARCHAR(255) NOT NULL                   COMMENT 'BCrypt hash. NEVER plain text.',
  `avatar`              VARCHAR(255) NULL DEFAULT NULL          COMMENT 'Profile picture path',
  `status`              ENUM('active','inactive','banned')
                        NOT NULL DEFAULT 'active',
  `email_verified_at`   DATETIME     NULL DEFAULT NULL          COMMENT 'NULL = email not yet verified',
  `created_at`          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email`   (`email`),
  KEY        `idx_users_status` (`status`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Future public user accounts — not activated yet';


-- ============================================================
-- TABLE 5: games
-- Purpose: Core games table. Stores all game information
--          including metadata, legal license type, and SEO fields.
--
-- LICENSE TYPES (all legal and authorized only):
--   freeware          — Free to download legally (developer-released)
--   open_source       — Source code publicly available (GPL, MIT, etc.)
--   demo              — Official limited demo from developer/publisher
--   official_mirror   — Authorized mirror of an official free release
--   indie_permission  — Indie developer granted explicit download permission
--   paid_future       — Reserved for future paid game support
--   other_authorized  — Any other verifiably authorized distribution
--
-- IMPORTANT: No cracked, pirated, DRM-bypass, or illegal values exist.
-- ============================================================
CREATE TABLE `games` (
  `id`                INT          NOT NULL AUTO_INCREMENT,
  `title`             VARCHAR(180) NOT NULL                   COMMENT 'Game title',
  `slug`              VARCHAR(220) NOT NULL                   COMMENT 'Unique URL slug',
  `short_description` VARCHAR(350) NULL                       COMMENT 'Brief description for cards/listings',
  `full_description`  LONGTEXT     NULL                       COMMENT 'Full game description with HTML allowed',
  `cover_image`       VARCHAR(255) NULL                       COMMENT 'Relative path to cover image (16:9)',
  `banner_image`      VARCHAR(255) NULL                       COMMENT 'Relative path to banner image (wide)',
  `trailer_url`       VARCHAR(500) NULL                       COMMENT 'YouTube or official trailer embed URL',
  `developer`         VARCHAR(150) NULL                       COMMENT 'Developer name',
  `publisher`         VARCHAR(150) NULL                       COMMENT 'Publisher name (may equal developer)',
  `version`           VARCHAR(80)  NULL                       COMMENT 'Current version string, e.g. "1.4.2"',
  `release_date`      DATE         NULL                       COMMENT 'Original game release date',
  `game_size`         VARCHAR(80)  NULL                       COMMENT 'Download size string, e.g. "2.4 GB"',
  `platform`          VARCHAR(100) NOT NULL DEFAULT 'Windows PC'
                                                              COMMENT 'Target platform(s)',
  `license_type`      ENUM(
                        'freeware',
                        'open_source',
                        'demo',
                        'official_mirror',
                        'indie_permission',
                        'paid_future',
                        'other_authorized'
                      ) NOT NULL DEFAULT 'freeware'           COMMENT 'Legal distribution license — authorized only',
  `status`            ENUM('draft','active','inactive','archived')
                      NOT NULL DEFAULT 'draft'                COMMENT 'draft=admin only, active=public visible',
  `is_featured`       TINYINT(1)   NOT NULL DEFAULT 0         COMMENT '1 = shown in Featured section',
  `is_trending`       TINYINT(1)   NOT NULL DEFAULT 0         COMMENT '1 = shown in Trending section',
  `is_low_end_pc`     TINYINT(1)   NOT NULL DEFAULT 0         COMMENT '1 = tagged as Low-End PC friendly',
  `views_count`       INT          NOT NULL DEFAULT 0         COMMENT 'Page view counter',
  `downloads_count`   INT          NOT NULL DEFAULT 0         COMMENT 'Total authorized download click count',
  `meta_title`        VARCHAR(255) NULL                       COMMENT 'SEO: custom <title> tag override',
  `meta_description`  VARCHAR(350) NULL                       COMMENT 'SEO: meta description override',
  `created_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_games_slug`             (`slug`),
  KEY        `idx_games_title`           (`title`),
  KEY        `idx_games_status`          (`status`),
  KEY        `idx_games_license_type`    (`license_type`),
  KEY        `idx_games_is_featured`     (`is_featured`),
  KEY        `idx_games_is_trending`     (`is_trending`),
  KEY        `idx_games_is_low_end_pc`   (`is_low_end_pc`),
  KEY        `idx_games_views_count`     (`views_count`),
  KEY        `idx_games_downloads_count` (`downloads_count`),
  KEY        `idx_games_created_at`      (`created_at`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Main games table — legal authorized games only';


-- ============================================================
-- TABLE 6: game_categories
-- Purpose: Many-to-many pivot between games and categories.
--          A game can belong to multiple categories.
-- ============================================================
CREATE TABLE `game_categories` (
  `id`          INT       NOT NULL AUTO_INCREMENT,
  `game_id`     INT       NOT NULL COMMENT 'FK → games.id',
  `category_id` INT       NOT NULL COMMENT 'FK → categories.id',
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_game_category`          (`game_id`, `category_id`),
  KEY        `idx_game_categories_game`  (`game_id`),
  KEY        `idx_game_categories_cat`   (`category_id`),
  CONSTRAINT `fk_gc_game`
    FOREIGN KEY (`game_id`)
    REFERENCES `games` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_gc_category`
    FOREIGN KEY (`category_id`)
    REFERENCES `categories` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Many-to-many: games ↔ categories';


-- ============================================================
-- TABLE 7: game_requirements
-- Purpose: Stores minimum and recommended system requirements.
--          One record per game (1:1 relationship).
-- ============================================================
CREATE TABLE `game_requirements` (
  `id`                    INT          NOT NULL AUTO_INCREMENT,
  `game_id`               INT          NOT NULL COMMENT 'FK → games.id (unique: one row per game)',
  `minimum_os`            VARCHAR(150) NULL     COMMENT 'e.g. "Windows 7 64-bit"',
  `minimum_processor`     VARCHAR(150) NULL     COMMENT 'e.g. "Intel Core i3-2100"',
  `minimum_ram`           VARCHAR(100) NULL     COMMENT 'e.g. "4 GB RAM"',
  `minimum_gpu`           VARCHAR(150) NULL     COMMENT 'e.g. "NVIDIA GTX 660"',
  `minimum_storage`       VARCHAR(100) NULL     COMMENT 'e.g. "8 GB available space"',
  `recommended_os`        VARCHAR(150) NULL     COMMENT 'e.g. "Windows 10 64-bit"',
  `recommended_processor` VARCHAR(150) NULL     COMMENT 'e.g. "Intel Core i7-8700K"',
  `recommended_ram`       VARCHAR(100) NULL     COMMENT 'e.g. "16 GB RAM"',
  `recommended_gpu`       VARCHAR(150) NULL     COMMENT 'e.g. "NVIDIA RTX 2060"',
  `recommended_storage`   VARCHAR(100) NULL     COMMENT 'e.g. "8 GB SSD"',
  `created_at`            TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_requirements_game` (`game_id`),
  CONSTRAINT `fk_req_game`
    FOREIGN KEY (`game_id`)
    REFERENCES `games` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Minimum and recommended system requirements per game';


-- ============================================================
-- TABLE 8: game_screenshots
-- Purpose: Stores screenshot images for each game.
--          Multiple screenshots per game (1:many).
-- ============================================================
CREATE TABLE `game_screenshots` (
  `id`         INT          NOT NULL AUTO_INCREMENT,
  `game_id`    INT          NOT NULL COMMENT 'FK → games.id',
  `image_path` VARCHAR(255) NOT NULL COMMENT 'Relative path to screenshot file',
  `alt_text`   VARCHAR(180) NULL     COMMENT 'Accessibility alt text for the screenshot',
  `sort_order` INT          NOT NULL DEFAULT 0 COMMENT 'Display order (ascending)',
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_screenshots_game`  (`game_id`),
  KEY `idx_screenshots_order` (`sort_order`),
  CONSTRAINT `fk_ss_game`
    FOREIGN KEY (`game_id`)
    REFERENCES `games` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Screenshot images per game';


-- ============================================================
-- TABLE 9: download_links
-- Purpose: Stores authorized download links for each game.
--
-- LINK TYPES (authorized sources only):
--   cloud          — e.g. Google Drive, OneDrive, Mega (authorized)
--   torrent        — Official or developer-authorized torrent only
--   official       — Direct link from developer/publisher website
--   mirror         — Authorized mirror of official release
--   developer_site — Developer's own website download page
--   store_link     — Legal free store (itch.io, Epic free tier, etc.)
--
-- LEGAL REQUIREMENT: Every URL added here MUST be verified as
-- a legal and authorized source. No piracy links ever.
-- ============================================================
CREATE TABLE `download_links` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `game_id`       INT          NOT NULL COMMENT 'FK → games.id',
  `link_title`    VARCHAR(180) NOT NULL COMMENT 'Display title, e.g. "Download via Google Drive"',
  `provider_name` VARCHAR(120) NOT NULL COMMENT 'Provider, e.g. "Google Drive", "itch.io"',
  `download_url`  TEXT         NOT NULL COMMENT 'LEGAL AUTHORIZED URL ONLY — verified before insertion',
  `link_type`     ENUM(
                    'cloud',
                    'torrent',
                    'official',
                    'mirror',
                    'developer_site',
                    'store_link'
                  ) NOT NULL DEFAULT 'cloud'                  COMMENT 'Type of authorized download source',
  `file_size`     VARCHAR(80)  NULL                           COMMENT 'File size string, e.g. "1.8 GB"',
  `status`        ENUM('active','inactive','broken','under_review')
                  NOT NULL DEFAULT 'active'                   COMMENT 'under_review = flagged, awaiting admin check',
  `clicks_count`  INT          NOT NULL DEFAULT 0             COMMENT 'Total clicks on this download link',
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dl_game`         (`game_id`),
  KEY `idx_dl_link_type`    (`link_type`),
  KEY `idx_dl_status`       (`status`),
  KEY `idx_dl_clicks_count` (`clicks_count`),
  CONSTRAINT `fk_dl_game`
    FOREIGN KEY (`game_id`)
    REFERENCES `games` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Authorized download links per game — legal sources only';


-- ============================================================
-- TABLE 10: download_reports
-- Purpose: Stores user-submitted reports about download issues.
--          Admins review and resolve reports in the admin panel.
-- ============================================================
CREATE TABLE `download_reports` (
  `id`               INT          NOT NULL AUTO_INCREMENT,
  `game_id`          INT          NOT NULL COMMENT 'FK → games.id',
  `download_link_id` INT          NULL     COMMENT 'FK → download_links.id (nullable: link may be deleted)',
  `report_type`      ENUM(
                       'broken_link',
                       'wrong_file',
                       'slow_download',
                       'password_issue',
                       'unsafe_file_concern',
                       'other'
                     ) NOT NULL DEFAULT 'broken_link'         COMMENT 'Category of the reported issue',
  `message`          TEXT         NULL                        COMMENT 'Optional user-submitted description',
  `user_email`       VARCHAR(150) NULL                        COMMENT 'Optional reporter email for follow-up',
  `status`           ENUM('pending','reviewed','fixed','ignored')
                     NOT NULL DEFAULT 'pending'               COMMENT 'Admin review status',
  `created_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rpt_game`        (`game_id`),
  KEY `idx_rpt_link`        (`download_link_id`),
  KEY `idx_rpt_status`      (`status`),
  KEY `idx_rpt_type`        (`report_type`),
  KEY `idx_rpt_created_at`  (`created_at`),
  CONSTRAINT `fk_rpt_game`
    FOREIGN KEY (`game_id`)
    REFERENCES `games` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rpt_link`
    FOREIGN KEY (`download_link_id`)
    REFERENCES `download_links` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='User-submitted reports about download link issues';


-- ============================================================
-- TABLE 11: contact_messages
-- Purpose: Stores messages submitted via the public contact form.
-- ============================================================
CREATE TABLE `contact_messages` (
  `id`         INT          NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120) NOT NULL COMMENT 'Sender full name',
  `email`      VARCHAR(150) NOT NULL COMMENT 'Sender email address',
  `subject`    VARCHAR(200) NOT NULL COMMENT 'Message subject',
  `message`    TEXT         NOT NULL COMMENT 'Full message body',
  `status`     ENUM('new','read','replied','archived')
               NOT NULL DEFAULT 'new'                         COMMENT 'Admin handling status',
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cm_email`      (`email`),
  KEY `idx_cm_status`     (`status`),
  KEY `idx_cm_created_at` (`created_at`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Public contact form submissions';


-- ============================================================
-- TABLE 12: site_settings
-- Purpose: Key-value store for editable site configuration.
--          Managed via admin panel settings page.
-- ============================================================
CREATE TABLE `site_settings` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `setting_key`   VARCHAR(120) NOT NULL COMMENT 'Unique setting identifier, e.g. "site_name"',
  `setting_value` TEXT         NULL     COMMENT 'Setting value (text, number, on/off, etc.)',
  `setting_group` VARCHAR(80)  NOT NULL DEFAULT 'general' COMMENT 'Group name for UI grouping',
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_settings_key`       (`setting_key`),
  KEY        `idx_settings_group`    (`setting_group`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Editable site settings (key-value store)';


-- ============================================================
-- TABLE 13: game_tags
-- Purpose: Many-to-many pivot between games and tags.
-- ============================================================
CREATE TABLE `game_tags` (
  `id`         INT       NOT NULL AUTO_INCREMENT,
  `game_id`    INT       NOT NULL COMMENT 'FK → games.id',
  `tag_id`     INT       NOT NULL COMMENT 'FK → tags.id',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_game_tag`        (`game_id`, `tag_id`),
  KEY        `idx_gt_game`        (`game_id`),
  KEY        `idx_gt_tag`         (`tag_id`),
  CONSTRAINT `fk_gt_game`
    FOREIGN KEY (`game_id`)
    REFERENCES `games` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_gt_tag`
    FOREIGN KEY (`tag_id`)
    REFERENCES `tags` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Many-to-many: games ↔ tags';


-- ============================================================
-- TABLE 14: user_library
-- Purpose: Links users to games they have accessed (free/purchased).
--          Part of the future user account system — not activated yet.
-- ============================================================
CREATE TABLE `user_library` (
  `id`          INT       NOT NULL AUTO_INCREMENT,
  `user_id`     INT       NOT NULL COMMENT 'FK → users.id',
  `game_id`     INT       NOT NULL COMMENT 'FK → games.id',
  `access_type` ENUM('free','purchased','gifted','demo')
                NOT NULL DEFAULT 'free'                       COMMENT 'How access was granted',
  `added_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_game`         (`user_id`, `game_id`),
  KEY        `idx_ul_user`          (`user_id`),
  KEY        `idx_ul_game`          (`game_id`),
  CONSTRAINT `fk_ul_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ul_game`
    FOREIGN KEY (`game_id`)
    REFERENCES `games` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='User game library — future feature, not activated yet';


-- ============================================================
-- TABLE 15: orders
-- Purpose: Future payment/order system.
--          Table created now, payment features not activated yet.
-- ============================================================
CREATE TABLE `orders` (
  `id`             INT             NOT NULL AUTO_INCREMENT,
  `user_id`        INT             NOT NULL COMMENT 'FK → users.id',
  `order_number`   VARCHAR(80)     NOT NULL COMMENT 'Unique human-readable order ID',
  `total_amount`   DECIMAL(10, 2)  NOT NULL DEFAULT 0.00,
  `currency`       VARCHAR(10)     NOT NULL DEFAULT 'USD',
  `payment_status` ENUM('pending','paid','failed','refunded','cancelled')
                   NOT NULL DEFAULT 'pending',
  `order_status`   ENUM('pending','completed','cancelled','refunded')
                   NOT NULL DEFAULT 'pending',
  `created_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orders_number`          (`order_number`),
  KEY        `idx_orders_user`           (`user_id`),
  KEY        `idx_orders_payment_status` (`payment_status`),
  KEY        `idx_orders_order_status`   (`order_status`),
  CONSTRAINT `fk_ord_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Future order/payment system — not activated yet';


-- ============================================================
-- TABLE 16: order_items
-- Purpose: Line items for each order.
--          Part of future payment system — not activated yet.
-- ============================================================
CREATE TABLE `order_items` (
  `id`         INT            NOT NULL AUTO_INCREMENT,
  `order_id`   INT            NOT NULL COMMENT 'FK → orders.id',
  `game_id`    INT            NOT NULL COMMENT 'FK → games.id',
  `price`      DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Price at time of purchase',
  `created_at` TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_oi_order` (`order_id`),
  KEY `idx_oi_game`  (`game_id`),
  CONSTRAINT `fk_oi_order`
    FOREIGN KEY (`order_id`)
    REFERENCES `orders` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_oi_game`
    FOREIGN KEY (`game_id`)
    REFERENCES `games` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Order line items — future payment feature, not activated yet';


-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- Schema creation complete.
-- Total tables: 16
-- Next: Import database/seed.sql for sample data.
-- ============================================================
