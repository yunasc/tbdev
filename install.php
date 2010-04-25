<?php

require_once('include/secrets.php');

$_DB_CONFIG = array(
	'host' => $mysql_host,
	'user' => $mysql_user,
	'passwd' => $mysql_pass,
	'db_names' => array ('DEFAULT_DB' => $mysql_db),
	'charset' => $mysql_charset,
);

die('todo');

?>