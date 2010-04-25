<?php

define('CWD', (($getcwd = getcwd()) ? $getcwd : '.'));

require_once(CWD . '/../include/secrets.php');

$_DB_CONFIG = array(
	'host' => $mysql_host,
	'user' => $mysql_user,
	'passwd' => $mysql_pass,
	'db_names' => array ('DEFAULT_DB' => $mysql_db),
	'charset' => $mysql_charset,
);

// Request MySQL class
require_once(CWD . '/mysql.php');

$db =& MySQL::get_instance();

?>