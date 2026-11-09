<?php
// Run against a restored backup first. Without --apply, print the planned DDL.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}
if (count($argv) > 2 || (isset($argv[1]) && $argv[1] !== '--apply')) {
    fwrite(STDERR, "Usage: php migrations/2026-utf8mb4.php [--apply]\n");
    exit(2);
}
$apply = isset($argv[1]);
require __DIR__ . '/../include/secrets.php';
require __DIR__ . '/../include/secrets.local.php';
require __DIR__ . '/../include/db.php';

if (!db_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_db)) {
    fwrite(STDERR, "Database unavailable.\n");
    exit(1);
}

function migration_query($sql) {
    $result = db_query($sql);
    if ($result === false)
        throw new RuntimeException(db_error());
    return $result;
}

function migration_identifier($name) {
    return '`' . str_replace('`', '``', $name) . '`';
}

try {
    $peer = migration_query("SHOW COLUMNS FROM peers LIKE 'peer_id'")->fetch_assoc();
    $hash = migration_query("SHOW COLUMNS FROM torrents LIKE 'info_hash'")->fetch_assoc();
    if (!in_array($peer['Type'] ?? '', ['varchar(20)', 'varbinary(20)'], true) ||
        ($hash['Type'] ?? '') !== 'varbinary(40)')
        throw new RuntimeException('Unexpected peer_id or info_hash schema.');
    if (migration_query('SELECT 1 FROM peers WHERE OCTET_LENGTH(peer_id) > 20 LIMIT 1')->num_rows)
        throw new RuntimeException('A peer_id exceeds 20 bytes. Inspect the legacy charset first.');

    $ddl = [];
    // Binary conversion must run before converting the surrounding text columns.
    if ($peer['Type'] === 'varchar(20)')
        $ddl[] = "ALTER TABLE peers MODIFY peer_id VARBINARY(20) NOT NULL DEFAULT ''";

    $charset = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
    $schema = file_get_contents(__DIR__ . '/../install/database.sql');
    preg_match_all('/^CREATE TABLE `([^\x60]+)`/m', $schema, $matches);
    $tables = migration_query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
    foreach ($tables->fetch_all() as [$table]) {
        if (in_array($table, $matches[1], true))
            $ddl[] = 'ALTER TABLE ' . migration_identifier($table) . ' CONVERT TO ' . $charset;
    }
    $ddl[] = 'ALTER DATABASE ' . migration_identifier($mysql_db) . ' ' . $charset;

    foreach ($ddl as $sql) {
        echo $sql, ";\n";
        if ($apply)
            migration_query($sql);
    }
} catch (RuntimeException $error) {
    fwrite(STDERR, $error->getMessage() . "\n");
    exit(1);
}
