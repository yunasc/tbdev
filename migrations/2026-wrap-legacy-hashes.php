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

mysql_connect($mysql_host, $mysql_user, $mysql_pass) or exit("Cannot connect: " . mysql_error() . "\n");
mysql_select_db($mysql_db) or exit("Cannot select database: " . mysql_error() . "\n");

// A wrapped hash is 64 chars; writing it into the old varchar(32) column would truncate it and lock the user out.
$col = mysql_fetch_row(mysql_query("SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS " .
    "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'passhash'"));
if (!$col || $col[0] < 64)
    exit("users.passhash is too short; apply migrations/2026-security-auth.sql first.\n");

$res = mysql_query("SELECT id, passhash FROM users WHERE passhash REGEXP '^[0-9a-f]{32}$'") or exit(mysql_error() . "\n");
$wrapped = 0;
while ($row = mysql_fetch_assoc($res)) {
    $hash = password_hash($row['passhash'], PASSWORD_BCRYPT);
    if ($hash === false)
        exit("password_hash() failed\n");
    mysql_query("UPDATE users SET passhash = '" . mysql_real_escape_string('md5:' . $hash) . "' WHERE id = " . (int)$row['id'] .
        " AND passhash = '" . mysql_real_escape_string($row['passhash']) . "'") or exit(mysql_error() . "\n");
    $wrapped += mysql_affected_rows();
}
echo "Wrapped $wrapped legacy password hashes.\n";
