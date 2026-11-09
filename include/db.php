<?php

function db_open($host, $user, $password, $database, $charset = 'utf8mb4', $port = null) {
    mysqli_report(MYSQLI_REPORT_OFF);
    $socket = null;
    if (str_starts_with($host, 'localhost:/')) {
        $socket = substr($host, strlen('localhost:'));
        $host = 'localhost';
    }
    $connection = @mysqli_connect($host, $user, $password, $database, $port, $socket);
    if (!$connection)
        return false;

    // The legacy schema uses zero dates and implicit defaults.
    if (!$connection->set_charset($charset) ||
        !$connection->query("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'")) {
        $connection->close();
        return false;
    }
    return $connection;
}

function db_connect($host, $user, $password, $database, $charset = 'utf8mb4') {
    $connection = db_open($host, $user, $password, $database, $charset);
    if (!$connection)
        return false;
    $GLOBALS['db_connection'] = $connection;
    return true;
}

function db_query($query) {
    return $GLOBALS['db_connection']->query($query);
}

function db_error() {
    return $GLOBALS['db_connection']->error;
}

function db_errno() {
    return $GLOBALS['db_connection']->errno;
}

function db_insert_id() {
    return $GLOBALS['db_connection']->insert_id;
}

function db_affected_rows() {
    return $GLOBALS['db_connection']->affected_rows;
}

function db_real_escape_string($value) {
    return $GLOBALS['db_connection']->real_escape_string((string) $value);
}

function db_result($result, $row, $field = 0) {
    if (!$result || !$result->data_seek($row))
        return false;
    return $result->fetch_array()[$field] ?? null;
}
