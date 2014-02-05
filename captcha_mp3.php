<?php

require_once('include/bittorrent.php');
dbconn(false);

/*
 * To-Do:
 * Implement captcha code regen when user used it
 * Don't cache signup page, for new link regen

 * Probable issues:
 * If not using Db for storage, but use PHP sessions
 * then two requests for captcha from different pages
 * will create bad user experience.

 * Must circumvent this issue in elegant manner.

*/

if($_GET['imagehash'] == "test") {
	$imagestring = "Yuna";
} else {
	$query = sql_query("SELECT * FROM captcha WHERE imagehash = ".sqlesc($_GET['imagehash'])." LIMIT 1") or sqlerr(__FILE__,__LINE__);
	$regimage = mysql_fetch_array($query);
	$imagestring = $regimage['imagestring'];
}

/* load php5 class file */
	require_once('./include/mp3captcha.php');

/* init mp3captcha class with captcha value from session*/
$mp3 = new mp3captcha($imagestring);

/* use language mapping */
$mp3->mapping = true;

/* output captcha mp3 */
$mp3->mp3stitch();

?>