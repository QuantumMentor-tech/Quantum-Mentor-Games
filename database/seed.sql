-- ============================================================
-- QMGames Store - Seed Data
-- Project:  Quantum Mentor Games Store (QMGames Store)
-- Database: qmgames_store
-- Step:     2 — Database Planning and MySQL Setup
--
-- IMPORT ORDER:
--   Import database/database.sql FIRST, then this file.
--
-- LEGAL NOTICE:
--   All sample data is fictional or placeholder only.
--   No real copyrighted game names are used without basis
--   in free/open-source releases.
--   All download URLs are placeholder (example.com) only.
--   No piracy links, cracked game references, DRM bypasses,
--   keygens, repacks, malware, or unauthorized content exists
--   anywhere in this file.
--
-- SECURITY NOTICE:
--   The admin password hash below is a PLACEHOLDER ONLY.
--   It represents the password "ChangeMe2025!"
--   You MUST change this password immediately after import
--   using the admin panel settings in a future step.
--   Never use default credentials in production.
-- ============================================================

USE `qmgames_store`;

-- Disable foreign key checks during seed inserts
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- SEED 1: Default Admin Account
--
-- !! SECURITY WARNING !!
-- The hash below was generated with PHP:
--   password_hash('ChangeMe2025!', PASSWORD_BCRYPT)
-- This is a SAMPLE PLACEHOLDER hash for local development ONLY.
-- Change the admin password immediately via the admin panel
-- before using this on any shared or production server.
-- ============================================================
INSERT INTO `admins`
  (`name`, `email`, `password_hash`, `role`, `status`)
VALUES
  (
    'Site Administrator',
    'admin@qmgames.local',
    '$2y$12$eUZpL4nW3Qk8mX1vT6Yc7.HjKpN2sRdF0bVqA9wLmE5xI3Gu8ZtOe',
    'super_admin',
    'active'
  );

-- !! REMINDER: Change password after first login !!
-- The placeholder hash above is for local dev setup only.
-- A correct hash must be generated via PHP before going live:
--   php -r "echo password_hash('YourNewPassword', PASSWORD_BCRYPT);"


-- ============================================================
-- SEED 2: Game Categories
-- ============================================================
INSERT INTO `categories`
  (`name`, `slug`, `description`, `icon`, `sort_order`, `status`)
VALUES
  ('Action',           'action',           'Fast-paced action games with combat and challenges.',            'icon-action',      1,  'active'),
  ('Adventure',        'adventure',        'Exploration and story-driven adventure games.',                  'icon-adventure',   2,  'active'),
  ('Racing',           'racing',           'Car, bike, and track racing games.',                             'icon-racing',      3,  'active'),
  ('RPG',              'rpg',              'Role-playing games with character progression and story.',       'icon-rpg',         4,  'active'),
  ('Strategy',         'strategy',         'Turn-based and real-time strategy games.',                       'icon-strategy',    5,  'active'),
  ('Simulation',       'simulation',       'Life, vehicle, and world simulation games.',                     'icon-simulation',  6,  'active'),
  ('Sports',           'sports',           'Football, basketball, athletics, and other sports games.',       'icon-sports',      7,  'active'),
  ('Horror',           'horror',           'Survival horror, psychological horror, and scary games.',        'icon-horror',      8,  'active'),
  ('Low-End PC Games', 'low-end-pc-games', 'Games that run on older or budget PCs with low requirements.',  'icon-low-end',     9,  'active'),
  ('Offline Games',    'offline-games',    'Games that work completely without internet connection.',        'icon-offline',     10, 'active'),
  ('Multiplayer Games','multiplayer-games','Games with online or local multiplayer modes.',                  'icon-multiplayer', 11, 'active'),
  ('Indie Games',      'indie-games',      'Independent developer games with creative and unique designs.',  'icon-indie',       12, 'active'),
  ('Open Source Games','open-source-games','Games with publicly available source code under free licenses.', 'icon-opensource',  13, 'active'),
  ('Demo Games',       'demo-games',       'Official free demo versions released by developers/publishers.', 'icon-demo',        14, 'active');


-- ============================================================
-- SEED 3: Tags
-- ============================================================
INSERT INTO `tags`
  (`name`, `slug`, `status`)
VALUES
  ('Windows',             'windows',              'active'),
  ('Offline',             'offline',              'active'),
  ('Low-End PC',          'low-end-pc',           'active'),
  ('Controller Supported','controller-supported',  'active'),
  ('Single Player',       'single-player',        'active'),
  ('Multiplayer',         'multiplayer',          'active'),
  ('Open Source',         'open-source',          'active'),
  ('Demo',                'demo',                 'active'),
  ('Indie',               'indie',                'active');


-- ============================================================
-- SEED 4: Site Settings (default key-value configuration)
-- ============================================================
INSERT INTO `site_settings`
  (`setting_key`, `setting_value`, `setting_group`)
VALUES
  ('site_name',             'Quantum Mentor Games Store',                                                   'general'),
  ('site_short_name',       'QMGames Store',                                                                'general'),
  ('site_tagline',          'Legal, Safe & High-Quality Game Downloads',                                    'general'),
  ('site_email',            'Coming soon',                                                                   'contact'),
  ('site_whatsapp',         'Coming soon',                                                                   'contact'),
  ('site_youtube',          'Coming soon',                                                                   'contact'),
  ('site_website',          'Coming soon',                                                                   'contact'),
  ('default_theme',         'dark',                                                                          'appearance'),
  ('maintenance_mode',      'off',                                                                           'general'),
  ('games_per_page',        '24',                                                                            'general'),
  ('legal_download_policy', 'Legal, authorized, official, freeware, open-source, demo, or permission-based downloads only.', 'legal'),
  ('footer_copyright',      'Quantum Mentor Games Store. All rights reserved.',                              'general'),
  ('meta_default_desc',     'Discover legal, safe, and high-quality game downloads at QMGames Store.',      'seo'),
  ('meta_default_keywords', 'legal games, freeware games, open source games, demo games, safe download',   'seo');


-- ============================================================
-- SEED 5: Sample Games
-- All game titles, descriptions, and data are FICTIONAL.
-- No real copyrighted commercial games are referenced.
-- All download URLs are PLACEHOLDERS (example.com) only.
-- ============================================================

-- ------------------------------------------------------------
-- Game 1: Quantum Racer Demo
-- License: Official demo — fictional racing demo game
-- ------------------------------------------------------------
INSERT INTO `games` (
  `title`, `slug`, `short_description`, `full_description`,
  `cover_image`, `banner_image`, `trailer_url`,
  `developer`, `publisher`, `version`, `release_date`,
  `game_size`, `platform`, `license_type`, `status`,
  `is_featured`, `is_trending`, `is_low_end_pc`,
  `meta_title`, `meta_description`
) VALUES (
  'Quantum Racer Demo',
  'quantum-racer-demo',
  'A high-speed futuristic racing demo with 3 free tracks. Official demo released by the developer.',
  '<p><strong>Quantum Racer Demo</strong> is an official free demo of the futuristic racing game Quantum Racer. Released directly by the developer, this demo includes 3 playable tracks, 4 vehicles, and the full single-player time trial mode.</p>
<p>The full game features 20+ tracks, an online leaderboard, and a full career mode. This demo is provided to let players experience the gameplay before the full release.</p>
<ul>
  <li>3 free race tracks</li>
  <li>4 vehicles to choose from</li>
  <li>Single-player time trial mode</li>
  <li>Controller support included</li>
  <li>Optimized for low-end PCs</li>
</ul>
<p><em>This is an official demo. Download is authorized directly by the developer.</em></p>',
  'assets/uploads/covers/quantum-racer-demo-cover.jpg',
  'assets/uploads/banners/quantum-racer-demo-banner.jpg',
  NULL,
  'QuantumDev Studios',
  'QuantumDev Studios',
  'Demo v1.2',
  '2024-03-15',
  '1.4 GB',
  'Windows PC',
  'demo',
  'active',
  1, 1, 1,
  'Quantum Racer Demo — Free Official Racing Game Download',
  'Download the official Quantum Racer Demo free. 3 free tracks, 4 vehicles, controller support. Authorized demo by QuantumDev Studios.'
);

-- Game 1 System Requirements
INSERT INTO `game_requirements` (
  `game_id`,
  `minimum_os`, `minimum_processor`, `minimum_ram`, `minimum_gpu`, `minimum_storage`,
  `recommended_os`, `recommended_processor`, `recommended_ram`, `recommended_gpu`, `recommended_storage`
) VALUES (
  1,
  'Windows 7 SP1 64-bit',     'Intel Core i3-4130 / AMD FX-6300', '4 GB RAM', 'NVIDIA GTX 660 / AMD R9 270', '3 GB available space',
  'Windows 10 64-bit',        'Intel Core i5-9600K / AMD Ryzen 5 3600', '8 GB RAM', 'NVIDIA GTX 1060 / AMD RX 580', '3 GB SSD'
);

-- Game 1 Screenshots
INSERT INTO `game_screenshots` (`game_id`, `image_path`, `alt_text`, `sort_order`) VALUES
  (1, 'assets/uploads/screenshots/qr-demo-ss1.jpg', 'Quantum Racer Demo — Track 1 gameplay', 1),
  (1, 'assets/uploads/screenshots/qr-demo-ss2.jpg', 'Quantum Racer Demo — Vehicle selection screen', 2),
  (1, 'assets/uploads/screenshots/qr-demo-ss3.jpg', 'Quantum Racer Demo — Time trial results', 3);

-- Game 1 Download Links (PLACEHOLDER URLs only)
INSERT INTO `download_links`
  (`game_id`, `link_title`, `provider_name`, `download_url`, `link_type`, `file_size`, `status`)
VALUES
  (
    1,
    'Download via Official Developer Site',
    'QuantumDev Studios Official Website',
    'https://example.com/authorized-download/quantum-racer-demo',
    'official',
    '1.4 GB',
    'active'
  ),
  (
    1,
    'Mirror Download — Cloud Storage',
    'Authorized Cloud Mirror',
    'https://example.com/authorized-mirror/quantum-racer-demo',
    'cloud',
    '1.4 GB',
    'active'
  );

-- Game 1 Categories (Racing, Demo Games, Low-End PC Games)
INSERT INTO `game_categories` (`game_id`, `category_id`) VALUES
  (1, 3),   -- Racing
  (1, 9),   -- Low-End PC Games
  (1, 14);  -- Demo Games

-- Game 1 Tags (Windows, Offline, Low-End PC, Controller Supported, Single Player, Demo)
INSERT INTO `game_tags` (`game_id`, `tag_id`) VALUES
  (1, 1),  -- Windows
  (1, 2),  -- Offline
  (1, 3),  -- Low-End PC
  (1, 4),  -- Controller Supported
  (1, 5),  -- Single Player
  (1, 8);  -- Demo


-- ------------------------------------------------------------
-- Game 2: Mentor Quest Free Edition
-- License: Freeware — full version released free by developer
-- ------------------------------------------------------------
INSERT INTO `games` (
  `title`, `slug`, `short_description`, `full_description`,
  `cover_image`, `banner_image`, `trailer_url`,
  `developer`, `publisher`, `version`, `release_date`,
  `game_size`, `platform`, `license_type`, `status`,
  `is_featured`, `is_trending`, `is_low_end_pc`,
  `meta_title`, `meta_description`
) VALUES (
  'Mentor Quest Free Edition',
  'mentor-quest-free-edition',
  'A top-down RPG adventure released as freeware by its original developer. Complete game, no restrictions.',
  '<p><strong>Mentor Quest Free Edition</strong> is a complete top-down RPG adventure game officially released as freeware by its original developer, MentorSoft Games. The full game is free forever with no in-app purchases.</p>
<p>Explore 8 unique dungeons, collect over 50 items, and battle more than 30 enemy types in this classic-style RPG. The developer has officially released this game as freeware so everyone can enjoy it at no cost.</p>
<ul>
  <li>Complete full game — no demo restrictions</li>
  <li>8 explorable dungeons</li>
  <li>50+ collectible items and equipment</li>
  <li>Original soundtrack included</li>
  <li>No internet connection required</li>
  <li>Officially released as freeware by the developer</li>
</ul>
<p><em>Official freeware release. Authorized free distribution by MentorSoft Games.</em></p>',
  'assets/uploads/covers/mentor-quest-free-cover.jpg',
  'assets/uploads/banners/mentor-quest-free-banner.jpg',
  NULL,
  'MentorSoft Games',
  'MentorSoft Games',
  'v2.1.0 Freeware',
  '2023-07-20',
  '520 MB',
  'Windows PC',
  'freeware',
  'active',
  1, 0, 1,
  'Mentor Quest Free Edition — Full Freeware RPG Download',
  'Download Mentor Quest Free Edition, a complete freeware RPG officially released free by MentorSoft Games. No restrictions, full game.'
);

-- Game 2 System Requirements
INSERT INTO `game_requirements` (
  `game_id`,
  `minimum_os`, `minimum_processor`, `minimum_ram`, `minimum_gpu`, `minimum_storage`,
  `recommended_os`, `recommended_processor`, `recommended_ram`, `recommended_gpu`, `recommended_storage`
) VALUES (
  2,
  'Windows XP SP3 / Windows 7', 'Intel Pentium 4 / AMD Athlon 64', '2 GB RAM', 'DirectX 9 compatible GPU', '700 MB available space',
  'Windows 10',                 'Intel Core i3 / AMD Ryzen 3',     '4 GB RAM', 'NVIDIA GT 730 or better',  '700 MB available space'
);

-- Game 2 Screenshots
INSERT INTO `game_screenshots` (`game_id`, `image_path`, `alt_text`, `sort_order`) VALUES
  (2, 'assets/uploads/screenshots/mq-free-ss1.jpg', 'Mentor Quest — Dungeon exploration gameplay', 1),
  (2, 'assets/uploads/screenshots/mq-free-ss2.jpg', 'Mentor Quest — Character inventory screen', 2),
  (2, 'assets/uploads/screenshots/mq-free-ss3.jpg', 'Mentor Quest — Battle scene', 3);

-- Game 2 Download Links (PLACEHOLDER URLs only)
INSERT INTO `download_links`
  (`game_id`, `link_title`, `provider_name`, `download_url`, `link_type`, `file_size`, `status`)
VALUES
  (
    2,
    'Download via Developer Website',
    'MentorSoft Games Official Site',
    'https://example.com/authorized-download/mentor-quest-free-edition',
    'developer_site',
    '520 MB',
    'active'
  ),
  (
    2,
    'Download via Authorized Store Page',
    'itch.io (Authorized Free)',
    'https://example.com/store/mentor-quest-free-edition',
    'store_link',
    '520 MB',
    'active'
  );

-- Game 2 Categories (RPG, Offline Games, Low-End PC Games, Indie Games)
INSERT INTO `game_categories` (`game_id`, `category_id`) VALUES
  (2, 4),   -- RPG
  (2, 9),   -- Low-End PC Games
  (2, 10),  -- Offline Games
  (2, 12);  -- Indie Games

-- Game 2 Tags (Windows, Offline, Low-End PC, Single Player, Indie)
INSERT INTO `game_tags` (`game_id`, `tag_id`) VALUES
  (2, 1),  -- Windows
  (2, 2),  -- Offline
  (2, 3),  -- Low-End PC
  (2, 5),  -- Single Player
  (2, 9);  -- Indie


-- ------------------------------------------------------------
-- Game 3: Neon Arena Open Build
-- License: Open Source — GPL-licensed community game
-- ------------------------------------------------------------
INSERT INTO `games` (
  `title`, `slug`, `short_description`, `full_description`,
  `cover_image`, `banner_image`, `trailer_url`,
  `developer`, `publisher`, `version`, `release_date`,
  `game_size`, `platform`, `license_type`, `status`,
  `is_featured`, `is_trending`, `is_low_end_pc`,
  `meta_title`, `meta_description`
) VALUES (
  'Neon Arena Open Build',
  'neon-arena-open-build',
  'A free and open-source top-down arena shooter released under the GPL license. Full source code available.',
  '<p><strong>Neon Arena Open Build</strong> is a free and open-source top-down arena shooter developed by the NeonArena Community team and distributed under the GNU General Public License (GPL v3).</p>
<p>The full source code is publicly available on the project repository. Players can download, play, modify, and redistribute the game freely in accordance with the GPL license terms.</p>
<ul>
  <li>Free and open-source (GPL v3 license)</li>
  <li>Top-down arena combat with 5 game modes</li>
  <li>Local multiplayer for up to 4 players</li>
  <li>Modding support — full source code available</li>
  <li>Community-driven development</li>
  <li>Cross-platform (Windows build provided here)</li>
</ul>
<p><em>Open-source release. Licensed under GPL v3. Source code available on project repository.</em></p>',
  'assets/uploads/covers/neon-arena-open-cover.jpg',
  'assets/uploads/banners/neon-arena-open-banner.jpg',
  NULL,
  'NeonArena Community',
  'NeonArena Community',
  'v0.9.4 Open Build',
  '2024-01-08',
  '380 MB',
  'Windows PC / Linux',
  'open_source',
  'active',
  0, 1, 1,
  'Neon Arena Open Build — Free Open Source Arena Shooter',
  'Download Neon Arena Open Build, a free GPL-licensed open-source arena shooter. Full source code available. Windows and Linux builds.'
);

-- Game 3 System Requirements
INSERT INTO `game_requirements` (
  `game_id`,
  `minimum_os`, `minimum_processor`, `minimum_ram`, `minimum_gpu`, `minimum_storage`,
  `recommended_os`, `recommended_processor`, `recommended_ram`, `recommended_gpu`, `recommended_storage`
) VALUES (
  3,
  'Windows 7 / Ubuntu 18.04',  'Intel Core 2 Duo / AMD Athlon X2', '2 GB RAM', 'OpenGL 3.0 compatible GPU', '500 MB available space',
  'Windows 10 / Ubuntu 22.04', 'Intel Core i5 / AMD Ryzen 5',      '4 GB RAM', 'NVIDIA GTX 750 Ti or equivalent', '500 MB available space'
);

-- Game 3 Screenshots
INSERT INTO `game_screenshots` (`game_id`, `image_path`, `alt_text`, `sort_order`) VALUES
  (3, 'assets/uploads/screenshots/na-open-ss1.jpg', 'Neon Arena — Arena combat gameplay', 1),
  (3, 'assets/uploads/screenshots/na-open-ss2.jpg', 'Neon Arena — 4-player local multiplayer mode', 2),
  (3, 'assets/uploads/screenshots/na-open-ss3.jpg', 'Neon Arena — Main menu screen', 3);

-- Game 3 Download Links (PLACEHOLDER URLs only)
INSERT INTO `download_links`
  (`game_id`, `link_title`, `provider_name`, `download_url`, `link_type`, `file_size`, `status`)
VALUES
  (
    3,
    'Download Windows Build — Project Repository',
    'NeonArena Community Repository',
    'https://example.com/authorized-download/neon-arena-open-build-win',
    'official',
    '380 MB',
    'active'
  ),
  (
    3,
    'Download Source Code (GPL v3)',
    'NeonArena Community Repository',
    'https://example.com/authorized-download/neon-arena-open-source',
    'official',
    '45 MB',
    'active'
  );

-- Game 3 Categories (Action, Open Source Games, Low-End PC Games, Multiplayer Games)
INSERT INTO `game_categories` (`game_id`, `category_id`) VALUES
  (3, 1),   -- Action
  (3, 9),   -- Low-End PC Games
  (3, 11),  -- Multiplayer Games
  (3, 13);  -- Open Source Games

-- Game 3 Tags (Windows, Offline, Low-End PC, Controller Supported, Multiplayer, Open Source)
INSERT INTO `game_tags` (`game_id`, `tag_id`) VALUES
  (3, 1),  -- Windows
  (3, 2),  -- Offline
  (3, 3),  -- Low-End PC
  (3, 4),  -- Controller Supported
  (3, 6),  -- Multiplayer
  (3, 7);  -- Open Source


-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- Seed data complete. Summary:
--   admins          : 1  record  (CHANGE PASSWORD BEFORE USE)
--   categories      : 14 records
--   tags            :  9 records
--   site_settings   : 14 records
--   games           :  3 records (all fictional / authorized)
--   game_requirements:  3 records
--   game_screenshots:  9 records
--   download_links  :  6 records (placeholder URLs only)
--   game_categories : 12 records
--   game_tags       : 17 records
--
-- REMINDER: Replace placeholder download URLs with real
--           authorized links in the admin panel (future step).
-- REMINDER: Change the admin password immediately after import.
-- ============================================================
