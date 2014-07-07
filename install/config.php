<?php

define('CWD', (($getcwd = getcwd()) ? $getcwd : '.'));

require_once(CWD . '/../include/secrets.php');
require_once(CWD . '/../include/secrets.local.php');

// Если вы такой б...н, что залезли сюда - то брысь на 2 строки выше
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