<?php

$op = isset($_POST['op']) ? $_POST['op'] : (isset($_GET['op']) ? $_GET['op'] : 'Main');

/*if (get_magic_quotes_gpc()) {
	if (!empty($_GET))    { $_GET    = strip_magic_quotes($_GET);    }
	if (!empty($_POST))   { $_POST   = strip_magic_quotes($_POST);   }
	if (!empty($_COOKIE)) { $_COOKIE = strip_magic_quotes($_COOKIE); }
}*/

$admin_params = array_flip(array(
    'title', 'content', 'bposition', 'active', 'hide', 'blockfile',
    'view', 'expire', 'action', 'newexpire', 'bid', 'bkey',
    'oldposition', 'weight', 'weightrep', 'bidrep', 'bidori',
    'ok', 'de', 'iname', 'ipass', 'imail'
));
foreach (array($_GET, $_POST) as $input) {
    foreach ($input as $key => $value) {
        if (isset($admin_params[$key]))
            $GLOBALS[$key] = $value;
    }
}

require_once('admin/functions.php');

?>