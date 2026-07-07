# Quantum Mentor Games Store
**Short Name:** QMGames Store
**Version:** v1.0 — Final Release

A legal game discovery and download webstore where users can browse and download
legally approved games from authorized freeware, open-source, demo, official mirror,
indie-permission, or developer-approved sources.

> **Legal Policy:** This platform is designed for **legal, authorized, official,
> freeware, open-source, demo, or permission-based game downloads only.**
> Piracy, cracked games, DRM bypassing, keygens, and unauthorized distribution are
> strictly prohibited and will never be supported.

---

## Technology Stack

| Layer      | Technology              |
|------------|-------------------------|
| Frontend   | HTML5, CSS3, JavaScript |
| Backend    | PHP 8+                  |
| Database   | MySQL / MariaDB         |
| Web Server | Apache (via XAMPP)      |
| Dev Env    | XAMPP (localhost)       |

---

## Local Setup Instructions (XAMPP)

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) installed (Apache + MySQL + PHP 8+)
- A modern web browser (Chrome, Firefox, Edge)

### Step 1 — Copy the Project

Copy this folder into your XAMPP web root:
```
C:\xampp\htdocs\quantum-mentor-games-store\
```

### Step 2 — Start XAMPP

Open **XAMPP Control Panel** and start both:
- ✅ Apache
- ✅ MySQL

### Step 3 — Import the Database

1. Open **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Click **Import** → **Choose File** → select `database/database.sql` → click **Go**
   - This creates the `qmgames_store` database and all 16 tables.
3. Select `qmgames_store` in the left panel.
4. Click **Import** → **Choose File** → select `database/seed.sql` → click **Go**
   - This inserts 14 categories, 3 sample games, tags, and site settings.

### Step 4 — Create the Admin Account

The admin account requires a BCrypt password hash. Since `tools/create_hash.php`
has been deleted (as required for security), generate your hash using one of:

**Option A — PHP CLI:**
```bash
php -r "echo password_hash('YourPassword', PASSWORD_DEFAULT);"
```

**Option B — phpMyAdmin SQL tab:**
```sql
SELECT PASSWORD('YourPassword');
```
*(Note: Use PHP's `password_hash()` result, not MySQL's PASSWORD() function.)*

**Option C — One-time PHP snippet** (create, run, then immediately delete):
```php
<?php echo password_hash('YourPassword', PASSWORD_DEFAULT); ?>
```

Once you have the hash (starts with `$2y$...`):

1. Open `database/migrations/step_19_admin_seed.sql`
2. Replace `PASTE_GENERATED_HASH_HERE` with your hash
3. Import the file: phpMyAdmin → `qmgames_store` → Import → select the file → Go
4. Log in at `http://localhost/quantum-mentor-games-store/admin/login.php`
   - Default email: `admin@qmgames.local`
   - Change the password immediately via the admin panel

### Step 5 — Open the Website

```
http://localhost/quantum-mentor-games-store/
```

### Step 6 — Verify the Database (optional)

In phpMyAdmin, click `qmgames_store` and confirm:
- 16 tables are present
- `categories` → 14 rows
- `games` → 3 rows
- `tags` → 9 rows
- `admins` → 1 row

---

## Database: `qmgames_store`

### All 16 Tables

| # | Table Name          | Purpose                                         | Status       |
|---|---------------------|-------------------------------------------------|--------------|
| 1 | `admins`            | Admin panel user accounts                       | Active       |
| 2 | `categories`        | Game categories and genres                      | Active       |
| 3 | `tags`              | Flexible tags for search and filtering          | Active       |
| 4 | `users`             | Future public user accounts                     | Created only |
| 5 | `games`             | Main games table                                | Active       |
| 6 | `game_categories`   | Many-to-many: games ↔ categories               | Active       |
| 7 | `game_requirements` | System requirements per game                    | Active       |
| 8 | `game_screenshots`  | Screenshot images per game                      | Active       |
| 9 | `download_links`    | Authorized download links per game              | Active       |
|10 | `download_reports`  | User-submitted link reports                     | Active       |
|11 | `contact_messages`  | Public contact form submissions                 | Active       |
|12 | `site_settings`     | Editable site config (key-value)                | Active       |
|13 | `game_tags`         | Many-to-many: games ↔ tags                     | Active       |
|14 | `user_library`      | Future user game library                        | Created only |
|15 | `orders`            | Future payment/order system                     | Created only |
|16 | `order_items`       | Future order line items                         | Created only |

Tables marked **Created only** exist structurally but have no active frontend features.

### Database Migrations

| File                                              | Purpose                                          |
|---------------------------------------------------|--------------------------------------------------|
| `database/migrations/step_19_admin_seed.sql`      | Inserts first admin account (hash required)      |
| `database/migrations/step_23_category_management.sql` | Adds `archived` value to `categories.status` ENUM |

Run migrations in phpMyAdmin: select `qmgames_store` → Import → select file → Go.

### License Types (`games.license_type`)

| Value               | Meaning                                              |
|---------------------|------------------------------------------------------|
| `freeware`          | Free to download legally (developer-released)        |
| `open_source`       | GPL, MIT, or other open-source license               |
| `demo`              | Official limited demo from developer/publisher       |
| `official_mirror`   | Authorized mirror of an official free release        |
| `indie_permission`  | Indie developer granted explicit download permission |
| `paid_future`       | Reserved for future paid game support                |
| `other_authorized`  | Any other verifiably authorized distribution         |

> No pirated, cracked, or DRM-bypass values exist anywhere in the schema.

---

## Seed Data Summary

| Table               | Records Inserted |
|---------------------|-----------------|
| `admins`            | 1 (via migration — change password!) |
| `categories`        | 14              |
| `tags`              | 9               |
| `site_settings`     | 14              |
| `games`             | 3 (fictional sample games) |
| `game_requirements` | 3               |
| `game_screenshots`  | 9               |
| `download_links`    | 6 (placeholder URLs) |
| `game_categories`   | 12              |
| `game_tags`         | 17              |

### Sample Categories (14)
Action · Adventure · Racing · RPG · Strategy · Simulation · Sports · Horror ·
Low-End PC Games · Offline Games · Multiplayer Games · Indie Games · Open Source Games · Demo Games

### Sample Tags (9)
Windows · Offline · Low-End PC · Controller Supported · Single Player ·
Multiplayer · Open Source · Demo · Indie

### Sample Games (3)

| Title                        | License     | Status |
|------------------------------|-------------|--------|
| Quantum Racer Demo           | demo        | active |
| Mentor Quest Free Edition    | freeware    | active |
| Neon Arena Open Build        | open_source | active |

> All game titles are **fictional**. All download URLs are **placeholder** (`example.com`).
> Replace them with real authorized links via the admin panel.

---

## Folder Structure

```
quantum-mentor-games-store/
│
├── assets/
│   ├── css/
│   │   ├── style.css            # Global styles (dark/light theme, all components)
│   │   ├── responsive.css       # Responsive breakpoints (mobile/tablet)
│   │   └── admin.css            # Admin panel styles
│   ├── js/
│   │   ├── main.js              # Mobile menu, UI utilities, lightbox
│   │   ├── theme.js             # Dark/light toggle (localStorage, flash-free)
│   │   └── admin.js             # Admin UI — image preview, slug gen, etc.
│   ├── images/                  # Logo SVGs, placeholder cover/banner SVGs
│   ├── icons/                   # Icon assets (.gitkeep)
│   └── uploads/                 # User-uploaded content (writable by Apache)
│       ├── covers/              # Game cover images
│       ├── banners/             # Game banner images
│       ├── screenshots/         # Game screenshots
│       ├── games/               # Direct game file uploads (future)
│       └── temp/                # Temporary upload staging
│
├── admin/                       # Admin panel (protected — requireAdmin() on every page)
│   ├── index.php                # Redirects to dashboard
│   ├── login.php                # BCrypt login + CSRF + honeypot
│   ├── logout.php               # Session destroy + redirect
│   ├── dashboard.php            # Stats, recent activity, health check
│   ├── games.php                # Game list with filters + quick status actions
│   ├── add-game.php             # Add game form with image upload
│   ├── edit-game.php            # Edit game form, image replace, slug update
│   ├── categories.php           # Category list/add/edit (multi-mode)
│   ├── download-links.php       # Download link list/add/edit (multi-mode)
│   ├── reports.php              # Download report queue (placeholder)
│   ├── messages.php             # Contact message queue (placeholder)
│   ├── settings.php             # Site settings (placeholder)
│   └── partials/
│       ├── game-form-fields.php     # Shared game form partial
│       ├── cat-form-fields.php      # Shared category form partial
│       └── dl-link-form-fields.php  # Shared download link form partial
│
├── includes/                    # Shared PHP includes
│   ├── config.php               # SITE_NAME, SITE_URL, DB constants, feature flags
│   ├── db.php                   # PDO singleton — getDB()
│   ├── init.php                 # Bootstrap: config → db → functions → session start
│   ├── functions.php            # 60+ helper functions (sanitize, DB, format, etc.)
│   ├── auth.php                 # Admin auth: loginAdmin, logoutAdmin, requireAdmin
│   ├── header.php               # HTML <head> — SEO meta, OG tags, CSS links, theme script
│   ├── navbar.php               # Responsive navbar with active page highlighting
│   ├── footer.php               # Footer + script tags
│   ├── admin-header.php         # Admin HTML header
│   ├── admin-sidebar.php        # Admin sidebar navigation
│   └── admin-footer.php         # Admin footer + admin scripts
│
├── pages/                       # Static public pages
│   ├── about.php                # About page (10 sections, roadmap)
│   ├── contact.php              # Contact form (CSRF, honeypot, PRG, cooldown)
│   ├── privacy-policy.php       # Privacy policy (11 sections, sticky ToC)
│   └── disclaimer.php           # Legal disclaimer (12 sections, legal highlight)
│
├── database/
│   ├── database.sql             # Full schema — 16 tables
│   ├── seed.sql                 # Sample data (categories, games, tags, settings)
│   └── migrations/
│       ├── step_19_admin_seed.sql           # First admin account (hash required)
│       └── step_23_category_management.sql  # Adds 'archived' to categories.status
│
├── logs/
│   └── error.log                # PHP application error log (writable by Apache)
│
├── index.php                    # Homepage (featured, trending, latest, low-end)
├── games.php                    # All games listing (filters, sort, pagination)
├── game-details.php             # Single game page (screenshots, requirements, links)
├── category.php                 # Category directory + individual category pages
├── search.php                   # Search results (keyword + filters + pagination)
├── download.php                 # Safe download redirect (token, validation, logging)
├── report-link.php              # Broken/illegal link report form
├── 404.php                      # Custom 404 page
├── robots.txt                   # SEO crawl rules (update domain before production)
├── sitemap.xml                  # XML sitemap (update URLs before production)
├── .htaccess                    # Apache: clean URLs, security headers, 404 routing
└── README.md                    # This file
```

---

## Legal Download Policy

QMGames Store **only** lists and links to games that are:

- ✅ Available from official developer/publisher websites
- ✅ Distributed as freeware (free to download legally)
- ✅ Released as open-source software
- ✅ Official demo versions
- ✅ Available via authorized cloud storage links
- ✅ Distributed via authorized torrent files with explicit permission
- ✅ Any source with explicit rights-holder permission

**Strictly Prohibited:**
- ❌ Pirated or cracked games
- ❌ DRM bypasses or cracks
- ❌ Keygens / serial generators
- ❌ Malware, adware, or untrusted files
- ❌ Unauthorized game distribution of any kind

---

## Security Notes for Production

Before deploying to a live server, complete ALL of the following:

| # | Action | Priority |
|---|--------|----------|
| 1 | Change the admin password from the default `Admin@12345` | 🔴 Critical |
| 2 | Set `DISPLAY_ERRORS = false` in `includes/config.php` (already set) | ✅ Done |
| 3 | Move `includes/`, `database/`, `logs/` **above** the web root, or verify `.htaccess` blocks direct access | 🔴 Critical |
| 4 | Confirm `tools/create_hash.php` is deleted (already deleted in v1.0) | ✅ Done |
| 5 | Replace all `localhost` URLs in `sitemap.xml` with your live domain | 🟡 Required |
| 6 | Replace `localhost` sitemap URL in `robots.txt` | 🟡 Required |
| 7 | Set proper file permissions: `uploads/` writable by web server, PHP files not writable | 🟡 Required |
| 8 | Enable HTTPS and update `SITE_URL` in `includes/config.php` | 🔴 Critical |
| 9 | Review and tighten `.htaccess` for your hosting environment | 🟡 Required |
|10 | Change `DB_USER` and `DB_PASS` from XAMPP defaults | 🔴 Critical |
|11 | Set `session.cookie_secure = 1` and `session.cookie_httponly = 1` in php.ini (or via `.htaccess`) | 🔴 Critical |
|12 | Remove or restrict `database/` folder from web access | 🔴 Critical |

---

## Development Roadmap

| Step | Description                            | Status          |
|------|----------------------------------------|-----------------|
| 1    | Project setup & folder structure       | ✅ Complete      |
| 2    | MySQL database schema (16 tables)      | ✅ Complete      |
| 3    | PHP backend & PDO connection           | ✅ Complete      |
| 4    | Global layout design (navbar, footer)  | ✅ Complete      |
| 5    | Dark/Light theme system (`data-theme`) | ✅ Complete      |
| 6    | Professional homepage (11 sections)    | ✅ Complete      |
| 7    | Games listing (filters, sort, paging)  | ✅ Complete      |
| 8    | Category system (directory + detail)   | ✅ Complete      |
| 9    | Game detail page                       | ✅ Complete      |
| 10   | Safe download link system              | ✅ Complete      |
| 11   | Analytics (view cooldown, counters)    | ✅ Complete      |
| 12   | Broken link report system              | ✅ Complete      |
| 13   | Search system (keyword + filters)      | ✅ Complete      |
| 14   | Error/404 system                       | ✅ Complete      |
| 15   | Contact page (PRG, CSRF, cooldown)     | ✅ Complete      |
| 16   | About page (10 sections, roadmap)      | ✅ Complete      |
| 17   | Privacy Policy page                    | ✅ Complete      |
| 18   | Disclaimer page                        | ✅ Complete      |
| 19   | Admin login (BCrypt, CSRF, timeout)    | ✅ Complete      |
| 20   | Admin dashboard                        | ✅ Complete      |
| 21   | Admin game management                  | ✅ Complete      |
| 22   | Admin download link management         | ✅ Complete      |
| 23   | Admin category management              | ✅ Complete      |
| 24   | Final polish, SEO, security, cleanup   | ✅ **Complete**  |

---

## v1.0 Final Readiness Report

| Category | Item | Status |
|----------|------|--------|
| **Security** | `tools/create_hash.php` deleted | ✅ Pass |
| **Security** | `DISPLAY_ERRORS = false` in config.php | ✅ Pass |
| **Security** | All admin pages call `requireAdmin()` | ✅ Pass |
| **Security** | CSRF on all POST forms (login, contact, reports, admin) | ✅ Pass |
| **Security** | Honeypot on all public forms | ✅ Pass |
| **Security** | All DB queries use prepared statements | ✅ Pass |
| **Security** | All output uses `e()` / `htmlspecialchars` | ✅ Pass |
| **Security** | BCrypt via `password_hash` / `password_verify` only | ✅ Pass |
| **Security** | No hard deletes — soft archive/status changes only | ✅ Pass |
| **Security** | Image uploads: MIME check, extension whitelist, random filenames | ✅ Pass |
| **Security** | `.htaccess` blocks directory listing and sensitive files | ✅ Pass |
| **UI/UX** | Navbar active link highlighting (`$activePage` + `aria-current`) | ✅ Pass |
| **UI/UX** | Dark/light theme via `data-theme` on `<html>` only (no flash) | ✅ Pass |
| **UI/UX** | Responsive design (mobile/tablet/desktop) | ✅ Pass |
| **UI/UX** | Custom 404 page wired via `.htaccess` | ✅ Pass |
| **SEO** | `<title>`, `<meta description>`, canonical, OG tags on all pages | ✅ Pass |
| **SEO** | `robots.txt` with admin/includes blocked | ✅ Pass |
| **SEO** | `sitemap.xml` covering all static public pages | ✅ Pass |
| **SEO** | `sitemap.xml` and `robots.txt` have production deployment notes | ✅ Pass |
| **Code Quality** | Stale TODO comments removed from `functions.php` | ✅ Pass |
| **Code Quality** | No `console.log` statements in `main.js` | ✅ Pass |
| **Code Quality** | No debug output in `config.php` | ✅ Pass |
| **Code Quality** | No `admin.css` included on public pages | ✅ Pass |
| **Assets** | `assets/icons/` directory exists (`.gitkeep`) | ✅ Pass |
| **Database** | 16 tables created by `database.sql` | ✅ Pass |
| **Database** | Seed data: 14 categories, 3 games, 9 tags, 14 settings | ✅ Pass |
| **Database** | Step 23 migration adds `archived` to `categories.status` | ✅ Pass |
| **Admin** | Dashboard, games, categories, download links management built | ✅ Pass |
| **Admin** | Reports, messages, settings stubs — protected, clean | ✅ Pass |
| **Public** | Homepage, games list, category, game detail, search, download | ✅ Pass |
| **Public** | Contact, About, Privacy Policy, Disclaimer pages complete | ✅ Pass |

**Result: v1.0 — All 24 steps complete. Ready for local testing and production prep.**

---

## Step 24 — Final Polish Summary

### Changes made in Step 24

| File | Change |
|------|--------|
| `tools/create_hash.php` | **Deleted** — security risk removed |
| `includes/navbar.php` | Active page highlighting via `$activePage` + `aria-current` |
| `includes/functions.php` | Removed stale `// TODO (Step 4)` comment from `isLoggedIn()` |
| `sitemap.xml` | Removed TODO comment; added production deployment note |
| `robots.txt` | Added production deployment note for sitemap URL |
| `README.md` | Rewritten as complete v1.0 final documentation |

### How active page highlighting works

Every public page sets `$activePage` before including `navbar.php`:

```php
// index.php
$activePage = 'home';
require_once 'includes/init.php';

// games.php
$activePage = 'games';
require_once 'includes/init.php';
```

Valid keys: `home`, `games`, `categories`, `search`, `about`, `contact`

The navbar applies `class="nav-link active"` and `aria-current="page"` to the
matching link — on both desktop nav and the mobile drawer.

---

## Step 23 — Admin Category Management

### Migration required before testing

Run `database/migrations/step_23_category_management.sql` in phpMyAdmin to add
`archived` to the `categories.status` ENUM. Existing data is not affected.

### Pages

| URL | Mode |
|-----|------|
| `admin/categories.php` | List with 3 filters + pagination + status actions |
| `admin/categories.php?action=add` | Add form |
| `admin/categories.php?action=edit&id=N` | Edit form |
| POST `action_status` | Set active/inactive/archived |

### Key behaviours

- Archiving a category does NOT delete linked games (`game_categories` rows preserved)
- Only `active` categories appear on public pages
- Slug auto-generated from name on add page; manually editable
- Icon field supports emoji — live preview via JS
- Sort order field controls display order on public category page

---

## Step 22 — Admin Download Link Management

### Pages

| URL | Mode |
|-----|------|
| `admin/download-links.php` | List with filters, pagination, status actions |
| `admin/download-links.php?action=add` | Add form |
| `admin/download-links.php?action=edit&id=N` | Edit form |

### URL Security

`isSafeDownloadUrl()` allows only `http`/`https` (or `magnet:` for torrent type).
Rejects `javascript:`, `data:`, `file:`, `ftp:`, `mailto:`, and empty strings.

### Legal confirmation

Required checkbox on every add/edit form: "I confirm this source is allowed."
Submission rejected if `legal_confirm` is missing.

---

## Step 21 — Admin Game Management

### Pages

| Page | Description |
|------|-------------|
| `admin/games.php` | Game list with 7 filters, sorting, pagination, quick status actions |
| `admin/add-game.php` | Full add form with validation, image upload, categories, tags |
| `admin/edit-game.php` | Same form pre-filled, image replace, slug uniqueness excluding self |

### Security features

- CSRF token on all POST forms
- Image upload: MIME check via `finfo_file()`, extension whitelist, size limits, random filename
- Status changes via POST only (never GET)
- Soft archive only — no hard deletes

---

## Step 19 — Admin Login System

### Security implementation

| Feature | Implementation |
|---------|---------------|
| Password storage | `password_hash()` BCrypt only |
| Password verification | `password_verify()` — timing-safe |
| Session fixation | `session_regenerate_id(true)` after every login |
| CSRF | Token on login form |
| Honeypot | Hidden `website_url` field |
| Error messages | Generic — never reveals which field failed |
| Session timeout | 2-hour inactivity (`ADMIN_SESSION_TIMEOUT = 7200`) |

### Admin auth flow

```
GET  admin/login.php  → Login form + CSRF token
POST admin/login.php  → Validate CSRF → honeypot → credentials
                      → password_verify() → session_regenerate_id()
                      → Set session keys → redirect dashboard
GET  admin/logout.php → session destroyed → redirect login?logged_out=1
Any  admin/*.php      → requireAdmin() → timeout check → allow/redirect
```
