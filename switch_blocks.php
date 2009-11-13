<?

require_once("include/bittorrent.php");
dbconn();

if ($_GET) {
	$bid = $_GET['bid'];
	if (!is_numeric($bid) || !in_array($_GET['type'], array('hide', 'show')))
		die();
	$hb = unserialize($_COOKIE['hb']);
	if (!$hb)
		$hb = array();
	if ($_GET['type'] == 'hide')
		$hb[$bid] = $bid;
	else
		unset($hb[$bid]);
	setcookie('hb', serialize($hb), time() + 32140800); // + 1 Year
} else
	die();

?>