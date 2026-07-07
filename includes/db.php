<?php
/**
 * QMGames Store - PDO Database Connection
 * Step: 3 — Backend Connection System
 *
 * PURPOSE:
 *   Establishes a single secure PDO connection to MySQL/MariaDB.
 *   Exposes getDB() so any file can safely retrieve the connection.
 *
 * LOADED BY: includes/init.php
 *
 * SECURITY:
 *   - Uses PDO with prepared statements — no raw SQL concatenation.
 *   - Connection errors are logged, never shown to public users.
 *   - DB_PASS and DB_USER are never echoed or exposed.
 *   - PDO::ATTR_EMULATE_PREPARES = false forces true prepared statements.
 *
 * !! Do not use mysqli anywhere in this project. PDO only. !!
 */

/* ============================================================
   Guard: must be loaded after config.php and error-handler.php
   ============================================================ */
if (!defined('DB_HOST')) {
    die('Direct access not permitted.');
}

/* ============================================================
   PDO Connection
   Stored in $pdo — accessible globally or via getDB().
   ============================================================ */
$pdo = null;

try {
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_NAME,
        DB_CHARSET
    );

    $pdoOptions = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // Throw PDOException on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Return associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                     // Use true prepared statements
        PDO::MYSQL_ATTR_FOUND_ROWS   => true,                      // Consistent row counts on UPDATE
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $pdoOptions);

} catch (PDOException $e) {
    // Log the real technical error internally — never expose it publicly
    logAppError(
        'PDO connection failed — host: ' . DB_HOST . ', db: ' . DB_NAME .
        ' — ' . $e->getMessage(),
        'DB'
    );

    // Show a safe, generic message to the user
    showErrorPage(
        'Database connection is temporarily unavailable. Please try again later.'
    );
}

/* ============================================================
   getDB()
   Returns the global PDO instance.
   Use this anywhere you need a DB connection:
     $pdo = getDB();
     $stmt = $pdo->prepare('SELECT ...');

   @return PDO
   @throws RuntimeException if connection was never established
   ============================================================ */
function getDB(): PDO
{
    global $pdo;

    if (!($pdo instanceof PDO)) {
        logAppError('getDB() called but $pdo is not a valid PDO instance.', 'DB');
        showErrorPage(
            'Database connection is temporarily unavailable. Please try again later.'
        );
    }

    return $pdo;
}
