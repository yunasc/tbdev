<?php
// One-off, from the tracker root: php migrations/2026-wrap-legacy-hashes.php
// Wraps legacy md5(secret.password.secret) hashes in bcrypt ("md5:<bcrypt>") so no bare MD5 stays at rest
// for accounts that never log in again. password_matches() accepts the wrapped form and takelogin.php
// rewrites it as plain bcrypt on the next login. Safe to re-run.
if (PHP_SAPI != 'cli')
    exit;
chdir(dirname(__FILE__) . '/..');
require 'include/secrets.php';
require 'include/secrets.local.php';

require_once __DIR__ . '/../include/db.php';
if (!db_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_db, $mysql_charset))
    exit("Database unavailable.\n");

// A wrapped hash is 64 chars; writing it into the old varchar(32) column would truncate it and lock the user out.
$col = mysqli_fetch_row(db_query("SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS " .
    "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'passhash'"));
if (!$col || $col[0] < 64)
    exit("users.passhash is too short; apply migrations/2026-security-auth.sql first.\n");

$res = db_query("SELECT id, passhash FROM users WHERE passhash REGEXP '^[0-9a-f]{32}$'") or exit(db_error() . "\n");
$wrapped = 0;
while ($row = mysqli_fetch_assoc($res)) {
    $hash = password_hash($row['passhash'], PASSWORD_BCRYPT);
    if ($hash === false)
        exit("password_hash() failed\n");
    db_query("UPDATE users SET passhash = '" . db_real_escape_string('md5:' . $hash) . "' WHERE id = " . (int)$row['id'] .
        " AND passhash = '" . db_real_escape_string($row['passhash']) . "'") or exit(db_error() . "\n");
    $wrapped += db_affected_rows();
}
echo "Wrapped $wrapped legacy password hashes.\n";
