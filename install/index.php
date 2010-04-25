<?php

// ####################### SET PHP ENVIRONMENT ###########################
error_reporting(E_ALL & ~E_NOTICE);

require_once('./config.php');

$total = $db->sdImportFromFile('database.sql');

echo "Success! Executed $total queries!";

?>