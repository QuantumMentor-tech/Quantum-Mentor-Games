<?php
/**
 * QMGames Store - Global Helper Functions
 * Step: 3 — Backend Connection System
 *
 * LOADED BY: includes/init.php (after db.php)
 *
 * All functions in this file are safe, stateless, and reusable
 * across every public and admin page in the application.
 *
 * SAFETY RULES:
 *   - All functions use prepared statements when touching the DB.
 *   - No raw SQL string concatenation.
 *   - Input is always sanitised before use or output.
 *   - No function name may be duplicated elsewhere.
 */

/* ============================================================
   Guard: must be loaded after config.php
   ============================================================ */
if (!defined('SITE_NAME')) {
    die('Direct access not permitted.');
}

/* ============================================================
   cleanInput()
   Sanitises a plain text string for safe output / storage.
   Use for: form fields, GET/POST params, search queries.

   @param  mixed  $value  Raw input value
   @return string         Trimmed, stripped, HTML-encoded string
   ============================================================ */
function cleanInput(mixed $value): string
{
    $value = (string) $value;
    $value = trim($value);
    $value = stripslashes($value);
    $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return $value;
}

/* ============================================================
   cleanTextarea()
   Sanitises multi-line textarea input while preserving line breaks.
   Line endings are normalised to \n before HTML encoding.

   @param  mixed  $value  Raw textarea value
   @return string         Cleaned multi-line string
   ============================================================ */
function cleanTextarea(mixed $value): string
{
    $value = (string) $value;
    $value = trim($value);
    // Normalise Windows (\r\n) and old Mac (\r) line endings to \n
    $value = str_replace(["\r\n", "\r"], "\n", $value);
    $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return $value;
}

/* ============================================================
   createSlug()
   Converts any string to a URL-safe slug.
   Example: "Quantum Racer Demo!" → "quantum-racer-demo"

   @param  string $text  Input string
   @return string        Lowercase hyphen-separated slug
   ============================================================ */
function createSlug(string $text): string
{
    // Convert to lowercase using multibyte safe function
    $text = mb_strtolower($text, 'UTF-8');
    // Replace non-alphanumeric characters (except spaces and hyphens) with empty
    $text = preg_replace('/[^a-z0-9\s\-]/', '', $text);
    // Collapse any run of spaces or hyphens into a single hyphen
    $text = preg_replace('/[\s\-]+/', '-', $text);
    // Remove leading and trailing hyphens
    return trim($text, '-');
}

/* ============================================================
   redirect()
   Issues an HTTP Location redirect and exits the script.
   Must be called before any HTML output.

   @param  string $url   Absolute URL or site-relative path
   @param  int    $code  HTTP status code (default 302)
   @return void          (never returns — always exits)
   ============================================================ */
function redirect(string $url, int $code = 302): void
{
    // Prepend SITE_URL for relative paths
    if (!preg_match('/^https?:\/\//i', $url)) {
        $url = rtrim(SITE_URL, '/') . '/' . ltrim($url, '/');
    }

    // Basic header injection guard — only allow safe characters
    $url = preg_replace('/[\r\n]/', '', $url);

    header('Location: ' . $url, true, $code);
    exit;
}

/* ============================================================
   siteUrl()
   Builds a full URL from a path relative to the project root.
   Example: siteUrl('games.php')
            → http://localhost/quantum-mentor-games-store/games.php

   @param  string $path  Relative path (optional)
   @return string        Full URL, no trailing slash on base
   ============================================================ */
function siteUrl(string $path = ''): string
{
    return rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');
}

/* ============================================================
   assetUrl()
   Builds a full URL for files inside /assets/.
   Example: assetUrl('css/style.css')
            → http://localhost/quantum-mentor-games-store/assets/css/style.css

   @param  string $path  Path relative to /assets/
   @return string        Full asset URL
   ============================================================ */
function assetUrl(string $path = ''): string
{
    return rtrim(ASSETS_URL, '/') . '/' . ltrim($path, '/');
}

/* ============================================================
   currentUrl()
   Returns the full URL of the current request.

   @return string  e.g. http://localhost/quantum-mentor-games-store/games.php
   ============================================================ */
function currentUrl(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri    = $_SERVER['REQUEST_URI'] ?? '/';
    return $scheme . '://' . $host . $uri;
}

/* ============================================================
   formatFileSize()
   Converts bytes (int) to human-readable string.
   Also safely returns an already-formatted string (e.g. "2.5 GB").

   Examples:
     formatFileSize(1500)        → "1.46 KB"
     formatFileSize(1048576)     → "1.00 MB"
     formatFileSize("2.5 GB")   → "2.5 GB"   (string passthrough)

   @param  int|string $size  Bytes as int, OR already-formatted string
   @param  int        $decimals
   @return string
   ============================================================ */
function formatFileSize(int|string $size, int $decimals = 2): string
{
    // If a string was passed and it's not purely numeric, return it cleaned
    if (is_string($size) && !ctype_digit($size)) {
        return cleanInput($size);
    }

    $bytes = (int) $size;
    if ($bytes <= 0) return '0 B';

    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i     = (int) floor(log($bytes, 1024));
    $i     = min($i, count($units) - 1);

    return number_format($bytes / (1024 ** $i), $decimals) . ' ' . $units[$i];
}

/* ============================================================
   timeAgo()
   Returns a human-friendly relative time string.
   Examples:
     Just now
     5 minutes ago
     2 hours ago
     3 days ago
     on 15 Jun 2024  (for dates older than 30 days)

   @param  string $datetime  MySQL DATETIME / TIMESTAMP string
   @return string
   ============================================================ */
function timeAgo(string $datetime): string
{
    $now  = time();
    $then = strtotime($datetime);

    if ($then === false) {
        return 'Unknown date';
    }

    $diff = $now - $then;

    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $m = (int) floor($diff / 60);
        return $m . ' minute' . ($m > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $h = (int) floor($diff / 3600);
        return $h . ' hour' . ($h > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) { // 30 days
        $d = (int) floor($diff / 86400);
        return $d . ' day' . ($d > 1 ? 's' : '') . ' ago';
    } else {
        return 'on ' . date('d M Y', $then);
    }
}

/* ============================================================
   truncateText()
   Shortens plain text to a character limit with ellipsis.

   @param  string $text    Input string (HTML tags stripped)
   @param  int    $length  Max character length (default 120)
   @param  string $suffix  Appended suffix (default '...')
   @return string
   ============================================================ */
function truncateText(string $text, int $length = 120, string $suffix = '...'): string
{
    $text = strip_tags($text);
    if (mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $length, 'UTF-8')) . $suffix;
}

/* ============================================================
   e()
   Short escape helper for safe HTML output.
   Equivalent to htmlspecialchars($v, ENT_QUOTES, 'UTF-8').
   Use inside HTML templates: <?= e($var) ?>

   @param  mixed  $value  Value to escape
   @return string         HTML-safe string
   ============================================================ */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/* ============================================================
   isValidUrl()
   Validates that a string is a well-formed HTTP/HTTPS URL.
   Used for validating download link URLs before storage.

   @param  string $url
   @return bool
   ============================================================ */
function isValidUrl(string $url): bool
{
    $url = filter_var(trim($url), FILTER_VALIDATE_URL);
    if ($url === false) {
        return false;
    }
    // Only allow http:// and https:// — no javascript:, data:, ftp:, etc.
    $scheme = parse_url($url, PHP_URL_SCHEME);
    return in_array(strtolower($scheme ?? ''), ['http', 'https'], true);
}

/* ============================================================
   getPlaceholderImage()
   Returns the URL to a placeholder image.
   Used when a game has no uploaded cover/banner/screenshot yet.

   @param  string $type  'cover' | 'banner' | 'screenshot'
   @return string        Full asset URL to placeholder SVG
   ============================================================ */
function getPlaceholderImage(string $type = 'cover'): string
{
    $map = [
        'cover'      => 'images/placeholder-cover.svg',
        'banner'     => 'images/placeholder-banner.svg',
        'screenshot' => 'images/placeholder-cover.svg',
    ];
    $file = $map[$type] ?? 'images/placeholder-cover.svg';
    return assetUrl($file);
}

/* ============================================================
   getSetting()
   Fetches a single value from the site_settings table.
   Returns $default if the key does not exist or DB is unavailable.

   @param  string $key      Setting key, e.g. 'site_name'
   @param  mixed  $default  Fallback value (default null)
   @return mixed            Setting value string or $default
   ============================================================ */
function getSetting(string $key, mixed $default = null): mixed
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT setting_value FROM site_settings WHERE setting_key = ? LIMIT 1'
        );
        $stmt->execute([$key]);
        $row = $stmt->fetch();

        return ($row !== false) ? $row['setting_value'] : $default;

    } catch (PDOException $e) {
        logAppError('getSetting() failed for key "' . $key . '": ' . $e->getMessage(), 'DB');
        return $default;
    }
}

/* ============================================================
   updateSetting()
   Inserts or updates a value in site_settings using
   INSERT ... ON DUPLICATE KEY UPDATE (upsert pattern).

   @param  string $key    Setting key
   @param  string $value  New value
   @param  string $group  Setting group (default 'general')
   @return bool           true on success, false on failure
   ============================================================ */
function updateSetting(string $key, string $value, string $group = 'general'): bool
{
    try {
        $db = getDB();
        $stmt = $db->prepare(
            'INSERT INTO site_settings (setting_key, setting_value, setting_group)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE
               setting_value = VALUES(setting_value),
               setting_group = VALUES(setting_group)'
        );
        return $stmt->execute([$key, $value, $group]);

    } catch (PDOException $e) {
        logAppError('updateSetting() failed for key "' . $key . '": ' . $e->getMessage(), 'DB');
        return false;
    }
}

/* ============================================================
   getActiveCategories()
   Fetches all active categories ordered by sort_order, then name.
   Used in navbar dropdowns, category page, sidebar filters.

   @param  int|null $limit  Optional maximum number of rows
   @return array            Array of category rows (assoc)
   ============================================================ */
function getActiveCategories(?int $limit = null): array
{
    try {
        $db  = getDB();
        $sql = 'SELECT id, name, slug, description, icon, sort_order
                FROM   categories
                WHERE  status = ?
                ORDER  BY sort_order ASC, name ASC';

        if ($limit !== null && $limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute(['active']);
        return $stmt->fetchAll();

    } catch (PDOException $e) {
        logAppError('getActiveCategories() failed: ' . $e->getMessage(), 'DB');
        return [];
    }
}

/* ============================================================
   getGameBySlug()
   Fetches a single active game by its URL slug.
   Returns false if not found or if game is not active.
   Used by game-details.php.

   @param  string $slug  URL slug to look up
   @return array|false   Game row or false
   ============================================================ */
function getGameBySlug(string $slug): array|false
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT * FROM games
             WHERE  slug = ? AND status = ?
             LIMIT  1'
        );
        $stmt->execute([$slug, 'active']);
        return $stmt->fetch();

    } catch (PDOException $e) {
        logAppError('getGameBySlug() failed for slug "' . $slug . '": ' . $e->getMessage(), 'DB');
        return false;
    }
}

/* ============================================================
   isLoggedIn()
   Returns true if an admin session is currently active.
   Full auth is implemented in includes/auth.php — this is a
   lightweight alias used outside the admin panel context.

   @return bool
   ============================================================ */
function isLoggedIn(): bool
{
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/* ============================================================
   Step 6 — Homepage Database Helpers
   All functions below use prepared statements, cast $limit to
   int, return [] on any failure, and never expose SQL errors.
   ============================================================ */

/* ============================================================
   getFeaturedGames()
   Returns active games with is_featured = 1.

   @param  int $limit  Max rows (default 4)
   @return array       Rows from games table, or []
   ============================================================ */
function getFeaturedGames(int $limit = 4): array
{
    $limit = max(1, min(20, $limit));
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id, title, slug, short_description, cover_image,
                    platform, game_size, license_type
             FROM   games
             WHERE  status = ? AND is_featured = 1
             ORDER  BY created_at DESC
             LIMIT  ' . $limit
        );
        $stmt->execute(['active']);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getFeaturedGames() failed: ' . $e->getMessage(), 'DB');
        return [];
    }
}

/* ============================================================
   getTrendingGames()
   Returns active games sorted by downloads_count then views_count.
   Falls back to is_trending = 1 filter when available data
   is limited.

   @param  int $limit  Max rows (default 4)
   @return array
   ============================================================ */
function getTrendingGames(int $limit = 4): array
{
    $limit = max(1, min(20, $limit));
    try {
        $db   = getDB();
        /* Improved sort: is_trending flag first, then activity metrics */
        $stmt = $db->prepare(
            'SELECT id, title, slug, short_description, cover_image,
                    platform, game_size, license_type, downloads_count, views_count,
                    is_trending
             FROM   games
             WHERE  status = ?
             ORDER  BY is_trending DESC,
                       downloads_count DESC,
                       views_count DESC,
                       created_at DESC
             LIMIT  ' . $limit
        );
        $stmt->execute(['active']);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getTrendingGames() failed: ' . $e->getMessage(), 'DB');
        return [];
    }
}

/* ============================================================
   getLatestGames()
   Returns the most recently added active games.

   @param  int $limit  Max rows (default 4)
   @return array
   ============================================================ */
function getLatestGames(int $limit = 4): array
{
    $limit = max(1, min(20, $limit));
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id, title, slug, short_description, cover_image,
                    platform, game_size, license_type, created_at
             FROM   games
             WHERE  status = ?
             ORDER  BY created_at DESC
             LIMIT  ' . $limit
        );
        $stmt->execute(['active']);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getLatestGames() failed: ' . $e->getMessage(), 'DB');
        return [];
    }
}

/* ============================================================
   getLowEndGames()
   Returns active games tagged as low-end PC friendly.

   @param  int $limit  Max rows (default 3)
   @return array
   ============================================================ */
function getLowEndGames(int $limit = 3): array
{
    $limit = max(1, min(12, $limit));
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id, title, slug, short_description, cover_image,
                    platform, game_size, license_type
             FROM   games
             WHERE  status = ? AND is_low_end_pc = 1
             ORDER  BY created_at DESC
             LIMIT  ' . $limit
        );
        $stmt->execute(['active']);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getLowEndGames() failed: ' . $e->getMessage(), 'DB');
        return [];
    }
}

/* ============================================================
   getLicenseLabel()
   Converts a license_type enum value into a readable label.

   @param  string $type  e.g. 'freeware', 'open_source', 'demo'
   @return string        Human-readable label
   ============================================================ */
function getLicenseLabel(string $type): string
{
    $labels = [
        'freeware'         => 'Freeware',
        'open_source'      => 'Open Source',
        'demo'             => 'Demo',
        'official_mirror'  => 'Official Mirror',
        'indie_permission' => 'Indie',
        'paid_future'      => 'Paid',
        'other_authorized' => 'Authorized',
    ];
    return $labels[$type] ?? 'Free';
}

/* ============================================================
   getLicenseBadgeClass()
   Returns a CSS badge class for a given license_type.

   @param  string $type
   @return string  CSS class e.g. 'badge-success'
   ============================================================ */
function getLicenseBadgeClass(string $type): string
{
    $map = [
        'freeware'         => 'badge-success',
        'open_source'      => 'badge-primary',
        'demo'             => 'badge-warning',
        'official_mirror'  => 'badge-secondary',
        'indie_permission' => 'badge-secondary',
        'paid_future'      => 'badge-muted',
        'other_authorized' => 'badge-muted',
    ];
    return $map[$type] ?? 'badge-muted';
}

/* ============================================================
   Step 7 — Games Listing Page Helpers
   ============================================================ */

/* ============================================================
   getGamesList()
   Returns a paginated array of active games plus metadata.
   Supports full filter, search, and sort pipeline via
   safe prepared statements and whitelisted values only.

   @param  array  $filters  Associative array of filter values:
                              q        => search string
                              category => category slug
                              license  => license_type string
                              platform => platform string
                              low_end  => '1' to filter
                              sort     => sort key string
   @param  int    $page     Current page (1-based)
   @param  int    $perPage  Results per page

   @return array  [
                    'games'       => array,
                    'total'       => int,
                    'page'        => int,
                    'per_page'    => int,
                    'total_pages' => int,
                  ]
   ============================================================ */
function getGamesList(array $filters = [], int $page = 1, int $perPage = 12): array
{
    /* ── Defaults ── */
    $empty = ['games' => [], 'total' => 0, 'page' => 1,
              'per_page' => $perPage, 'total_pages' => 0];

    /* ── Sanitise page / perPage ── */
    $page    = max(1, (int) $page);
    $perPage = max(1, min(60, (int) $perPage));
    $offset  = ($page - 1) * $perPage;

    /* ── Whitelisted sort map ── */
    $sortMap = [
        'latest'          => 'g.created_at DESC',
        'oldest'          => 'g.created_at ASC',
        'most_viewed'     => 'g.views_count DESC',
        'most_downloaded' => 'g.downloads_count DESC',
        'az'              => 'g.title ASC',
        'za'              => 'g.title DESC',
        'featured'        => 'g.is_featured DESC, g.created_at DESC',
        'trending'        => 'g.is_trending DESC, g.downloads_count DESC, g.created_at DESC',
    ];

    /* ── Whitelisted license values ── */
    $validLicenses = [
        'freeware', 'open_source', 'demo',
        'official_mirror', 'indie_permission', 'paid_future', 'other_authorized',
    ];

    /* ── Extract and sanitise filter values ── */
    $q        = isset($filters['q'])        ? trim(mb_substr((string)$filters['q'], 0, 100)) : '';
    $catSlug  = isset($filters['category']) ? trim((string)$filters['category'])  : '';
    $license  = isset($filters['license'])  ? trim((string)$filters['license'])   : '';
    $platform = isset($filters['platform']) ? trim((string)$filters['platform'])  : '';
    $lowEnd   = isset($filters['low_end'])  && $filters['low_end'] === '1';
    $sortKey  = isset($filters['sort'])     ? trim((string)$filters['sort'])      : 'latest';

    /* Validate license against whitelist */
    if ($license !== '' && !in_array($license, $validLicenses, true)) {
        $license = '';
    }

    /* Validate sort against whitelist */
    $orderBy = $sortMap[$sortKey] ?? $sortMap['latest'];

    try {
        $db = getDB();

        /* ── Build WHERE clause incrementally ── */
        $where  = ['g.status = ?'];
        $params = ['active'];

        /* Search */
        if ($q !== '') {
            $like = '%' . $q . '%';
            $where[]  = '(g.title LIKE ? OR g.short_description LIKE ?
                          OR g.full_description LIKE ? OR g.developer LIKE ?
                          OR g.publisher LIKE ? OR g.platform LIKE ?)';
            $params[] = $like; $params[] = $like; $params[] = $like;
            $params[] = $like; $params[] = $like; $params[] = $like;
        }

        /* License */
        if ($license !== '') {
            $where[]  = 'g.license_type = ?';
            $params[] = $license;
        }

        /* Platform (partial match — covers "Windows PC", "Linux", etc.) */
        if ($platform !== '') {
            $where[]  = 'g.platform LIKE ?';
            $params[] = '%' . $platform . '%';
        }

        /* Low-end PC */
        if ($lowEnd) {
            $where[]  = 'g.is_low_end_pc = 1';
        }

        /* Base FROM — join categories only when slug filter is active */
        $fromClause = 'FROM games g';
        if ($catSlug !== '') {
            $fromClause .= '
                INNER JOIN game_categories gc ON gc.game_id = g.id
                INNER JOIN categories       c  ON c.id = gc.category_id
                                               AND c.slug = ?
                                               AND c.status = ?';
            $params = array_merge([$catSlug, 'active'], $params);
            // params order: catSlug, 'active', then 'active' (g.status), then rest
            // Re-order: category params must come before WHERE params
            // Rebuild cleanly:
            $catParams = [$catSlug, 'active'];
        } else {
            $catParams = [];
        }

        /* Merge in correct order for INNER JOIN (join params first, then WHERE) */
        $allParams = array_merge($catParams, ['active']); // g.status = ?
        /* Re-add other WHERE params (skip the first 'active' already added) */
        $extraParams = array_slice($params, $catSlug !== '' ? 3 : 1);
        $allParams   = array_merge($catParams, ['active'], $extraParams);

        $whereStr = implode(' AND ', $where);

        /* ── COUNT query ── */
        $countSql = "SELECT COUNT(DISTINCT g.id) AS cnt
                     {$fromClause}
                     WHERE {$whereStr}";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($allParams);
        $total = (int) ($countStmt->fetchColumn() ?? 0);

        if ($total === 0) {
            return array_merge($empty, ['page' => $page, 'per_page' => $perPage]);
        }

        $totalPages = (int) ceil($total / $perPage);
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        /* ── DATA query ── */
        $dataSql = "SELECT DISTINCT g.id, g.title, g.slug, g.short_description,
                           g.cover_image, g.platform, g.game_size, g.license_type,
                           g.is_featured, g.is_trending, g.is_low_end_pc,
                           g.views_count, g.downloads_count, g.created_at
                    {$fromClause}
                    WHERE {$whereStr}
                    ORDER BY {$orderBy}
                    LIMIT {$perPage} OFFSET {$offset}";

        $dataStmt = $db->prepare($dataSql);
        $dataStmt->execute($allParams);
        $games = $dataStmt->fetchAll();

        return [
            'games'       => $games,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages,
        ];

    } catch (PDOException $ex) {
        logAppError('getGamesList() failed: ' . $ex->getMessage(), 'DB');
        return $empty;
    }
}

/* ============================================================
   getActivePlatforms()
   Returns a distinct list of platforms from active games.
   Falls back to a safe static list if DB is unavailable.

   @return array  e.g. ['Windows PC', 'Linux', ...]
   ============================================================ */
function getActivePlatforms(): array
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT DISTINCT platform FROM games WHERE status = ? ORDER BY platform ASC'
        );
        $stmt->execute(['active']);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $rows ?: [];
    } catch (PDOException $e) {
        logAppError('getActivePlatforms() failed: ' . $e->getMessage(), 'DB');
        return [];
    }
}

/* ============================================================
   buildFilterUrl()
   Builds a query string URL preserving existing filters
   while overriding a specific key.
   Safe: strips page when changing filters; keeps page for
   pagination links.

   @param  array  $current  Current filter array
   @param  array  $override Key-value pairs to override
   @return string           Full siteUrl(games.php?...) href
   ============================================================ */
function buildFilterUrl(array $current, array $override = []): string
{
    $merged = array_merge($current, $override);
    $params = [];
    $allowed = ['q', 'category', 'license', 'platform', 'low_end', 'sort', 'page'];
    foreach ($allowed as $key) {
        if (isset($merged[$key]) && $merged[$key] !== '' && $merged[$key] !== null) {
            $params[$key] = (string) $merged[$key];
        }
    }
    $qs = http_build_query($params);
    return siteUrl('games.php') . ($qs ? '?' . $qs : '');
}

/* ============================================================
   Step 8 — Category System Helpers
   ============================================================ */

/* ============================================================
   getCategoryBySlug()
   Fetches a single active category by its slug.
   Returns null if not found or inactive.

   @param  string $slug
   @return array|null
   ============================================================ */
function getCategoryBySlug(string $slug): ?array
{
    $slug = trim($slug);
    if ($slug === '') return null;
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id, name, slug, description, icon, sort_order
             FROM   categories
             WHERE  slug = ? AND status = ?
             LIMIT  1'
        );
        $stmt->execute([$slug, 'active']);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    } catch (PDOException $e) {
        logAppError('getCategoryBySlug() failed for slug "' . $slug . '": ' . $e->getMessage(), 'DB');
        return null;
    }
}

/* ============================================================
   getAllActiveCategoriesWithCounts()
   Fetches all active categories plus a count of their
   active games (LEFT JOIN — categories with 0 games included).

   @return array  Rows with additional column: game_count
   ============================================================ */
function getAllActiveCategoriesWithCounts(): array
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT c.id, c.name, c.slug, c.description, c.icon, c.sort_order,
                    COUNT(DISTINCT g.id) AS game_count
             FROM   categories c
             LEFT JOIN game_categories gc ON gc.category_id = c.id
             LEFT JOIN games g             ON g.id = gc.game_id AND g.status = ?
             WHERE  c.status = ?
             GROUP  BY c.id, c.name, c.slug, c.description, c.icon, c.sort_order
             ORDER  BY c.sort_order ASC, c.name ASC'
        );
        $stmt->execute(['active', 'active']);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getAllActiveCategoriesWithCounts() failed: ' . $e->getMessage(), 'DB');
        return [];
    }
}

/* ============================================================
   getCategoryGames()
   Returns paginated active games belonging to a category,
   with optional search, license, low-end, and sort filters.
   Uses category ID (not slug) — resolve slug → ID first.

   @param  int    $categoryId
   @param  array  $filters  [q, license, low_end, sort]
   @param  int    $page
   @param  int    $perPage
   @return array  [games, total, page, per_page, total_pages]
   ============================================================ */
function getCategoryGames(int $categoryId, array $filters = [], int $page = 1, int $perPage = 12): array
{
    $empty = ['games' => [], 'total' => 0, 'page' => 1,
              'per_page' => $perPage, 'total_pages' => 0];

    $page    = max(1, (int) $page);
    $perPage = max(1, min(60, (int) $perPage));

    /* ── Whitelists ── */
    $sortMap = [
        'latest'          => 'g.created_at DESC',
        'oldest'          => 'g.created_at ASC',
        'most_viewed'     => 'g.views_count DESC',
        'most_downloaded' => 'g.downloads_count DESC',
        'az'              => 'g.title ASC',
        'za'              => 'g.title DESC',
        'featured'        => 'g.is_featured DESC, g.created_at DESC',
        'trending'        => 'g.is_trending DESC, g.downloads_count DESC, g.created_at DESC',
    ];

    $validLicenses = [
        'freeware', 'open_source', 'demo',
        'official_mirror', 'indie_permission', 'paid_future', 'other_authorized',
    ];

    /* ── Sanitise filters ── */
    $q       = isset($filters['q'])       ? trim(mb_substr((string)$filters['q'], 0, 100)) : '';
    $license = isset($filters['license']) ? trim((string)$filters['license']) : '';
    $lowEnd  = isset($filters['low_end']) && (string)$filters['low_end'] === '1';
    $sortKey = isset($filters['sort'])    ? trim((string)$filters['sort']) : 'latest';

    if ($license !== '' && !in_array($license, $validLicenses, true)) {
        $license = '';
    }
    $orderBy = $sortMap[$sortKey] ?? $sortMap['latest'];

    try {
        $db = getDB();

        /* ── WHERE clause ── */
        $where  = ['g.status = ?', 'gc.category_id = ?'];
        $params = ['active', $categoryId];

        if ($q !== '') {
            $like = '%' . $q . '%';
            $where[] = '(g.title LIKE ? OR g.short_description LIKE ?
                         OR g.full_description LIKE ? OR g.developer LIKE ?
                         OR g.publisher LIKE ? OR g.platform LIKE ?)';
            $params[] = $like; $params[] = $like; $params[] = $like;
            $params[] = $like; $params[] = $like; $params[] = $like;
        }
        if ($license !== '') {
            $where[]  = 'g.license_type = ?';
            $params[] = $license;
        }
        if ($lowEnd) {
            $where[] = 'g.is_low_end_pc = 1';
        }

        $whereStr = implode(' AND ', $where);
        $fromSql  = 'FROM games g INNER JOIN game_categories gc ON gc.game_id = g.id';

        /* COUNT */
        $countStmt = $db->prepare(
            "SELECT COUNT(DISTINCT g.id) AS cnt {$fromSql} WHERE {$whereStr}"
        );
        $countStmt->execute($params);
        $total = (int) ($countStmt->fetchColumn() ?? 0);

        if ($total === 0) {
            return array_merge($empty, ['page' => $page, 'per_page' => $perPage]);
        }

        $totalPages = (int) ceil($total / $perPage);
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        /* DATA */
        $dataStmt = $db->prepare(
            "SELECT DISTINCT g.id, g.title, g.slug, g.short_description,
                    g.cover_image, g.platform, g.game_size, g.license_type,
                    g.is_featured, g.is_trending, g.is_low_end_pc,
                    g.views_count, g.downloads_count, g.created_at
             {$fromSql}
             WHERE {$whereStr}
             ORDER BY {$orderBy}
             LIMIT {$perPage} OFFSET {$offset}"
        );
        $dataStmt->execute($params);
        $games = $dataStmt->fetchAll();

        return [
            'games'       => $games,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages,
        ];
    } catch (PDOException $e) {
        logAppError('getCategoryGames() failed for cat #' . $categoryId . ': ' . $e->getMessage(), 'DB');
        return $empty;
    }
}

/* ============================================================
   getCategoryHighlights()
   Returns a fixed set of highlighted categories by slug.
   If a slug isn't in the DB, returns a placeholder row instead.
   Never throws or crashes.

   @return array  Array of category rows (real or placeholder)
   ============================================================ */
function getCategoryHighlights(): array
{
    $targetSlugs = [
        'low-end-pc-games'  => ['name' => 'Low-End PC Games',  'icon' => '🖥️'],
        'offline-games'     => ['name' => 'Offline Games',     'icon' => '📴'],
        'indie-games'       => ['name' => 'Indie Games',       'icon' => '🎨'],
        'open-source-games' => ['name' => 'Open Source Games', 'icon' => '📖'],
        'demo-games'        => ['name' => 'Demo Games',        'icon' => '🎯'],
    ];

    $result = [];
    try {
        $db = getDB();
        foreach ($targetSlugs as $slug => $fallback) {
            $stmt = $db->prepare(
                'SELECT id, name, slug, description, icon
                 FROM   categories
                 WHERE  slug = ? AND status = ?
                 LIMIT  1'
            );
            $stmt->execute([$slug, 'active']);
            $row = $stmt->fetch();
            if ($row !== false) {
                $result[] = $row;
            } else {
                /* Placeholder — still links to the slug even if DB is missing it */
                $result[] = [
                    'id'          => 0,
                    'name'        => $fallback['name'],
                    'slug'        => $slug,
                    'description' => null,
                    'icon'        => $fallback['icon'],
                ];
            }
        }
    } catch (PDOException $e) {
        logAppError('getCategoryHighlights() failed: ' . $e->getMessage(), 'DB');
        /* Return full placeholder list on failure */
        foreach ($targetSlugs as $slug => $fallback) {
            $result[] = [
                'id' => 0, 'name' => $fallback['name'], 'slug' => $slug,
                'description' => null, 'icon' => $fallback['icon'],
            ];
        }
    }
    return $result;
}

/* ============================================================
   getCategoryUrl()
   Returns the full URL to a single category page.

   @param  string $slug
   @return string
   ============================================================ */
function getCategoryUrl(string $slug): string
{
    return siteUrl('category.php?slug=' . rawurlencode($slug));
}

/* ============================================================
   getCategoryDirectoryUrl()
   Returns the full URL to the category directory.

   @return string
   ============================================================ */
function getCategoryDirectoryUrl(): string
{
    return siteUrl('category.php');
}

/* ============================================================
   buildCategoryFilterUrl()
   Like buildFilterUrl() but preserves slug and targets category.php.

   @param  string $slug      Current category slug
   @param  array  $current   Current filter params (q, license, low_end, sort, page)
   @param  array  $override  Params to override
   @return string
   ============================================================ */
function buildCategoryFilterUrl(string $slug, array $current, array $override = []): string
{
    $merged = array_merge($current, $override);
    $params = ['slug' => $slug];
    $allowed = ['q', 'license', 'low_end', 'sort', 'page'];
    foreach ($allowed as $key) {
        if (isset($merged[$key]) && (string)$merged[$key] !== '') {
            $params[$key] = (string)$merged[$key];
        }
    }
    $qs = http_build_query($params);
    return siteUrl('category.php') . ($qs ? '?' . $qs : '');
}

/* ============================================================
   Step 9 — Game Detail Page Helpers
   ============================================================ */

/* ============================================================
   getGameDetailsBySlug()
   Loads a complete active game row by URL slug.
   Returns null for any non-active or missing game so that
   draft/inactive/archived games are never exposed publicly.

   @param  string $slug
   @return array|null
   ============================================================ */
function getGameDetailsBySlug(string $slug): ?array
{
    $slug = trim($slug);
    if ($slug === '') return null;
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT * FROM games WHERE slug = ? AND status = ? LIMIT 1'
        );
        $stmt->execute([$slug, 'active']);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    } catch (PDOException $e) {
        logAppError('getGameDetailsBySlug() failed for "' . $slug . '": ' . $e->getMessage(), 'DB');
        return null;
    }
}

/* ============================================================
   getGameCategories()
   Active categories linked to a game, sorted by sort_order.

   @param  int $gameId
   @return array
   ============================================================ */
function getGameCategories(int $gameId): array
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT c.id, c.name, c.slug
             FROM   game_categories gc
             JOIN   categories c ON c.id = gc.category_id
             WHERE  gc.game_id = ? AND c.status = ?
             ORDER  BY c.sort_order ASC, c.name ASC'
        );
        $stmt->execute([$gameId, 'active']);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getGameCategories() failed for game #' . $gameId . ': ' . $e->getMessage(), 'DB');
        return [];
    }
}

/* ============================================================
   getGameTags()
   Active tags linked to a game.

   @param  int $gameId
   @return array
   ============================================================ */
function getGameTags(int $gameId): array
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT t.id, t.name, t.slug
             FROM   game_tags gt
             JOIN   tags t ON t.id = gt.tag_id
             WHERE  gt.game_id = ? AND t.status = ?
             ORDER  BY t.name ASC'
        );
        $stmt->execute([$gameId, 'active']);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getGameTags() failed for game #' . $gameId . ': ' . $e->getMessage(), 'DB');
        return [];
    }
}

/* ============================================================
   getGameRequirements()
   System requirements for a game (min + recommended).
   Returns null if no requirements row exists.

   @param  int $gameId
   @return array|null
   ============================================================ */
function getGameRequirements(int $gameId): ?array
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT * FROM game_requirements WHERE game_id = ? LIMIT 1'
        );
        $stmt->execute([$gameId]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    } catch (PDOException $e) {
        logAppError('getGameRequirements() failed for game #' . $gameId . ': ' . $e->getMessage(), 'DB');
        return null;
    }
}

/* ============================================================
   getGameScreenshots()
   Screenshots for a game sorted by sort_order.

   @param  int $gameId
   @return array
   ============================================================ */
function getGameScreenshots(int $gameId): array
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id, image_path, alt_text, sort_order
             FROM   game_screenshots
             WHERE  game_id = ?
             ORDER  BY sort_order ASC, id ASC'
        );
        $stmt->execute([$gameId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getGameScreenshots() failed for game #' . $gameId . ': ' . $e->getMessage(), 'DB');
        return [];
    }
}

/* ============================================================
   getGameDownloadLinksPreview()
   Active download links for display on the detail page.
   Returns only safe preview fields — external download_url
   is intentionally excluded to avoid direct exposure.
   The actual URL is looked up in download.php by link id.

   @param  int $gameId
   @return array
   ============================================================ */
function getGameDownloadLinksPreview(int $gameId): array
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id, link_title, provider_name, link_type, file_size
             FROM   download_links
             WHERE  game_id = ? AND status = ?
             ORDER  BY id ASC'
        );
        $stmt->execute([$gameId, 'active']);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getGameDownloadLinksPreview() failed for game #' . $gameId . ': ' . $e->getMessage(), 'DB');
        return [];
    }
}

/* ============================================================
   incrementGameViews()
   Increments views_count by 1. Fails silently on error.

   @param  int $gameId
   @return void
   ============================================================ */
function incrementGameViews(int $gameId): void
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'UPDATE games SET views_count = views_count + 1 WHERE id = ? AND status = ?'
        );
        $stmt->execute([$gameId, 'active']);
    } catch (PDOException $e) {
        logAppError('incrementGameViews() failed for game #' . $gameId . ': ' . $e->getMessage(), 'DB');
        /* Silently fail — page still loads */
    }
}

/* ============================================================
   getRelatedGames()
   Returns active games sharing categories with the current game.
   Excludes the current game. Falls back to latest games if no
   category match is found.

   @param  int   $gameId       Current game id to exclude
   @param  array $categoryIds  Category IDs of current game
   @param  int   $limit        Max results (default 4)
   @return array
   ============================================================ */
function getRelatedGames(int $gameId, array $categoryIds = [], int $limit = 4): array
{
    $limit = max(1, min(12, $limit));
    try {
        $db = getDB();

        if (!empty($categoryIds)) {
            /* Build safe IN clause with integer casting */
            $safeCatIds = array_map('intval', $categoryIds);
            $placeholders = implode(',', array_fill(0, count($safeCatIds), '?'));
            $params = array_merge(['active', $gameId], $safeCatIds);

            $stmt = $db->prepare(
                "SELECT DISTINCT g.id, g.title, g.slug, g.short_description,
                        g.cover_image, g.platform, g.game_size, g.license_type
                 FROM   games g
                 JOIN   game_categories gc ON gc.game_id = g.id
                 WHERE  g.status = ?
                   AND  g.id != ?
                   AND  gc.category_id IN ({$placeholders})
                 ORDER  BY g.created_at DESC
                 LIMIT  {$limit}"
            );
            $stmt->execute($params);
            $rows = $stmt->fetchAll();
            if (!empty($rows)) return $rows;
        }

        /* Fallback — latest active games excluding current */
        $stmt = $db->prepare(
            "SELECT id, title, slug, short_description, cover_image,
                    platform, game_size, license_type
             FROM   games
             WHERE  status = ? AND id != ?
             ORDER  BY created_at DESC
             LIMIT  {$limit}"
        );
        $stmt->execute(['active', $gameId]);
        return $stmt->fetchAll();

    } catch (PDOException $e) {
        logAppError('getRelatedGames() failed for game #' . $gameId . ': ' . $e->getMessage(), 'DB');
        return [];
    }
}

/* ============================================================
   getReadableLinkType()
   Converts a download link_type enum value to a readable label.

   @param  string $type
   @return string
   ============================================================ */
function getReadableLinkType(string $type): string
{
    $map = [
        'cloud'          => 'Cloud',
        'torrent'        => 'Torrent',
        'official'       => 'Official',
        'mirror'         => 'Mirror',
        'developer_site' => 'Developer Site',
        'store_link'     => 'Store Link',
    ];
    return $map[$type] ?? 'Download';
}

/* ============================================================
   getLinkTypeBadgeClass()
   Returns a badge class for a download link type.

   @param  string $type
   @return string
   ============================================================ */
function getLinkTypeBadgeClass(string $type): string
{
    $map = [
        'official'       => 'badge-primary',
        'developer_site' => 'badge-primary',
        'store_link'     => 'badge-success',
        'cloud'          => 'badge-secondary',
        'mirror'         => 'badge-secondary',
        'torrent'        => 'badge-warning',
    ];
    return $map[$type] ?? 'badge-muted';
}

/* ============================================================
   getYouTubeEmbedUrl()
   Converts a YouTube watch/short URL into a safe embed URL.
   Returns empty string if the URL is not a recognised
   YouTube domain — never embeds unknown URLs.

   Supported patterns:
     https://www.youtube.com/watch?v=VIDEO_ID
     https://youtu.be/VIDEO_ID
     https://youtube.com/shorts/VIDEO_ID

   @param  string $url  Raw trailer URL
   @return string       Safe embed URL, or '' if not YouTube
   ============================================================ */
function getYouTubeEmbedUrl(string $url): string
{
    $url   = trim($url);
    $host  = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
    $ytHosts = ['youtube.com', 'www.youtube.com', 'youtu.be', 'm.youtube.com'];

    if (!in_array($host, $ytHosts, true)) {
        return ''; /* Not a YouTube URL — never embed */
    }

    $videoId = '';

    if ($host === 'youtu.be') {
        /* https://youtu.be/VIDEO_ID */
        $path    = parse_url($url, PHP_URL_PATH) ?? '';
        $videoId = ltrim($path, '/');
    } else {
        /* watch?v=VIDEO_ID or /shorts/VIDEO_ID */
        $query = [];
        parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $query);
        if (!empty($query['v'])) {
            $videoId = $query['v'];
        } else {
            $path = parse_url($url, PHP_URL_PATH) ?? '';
            if (preg_match('#/shorts/([a-zA-Z0-9_\-]+)#', $path, $m)) {
                $videoId = $m[1];
            }
        }
    }

    /* Validate video ID — only alphanumeric, hyphens, underscores */
    if (!preg_match('/^[a-zA-Z0-9_\-]{5,20}$/', $videoId)) {
        return '';
    }

    return 'https://www.youtube.com/embed/' . $videoId
         . '?rel=0&modestbranding=1';
}

/* ============================================================
   Step 10 — Safe Download Link System Helpers
   ============================================================ */

/* ============================================================
   getDownloadLinkDetails()
   Loads one active download link joined with its active game.
   Returns null if the link is inactive/missing, or if the
   related game is not active.
   SECURITY: does NOT expose download_url here — caller decides
             whether to use it.

   @param  int $linkId
   @return array|null
   ============================================================ */
function getDownloadLinkDetails(int $linkId): ?array
{
    if ($linkId <= 0) return null;
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT
                dl.id,
                dl.game_id,
                dl.link_title,
                dl.provider_name,
                dl.download_url,
                dl.link_type,
                dl.file_size,
                dl.status         AS link_status,
                dl.clicks_count,
                g.title           AS game_title,
                g.slug            AS game_slug,
                g.short_description AS game_desc,
                g.cover_image     AS game_cover,
                g.platform        AS game_platform,
                g.license_type    AS game_license,
                g.game_size       AS game_size,
                g.downloads_count AS game_downloads,
                g.status          AS game_status
             FROM   download_links dl
             JOIN   games g ON g.id = dl.game_id
             WHERE  dl.id = ?
               AND  dl.status = ?
               AND  g.status  = ?
             LIMIT  1'
        );
        $stmt->execute([$linkId, 'active', 'active']);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    } catch (PDOException $e) {
        logAppError('getDownloadLinkDetails() failed for link #' . $linkId . ': ' . $e->getMessage(), 'DB');
        return null;
    }
}

/* ============================================================
   getDownloadLinkAnyStatus()
   Loads a download link and its active game regardless of the
   link's own status. Used to show better unavailable-link error
   pages (e.g. "this link is broken, not that it doesn't exist").
   SECURITY: does NOT expose download_url in the return.

   @param  int $linkId
   @return array|null  null if game is inactive or link missing
   ============================================================ */
function getDownloadLinkAnyStatus(int $linkId): ?array
{
    if ($linkId <= 0) return null;
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT
                dl.id,
                dl.game_id,
                dl.link_title,
                dl.provider_name,
                dl.link_type,
                dl.file_size,
                dl.status         AS link_status,
                g.title           AS game_title,
                g.slug            AS game_slug,
                g.status          AS game_status
             FROM   download_links dl
             JOIN   games g ON g.id = dl.game_id
             WHERE  dl.id = ?
               AND  g.status = ?
             LIMIT  1'
        );
        $stmt->execute([$linkId, 'active']);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    } catch (PDOException $e) {
        logAppError('getDownloadLinkAnyStatus() failed for link #' . $linkId . ': ' . $e->getMessage(), 'DB');
        return null;
    }
}

/* ============================================================
   validateStoredDownloadUrl()
   Validates that a stored download URL is safe to redirect to.

   Allowed schemes: http, https
   Allowed for torrent link type only: magnet
   Rejected: javascript:, data:, file:, ftp:, vbscript:, etc.

   @param  string      $url       The stored URL to validate
   @param  string|null $linkType  Optional — pass 'torrent' to
                                  allow magnet: URIs
   @return bool
   ============================================================ */
function validateStoredDownloadUrl(string $url, ?string $linkType = null): bool
{
    $url = trim($url);
    if ($url === '') return false;

    $scheme = strtolower(parse_url($url, PHP_URL_SCHEME) ?? '');

    /* Always allow standard web URLs */
    if (in_array($scheme, ['http', 'https'], true)) {
        /* Additional safety: ensure it parses as a real URL */
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /* Allow magnet links ONLY when link_type is explicitly 'torrent' */
    if ($scheme === 'magnet' && $linkType === 'torrent') {
        /* Magnet links follow magnet:?xt=... pattern */
        return (bool) preg_match('/^magnet:\?/i', $url);
    }

    /* All other schemes are rejected */
    return false;
}

/* ============================================================
   incrementDownloadCounters()
   Increments both games.downloads_count and
   download_links.clicks_count safely.
   Fails silently and logs errors so the redirect still works.

   @param  int $gameId
   @param  int $linkId
   @return bool  true if both increments succeeded
   ============================================================ */
function incrementDownloadCounters(int $gameId, int $linkId): bool
{
    $ok = true;
    try {
        $db = getDB();

        $s1 = $db->prepare(
            'UPDATE games SET downloads_count = downloads_count + 1
             WHERE  id = ? AND status = ?'
        );
        if (!$s1->execute([$gameId, 'active'])) $ok = false;

        $s2 = $db->prepare(
            'UPDATE download_links SET clicks_count = clicks_count + 1
             WHERE  id = ?'
        );
        if (!$s2->execute([$linkId])) $ok = false;

    } catch (PDOException $e) {
        logAppError(
            'incrementDownloadCounters() failed game #' . $gameId .
            ' link #' . $linkId . ': ' . $e->getMessage(),
            'DB'
        );
        $ok = false;
    }
    return $ok;
}

/* ============================================================
   createDownloadToken()
   Generates a CSRF-style one-time token for the continue
   action. Stored in $_SESSION keyed by link ID.
   Session must already be started before calling this.

   @param  int $linkId
   @return string  Hex-encoded token
   ============================================================ */
function createDownloadToken(int $linkId): string
{
    $token = bin2hex(random_bytes(32));
    $_SESSION['dl_token_' . $linkId] = $token;
    $_SESSION['dl_token_ts_' . $linkId] = time(); /* timestamp for TTL check */
    return $token;
}

/* ============================================================
   validateDownloadToken()
   Checks the provided token against the session value.
   Token expires after 30 minutes (1800 seconds) for safety.
   Uses hash_equals() to prevent timing attacks.

   @param  int    $linkId
   @param  string $token   Token from GET parameter
   @return bool
   ============================================================ */
function validateDownloadToken(int $linkId, string $token): bool
{
    $sessionKey = 'dl_token_' . $linkId;
    $tsKey      = 'dl_token_ts_' . $linkId;

    if (empty($_SESSION[$sessionKey]) || empty($_SESSION[$tsKey])) {
        return false;
    }

    /* Token TTL: 30 minutes */
    if ((time() - (int)$_SESSION[$tsKey]) > 1800) {
        clearDownloadToken($linkId);
        return false;
    }

    /* Constant-time comparison */
    return hash_equals($_SESSION[$sessionKey], $token);
}

/* ============================================================
   clearDownloadToken()
   Removes the token from session after use or on failure.

   @param  int $linkId
   @return void
   ============================================================ */
function clearDownloadToken(int $linkId): void
{
    unset($_SESSION['dl_token_' . $linkId]);
    unset($_SESSION['dl_token_ts_' . $linkId]);
}

/* ============================================================
   Step 11 — Analytics Polish Helpers
   ============================================================ */

/* ============================================================
   incrementGameViews() — IMPROVED VERSION (replaces Step 9)
   Increments views_count with a 30-minute session cooldown
   to prevent aggressive repeat-refresh inflation.

   Session key: qmgames_viewed_games[$gameId] = timestamp

   @param  int $gameId
   @return void
   ============================================================ */
function incrementGameViewsWithCooldown(int $gameId): void
{
    if ($gameId <= 0) return;

    /* Ensure session is running */
    if (session_status() === PHP_SESSION_NONE) {
        try { session_start(); } catch (\Throwable $ex) { /* ignore */ }
    }

    $cooldown = 1800; /* 30 minutes in seconds */
    $key      = 'qmgames_viewed_games';
    $now      = time();

    /* Check whether cooldown has passed for this game */
    $lastViewed = $_SESSION[$key][$gameId] ?? 0;
    if (($now - $lastViewed) < $cooldown) {
        return; /* Still within cooldown — do not increment */
    }

    /* Update cooldown timestamp before DB call to avoid race
       conditions on slow connections */
    $_SESSION[$key][$gameId] = $now;

    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'UPDATE games SET views_count = views_count + 1
             WHERE  id = ? AND status = ?'
        );
        $stmt->execute([$gameId, 'active']);
    } catch (PDOException $e) {
        /* Roll back session timestamp if DB update fails */
        $_SESSION[$key][$gameId] = $lastViewed;
        logAppError('incrementGameViewsWithCooldown() failed for game #' . $gameId . ': ' . $e->getMessage(), 'DB');
    }
}

/* ============================================================
   incrementDownloadCounters() — IMPROVED VERSION (replaces Step 10)
   Wraps both counter updates in a transaction so they succeed
   or fail together. Falls back to individual updates if
   transactions are unavailable (very rare with InnoDB).

   @param  int $gameId
   @param  int $linkId
   @return bool
   ============================================================ */
function incrementDownloadCountersSafe(int $gameId, int $linkId): bool
{
    if ($gameId <= 0 || $linkId <= 0) return false;
    try {
        $db = getDB();
        $db->beginTransaction();

        $s1 = $db->prepare(
            'UPDATE games SET downloads_count = downloads_count + 1
             WHERE  id = ? AND status = ?'
        );
        $s1->execute([$gameId, 'active']);

        $s2 = $db->prepare(
            'UPDATE download_links SET clicks_count = clicks_count + 1
             WHERE  id = ?'
        );
        $s2->execute([$linkId]);

        $db->commit();
        return true;
    } catch (PDOException $e) {
        try { $db->rollBack(); } catch (\Throwable $ex) {}
        logAppError(
            'incrementDownloadCountersSafe() failed game #' . $gameId .
            ' link #' . $linkId . ': ' . $e->getMessage(),
            'DB'
        );
        return false;
    }
}

/* ============================================================
   logDownloadEvent()
   Logs a successful download redirect to download_events table.
   Hashes IP and UA with sha256 + APP_SALT for privacy.
   Silently fails if table doesn't exist or DB is unavailable.
   Requires download_events table from step_11_analytics.sql.

   @param  int $gameId
   @param  int $linkId
   @return bool
   ============================================================ */
function logDownloadEvent(int $gameId, int $linkId): bool
{
    if ($gameId <= 0 || $linkId <= 0) return false;

    $salt = defined('APP_SALT') ? APP_SALT : 'default_salt';

    /* Hash IP — never store raw */
    $rawIp   = $_SERVER['REMOTE_ADDR'] ?? '';
    $ipHash  = ($rawIp !== '') ? hash('sha256', $rawIp . $salt) : null;

    /* Hash user agent — never store raw */
    $rawUa   = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $uaHash  = ($rawUa !== '') ? hash('sha256', $rawUa . $salt) : null;

    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'INSERT INTO download_events
               (game_id, download_link_id, event_type, ip_hash, user_agent_hash)
             VALUES (?, ?, ?, ?, ?)'
        );
        return $stmt->execute([$gameId, $linkId, 'download_redirect', $ipHash, $uaHash]);
    } catch (PDOException $e) {
        /* Silently fail — table may not exist yet in all environments */
        logAppError('logDownloadEvent() failed game #' . $gameId . ': ' . $e->getMessage(), 'DB');
        return false;
    }
}

/* ============================================================
   formatNumberShort()
   Converts an integer to a compact human-readable string.
   Examples: 0→0, 999→999, 1200→1.2K, 15000→15K, 1200000→1.2M

   @param  int $n
   @return string
   ============================================================ */
function formatNumberShort(int $n): string
{
    if ($n < 0)       return '0';
    if ($n < 1000)    return (string) $n;
    if ($n < 10000)   return rtrim(rtrim(number_format($n / 1000, 1), '0'), '.') . 'K';
    if ($n < 1000000) return rtrim(rtrim(number_format($n / 1000, 0), '0'), '.') . 'K';
    return rtrim(rtrim(number_format($n / 1000000, 1), '0'), '.') . 'M';
}

/* ============================================================
   getPopularGames()
   Active games ranked by downloads_count DESC, then views_count.

   @param  int $limit
   @return array
   ============================================================ */
function getPopularGames(int $limit = 4): array
{
    $limit = max(1, min(20, $limit));
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id, title, slug, short_description, cover_image,
                    platform, game_size, license_type, downloads_count, views_count
             FROM   games
             WHERE  status = ?
             ORDER  BY downloads_count DESC, views_count DESC, created_at DESC
             LIMIT  ' . $limit
        );
        $stmt->execute(['active']);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getPopularGames() failed: ' . $e->getMessage(), 'DB');
        return [];
    }
}

/* ============================================================
   getMostViewedGames()
   Active games ranked by views_count DESC.

   @param  int $limit
   @return array
   ============================================================ */
function getMostViewedGames(int $limit = 4): array
{
    $limit = max(1, min(20, $limit));
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id, title, slug, short_description, cover_image,
                    platform, game_size, license_type, views_count, downloads_count
             FROM   games
             WHERE  status = ?
             ORDER  BY views_count DESC, downloads_count DESC, created_at DESC
             LIMIT  ' . $limit
        );
        $stmt->execute(['active']);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getMostViewedGames() failed: ' . $e->getMessage(), 'DB');
        return [];
    }
}

/* ============================================================
   getMostDownloadedGames()
   Active games ranked by downloads_count DESC.

   @param  int $limit
   @return array
   ============================================================ */
function getMostDownloadedGames(int $limit = 4): array
{
    $limit = max(1, min(20, $limit));
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id, title, slug, short_description, cover_image,
                    platform, game_size, license_type, downloads_count, views_count
             FROM   games
             WHERE  status = ?
             ORDER  BY downloads_count DESC, views_count DESC, created_at DESC
             LIMIT  ' . $limit
        );
        $stmt->execute(['active']);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getMostDownloadedGames() failed: ' . $e->getMessage(), 'DB');
        return [];
    }
}

/* ============================================================
   Step 12 — Broken Link Report System Helpers
   ============================================================ */

/* ============================================================
   startSafeSession()
   Starts PHP session safely if not already active.
   Sets secure cookie options. Never crashes the page.

   @return void
   ============================================================ */
function startSafeSession(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return; /* Already running */
    }
    if (headers_sent()) {
        logAppError('startSafeSession() called after headers sent.', 'SESSION');
        return;
    }
    try {
        session_set_cookie_params([
            'lifetime' => defined('SESSION_LIFETIME') ? SESSION_LIFETIME : 3600,
            'path'     => '/',
            'secure'   => false,  /* Set true in production with HTTPS */
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    } catch (\Throwable $e) {
        logAppError('startSafeSession() failed: ' . $e->getMessage(), 'SESSION');
    }
}

/* ============================================================
   generateCsrfToken()
   Creates a random CSRF token and stores it in $_SESSION.
   The $key allows multiple independent tokens on one page.

   @param  string $key  Session key suffix (default 'default')
   @return string       Hex-encoded 32-byte token
   ============================================================ */
function generateCsrfToken(string $key = 'default'): string
{
    startSafeSession();
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_' . $key] = $token;
    return $token;
}

/* ============================================================
   validateCsrfToken()
   Validates a submitted CSRF token using hash_equals().
   Consumes token after successful validation (one-time).

   @param  string $token  Token from POST form field
   @param  string $key    Must match the key used in generateCsrfToken()
   @return bool
   ============================================================ */
function validateCsrfToken(string $token, string $key = 'default'): bool
{
    startSafeSession();
    $sessionKey = 'csrf_' . $key;
    if (empty($_SESSION[$sessionKey]) || $token === '') {
        return false;
    }
    $valid = hash_equals($_SESSION[$sessionKey], $token);
    if ($valid) {
        unset($_SESSION[$sessionKey]); /* One-time use */
    }
    return $valid;
}

/* ============================================================
   getPublicGameById()
   Fetches one active (public) game by integer ID.
   Returns null if not found or not active — never exposes
   draft/inactive/archived games.

   @param  int $gameId
   @return array|null
   ============================================================ */
function getPublicGameById(int $gameId): ?array
{
    if ($gameId <= 0) return null;
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id, title, slug, short_description, cover_image,
                    platform, license_type, game_size
             FROM   games
             WHERE  id = ? AND status = ?
             LIMIT  1'
        );
        $stmt->execute([$gameId, 'active']);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    } catch (PDOException $e) {
        logAppError('getPublicGameById() failed for #' . $gameId . ': ' . $e->getMessage(), 'DB');
        return null;
    }
}

/* ============================================================
   getPublicDownloadLinkById()
   Fetches a download link joined with its active game.
   Does NOT expose download_url — safe for public forms.

   @param  int $linkId
   @return array|null
   ============================================================ */
function getPublicDownloadLinkById(int $linkId): ?array
{
    if ($linkId <= 0) return null;
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT dl.id,
                    dl.game_id,
                    dl.link_title,
                    dl.provider_name,
                    dl.link_type,
                    dl.file_size,
                    dl.status        AS link_status,
                    g.id             AS game_id_check,
                    g.title          AS game_title,
                    g.slug           AS game_slug
             FROM   download_links dl
             JOIN   games g ON g.id = dl.game_id
             WHERE  dl.id = ? AND g.status = ?
             LIMIT  1'
        );
        $stmt->execute([$linkId, 'active']);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    } catch (PDOException $e) {
        logAppError('getPublicDownloadLinkById() failed for #' . $linkId . ': ' . $e->getMessage(), 'DB');
        return null;
    }
}

/* ============================================================
   getPublicDownloadLinkForGame()
   Confirms a specific link belongs to a specific active game.
   Used to prevent cross-game report injection.

   @param  int $gameId
   @param  int $linkId
   @return array|null  Link row or null if mismatch/inactive
   ============================================================ */
function getPublicDownloadLinkForGame(int $gameId, int $linkId): ?array
{
    if ($gameId <= 0 || $linkId <= 0) return null;
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT dl.id, dl.game_id, dl.link_title, dl.provider_name,
                    dl.link_type, dl.file_size, dl.status AS link_status
             FROM   download_links dl
             JOIN   games g ON g.id = dl.game_id
             WHERE  dl.id = ? AND dl.game_id = ? AND g.status = ?
             LIMIT  1'
        );
        $stmt->execute([$linkId, $gameId, 'active']);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    } catch (PDOException $e) {
        logAppError('getPublicDownloadLinkForGame() failed: ' . $e->getMessage(), 'DB');
        return null;
    }
}

/* ============================================================
   getReportTypeOptions()
   Returns the safe whitelist of report_type enum values
   and their human-readable labels.

   @return array  ['value' => 'Label', ...]
   ============================================================ */
function getReportTypeOptions(): array
{
    return [
        'broken_link'        => 'Broken Link',
        'wrong_file'         => 'Wrong File',
        'slow_download'      => 'Slow Download',
        'password_issue'     => 'Password Issue',
        'unsafe_file_concern'=> 'Unsafe File Concern',
        'other'              => 'Other',
    ];
}

/* ============================================================
   cleanReportMessage()
   Trims, truncates, and strips HTML from a report message.
   Output is stored as plain text; use e() when rendering.

   @param  string $msg
   @return string
   ============================================================ */
function cleanReportMessage(string $msg): string
{
    $msg = trim($msg);
    $msg = strip_tags($msg);               /* Remove any HTML tags */
    $msg = mb_substr($msg, 0, 2000, 'UTF-8'); /* Hard cap at 2000 chars */
    return $msg;
}

/* ============================================================
   submitDownloadReport()
   Inserts a validated report into the download_reports table.
   game_id is required (NOT NULL in schema).
   download_link_id is nullable.

   @param  array $data  [game_id, download_link_id, report_type, message, user_email]
   @return bool
   ============================================================ */
function submitDownloadReport(array $data): bool
{
    $gameId   = isset($data['game_id'])          ? (int)$data['game_id']          : 0;
    $linkId   = isset($data['download_link_id']) ? (int)$data['download_link_id'] : null;
    $type     = trim((string)($data['report_type'] ?? ''));
    $message  = isset($data['message'])    ? cleanReportMessage((string)$data['message']) : null;
    $email    = isset($data['user_email']) ? trim((string)$data['user_email'])            : null;

    /* game_id is NOT NULL in schema */
    if ($gameId <= 0) {
        logAppError('submitDownloadReport() called with invalid game_id.', 'REPORT');
        return false;
    }

    /* Whitelist report_type */
    $validTypes = array_keys(getReportTypeOptions());
    if (!in_array($type, $validTypes, true)) {
        logAppError('submitDownloadReport() invalid report_type: ' . $type, 'REPORT');
        return false;
    }

    /* Clean nullables */
    if ($linkId !== null && $linkId <= 0) $linkId = null;
    if ($email !== null && $email === '')  $email  = null;
    if ($message !== null && $message === '') $message = null;

    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'INSERT INTO download_reports
               (game_id, download_link_id, report_type, message, user_email, status)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        return $stmt->execute([
            $gameId,
            $linkId,
            $type,
            $message,
            $email,
            'pending',
        ]);
    } catch (PDOException $e) {
        logAppError('submitDownloadReport() DB insert failed: ' . $e->getMessage(), 'DB');
        return false;
    }
}

/* ============================================================
   Step 13 — Search System Helpers
   ============================================================ */

/* ============================================================
   sanitizeSearchQuery()
   Cleans a raw search string for safe use in search forms.
   Does NOT escape for SQL — that's handled by PDO bindings.
   Escaping for HTML output is done separately with e().

   @param  string $query  Raw input
   @return string         Cleaned query (max 100 chars)
   ============================================================ */
function sanitizeSearchQuery(string $query): string
{
    $query = trim($query);
    /* Remove non-printable / control characters */
    $query = preg_replace('/[\x00-\x1F\x7F]/u', '', $query);
    /* Limit length */
    return mb_substr($query, 0, 100, 'UTF-8');
}

/* ============================================================
   getSearchFiltersFromRequest()
   Reads, sanitises, and whitelists all search GET parameters.
   Returns a safe filter array ready for searchGames().

   @return array  [q, category, license, platform, low_end, sort, page]
   ============================================================ */
function getSearchFiltersFromRequest(): array
{
    $validLicenses = [
        'freeware','open_source','demo',
        'official_mirror','indie_permission','paid_future','other_authorized',
    ];
    $validSorts = [
        'relevance','latest','oldest','most_viewed','most_downloaded',
        'az','za','featured','trending',
    ];

    $q        = sanitizeSearchQuery((string)($_GET['q'] ?? ''));
    $category = preg_replace('/[^a-z0-9\-]/', '', strtolower(trim((string)($_GET['category'] ?? ''))));
    $license  = trim((string)($_GET['license'] ?? ''));
    $platform = trim(mb_substr((string)($_GET['platform'] ?? ''), 0, 80));
    $lowEnd   = (isset($_GET['low_end']) && $_GET['low_end'] === '1') ? '1' : '';
    $sort     = trim((string)($_GET['sort'] ?? 'relevance'));
    $page     = max(1, (int)($_GET['page'] ?? 1));

    if (!in_array($license, $validLicenses, true)) $license = '';
    if (!in_array($sort,    $validSorts,    true)) $sort    = 'relevance';

    return compact('q','category','license','platform','lowEnd','sort','page');
}

/* ============================================================
   searchGames()
   Full-text search across games, categories, and tags.
   Supports relevance sorting with title-first priority.
   Uses LEFT JOINs + GROUP BY to avoid duplicates from
   multi-row joins.

   @param  array $filters   From getSearchFiltersFromRequest()
   @param  int   $page
   @param  int   $perPage
   @return array  [games, total, page, per_page, total_pages]
   ============================================================ */
function searchGames(array $filters = [], int $page = 1, int $perPage = 12): array
{
    $empty = ['games'=>[], 'total'=>0, 'page'=>1, 'per_page'=>$perPage, 'total_pages'=>0];
    $page    = max(1, (int)$page);
    $perPage = max(1, min(60, (int)$perPage));

    /* ── Sort map ── */
    $sortMap = [
        'latest'          => 'g.created_at DESC',
        'oldest'          => 'g.created_at ASC',
        'most_viewed'     => 'g.views_count DESC, g.created_at DESC',
        'most_downloaded' => 'g.downloads_count DESC, g.created_at DESC',
        'az'              => 'g.title ASC',
        'za'              => 'g.title DESC',
        'featured'        => 'g.is_featured DESC, g.created_at DESC',
        'trending'        => 'g.is_trending DESC, g.downloads_count DESC, g.created_at DESC',
    ];

    /* ── Whitelist ── */
    $validLicenses = [
        'freeware','open_source','demo',
        'official_mirror','indie_permission','paid_future','other_authorized',
    ];

    /* ── Extract filters ── */
    $q        = sanitizeSearchQuery((string)($filters['q'] ?? ''));
    $catSlug  = trim((string)($filters['category'] ?? ''));
    $license  = trim((string)($filters['license']  ?? ''));
    $platform = trim((string)($filters['platform'] ?? ''));
    $lowEnd   = ($filters['lowEnd'] ?? '') === '1' || ($filters['low_end'] ?? '') === '1';
    $sortKey  = trim((string)($filters['sort'] ?? 'relevance'));

    if (!in_array($license, $validLicenses, true)) $license = '';
    $orderBy = $sortMap[$sortKey] ?? null;

    try {
        $db = getDB();

        /* ── Base FROM with optional JOINs ── */
        /* Always LEFT JOIN categories and tags for search + category filter */
        $from = 'FROM games g
                 LEFT JOIN game_categories gc ON gc.game_id = g.id
                 LEFT JOIN categories c        ON c.id = gc.category_id
                                              AND c.status = ?
                 LEFT JOIN game_tags gt         ON gt.game_id = g.id
                 LEFT JOIN tags t               ON t.id = gt.tag_id
                                              AND t.status = ?';
        $params = ['active', 'active']; /* for category status + tag status */

        /* ── WHERE ── */
        $where = ['g.status = ?'];
        $params[] = 'active';

        /* Keyword search */
        if ($q !== '') {
            $like = '%' . $q . '%';
            $where[] = '(g.title LIKE ?
                         OR g.short_description LIKE ?
                         OR g.full_description LIKE ?
                         OR g.developer LIKE ?
                         OR g.publisher LIKE ?
                         OR g.platform LIKE ?
                         OR c.name LIKE ?
                         OR t.name LIKE ?)';
            for ($i = 0; $i < 8; $i++) $params[] = $like;
        }

        /* Category filter */
        if ($catSlug !== '') {
            $where[]  = 'c.slug = ?';
            $params[] = $catSlug;
        }

        /* License filter */
        if ($license !== '') {
            $where[]  = 'g.license_type = ?';
            $params[] = $license;
        }

        /* Platform filter */
        if ($platform !== '') {
            $where[]  = 'g.platform LIKE ?';
            $params[] = '%' . $platform . '%';
        }

        /* Low-end filter */
        if ($lowEnd) {
            $where[] = 'g.is_low_end_pc = 1';
        }

        $whereStr = implode(' AND ', $where);

        /* ── COUNT (use DISTINCT to handle JOIN duplicates) ── */
        $countSql = "SELECT COUNT(DISTINCT g.id) AS cnt {$from} WHERE {$whereStr}";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)($countStmt->fetchColumn() ?? 0);

        if ($total === 0) {
            return array_merge($empty, ['page'=>$page, 'per_page'=>$perPage]);
        }

        $totalPages = (int)ceil($total / $perPage);
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        /* ── ORDER BY ── */
        if ($orderBy === null) {
            /* Relevance sort */
            if ($q !== '') {
                $exactLike  = $q . '%';    /* starts with */
                $anyLike    = '%' . $q . '%'; /* anywhere */
                $relevanceSql = "ORDER BY
                    CASE
                        WHEN g.title LIKE ? THEN 1
                        WHEN g.title LIKE ? THEN 2
                        WHEN c.name  LIKE ? THEN 3
                        WHEN t.name  LIKE ? THEN 4
                        WHEN g.short_description LIKE ? THEN 5
                        ELSE 6
                    END,
                    g.is_featured DESC,
                    g.is_trending DESC,
                    g.downloads_count DESC,
                    g.views_count DESC,
                    g.created_at DESC";
                /* Append relevance params */
                $sortParams = [$exactLike, $anyLike, $anyLike, $anyLike, $anyLike];
            } else {
                $relevanceSql = 'ORDER BY g.created_at DESC';
                $sortParams   = [];
            }
        } else {
            $relevanceSql = 'ORDER BY ' . $orderBy;
            $sortParams   = [];
        }

        /* ── DATA query ── */
        $dataSql = "SELECT DISTINCT g.id, g.title, g.slug, g.short_description,
                           g.cover_image, g.platform, g.game_size, g.license_type,
                           g.is_featured, g.is_trending, g.is_low_end_pc,
                           g.views_count, g.downloads_count, g.created_at
                    {$from}
                    WHERE {$whereStr}
                    GROUP BY g.id
                    {$relevanceSql}
                    LIMIT {$perPage} OFFSET {$offset}";

        $dataParams = array_merge($params, $sortParams);
        $dataStmt   = $db->prepare($dataSql);
        $dataStmt->execute($dataParams);
        $games = $dataStmt->fetchAll();

        return [
            'games'       => $games,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages,
        ];

    } catch (PDOException $ex) {
        logAppError('searchGames() failed: ' . $ex->getMessage(), 'DB');
        return $empty;
    }
}

/* ============================================================
   getSearchSuggestions()
   Returns quick-start search chips for the empty search state.

   @return array  Each item: ['label', 'url']
   ============================================================ */
function getSearchSuggestions(): array
{
    return [
        ['label' => '🏎️ Racing',      'url' => siteUrl('search.php?q=racing')],
        ['label' => '⚔️ Action',       'url' => siteUrl('search.php?q=action')],
        ['label' => '🎯 Demo',         'url' => siteUrl('search.php?license=demo')],
        ['label' => '📖 Open Source',  'url' => siteUrl('search.php?license=open_source')],
        ['label' => '🖥️ Low-End PC',   'url' => siteUrl('search.php?low_end=1')],
        ['label' => '📴 Offline',      'url' => siteUrl('search.php?q=offline')],
        ['label' => '🎨 Indie',        'url' => siteUrl('search.php?license=indie_permission')],
        ['label' => '♟️ Strategy',     'url' => siteUrl('search.php?q=strategy')],
    ];
}

/* ============================================================
   buildSearchUrl()
   Like buildFilterUrl() but targets search.php.

   @param  array $current  Current filter params
   @param  array $override Key-value pairs to override
   @return string
   ============================================================ */
function buildSearchUrl(array $current, array $override = []): string
{
    $merged  = array_merge($current, $override);
    $allowed = ['q', 'category', 'license', 'platform', 'low_end', 'sort', 'page'];
    $params  = [];
    foreach ($allowed as $key) {
        if (isset($merged[$key]) && (string)$merged[$key] !== '') {
            $params[$key] = (string)$merged[$key];
        }
    }
    $qs = http_build_query($params);
    return siteUrl('search.php') . ($qs ? '?' . $qs : '');
}

/* ============================================================
   Step 15 — Contact Page Helpers
   ============================================================ */

/* ============================================================
   getContactSetting()
   Fetches a contact-related setting from site_settings.
   Wraps getSetting() with a sensible default.

   @param  string $key      e.g. 'site_email'
   @param  string $default  Fallback text
   @return string
   ============================================================ */
function getContactSetting(string $key, string $default = 'Coming soon'): string
{
    $val = getSetting($key, $default);
    return (is_string($val) && trim($val) !== '') ? $val : $default;
}

/* ============================================================
   getContactCards()
   Returns an array of contact detail cards for display.
   Values come from site_settings; default to 'Coming soon'.

   @return array  Each item: [icon, label, value]
   ============================================================ */
function getContactCards(): array
{
    return [
        ['icon' => '📧', 'label' => 'Email',    'value' => getContactSetting('site_email')],
        ['icon' => '💬', 'label' => 'WhatsApp', 'value' => getContactSetting('site_whatsapp')],
        ['icon' => '▶️', 'label' => 'YouTube',  'value' => getContactSetting('site_youtube')],
        ['icon' => '🌐', 'label' => 'Website',  'value' => getContactSetting('site_website')],
    ];
}

/* ============================================================
   validateContactForm()
   Validates all contact form POST fields.
   Returns ['valid' => bool, 'errors' => [], 'data' => []]
   The 'data' array contains plain (un-HTML-escaped) trimmed values
   ready for database insertion via prepared statements.

   @param  array $post  Raw $_POST values
   @return array
   ============================================================ */
function validateContactForm(array $post): array
{
    $errors = [];
    $data   = [];

    /* ── Name ── */
    $name = trim((string)($post['name'] ?? ''));
    if ($name === '') {
        $errors[] = 'Please enter your name.';
    } elseif (mb_strlen($name, 'UTF-8') < 2) {
        $errors[] = 'Name must be at least 2 characters.';
    } else {
        $name = mb_substr($name, 0, 120, 'UTF-8');
        $data['name'] = $name;
    }

    /* ── Email ── */
    $email = trim((string)($post['email'] ?? ''));
    if ($email === '') {
        $errors[] = 'Please enter a valid email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
        $errors[] = 'Please enter a valid email address.';
    } else {
        $data['email'] = $email;
    }

    /* ── Subject ── */
    $subject = trim((string)($post['subject'] ?? ''));
    if ($subject === '') {
        $errors[] = 'Please enter a subject.';
    } elseif (mb_strlen($subject, 'UTF-8') < 3) {
        $errors[] = 'Subject must be at least 3 characters.';
    } else {
        $subject      = mb_substr($subject, 0, 200, 'UTF-8');
        $data['subject'] = $subject;
    }

    /* ── Message ── */
    $message = trim((string)($post['message'] ?? ''));
    if ($message === '') {
        $errors[] = 'Please enter your message.';
    } elseif (mb_strlen($message, 'UTF-8') < 10) {
        $errors[] = 'Message must be at least 10 characters.';
    } elseif (mb_strlen($message, 'UTF-8') > 3000) {
        $errors[] = 'Message is too long. Please keep it under 3000 characters.';
    } else {
        /* Strip tags — store plain text only */
        $message = strip_tags($message);
        $message = mb_substr($message, 0, 3000, 'UTF-8');
        $data['message'] = $message;
    }

    return [
        'valid'  => empty($errors),
        'errors' => $errors,
        'data'   => $data,
    ];
}

/* ============================================================
   submitContactMessage()
   Inserts a validated contact message into contact_messages.

   @param  array $data  [name, email, subject, message]
   @return bool
   ============================================================ */
function submitContactMessage(array $data): bool
{
    $name    = trim((string)($data['name']    ?? ''));
    $email   = trim((string)($data['email']   ?? ''));
    $subject = trim((string)($data['subject'] ?? ''));
    $message = trim((string)($data['message'] ?? ''));

    if ($name === '' || $email === '' || $subject === '' || $message === '') {
        logAppError('submitContactMessage() called with incomplete data.', 'CONTACT');
        return false;
    }

    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'INSERT INTO contact_messages (name, email, subject, message, status)
             VALUES (?, ?, ?, ?, ?)'
        );
        return $stmt->execute([$name, $email, $subject, $message, 'new']);
    } catch (PDOException $e) {
        logAppError('submitContactMessage() DB insert failed: ' . $e->getMessage(), 'DB');
        return false;
    }
}

/* ============================================================
   Step 20 — Admin Dashboard Helpers
   ============================================================ */

/* ============================================================
   getAdminDashboardStats()
   Returns all dashboard counts in one efficient query set.
   Uses separate COUNT queries for accuracy and safety.
   Returns safe defaults on any failure.

   @return array
   ============================================================ */
function getAdminDashboardStats(): array
{
    $defaults = [
        'total_games'         => 0,
        'active_games'        => 0,
        'draft_games'         => 0,
        'inactive_games'      => 0,
        'total_categories'    => 0,
        'total_download_links'=> 0,
        'total_downloads'     => 0,
        'total_views'         => 0,
        'pending_reports'     => 0,
        'new_messages'        => 0,
        'featured_games'      => 0,
        'trending_games'      => 0,
        'low_end_games'       => 0,
    ];

    try {
        $db = getDB();

        /* All counts in a single multi-query to minimise DB round-trips */
        $row = $db->query(
            'SELECT
               COUNT(*)                                        AS total_games,
               SUM(status = \'active\')                       AS active_games,
               SUM(status = \'draft\')                        AS draft_games,
               SUM(status = \'inactive\' OR status = \'archived\') AS inactive_games,
               SUM(is_featured = 1)                           AS featured_games,
               SUM(is_trending = 1)                           AS trending_games,
               SUM(is_low_end_pc = 1)                         AS low_end_games,
               COALESCE(SUM(downloads_count), 0)              AS total_downloads,
               COALESCE(SUM(views_count), 0)                  AS total_views
             FROM games'
        )->fetch();

        if ($row) {
            $defaults['total_games']    = (int)$row['total_games'];
            $defaults['active_games']   = (int)$row['active_games'];
            $defaults['draft_games']    = (int)$row['draft_games'];
            $defaults['inactive_games'] = (int)$row['inactive_games'];
            $defaults['featured_games'] = (int)$row['featured_games'];
            $defaults['trending_games'] = (int)$row['trending_games'];
            $defaults['low_end_games']  = (int)$row['low_end_games'];
            $defaults['total_downloads']= (int)$row['total_downloads'];
            $defaults['total_views']    = (int)$row['total_views'];
        }

        $defaults['total_categories']     = (int)$db->query('SELECT COUNT(*) FROM categories')->fetchColumn();
        $defaults['total_download_links'] = (int)$db->query('SELECT COUNT(*) FROM download_links')->fetchColumn();
        $defaults['pending_reports']      = (int)$db->query("SELECT COUNT(*) FROM download_reports WHERE status = 'pending'")->fetchColumn();
        $defaults['new_messages']         = (int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn();

    } catch (PDOException $e) {
        logAppError('getAdminDashboardStats() failed: ' . $e->getMessage(), 'ADMIN');
    }

    return $defaults;
}

/* ============================================================
   getRecentAdminGames()
   Fetches recently added games for the dashboard panel.

   @param  int $limit
   @return array
   ============================================================ */
function getRecentAdminGames(int $limit = 5): array
{
    $limit = max(1, min(20, $limit));
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id, title, slug, status, license_type,
                    views_count, downloads_count, created_at, updated_at
             FROM   games
             ORDER  BY created_at DESC
             LIMIT  ' . $limit
        );
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getRecentAdminGames() failed: ' . $e->getMessage(), 'ADMIN');
        return [];
    }
}

/* ============================================================
   getRecentDownloadReports()
   Fetches recent download reports with game and link context.

   @param  int $limit
   @return array
   ============================================================ */
function getRecentDownloadReports(int $limit = 5): array
{
    $limit = max(1, min(20, $limit));
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT
                dr.id,
                dr.game_id,
                dr.download_link_id,
                dr.report_type,
                dr.status           AS report_status,
                dr.created_at,
                g.title             AS game_title,
                g.slug              AS game_slug,
                dl.link_title       AS link_title,
                dl.provider_name    AS provider_name
             FROM   download_reports dr
             LEFT JOIN games         g  ON g.id  = dr.game_id
             LEFT JOIN download_links dl ON dl.id = dr.download_link_id
             ORDER  BY dr.created_at DESC
             LIMIT  ' . $limit
        );
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getRecentDownloadReports() failed: ' . $e->getMessage(), 'ADMIN');
        return [];
    }
}

/* ============================================================
   getRecentContactMessages()
   Fetches recent contact form submissions.

   @param  int $limit
   @return array
   ============================================================ */
function getRecentContactMessages(int $limit = 5): array
{
    $limit = max(1, min(20, $limit));
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id, name, email, subject, status, created_at
             FROM   contact_messages
             ORDER  BY created_at DESC
             LIMIT  ' . $limit
        );
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getRecentContactMessages() failed: ' . $e->getMessage(), 'ADMIN');
        return [];
    }
}

/* ============================================================
   getAdminSystemStatus()
   Returns basic local system status for the health panel.
   Does not expose sensitive server paths or credentials.

   @return array
   ============================================================ */
function getAdminSystemStatus(): array
{
    /* Check DB connectivity */
    $dbStatus = 'Unknown';
    try {
        $db = getDB();
        $db->query('SELECT 1');
        $dbStatus = 'Connected';
    } catch (\Throwable $e) {
        $dbStatus = 'Error';
    }

    /* Check writable directories — use constant paths but show only status */
    $uploadsWritable = defined('UPLOADS_PATH') && is_writable(UPLOADS_PATH);
    $logsWritable    = defined('LOGS_PATH')    && is_writable(LOGS_PATH);

    return [
        'php_version'       => PHP_VERSION,
        'database'          => $dbStatus,
        'uploads_writable'  => $uploadsWritable,
        'logs_writable'     => $logsWritable,
        'environment'       => defined('APP_ENV') ? APP_ENV : 'unknown',
        'admin_session'     => 'Active',
    ];
}

/* ============================================================
   Step 21 — Admin Game Management Helpers
   ============================================================ */

/* ── Constants ── */
define('ADMIN_GAMES_PER_PAGE', 15);
define('MAX_COVER_SIZE',  2 * 1024 * 1024); /* 2 MB */
define('MAX_BANNER_SIZE', 4 * 1024 * 1024); /* 4 MB */
define('ALLOWED_GAME_IMG_EXTS',  ['jpg','jpeg','png','webp']);
define('ALLOWED_GAME_IMG_MIMES', ['image/jpeg','image/png','image/webp']);

/* ============================================================
   getAdminGamesList()
   Paginated game list for admin panel — includes all statuses.
   Supports q, status, license, category (slug), featured,
   trending, low_end, sort filters. Uses prepared statements.

   @return array [games, total, page, per_page, total_pages]
   ============================================================ */
function getAdminGamesList(array $filters = [], int $page = 1, int $perPage = 15): array
{
    $empty = ['games'=>[], 'total'=>0, 'page'=>1,
              'per_page'=>$perPage, 'total_pages'=>0];
    $page    = max(1, (int)$page);
    $perPage = max(1, min(60, (int)$perPage));

    $validStatuses  = ['draft','active','inactive','archived'];
    $validLicenses  = ['freeware','open_source','demo','official_mirror',
                       'indie_permission','paid_future','other_authorized'];
    $sortMap = [
        'latest'          => 'g.created_at DESC',
        'oldest'          => 'g.created_at ASC',
        'title_az'        => 'g.title ASC',
        'title_za'        => 'g.title DESC',
        'most_viewed'     => 'g.views_count DESC',
        'most_downloaded' => 'g.downloads_count DESC',
        'updated'         => 'g.updated_at DESC',
    ];

    $q        = trim(mb_substr((string)($filters['q']        ?? ''), 0, 100));
    $status   = trim((string)($filters['status']   ?? ''));
    $license  = trim((string)($filters['license']  ?? ''));
    $catSlug  = trim((string)($filters['category'] ?? ''));
    $featured = $filters['featured'] ?? '';
    $trending = $filters['trending'] ?? '';
    $lowEnd   = $filters['low_end']  ?? '';
    $sortKey  = trim((string)($filters['sort'] ?? 'latest'));

    if ($status  !== '' && !in_array($status,  $validStatuses, true))  $status  = '';
    if ($license !== '' && !in_array($license, $validLicenses, true))  $license = '';
    $orderBy = $sortMap[$sortKey] ?? $sortMap['latest'];

    try {
        $db = getDB();

        $from   = 'FROM games g';
        $params = [];
        $where  = ['1=1'];

        if ($q !== '') {
            $like = '%' . $q . '%';
            $where[] = '(g.title LIKE ? OR g.developer LIKE ? OR g.publisher LIKE ?
                         OR g.platform LIKE ? OR g.slug LIKE ?)';
            $params = array_merge($params, [$like,$like,$like,$like,$like]);
        }
        if ($status  !== '') { $where[] = 'g.status = ?';       $params[] = $status; }
        if ($license !== '') { $where[] = 'g.license_type = ?'; $params[] = $license; }
        if ($featured === '1') { $where[] = 'g.is_featured = 1'; }
        if ($featured === '0') { $where[] = 'g.is_featured = 0'; }
        if ($trending === '1') { $where[] = 'g.is_trending = 1'; }
        if ($trending === '0') { $where[] = 'g.is_trending = 0'; }
        if ($lowEnd   === '1') { $where[] = 'g.is_low_end_pc = 1'; }
        if ($lowEnd   === '0') { $where[] = 'g.is_low_end_pc = 0'; }

        if ($catSlug !== '') {
            $from   .= ' INNER JOIN game_categories gc ON gc.game_id = g.id
                         INNER JOIN categories c ON c.id = gc.category_id AND c.slug = ?';
            array_unshift($params, $catSlug);
        }

        $whereStr = implode(' AND ', $where);

        $total = (int)$db->prepare("SELECT COUNT(DISTINCT g.id) $from WHERE $whereStr")
                         ->execute($params) ? 0 : 0; /* init below */
        $countStmt = $db->prepare("SELECT COUNT(DISTINCT g.id) AS cnt $from WHERE $whereStr");
        $countStmt->execute($params);
        $total = (int)($countStmt->fetchColumn() ?? 0);

        if ($total === 0) return array_merge($empty, ['page'=>$page,'per_page'=>$perPage]);

        $totalPages = (int)ceil($total / $perPage);
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        $dataStmt = $db->prepare(
            "SELECT DISTINCT g.id, g.title, g.slug, g.status, g.license_type,
                    g.platform, g.game_size, g.cover_image, g.is_featured,
                    g.is_trending, g.is_low_end_pc, g.views_count,
                    g.downloads_count, g.created_at, g.updated_at,
                    g.developer
             $from WHERE $whereStr
             ORDER BY $orderBy
             LIMIT $perPage OFFSET $offset"
        );
        $dataStmt->execute($params);

        return [
            'games'       => $dataStmt->fetchAll(),
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages,
        ];
    } catch (PDOException $e) {
        logAppError('getAdminGamesList() failed: ' . $e->getMessage(), 'ADMIN');
        return $empty;
    }
}

/* ============================================================
   getAdminGameById()
   Fetch a single game by ID for editing (all statuses).
   ============================================================ */
function getAdminGameById(int $gameId): ?array
{
    if ($gameId <= 0) return null;
    try {
        $db   = getDB();
        $stmt = $db->prepare('SELECT * FROM games WHERE id = ? LIMIT 1');
        $stmt->execute([$gameId]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    } catch (PDOException $e) {
        logAppError('getAdminGameById() failed: ' . $e->getMessage(), 'ADMIN');
        return null;
    }
}

/* ============================================================
   isGameSlugUnique()
   Returns true if slug is not used by any other game.
   ============================================================ */
function isGameSlugUnique(string $slug, ?int $excludeGameId = null): bool
{
    try {
        $db = getDB();
        if ($excludeGameId !== null && $excludeGameId > 0) {
            $stmt = $db->prepare(
                'SELECT COUNT(*) FROM games WHERE slug = ? AND id != ? LIMIT 1'
            );
            $stmt->execute([$slug, $excludeGameId]);
        } else {
            $stmt = $db->prepare(
                'SELECT COUNT(*) FROM games WHERE slug = ? LIMIT 1'
            );
            $stmt->execute([$slug]);
        }
        return (int)$stmt->fetchColumn() === 0;
    } catch (PDOException $e) {
        logAppError('isGameSlugUnique() failed: ' . $e->getMessage(), 'ADMIN');
        return false;
    }
}

/* ============================================================
   validateGameForm()
   Validates add/edit game form data.
   Returns ['valid', 'errors', 'data'] — data is clean plain text
   ready for DB insertion via prepared statements.
   ============================================================ */
function validateGameForm(array $post, array $files = [], ?int $gameId = null): array
{
    $errors = [];
    $data   = [];

    /* Title */
    $title = trim(strip_tags((string)($post['title'] ?? '')));
    if (mb_strlen($title) < 2)        $errors[] = 'Title must be at least 2 characters.';
    elseif (mb_strlen($title) > 180)   $errors[] = 'Title must be 180 characters or fewer.';
    else                               $data['title'] = $title;

    /* Slug */
    $slugRaw = trim((string)($post['slug'] ?? ''));
    $slug    = $slugRaw !== '' ? createSlug($slugRaw) : createSlug($title);
    if (mb_strlen($slug) === 0)  $errors[] = 'Could not generate a valid slug.';
    elseif (mb_strlen($slug) > 220) $errors[] = 'Slug must be 220 characters or fewer.';
    elseif (!isGameSlugUnique($slug, $gameId))
        $errors[] = 'This slug is already used by another game. Please choose a different title or slug.';
    else $data['slug'] = $slug;

    /* Short description */
    $short = trim(strip_tags((string)($post['short_description'] ?? '')));
    if (mb_strlen($short) > 350) $errors[] = 'Short description must be 350 characters or fewer.';
    else $data['short_description'] = $short !== '' ? $short : null;

    /* Full description */
    $full = trim(strip_tags((string)($post['full_description'] ?? ''),
        '<p><br><strong><em><ul><ol><li><h3><h4>'));
    $data['full_description'] = $full !== '' ? $full : null;

    /* Developer / publisher / version */
    $data['developer']  = mb_substr(trim(strip_tags((string)($post['developer']  ?? ''))), 0, 150) ?: null;
    $data['publisher']  = mb_substr(trim(strip_tags((string)($post['publisher']  ?? ''))), 0, 150) ?: null;
    $data['version']    = mb_substr(trim(strip_tags((string)($post['version']    ?? ''))), 0,  80) ?: null;
    $data['game_size']  = mb_substr(trim(strip_tags((string)($post['game_size']  ?? ''))), 0,  80) ?: null;
    $data['developer']  = $data['developer']  ?? null;

    /* Platform */
    $validPlatforms = ['Windows PC','Linux','Mac','Browser','Multi-platform','Other'];
    $plat = trim((string)($post['platform'] ?? 'Windows PC'));
    $data['platform'] = in_array($plat, $validPlatforms, true) ? $plat : 'Windows PC';

    /* Release date */
    $relDate = trim((string)($post['release_date'] ?? ''));
    if ($relDate !== '') {
        $d = date_create_from_format('Y-m-d', $relDate);
        $data['release_date'] = ($d && date_format($d, 'Y-m-d') === $relDate) ? $relDate : null;
    } else {
        $data['release_date'] = null;
    }

    /* License type */
    $validLicenses = ['freeware','open_source','demo','official_mirror',
                      'indie_permission','paid_future','other_authorized'];
    $license = trim((string)($post['license_type'] ?? 'freeware'));
    if (!in_array($license, $validLicenses, true)) {
        $errors[] = 'Please select a valid license type.';
    } else {
        $data['license_type'] = $license;
    }

    /* Status */
    $validStatuses = ['draft','active','inactive','archived'];
    $status = trim((string)($post['status'] ?? 'draft'));
    $data['status'] = in_array($status, $validStatuses, true) ? $status : 'draft';

    /* Checkboxes */
    $data['is_featured']  = isset($post['is_featured'])  ? 1 : 0;
    $data['is_trending']  = isset($post['is_trending'])  ? 1 : 0;
    $data['is_low_end_pc']= isset($post['is_low_end_pc'])? 1 : 0;

    /* Trailer URL */
    $trailerUrl = trim((string)($post['trailer_url'] ?? ''));
    if ($trailerUrl !== '' && !isValidUrl($trailerUrl)) {
        $errors[] = 'Trailer URL must be a valid http/https URL.';
    } else {
        $data['trailer_url'] = $trailerUrl !== '' ? $trailerUrl : null;
    }

    /* SEO */
    $data['meta_title']       = mb_substr(trim(strip_tags((string)($post['meta_title']??''))), 0, 255) ?: null;
    $data['meta_description'] = mb_substr(trim(strip_tags((string)($post['meta_description']??''))), 0, 350) ?: null;

    /* Categories (array of IDs) */
    $cats = $_POST['categories'] ?? [];
    $data['category_ids'] = array_filter(
        array_map('intval', is_array($cats) ? $cats : []),
        fn($id) => $id > 0
    );

    /* Tags (comma-separated string) */
    $tagsRaw = trim((string)($post['tags'] ?? ''));
    $tagList = [];
    if ($tagsRaw !== '') {
        foreach (explode(',', $tagsRaw) as $t) {
            $t = mb_substr(trim(strip_tags($t)), 0, 100);
            if ($t !== '') $tagList[] = $t;
        }
    }
    $data['tags'] = array_unique($tagList);

    return [
        'valid'  => empty($errors),
        'errors' => $errors,
        'data'   => $data,
    ];
}

/* ============================================================
   handleGameImageUpload()
   Validates and stores an uploaded image.
   type: 'cover' | 'banner'
   Returns relative path for DB storage, or null on failure/skip.
   ============================================================ */
function handleGameImageUpload(array $file, string $type = 'cover'): ?string
{
    /* No upload provided */
    if (!isset($file['tmp_name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        logAppError('Upload error code ' . $file['error'] . ' for type=' . $type, 'UPLOAD');
        return null;
    }

    /* Size check */
    $maxSize = ($type === 'banner') ? MAX_BANNER_SIZE : MAX_COVER_SIZE;
    if ($file['size'] > $maxSize) return null;

    /* Extension check */
    $origName = basename($file['name'] ?? '');
    $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_GAME_IMG_EXTS, true)) return null;

    /* MIME check using finfo */
    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mime     = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, ALLOWED_GAME_IMG_MIMES, true)) return null;

    /* Destination folder */
    $destDir  = ($type === 'banner') ? BANNERS_PATH : COVERS_PATH;
    if (!is_dir($destDir)) @mkdir($destDir, 0755, true);

    /* Safe unique filename — no original name used */
    $safeName = bin2hex(random_bytes(12)) . '.' . $ext;
    $destPath = $destDir . DIRECTORY_SEPARATOR . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        logAppError('move_uploaded_file() failed for type=' . $type, 'UPLOAD');
        return null;
    }

    /* Return relative path for DB */
    return 'assets/uploads/' . ($type === 'banner' ? 'banners' : 'covers') . '/' . $safeName;
}

/* ============================================================
   createGame()
   Inserts a new game row. Returns new game ID or false.
   ============================================================ */
function createGame(array $data): int|false
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'INSERT INTO games
               (title, slug, short_description, full_description,
                cover_image, banner_image, trailer_url,
                developer, publisher, version, release_date,
                game_size, platform, license_type, status,
                is_featured, is_trending, is_low_end_pc,
                meta_title, meta_description)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $data['title'], $data['slug'],
            $data['short_description'], $data['full_description'],
            $data['cover_image'] ?? null, $data['banner_image'] ?? null,
            $data['trailer_url'], $data['developer'], $data['publisher'],
            $data['version'], $data['release_date'], $data['game_size'],
            $data['platform'], $data['license_type'], $data['status'],
            $data['is_featured'], $data['is_trending'], $data['is_low_end_pc'],
            $data['meta_title'], $data['meta_description'],
        ]);
        return (int)$db->lastInsertId();
    } catch (PDOException $e) {
        logAppError('createGame() failed: ' . $e->getMessage(), 'ADMIN');
        return false;
    }
}

/* ============================================================
   updateGame()
   Updates an existing game row.
   ============================================================ */
function updateGame(int $gameId, array $data): bool
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'UPDATE games SET
               title=?, slug=?, short_description=?, full_description=?,
               cover_image=?, banner_image=?, trailer_url=?,
               developer=?, publisher=?, version=?, release_date=?,
               game_size=?, platform=?, license_type=?, status=?,
               is_featured=?, is_trending=?, is_low_end_pc=?,
               meta_title=?, meta_description=?,
               updated_at=NOW()
             WHERE id=?'
        );
        return $stmt->execute([
            $data['title'], $data['slug'],
            $data['short_description'], $data['full_description'],
            $data['cover_image'] ?? null, $data['banner_image'] ?? null,
            $data['trailer_url'], $data['developer'], $data['publisher'],
            $data['version'], $data['release_date'], $data['game_size'],
            $data['platform'], $data['license_type'], $data['status'],
            $data['is_featured'], $data['is_trending'], $data['is_low_end_pc'],
            $data['meta_title'], $data['meta_description'],
            $gameId,
        ]);
    } catch (PDOException $e) {
        logAppError('updateGame() failed for #' . $gameId . ': ' . $e->getMessage(), 'ADMIN');
        return false;
    }
}

/* ============================================================
   updateGameCategories()
   Replaces all category links for a game using a transaction.
   ============================================================ */
function updateGameCategories(int $gameId, array $categoryIds): bool
{
    try {
        $db = getDB();
        $db->beginTransaction();
        $db->prepare('DELETE FROM game_categories WHERE game_id = ?')->execute([$gameId]);
        if (!empty($categoryIds)) {
            $ins = $db->prepare(
                'INSERT IGNORE INTO game_categories (game_id, category_id) VALUES (?,?)'
            );
            foreach (array_unique(array_map('intval', $categoryIds)) as $catId) {
                if ($catId > 0) $ins->execute([$gameId, $catId]);
            }
        }
        $db->commit();
        return true;
    } catch (PDOException $e) {
        try { $db->rollBack(); } catch (\Throwable $ex) {}
        logAppError('updateGameCategories() failed: ' . $e->getMessage(), 'ADMIN');
        return false;
    }
}

/* ============================================================
   updateGameTags()
   Replaces all tag links for a game. Creates missing tags.
   ============================================================ */
function updateGameTags(int $gameId, array $tagNames): bool
{
    try {
        $db = getDB();
        $db->beginTransaction();
        $db->prepare('DELETE FROM game_tags WHERE game_id = ?')->execute([$gameId]);
        foreach (array_unique($tagNames) as $name) {
            $name = mb_substr(trim($name), 0, 100);
            if ($name === '') continue;
            $slug = createSlug($name);

            /* Find or create tag */
            $find = $db->prepare('SELECT id FROM tags WHERE slug = ? LIMIT 1');
            $find->execute([$slug]);
            $tagRow = $find->fetch();
            if ($tagRow === false) {
                $ins = $db->prepare(
                    'INSERT IGNORE INTO tags (name, slug, status) VALUES (?,?,?)'
                );
                $ins->execute([$name, $slug, 'active']);
                $tagId = (int)$db->lastInsertId();
            } else {
                $tagId = (int)$tagRow['id'];
            }
            if ($tagId > 0) {
                $db->prepare(
                    'INSERT IGNORE INTO game_tags (game_id, tag_id) VALUES (?,?)'
                )->execute([$gameId, $tagId]);
            }
        }
        $db->commit();
        return true;
    } catch (PDOException $e) {
        try { $db->rollBack(); } catch (\Throwable $ex) {}
        logAppError('updateGameTags() failed: ' . $e->getMessage(), 'ADMIN');
        return false;
    }
}

/* ============================================================
   updateGameStatus()
   Quick status update from games list.
   ============================================================ */
function updateGameStatus(int $gameId, string $status): bool
{
    $valid = ['draft','active','inactive','archived'];
    if (!in_array($status, $valid, true) || $gameId <= 0) return false;
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'UPDATE games SET status = ?, updated_at = NOW() WHERE id = ?'
        );
        return $stmt->execute([$status, $gameId]);
    } catch (PDOException $e) {
        logAppError('updateGameStatus() failed: ' . $e->getMessage(), 'ADMIN');
        return false;
    }
}

/* ============================================================
   getAdminGameCategories()
   Returns category IDs linked to a game.
   ============================================================ */
function getAdminGameCategories(int $gameId): array
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT category_id FROM game_categories WHERE game_id = ?'
        );
        $stmt->execute([$gameId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    } catch (PDOException $e) {
        logAppError('getAdminGameCategories() failed: ' . $e->getMessage(), 'ADMIN');
        return [];
    }
}

/* ============================================================
   getAdminGameTags()
   Returns tag names linked to a game.
   ============================================================ */
function getAdminGameTags(int $gameId): array
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT t.name FROM game_tags gt
             JOIN   tags t ON t.id = gt.tag_id
             WHERE  gt.game_id = ?
             ORDER  BY t.name ASC'
        );
        $stmt->execute([$gameId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    } catch (PDOException $e) {
        logAppError('getAdminGameTags() failed: ' . $e->getMessage(), 'ADMIN');
        return [];
    }
}

/* ============================================================
   getAllAdminCategories()
   Returns all categories (active + inactive) for admin select.
   ============================================================ */
function getAllAdminCategories(): array
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id, name, slug, status FROM categories
             ORDER BY sort_order ASC, name ASC'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getAllAdminCategories() failed: ' . $e->getMessage(), 'ADMIN');
        return [];
    }
}

/* ============================================================
   buildAdminGamesUrl()
   Builds admin games list URL preserving filters.
   ============================================================ */
function buildAdminGamesUrl(array $current, array $override = []): string
{
    $merged  = array_merge($current, $override);
    $allowed = ['q','status','license','category','featured','trending',
                'low_end','sort','page'];
    $params  = [];
    foreach ($allowed as $k) {
        if (isset($merged[$k]) && (string)$merged[$k] !== '') {
            $params[$k] = (string)$merged[$k];
        }
    }
    $qs = http_build_query($params);
    return siteUrl('admin/games.php') . ($qs ? '?' . $qs : '');
}

/* ============================================================
   Step 22 — Admin Download Link Management Helpers
   ============================================================ */

/* ============================================================
   getAdminDownloadLinksList()
   Paginated download links with game data for admin list.

   @param  array $filters  [q, game_id, link_type, status, sort]
   @param  int   $page
   @param  int   $perPage
   @return array [links, total, page, per_page, total_pages]
   ============================================================ */
function getAdminDownloadLinksList(array $filters = [], int $page = 1, int $perPage = 15): array
{
    $empty = ['links'=>[], 'total'=>0, 'page'=>1,
              'per_page'=>$perPage, 'total_pages'=>0];
    $page    = max(1, (int)$page);
    $perPage = max(1, min(60, (int)$perPage));

    $validTypes    = ['cloud','torrent','official','mirror','developer_site','store_link'];
    $validStatuses = ['active','inactive','broken','under_review'];
    $sortMap = [
        'latest'           => 'dl.created_at DESC',
        'oldest'           => 'dl.created_at ASC',
        'most_clicked'     => 'dl.clicks_count DESC',
        'provider_az'      => 'dl.provider_name ASC',
        'provider_za'      => 'dl.provider_name DESC',
        'game_az'          => 'g.title ASC',
        'game_za'          => 'g.title DESC',
        'recently_updated' => 'dl.updated_at DESC',
    ];

    $q        = trim(mb_substr((string)($filters['q']         ?? ''), 0, 100));
    $gameId   = (int)($filters['game_id']   ?? 0);
    $linkType = trim((string)($filters['link_type'] ?? ''));
    $status   = trim((string)($filters['status']    ?? ''));
    $sortKey  = trim((string)($filters['sort']       ?? 'latest'));

    if ($linkType !== '' && !in_array($linkType, $validTypes, true))    $linkType = '';
    if ($status   !== '' && !in_array($status,   $validStatuses, true)) $status   = '';
    $orderBy = $sortMap[$sortKey] ?? $sortMap['latest'];

    try {
        $db = getDB();

        $where  = ['1=1'];
        $params = [];

        if ($q !== '') {
            $like = '%' . $q . '%';
            $where[] = '(g.title LIKE ? OR dl.link_title LIKE ?
                         OR dl.provider_name LIKE ? OR dl.download_url LIKE ?)';
            $params = array_merge($params, [$like,$like,$like,$like]);
        }
        if ($gameId > 0)  { $where[] = 'dl.game_id = ?';    $params[] = $gameId; }
        if ($linkType !== '') { $where[] = 'dl.link_type = ?'; $params[] = $linkType; }
        if ($status   !== '') { $where[] = 'dl.status = ?';    $params[] = $status; }

        $whereStr = implode(' AND ', $where);
        $from     = 'FROM download_links dl LEFT JOIN games g ON g.id = dl.game_id';

        $countStmt = $db->prepare(
            "SELECT COUNT(dl.id) AS cnt $from WHERE $whereStr"
        );
        $countStmt->execute($params);
        $total = (int)($countStmt->fetchColumn() ?? 0);

        if ($total === 0) return array_merge($empty, ['page'=>$page,'per_page'=>$perPage]);

        $totalPages = (int)ceil($total / $perPage);
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        $dataStmt = $db->prepare(
            "SELECT dl.id, dl.game_id, dl.link_title, dl.provider_name,
                    dl.download_url, dl.link_type, dl.file_size,
                    dl.status, dl.clicks_count, dl.created_at, dl.updated_at,
                    g.title AS game_title, g.slug AS game_slug,
                    g.status AS game_status
             $from WHERE $whereStr
             ORDER BY $orderBy
             LIMIT $perPage OFFSET $offset"
        );
        $dataStmt->execute($params);

        return [
            'links'       => $dataStmt->fetchAll(),
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages,
        ];
    } catch (PDOException $e) {
        logAppError('getAdminDownloadLinksList() failed: ' . $e->getMessage(), 'ADMIN');
        return $empty;
    }
}

/* ============================================================
   getAdminDownloadLinkById()
   Fetch one download link with its game data for editing.
   ============================================================ */
function getAdminDownloadLinkById(int $linkId): ?array
{
    if ($linkId <= 0) return null;
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT dl.*,
                    g.title  AS game_title,
                    g.slug   AS game_slug,
                    g.status AS game_status
             FROM   download_links dl
             LEFT JOIN games g ON g.id = dl.game_id
             WHERE  dl.id = ?
             LIMIT  1'
        );
        $stmt->execute([$linkId]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    } catch (PDOException $e) {
        logAppError('getAdminDownloadLinkById() failed: ' . $e->getMessage(), 'ADMIN');
        return null;
    }
}

/* ============================================================
   getAdminGameOptionsForLinks()
   Returns games for link management dropdowns.
   Includes draft, active, inactive — excludes archived.
   ============================================================ */
function getAdminGameOptionsForLinks(): array
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            "SELECT id, title, status, slug
             FROM   games
             WHERE  status IN ('draft','active','inactive')
             ORDER  BY title ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logAppError('getAdminGameOptionsForLinks() failed: ' . $e->getMessage(), 'ADMIN');
        return [];
    }
}

/* ============================================================
   isSafeDownloadUrl()
   Validates a download URL for admin forms.
   Allows http/https for all types; magnet only for torrent.
   Rejects javascript:, data:, file:, ftp:, mailto:, etc.

   @param  string $url
   @param  string $linkType  e.g. 'torrent', 'cloud', etc.
   @return bool
   ============================================================ */
function isSafeDownloadUrl(string $url, string $linkType = ''): bool
{
    $url    = trim($url);
    if ($url === '') return false;
    $scheme = strtolower(parse_url($url, PHP_URL_SCHEME) ?? '');

    if (in_array($scheme, ['http','https'], true)) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    if ($scheme === 'magnet' && $linkType === 'torrent') {
        return (bool)preg_match('/^magnet:\?/i', $url);
    }
    return false; /* all other schemes rejected */
}

/* ============================================================
   validateDownloadLinkForm()
   Validates add/edit download link POST data.
   Returns ['valid', 'errors', 'data'].
   ============================================================ */
function validateDownloadLinkForm(array $post): array
{
    $errors = [];
    $data   = [];

    /* game_id */
    $gameId = (int)($post['game_id'] ?? 0);
    if ($gameId <= 0) {
        $errors[] = 'Please select a game.';
    } else {
        $data['game_id'] = $gameId;
    }

    /* link_title */
    $title = trim(strip_tags((string)($post['link_title'] ?? '')));
    if (mb_strlen($title) < 2)      $errors[] = 'Link title must be at least 2 characters.';
    elseif (mb_strlen($title) > 180) $errors[] = 'Link title must be 180 characters or fewer.';
    else $data['link_title'] = $title;

    /* provider_name */
    $provider = trim(strip_tags((string)($post['provider_name'] ?? '')));
    if ($provider === '')           $errors[] = 'Provider name is required.';
    elseif (mb_strlen($provider) > 120) $errors[] = 'Provider name must be 120 characters or fewer.';
    else $data['provider_name'] = $provider;

    /* link_type */
    $validTypes = ['cloud','torrent','official','mirror','developer_site','store_link'];
    $linkType   = trim((string)($post['link_type'] ?? ''));
    if (!in_array($linkType, $validTypes, true)) {
        $errors[] = 'Please select a valid link type.';
    } else {
        $data['link_type'] = $linkType;
    }

    /* download_url */
    $url = trim((string)($post['download_url'] ?? ''));
    if ($url === '') {
        $errors[] = 'Download URL is required.';
    } elseif (!isSafeDownloadUrl($url, $linkType)) {
        $errors[] = 'Download URL must be a valid https:// or http:// URL. '
                  . 'Unsafe URL schemes (javascript:, ftp:, data:, etc.) are not allowed.';
    } elseif (mb_strlen($url) > 2000) {
        $errors[] = 'Download URL is too long.';
    } else {
        $data['download_url'] = $url;
    }

    /* file_size */
    $fileSize = mb_substr(trim(strip_tags((string)($post['file_size'] ?? ''))), 0, 80);
    $data['file_size'] = $fileSize !== '' ? $fileSize : null;

    /* status */
    $validStatuses = ['active','inactive','broken','under_review'];
    $status = trim((string)($post['status'] ?? 'active'));
    $data['status'] = in_array($status, $validStatuses, true) ? $status : 'active';

    /* legal_confirm */
    if (empty($post['legal_confirm'])) {
        $errors[] = 'You must confirm this is a legal, authorized download source.';
    }

    return ['valid' => empty($errors), 'errors' => $errors, 'data' => $data];
}

/* ============================================================
   createDownloadLink()
   Insert a new download link row.
   Returns new ID or false.
   ============================================================ */
function createDownloadLink(array $data): int|false
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'INSERT INTO download_links
               (game_id, link_title, provider_name, download_url,
                link_type, file_size, status)
             VALUES (?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $data['game_id'], $data['link_title'], $data['provider_name'],
            $data['download_url'], $data['link_type'],
            $data['file_size'], $data['status'],
        ]);
        return (int)$db->lastInsertId();
    } catch (PDOException $e) {
        logAppError('createDownloadLink() failed: ' . $e->getMessage(), 'ADMIN');
        return false;
    }
}

/* ============================================================
   updateDownloadLink()
   Update an existing download link row.
   ============================================================ */
function updateDownloadLink(int $linkId, array $data): bool
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'UPDATE download_links
             SET game_id=?, link_title=?, provider_name=?, download_url=?,
                 link_type=?, file_size=?, status=?, updated_at=NOW()
             WHERE id=?'
        );
        return $stmt->execute([
            $data['game_id'], $data['link_title'], $data['provider_name'],
            $data['download_url'], $data['link_type'],
            $data['file_size'], $data['status'],
            $linkId,
        ]);
    } catch (PDOException $e) {
        logAppError('updateDownloadLink() failed for #' . $linkId . ': ' . $e->getMessage(), 'ADMIN');
        return false;
    }
}

/* ============================================================
   updateDownloadLinkStatus()
   Quick status update from list actions.
   ============================================================ */
function updateDownloadLinkStatus(int $linkId, string $status): bool
{
    $valid = ['active','inactive','broken','under_review'];
    if (!in_array($status, $valid, true) || $linkId <= 0) return false;
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'UPDATE download_links SET status=?, updated_at=NOW() WHERE id=?'
        );
        return $stmt->execute([$status, $linkId]);
    } catch (PDOException $e) {
        logAppError('updateDownloadLinkStatus() failed: ' . $e->getMessage(), 'ADMIN');
        return false;
    }
}

/* ============================================================
   getReadableLinkStatus()
   Human-readable label for a link status.
   ============================================================ */
function getReadableLinkStatus(string $status): string
{
    $map = [
        'active'       => 'Active',
        'inactive'     => 'Inactive',
        'broken'       => 'Broken',
        'under_review' => 'Under Review',
    ];
    return $map[$status] ?? ucfirst($status);
}

/* ============================================================
   getLinkStatusBadgeClass()
   CSS class for admin status badge.
   ============================================================ */
function getLinkStatusBadgeClass(string $status): string
{
    $map = [
        'active'       => 'admin-status-active',
        'inactive'     => 'admin-status-inactive',
        'broken'       => 'admin-status-broken',
        'under_review' => 'admin-status-under-review',
    ];
    return $map[$status] ?? 'admin-status-inactive';
}

/* ============================================================
   buildAdminLinksUrl()
   URL builder for download links list with filter preservation.
   ============================================================ */
function buildAdminLinksUrl(array $current, array $override = []): string
{
    $merged  = array_merge($current, $override);
    $allowed = ['q','game_id','link_type','status','sort','page'];
    $params  = [];
    foreach ($allowed as $k) {
        if (isset($merged[$k]) && (string)$merged[$k] !== '') {
            $params[$k] = (string)$merged[$k];
        }
    }
    $qs = http_build_query($params);
    return siteUrl('admin/download-links.php') . ($qs ? '?' . $qs : '');
}

/* ============================================================
   Step 23 — Admin Category Management Helpers
   ============================================================ */

/* ============================================================
   getAdminCategoriesList()
   All categories (all statuses) with game counts, pagination.

   @param  array $filters  [q, status, sort]
   @param  int   $page
   @param  int   $perPage
   @return array [categories, total, page, per_page, total_pages]
   ============================================================ */
function getAdminCategoriesList(array $filters = [], int $page = 1, int $perPage = 15): array
{
    $empty = ['categories'=>[], 'total'=>0, 'page'=>1,
              'per_page'=>$perPage, 'total_pages'=>0];
    $page    = max(1, (int)$page);
    $perPage = max(1, min(60, (int)$perPage));

    $validStatuses = ['active','inactive','archived'];
    $sortMap = [
        'sort_order'      => 'c.sort_order ASC, c.name ASC',
        'latest'          => 'c.created_at DESC',
        'oldest'          => 'c.created_at ASC',
        'name_az'         => 'c.name ASC',
        'name_za'         => 'c.name DESC',
        'most_games'      => 'game_count DESC, c.name ASC',
        'recently_updated'=> 'c.updated_at DESC',
    ];

    $q      = trim(mb_substr((string)($filters['q']      ?? ''), 0, 100));
    $status = trim((string)($filters['status'] ?? ''));
    $sortKey= trim((string)($filters['sort']   ?? 'sort_order'));

    if ($status !== '' && !in_array($status, $validStatuses, true)) $status = '';
    $orderBy = $sortMap[$sortKey] ?? $sortMap['sort_order'];

    try {
        $db = getDB();

        $where  = ['1=1'];
        $params = [];

        if ($q !== '') {
            $like = '%' . $q . '%';
            $where[] = '(c.name LIKE ? OR c.slug LIKE ? OR c.description LIKE ?)';
            $params  = array_merge($params, [$like, $like, $like]);
        }
        if ($status !== '') { $where[] = 'c.status = ?'; $params[] = $status; }

        $whereStr = implode(' AND ', $where);
        $from     = 'FROM categories c
                     LEFT JOIN game_categories gc ON gc.category_id = c.id
                     LEFT JOIN games g             ON g.id = gc.game_id AND g.status = \'active\'';

        $countStmt = $db->prepare(
            "SELECT COUNT(DISTINCT c.id) AS cnt $from WHERE $whereStr"
        );
        $countStmt->execute($params);
        $total = (int)($countStmt->fetchColumn() ?? 0);

        if ($total === 0) return array_merge($empty, ['page'=>$page,'per_page'=>$perPage]);

        $totalPages = (int)ceil($total / $perPage);
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        $dataStmt = $db->prepare(
            "SELECT c.id, c.name, c.slug, c.description, c.icon,
                    c.status, c.sort_order, c.created_at, c.updated_at,
                    COUNT(DISTINCT g.id) AS game_count
             $from WHERE $whereStr
             GROUP BY c.id, c.name, c.slug, c.description, c.icon,
                      c.status, c.sort_order, c.created_at, c.updated_at
             ORDER BY $orderBy
             LIMIT $perPage OFFSET $offset"
        );
        $dataStmt->execute($params);

        return [
            'categories' => $dataStmt->fetchAll(),
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
            'total_pages'=> $totalPages,
        ];
    } catch (PDOException $e) {
        logAppError('getAdminCategoriesList() failed: ' . $e->getMessage(), 'ADMIN');
        return $empty;
    }
}

/* ============================================================
   getAdminCategoryById()
   Fetch a single category by ID (any status) for editing.
   ============================================================ */
function getAdminCategoryById(int $catId): ?array
{
    if ($catId <= 0) return null;
    try {
        $db   = getDB();
        $stmt = $db->prepare('SELECT * FROM categories WHERE id = ? LIMIT 1');
        $stmt->execute([$catId]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    } catch (PDOException $e) {
        logAppError('getAdminCategoryById() failed: ' . $e->getMessage(), 'ADMIN');
        return null;
    }
}

/* ============================================================
   isCategorySlugUnique()
   Returns true if the slug is not used by another category.
   ============================================================ */
function isCategorySlugUnique(string $slug, ?int $excludeId = null): bool
{
    try {
        $db = getDB();
        if ($excludeId !== null && $excludeId > 0) {
            $stmt = $db->prepare(
                'SELECT COUNT(*) FROM categories WHERE slug = ? AND id != ? LIMIT 1'
            );
            $stmt->execute([$slug, $excludeId]);
        } else {
            $stmt = $db->prepare('SELECT COUNT(*) FROM categories WHERE slug = ? LIMIT 1');
            $stmt->execute([$slug]);
        }
        return (int)$stmt->fetchColumn() === 0;
    } catch (PDOException $e) {
        logAppError('isCategorySlugUnique() failed: ' . $e->getMessage(), 'ADMIN');
        return false;
    }
}

/* ============================================================
   generateUniqueCategorySlug()
   Generates a unique slug from name, appending -2/-3 if needed.
   ============================================================ */
function generateUniqueCategorySlug(string $name, ?int $excludeId = null): string
{
    $base = createSlug($name);
    if ($base === '') return '';

    $slug = $base;
    $i    = 2;
    while (!isCategorySlugUnique($slug, $excludeId)) {
        $slug = $base . '-' . $i;
        $i++;
        if ($i > 99) break; /* safety cap */
    }
    return $slug;
}

/* ============================================================
   validateCategoryForm()
   Validates add/edit category POST data.
   Returns ['valid', 'errors', 'data'].
   ============================================================ */
function validateCategoryForm(array $post, ?int $catId = null): array
{
    $errors = [];
    $data   = [];

    /* name */
    $name = trim(strip_tags((string)($post['name'] ?? '')));
    if (mb_strlen($name) < 2)       $errors[] = 'Category name must be at least 2 characters.';
    elseif (mb_strlen($name) > 120)  $errors[] = 'Category name must be 120 characters or fewer.';
    else                             $data['name'] = $name;

    /* slug */
    $slugRaw = trim((string)($post['slug'] ?? ''));
    $slug    = $slugRaw !== '' ? createSlug($slugRaw) : (isset($name) ? createSlug($name) : '');
    if ($slug === '') {
        $errors[] = 'Please enter a valid category name or slug.';
    } elseif (mb_strlen($slug) > 150) {
        $errors[] = 'Slug must be 150 characters or fewer.';
    } elseif (!isCategorySlugUnique($slug, $catId)) {
        $errors[] = 'This category slug is already used. Please choose a different name or slug.';
    } else {
        $data['slug'] = $slug;
    }

    /* description */
    $desc = trim(strip_tags((string)($post['description'] ?? '')));
    if (mb_strlen($desc) > 600) $errors[] = 'Description must be 600 characters or fewer.';
    else $data['description'] = $desc !== '' ? $desc : null;

    /* icon */
    $icon = trim(strip_tags((string)($post['icon'] ?? '')));
    if (mb_strlen($icon) > 80) $errors[] = 'Icon must be 80 characters or fewer.';
    else $data['icon'] = $icon !== '' ? $icon : null;

    /* status */
    $validStatuses = ['active','inactive','archived'];
    $status = trim((string)($post['status'] ?? 'active'));
    $data['status'] = in_array($status, $validStatuses, true) ? $status : 'active';

    /* sort_order */
    $sortOrder = (int)($post['sort_order'] ?? 0);
    $data['sort_order'] = max(0, $sortOrder);

    return ['valid' => empty($errors), 'errors' => $errors, 'data' => $data];
}

/* ============================================================
   createCategory()
   Inserts a new category. Returns new ID or false.
   ============================================================ */
function createCategory(array $data): int|false
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'INSERT INTO categories (name, slug, description, icon, status, sort_order)
             VALUES (?,?,?,?,?,?)'
        );
        $stmt->execute([
            $data['name'], $data['slug'], $data['description'],
            $data['icon'], $data['status'], $data['sort_order'],
        ]);
        return (int)$db->lastInsertId();
    } catch (PDOException $e) {
        logAppError('createCategory() failed: ' . $e->getMessage(), 'ADMIN');
        return false;
    }
}

/* ============================================================
   updateCategory()
   Updates an existing category. Returns true/false.
   ============================================================ */
function updateCategory(int $catId, array $data): bool
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'UPDATE categories
             SET name=?, slug=?, description=?, icon=?, status=?, sort_order=?,
                 updated_at=NOW()
             WHERE id=?'
        );
        return $stmt->execute([
            $data['name'], $data['slug'], $data['description'],
            $data['icon'], $data['status'], $data['sort_order'],
            $catId,
        ]);
    } catch (PDOException $e) {
        logAppError('updateCategory() failed for #' . $catId . ': ' . $e->getMessage(), 'ADMIN');
        return false;
    }
}

/* ============================================================
   updateCategoryStatus()
   Quick status update from list actions.
   ============================================================ */
function updateCategoryStatus(int $catId, string $status): bool
{
    $valid = ['active','inactive','archived'];
    if (!in_array($status, $valid, true) || $catId <= 0) return false;
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'UPDATE categories SET status=?, updated_at=NOW() WHERE id=?'
        );
        return $stmt->execute([$status, $catId]);
    } catch (PDOException $e) {
        logAppError('updateCategoryStatus() failed: ' . $e->getMessage(), 'ADMIN');
        return false;
    }
}

/* ============================================================
   getCategoryGameCount()
   Count active games linked to a category.
   ============================================================ */
function getCategoryGameCount(int $catId): int
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT COUNT(DISTINCT g.id)
             FROM   game_categories gc
             JOIN   games g ON g.id = gc.game_id AND g.status = ?
             WHERE  gc.category_id = ?'
        );
        $stmt->execute(['active', $catId]);
        return (int)($stmt->fetchColumn() ?? 0);
    } catch (PDOException $e) {
        logAppError('getCategoryGameCount() failed: ' . $e->getMessage(), 'ADMIN');
        return 0;
    }
}

/* ============================================================
   getReadableCategoryStatus()
   Human-readable label for a category status.
   ============================================================ */
function getReadableCategoryStatus(string $status): string
{
    return ['active'=>'Active','inactive'=>'Inactive','archived'=>'Archived'][$status]
        ?? ucfirst($status);
}

/* ============================================================
   getCategoryStatusBadgeClass()
   Admin CSS badge class for category status.
   ============================================================ */
function getCategoryStatusBadgeClass(string $status): string
{
    return [
        'active'   => 'admin-status-active',
        'inactive' => 'admin-status-inactive',
        'archived' => 'admin-status-archived',
    ][$status] ?? 'admin-status-inactive';
}

/* ============================================================
   buildAdminCatsUrl()
   URL builder for category list pagination + filter links.
   ============================================================ */
function buildAdminCatsUrl(array $current, array $override = []): string
{
    $merged  = array_merge($current, $override);
    $allowed = ['q','status','sort','page'];
    $params  = [];
    foreach ($allowed as $k) {
        if (isset($merged[$k]) && (string)$merged[$k] !== '') {
            $params[$k] = (string)$merged[$k];
        }
    }
    $qs = http_build_query($params);
    return siteUrl('admin/categories.php') . ($qs ? '?' . $qs : '');
}
