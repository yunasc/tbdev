<?php

require_once('include/bittorrent.php');
dbconn(false);

if($_GET['imagehash'] == "test") {
	$imagestring = "Yuna";
} else {
	$query = sql_query("SELECT * FROM captcha WHERE imagehash = ".sqlesc($_GET['imagehash'])." LIMIT 1") or sqlerr(__FILE__,__LINE__);
	$regimage = mysql_fetch_array($query);
	$imagestring = $regimage['imagestring'];
}

/* load php5 or php4 class files for demo */
if (version_compare(phpversion(), "5.0.0", ">=")) { 
	require_once('./include/mp3captcha.php');
} else { 
	require_once('./include/php4/mp3captcha.php');
} 

/* init mp3captcha class with captcha value from session*/
$mp3 = new mp3captcha($imagestring);

/* use language mapping */
$mp3->mapping = true;

/* output captcha mp3 */
$mp3->mp3stitch();

?>