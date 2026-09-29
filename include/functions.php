<?php

/*
// +----------------------------------------------------------------------------+
// | Project:    TBDevYSE - TBDev Yuna Scatari Edition							|
// +----------------------------------------------------------------------------+
// | This file is part of TBDevYSE. TBDevYSE is based on TBDev,					|
// | originally by RedBeard of TorrentBits, extensively modified by				|
// | Gartenzwerg.																|
// |									 										|
// | TBDevYSE is free software; you can redistribute it and/or modify			|
// | it under the terms of the GNU General Public License as published by		|
// | the Free Software Foundation; either version 2 of the License, or			|
// | (at your option) any later version.										|
// |																			|
// | TBDevYSE is distributed in the hope that it will be useful,				|
// | but WITHOUT ANY WARRANTY; without even the implied warranty of				|
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the				|
// | GNU General Public License for more details.								|
// |																			|
// | You should have received a copy of the GNU General Public License			|
// | along with TBDevYSE; if not, write to the Free Software Foundation,		|
// | Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA				|
// +----------------------------------------------------------------------------+
// |					       Do not remove above lines!						|
// +----------------------------------------------------------------------------+
*/

# IMPORTANT: Do not edit below unless you know what you are doing!
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');

require_once($rootpath . 'include/functions_global.php');
require_once($rootpath . 'include/functions_torrenttable.php');
require_once($rootpath . 'include/functions_commenttable.php');

///////////////////////////////////////////////////////////////////////////////
// Check open port, requires --enable-sockets
function check_port($host, $port, $timeout, $force_fsock = false) {
	if (function_exists('socket_create') && !$force_fsock) {
		// Create a TCP/IP socket.
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket == false) {
			return false;
		}
		//
		if (socket_set_nonblock($socket) == false) {
			socket_close($socket);
			return false;
		}
		//
		@socket_connect($socket, $host, $port); // will return FALSE as it's async, so no check
		//
		if (socket_set_block($socket) == false) {
			socket_close($socket);
			return false;
		}

		switch(socket_select($r = array($socket), $w = array($socket), $f = array($socket), $timeout)) {
			case 2:
			// Refused
				$result = false;
				break;
			case 1:
				$result = true;
				break;
			case 0:
				// Timeout
				$result = false;
				break;
		}

		// cleanup
		socket_close($socket);
	} else {
		$socket = @fsockopen($host, $port, $errno, $errstr, 5);
		if (!$socket)
			$result = false;
		else {
			$result = true;
			@fclose($socket);
		}
	}

	return $result;
}

function is_theme($theme = "") {
	global $rootpath;
	return is_string($theme) && preg_match('/\A[A-Za-z0-9_-]+\z/D', $theme) && file_exists($rootpath . "themes/$theme/stdhead.php") && file_exists($rootpath . "themes/$theme/stdfoot.php") && file_exists($rootpath . "themes/$theme/template.php");
}

function get_themes() {
	global $rootpath;
	$handle = opendir($rootpath . "themes");
	$themelist = array();
	while ($file = readdir($handle)) {
		if (is_theme($file) && $file != "." && $file != "..") {
			$themelist[] = $file;
		}
	}
	closedir($handle);
	sort($themelist);
	return $themelist;
}

function theme_selector($sel_theme = "") {
	$themes = get_themes();
	$content = "<select name=\"theme\">\n";
	foreach ($themes as $theme)
		$content .= "<option value=\"$theme\"".($theme == $sel_theme ? " selected" : "").">$theme</option>\n";
	$content .= "</select>";
	return $content;
}

function select_theme() {
	global $CURUSER, $default_theme;
	if ($CURUSER)
		$theme = $CURUSER["theme"];
	else
		$theme = $default_theme;
	if (!is_theme($theme))
		$theme = $default_theme;
	return $theme;
}

function decode_to_utf8($int = 0) {
	$t = '';
	if ( $int < 0 ) {
		return chr(0);
	} else if ( $int <= 0x007f ) {
		$t .= chr($int);
	} else if ( $int <= 0x07ff ) {
		$t .= chr(0xc0 | ($int >> 6));
		$t .= chr(0x80 | ($int & 0x003f));
	} else if ( $int <= 0xffff ) {
		$t .= chr(0xe0 | ($int  >> 12));
		$t .= chr(0x80 | (($int >> 6) & 0x003f));
		$t .= chr(0x80 | ($int  & 0x003f));
	} else if ( $int <= 0x10ffff ) {
		$t .= chr(0xf0 | ($int  >> 18));
		$t .= chr(0x80 | (($int >> 12) & 0x3f));
		$t .= chr(0x80 | (($int >> 6) & 0x3f));
		$t .= chr(0x80 | ($int  &  0x3f));
	} else {
		return chr(0);
	}
	return $t;
}

function convert_unicode($t, $to = 'windows-1251') {
	$to = strtolower($to);
	if ($to == 'utf-8') {
		$t = preg_replace( '#%u([0-9A-F]{1,4})#ie', "decode_to_utf8(hexdec('\\1'))", utf8_encode($t) );
		$t = urldecode ($t);
	} else {
		$t = preg_replace( '#%u([0-9A-F]{1,4})#ie', "'&#' . hexdec('\\1') . ';'", $t );
		$t = urldecode ($t);
		$t = @html_entity_decode($t, ENT_NOQUOTES, $to);
	}
	return $t;
}

function strip_magic_quotes($arr) {
	foreach ($arr as $k => $v) {
		if (is_array($v)) {
			$arr[$k] = strip_magic_quotes($v);
			} else {
			$arr[$k] = stripslashes($v);
			}
	}
	return $arr;
}

function local_user() {
	return $_SERVER["SERVER_ADDR"] == $_SERVER["REMOTE_ADDR"];
}

function sql_query($query) {
	global $queries, $query_stat, $querytime;
	$queries++;
	$query_start_time = timer(); // Start time
	$result = mysql_query($query);
	$query_end_time = timer(); // End time
	$query_time = ($query_end_time - $query_start_time);
	$querytime = $querytime + $query_time;
	$query_stat[] = array("seconds" => $query_time, "query" => $query);
	return $result;
}

function dbconn($autoclean = false, $lightmode = false) {
	global $mysql_host, $mysql_user, $mysql_pass, $mysql_db, $mysql_charset;

	if (!@mysql_connect($mysql_host, $mysql_user, $mysql_pass))
		die('Database unavailable.');

	mysql_select_db($mysql_db)
		or die('Database unavailable.');

	mysql_query("SET NAMES $mysql_charset");

	userlogin($lightmode);

	if (basename($_SERVER['SCRIPT_FILENAME']) == 'index.php')
		register_shutdown_function("autoclean");

	register_shutdown_function("mysql_close");

}

function userlogin($lightmode = false) {
	global $SITE_ONLINE, $default_language, $tracker_lang, $use_lang, $use_ipbans, $_COOKIE_SALT;
	unset($GLOBALS["CURUSER"]);

	if ($_COOKIE_SALT == 'default' && $_SERVER['SERVER_ADDR'] != '127.0.0.1' && $_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR'])
		die('Скрипт заблокирован! Измените значение переменной $_COOKIE_SALT в файле include/config.local.php на случайное');

	if (empty($_COOKIE_SALT) || !isset($_COOKIE_SALT))
		die('Идите и учите <a href="http://www.php.net">PHP</a>... Сказано было ИЗМЕНИТЬ значение, а не удалить переменную!');

	$ip = getip();
	$nip = ip2long($ip);

	if ($use_ipbans && !$lightmode) {
		$res = sql_query("SELECT * FROM bans WHERE $nip >= first AND $nip <= last") or sqlerr(__FILE__, __LINE__);
		if (mysql_num_rows($res) > 0) {
			$comment = mysql_fetch_assoc($res);
			$comment = $comment["comment"];
			header("HTTP/1.0 403 Forbidden");
			print("<html><body><h1>403 Forbidden</h1>Unauthorized IP address.</body></html>\n");
			die;
		}
	}

	$c_uid = isset($_COOKIE[COOKIE_UID]) ? $_COOKIE[COOKIE_UID] : '';
	$c_pass = isset($_COOKIE[COOKIE_PASSHASH]) ? $_COOKIE[COOKIE_PASSHASH] : '';
	$row = false;
	if ($SITE_ONLINE && is_string($c_uid) && ctype_digit($c_uid) &&
		is_string($c_pass) && preg_match('/\A[a-f0-9]{64}\z/D', $c_pass)) {
		$id = intval($c_uid);
		$token_hash = hash('sha256', $c_pass);
		$token_res = sql_query('SELECT passhash_fingerprint FROM auth_tokens WHERE token_hash = '.sqlesc($token_hash).
			' AND uid = '.$id.' AND expires > '.time().' LIMIT 1') or sqlerr(__FILE__, __LINE__);
		$token_row = mysql_fetch_assoc($token_res);
		if ($token_row) {
			$res = sql_query('SELECT * FROM users WHERE id = '.$id) or sqlerr(__FILE__, __LINE__);
			$candidate = mysql_fetch_assoc($res);
			if ($candidate && hash('sha256', $candidate['passhash']) === $token_row['passhash_fingerprint'])
				$row = $candidate;
		}
	}
	if (!$row) {
		if ($use_lang)
			include_once('languages/lang_' . $default_language . '/lang_main.php');
		user_session();
		return;
	}

	$updateset = array();

	if ($ip != $row['ip']) {
		$updateset[] = 'ip = '. sqlesc($ip);
		$row['ip'] = $ip;
	}
	$updateset[] = 'last_access = ' . sqlesc(get_date_time());

	if (count($updateset))
		sql_query('UPDATE users SET '.implode(', ', $updateset).' WHERE id = ' . $row['id']) or sqlerr(__FILE__,__LINE__);

	if ($row['override_class'] < $row['class'])
		$row['class'] = $row['override_class']; // Override class and save in GLOBAL array below.

	$GLOBALS["CURUSER"] = $row;
	if ($use_lang)
		include_once('languages/lang_' . (preg_match('/\A[a-z]+\z/D', $row['language']) ? $row['language'] : $default_language) . '/lang_main.php');

	if ($row['enabled'] == 'no') {
		$GLOBALS['use_blocks'] = 0;
		list($reason, $disuntil) = mysql_fetch_row(sql_query('SELECT reason, disuntil FROM users_ban WHERE userid = '.$row['id']));
		stderr($tracker_lang['error'], 'Вы забанены на трекере.' . ($disuntil != '0000-00-00 00:00:00' ? '<br />Дата снятия бана: '.$disuntil : '<br />Дата снятия бана: никогда') . '<br />Причина: '.$reason);
	}

	if (!$lightmode)
		user_session();

}

function get_server_load() {
	global $tracker_lang, $phpver;
	if (strtolower(substr(PHP_OS, 0, 3)) === 'win') {
		return 0;
	} elseif (@file_exists("/proc/loadavg")) {
		$load = @file_get_contents("/proc/loadavg");
		$serverload = explode(" ", $load);
		$serverload[0] = round($serverload[0], 4);
		if(!$serverload) {
			$load = @exec("uptime");
			$load = @split("load averages?: ", $load);
			$serverload = explode(",", $load[1]);
		}
	} else {
		$load = @exec("uptime");
		$load = @split("load averages?: ", $load);
		$serverload = explode(",", $load[1]);
	}
	$returnload = trim($serverload[0]);
	if(!$returnload) {
		$returnload = $tracker_lang['unknown'];
	}
	return $returnload;
}

function user_session() {
	global $CURUSER, $use_sessions;

	if (!$use_sessions)
		return;

	$ip = getip();
	$url = getenv("REQUEST_URI");

	if (!$CURUSER) {
		$uid = -1;
		$username = '';
		$class = -1;
	} else {
		$uid = $CURUSER['id'];
		$username = $CURUSER['username'];
		$class = $CURUSER['class'];
	}

	$past = time() - 300;
	$sid = session_id();
	$where = array();
	$updateset = array();
	if ($sid)
		$where[] = "sid = ".sqlesc($sid);
	elseif ($uid)
		$where[] = "uid = $uid";
	else
		$where[] = "ip = ".sqlesc($ip);
	//sql_query("DELETE FROM sessions WHERE ".implode(" AND ", $where));
	$ctime = time();
	$agent = $_SERVER["HTTP_USER_AGENT"];
	$updateset[] = "sid = ".sqlesc($sid);
	$updateset[] = "uid = ".sqlesc($uid);
	$updateset[] = "username = ".sqlesc($username);
	$updateset[] = "class = ".sqlesc($class);
	$updateset[] = "ip = ".sqlesc($ip);
	$updateset[] = "time = ".sqlesc($ctime);
	$updateset[] = "url = ".sqlesc($url);
	$updateset[] = "useragent = ".sqlesc($agent);
	if (count($updateset))
		sql_query("UPDATE sessions SET ".implode(", ", $updateset)." WHERE ".implode(" AND ", $where)) or sqlerr(__FILE__,__LINE__);
	if (mysql_modified_rows() < 1)
		sql_query("INSERT INTO sessions (sid, uid, username, class, ip, time, url, useragent) VALUES (".implode(", ", array_map("sqlesc",
									array($sid, $uid, $username, $class, $ip, $ctime, $url, $agent))).")") or sqlerr(__FILE__,__LINE__);
}

function unesc($x) {
	if (get_magic_quotes_gpc())
		return stripslashes($x);
	return $x;
}

function gzip() {
	global $use_gzip;
	static $already_loaded;
	if (extension_loaded('zlib') && ini_get('zlib.output_compression') != '1' && ini_get('output_handler') != 'ob_gzhandler' && $use_gzip && !$already_loaded) {
		@ob_start('ob_gzhandler');
	} elseif (!$already_loaded)
		@ob_start();
	$already_loaded = true;
}

// IP Validation
function validip($ip) {
	if (!empty($ip) && $ip == long2ip(ip2long($ip)))
	{
		// reserved IANA IPv4 addresses
		// http://www.iana.org/assignments/ipv4-address-space
		$reserved_ips = array (
				array('0.0.0.0','2.255.255.255'),
				array('10.0.0.0','10.255.255.255'),
				array('127.0.0.0','127.255.255.255'),
				array('169.254.0.0','169.254.255.255'),
				array('172.16.0.0','172.31.255.255'),
				array('192.0.2.0','192.0.2.255'),
				array('192.168.0.0','192.168.255.255'),
				array('255.255.255.0','255.255.255.255')
		);

		foreach ($reserved_ips as $r) {
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}
	else return false;
}

function getip() {
	global $trusted_proxies;
	$ip = $_SERVER['REMOTE_ADDR'];
	// X-Forwarded-For is client-controlled; only a proxy in $trusted_proxies may name the client.
	// Walk right to left past trusted hops and take the first untrusted one; anything left of it is spoofable.
	// ponytail: exact-IP match, add CIDR support if a proxy pool outgrows a list.
	if (empty($trusted_proxies) || !in_array($ip, $trusted_proxies, true) || empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		return $ip;
	foreach (array_reverse(array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']))) as $hop) {
		if (in_array($hop, $trusted_proxies, true))
			continue;
		// IPv4 only: userlogin() feeds ip2long() into the bans query.
		return filter_var($hop, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $hop : $ip;
	}
	return $ip;
}

function autoclean() {
	global $autoclean_interval, $rootpath;

	$now = time();
	$docleanup = 0;

	$res = sql_query("SELECT value_u FROM avps WHERE arg = 'lastcleantime'");
	$row = mysql_fetch_array($res);
	if (!$row) {
		sql_query("INSERT INTO avps (arg, value_u) VALUES ('lastcleantime',$now)");
		return;
	}
	$ts = $row[0];
	if ($ts + $autoclean_interval > $now)
		return;
	if ($ts > $now) { // Fuck, someone has set time in future!
		sql_query("UPDATE avps SET value_u=$now WHERE arg='lastcleantime' AND value_u = $ts");
		return;
	}
	sql_query("UPDATE avps SET value_u=$now WHERE arg='lastcleantime' AND value_u = $ts");
	if (!mysql_affected_rows())
		return;

	require_once($rootpath . 'include/cleanup.php');

	docleanup();
}

function mksize($bytes) {
	if ($bytes < 1000 * 1024)
		return number_format($bytes / 1024, 2) . " kB";
	elseif ($bytes < 1000 * 1048576)
		return number_format($bytes / 1048576, 2) . " MB";
	elseif ($bytes < 1000 * 1073741824)
		return number_format($bytes / 1073741824, 2) . " GB";
	else
		return number_format($bytes / 1099511627776, 2) . " TB";
}

function mksizeint($bytes) {
		$bytes = max(0, $bytes);
		if ($bytes < 1000)
				return floor($bytes) . " B";
		elseif ($bytes < 1000 * 1024)
				return floor($bytes / 1024) . " kB";
		elseif ($bytes < 1000 * 1048576)
				return floor($bytes / 1048576) . " MB";
		elseif ($bytes < 1000 * 1073741824)
				return floor($bytes / 1073741824) . " GB";
		else
				return floor($bytes / 1099511627776) . " TB";
}

function deadtime() {
	global $announce_interval;
	return time() - floor($announce_interval * 1.3);
}

function mkprettytime($s) {
    if ($s < 0)
	$s = 0;
    $t = array();
    foreach (array("60:sec","60:min","24:hour","0:day") as $x) {
		$y = explode(":", $x);
		if ($y[0] > 1) {
		    $v = $s % $y[0];
		    $s = floor($s / $y[0]);
		} else
		    $v = $s;
	$t[$y[1]] = $v;
    }

    if ($t["day"])
	return $t["day"] . "d " . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    if ($t["hour"])
	return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
	return sprintf("%d:%02d", $t["min"], $t["sec"]);
}

function mkglobal($vars) {
	if (!is_array($vars))
		$vars = explode(":", $vars);
	foreach ($vars as $v) {
		if (isset($_GET[$v]))
			$GLOBALS[$v] = unesc($_GET[$v]);
		elseif (isset($_POST[$v]))
			$GLOBALS[$v] = unesc($_POST[$v]);
		else
			return 0;
	}
	return 1;
}

function tr($x, $y, $noesc=0, $prints = true, $width = "", $relation = '') {
	if ($noesc)
		$a = $y;
	else {
		$a = htmlspecialchars_uni($y);
		$a = str_replace("\n", "<br />\n", $a);
	}
	if ($prints) {
	  $print = "<td width=\"". $width ."\" class=\"heading\" valign=\"top\" align=\"right\">$x</td>";
	  $colpan = "align=\"left\"";
	} else {
		$colpan = "colspan=\"2\"";
	}

	print("<tr".( $relation ? " relation=\"$relation\"" : "").">$print<td valign=\"top\" $colpan>$a</td></tr>\n");
}

function validfilename($name) {
	return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
}

function validemail($email) {
	// FILTER_VALIDATE_EMAIL accepts quoted local parts, which can carry markup; real mailboxes never need them.
	return filter_var($email, FILTER_VALIDATE_EMAIL) && strpbrk($email, '"<>') === false;
}

function mail_possible($email) {
	list(, $domain) = explode('@', $email);
	if (function_exists('checkdnsrr'))
		return checkdnsrr($domain, 'MX');
	else
		return true;
}

function send_pm($sender, $receiver, $added, $subject, $msg) {
	sql_query('INSERT INTO messages (sender, receiver, added, subject, msg) VALUES ('.implode(', ', array_map('sqlesc', array($sender, $receiver, $added, $subject, $msg))).')') or sqlerr(__FILE__,__LINE__);
}

function sent_mail($to,$fromname,$fromemail,$subject,$body,$multiple=false,$multiplemail='') {
	global $SITENAME,$SITEEMAIL,$smtptype,$smtp,$smtp_host,$smtp_port,$smtp_from,$smtpaddress,$accountname,$accountpassword,$rootpath;
	# Sent Mail Function v.05 by xam (This function to help avoid spam-filters.)
	$result = true;
	if ($smtptype == 'default') {
		@mail($to, $subject, $body, "From: $SITEEMAIL") or $result = false;
	} elseif ($smtptype == 'advanced') {
	# Is the OS Windows or Mac or Linux?
	if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
		$eol="\r\n";
		$windows = true;
	}
	elseif (strtoupper(substr(PHP_OS,0,3)=='MAC'))
		$eol="\r";
	else
		$eol="\n";
	$mid = md5(getip() . $fromname);
	$name = $_SERVER["SERVER_NAME"];
	$headers .= "From: $fromname <$fromemail>".$eol;
	$headers .= "Reply-To: $fromname <$fromemail>".$eol;
	$headers .= "Return-Path: $fromname <$fromemail>".$eol;
	$headers .= "Message-ID: <$mid.thesystem@$name>".$eol;
	$headers .= "X-Mailer: PHP v".phpversion().$eol;
    $headers .= "MIME-Version: 1.0".$eol;
    $headers .= "Content-type: text/plain; charset=windows-1251".$eol;
    $headers .= "X-Sender: PHP".$eol;
    if ($multiple)
    	$headers .= "Bcc: $multiplemail.$eol";
	if ($smtp == "yes") {
		ini_set('SMTP', $smtp_host);
		ini_set('smtp_port', $smtp_port);
		if ($windows)
			ini_set('sendmail_from', $smtp_from);
		}

    	@mail($to, $subject, $body, $headers) or $result = false;

    	ini_restore(SMTP);
		ini_restore(smtp_port);
		if ($windows)
			ini_restore(sendmail_from);
	} elseif ($smtptype == 'external') {
		require_once($rootpath . 'include/smtp/smtp.lib.php');
		$mail = new smtp;
		$mail->debug(false);
		$mail->open($smtp_host, $smtp_port);
		if (!empty($accountname) && !empty($accountpassword))
			$mail->auth($accountname, $accountpassword);
		$mail->from($SITEEMAIL);
		$mail->to($to);
		$mail->subject($subject);
		$mail->body($body);
		$result = $mail->send();
		$mail->close();
	} else
		$result = false;

	return $result;
}

function sqlesc($value, $force = false) {
    // Stripslashes
    /*if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }*/
    // Quote if not a number or a numeric string
    if (!is_numeric($value) || $force) {
        $value = "'" . mysql_real_escape_string($value) . "'";
    }
    return $value;
}

function sqlwildcardesc($x) {
	return str_replace(array("%","_"), array("\\%","\\_"), mysql_real_escape_string($x));
}

function urlparse($m) {
	$t = $m[0];
	if (preg_match(',^\w+://,', $t))
		return "<a href=\"$t\">$t</a>";
	return "<a href=\"http://$t\">$t</a>";
}

function parsedescr($d, $html) {
	if (!$html) {
	  $d = htmlspecialchars_uni($d);
	  $d = str_replace("\n", "\n<br>", $d);
	}
	return $d;
}

function stdhead($title = "", $msgalert = true) {
	global $CURUSER, $SITE_ONLINE, $FUNDS, $SITENAME, $DEFAULTBASEURL, $ss_uri, $tracker_lang, $default_theme, $keywords, $description, $pic_base_url;

	if (!$SITE_ONLINE)
		die('Site is down for maintenance, please check back again later... thanks<br />');

	header ('Content-Type: text/html; charset=' . $tracker_lang['language_charset']);
	header ('X-Powered-by: TBDev Yuna Scatari Edition - http://bit-torrent.kiev.ua');
	header ('X-Chocolate-to: ICQ 7282521');
	header ('Cache-Control: no-cache');
	header ('Pragma: no-cache');
	if ($title == '')
		$title = $SITENAME;
	else
		$title = $SITENAME . ' :: ' . htmlspecialchars_uni($title);

	$ss_uri = select_theme();

	if ($msgalert && $CURUSER) {
		$res = sql_query('SELECT COUNT(*) FROM messages WHERE receiver = ' . $CURUSER['id'] . ' AND unread="yes"') or die('OopppsY!');
		$arr = mysql_fetch_row($res);
		$unread = $arr[0];
	}

	require_once('themes/' . $ss_uri . '/template.php');
	require_once('themes/' . $ss_uri . '/stdhead.php');

} // stdhead

function stdfoot() {
	global $CURUSER, $ss_uri, $tracker_lang, $queries, $tstart, $query_stat, $querytime;

	if (!is_theme($ss_uri) || empty($ss_uri))
		$ss_uri = select_theme();

	require_once('themes/' . $ss_uri . '/template.php');
	require_once('themes/' . $ss_uri . '/stdfoot.php');
	if (DEBUG_MODE && count($query_stat)) {
		foreach ($query_stat as $key => $value) {
			print('<div>['.($key+1).'] => <b>'.($value['seconds'] > 0.01 ? '<font color="red" title="Рекомендуется оптимизировать запрос. Время исполнения превышает норму.">'.$value['seconds'].'</font>' : '<font color="green" title="Запрос не нуждается в оптимизации. Время исполнения допустимое.">'.$value['seconds'].'</font>' ).'</b> ['.htmlspecialchars_uni($value['query']).']</div>'."\n");
		}
		print('<br />');
	}
}

function genbark($x,$y) {
	stdhead($y);
	print('<h2>' . htmlspecialchars_uni($y) . '</h2>');
	print('<p>' . htmlspecialchars_uni($x) . '</p>');
	stdfoot();
	exit();
}

function valid_avatar_url($url) {
    if (!is_string($url) || strlen($url) > 100 || strpos($url, '\\') !== false ||
        preg_match('/[\x00-\x20\x7f<>"]/', $url))
        return false;
    $parts = @parse_url($url);
    if ($parts === false || !isset($parts['scheme'], $parts['host']) ||
        !in_array(strtolower($parts['scheme']), array('http', 'https')) ||
        isset($parts['user']) || isset($parts['pass']) ||
        !preg_match('/\A[A-Za-z0-9-]+(?:\.[A-Za-z0-9-]+)+\z/D', $parts['host']) ||
        !isset($parts['path']) ||
        !preg_match('/\.(?:gif|jpe?g|png)\z/iD', $parts['path']))
        return false;
    return true;
}

function safe_local_return($url, $fallback = 'index.php') {
    if (!is_string($url) || strpos($url, '\\') !== false ||
        !preg_match('/\A(?:\/(?!\/)|[A-Za-z0-9])[^\x00-\x20\x7f]*\z/D', $url))
        return $fallback;
    $parts = @parse_url($url);
    if ($parts === false || isset($parts['scheme']) || isset($parts['host']) ||
        isset($parts['user']) || isset($parts['pass']))
        return $fallback;
    return $url;
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        if (!function_exists('openssl_random_pseudo_bytes'))
            die('Secure random source unavailable.');
        $strong = false;
        $bytes = openssl_random_pseudo_bytes(32, $strong);
        if ($bytes === false || !$strong)
            die('Secure random source unavailable.');
        $_SESSION['csrf_token'] = bin2hex($bytes);
    }
    return $_SESSION['csrf_token'];
}

function csrf_valid($token) {
    return is_string($token) && isset($_SESSION['csrf_token']) &&
        strlen($token) == 64 && $_SESSION['csrf_token'] === $token;
}

function csrf_require_post() {
    if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] != 'POST' ||
        !csrf_valid(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
        header('HTTP/1.1 403 Forbidden');
        exit;
    }
}

// Inline POST form for a state-changing link: the CSRF token travels in the body, never in a URL.
// $label_html is trusted markup; $fields values and $confirm are escaped here.
function post_link($action, $fields, $label_html, $confirm = '') {
    $html = '<form method="post" action="' . htmlspecialchars_uni($action) . '" style="display: inline; margin: 0"' .
        ($confirm !== '' ? ' onsubmit="return confirm(\'' . htmlspecialchars_uni(addslashes($confirm)) . '\');"' : '') .
        '><input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
    foreach ($fields as $name => $value)
        $html .= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars_uni($value) . '">';
    return $html . '<button type="submit" style="border: 0; background: none; padding: 0; cursor: pointer; color: inherit; font: inherit; text-decoration: underline">' . $label_html . '</button></form>';
}

function password_matches($password, $secret, $hash) {
    if (!is_string($password) || !is_string($hash))
        return false;
    return password_matches_exact($password, $secret, $hash) || password_matches_exact(trim($password), $secret, $hash);
}

// Legacy hashes are md5(secret.password.secret), stored bare or wrapped in bcrypt as "md5:<bcrypt>"
// by migrations/2026-wrap-legacy-hashes.php; takelogin.php rewrites both as plain bcrypt on next login.
function password_matches_exact($password, $secret, $hash) {
    if (preg_match('/\A[a-f0-9]{32}\z/iD', $hash))
        return $hash === md5($secret . $password . $secret);
    if (strncmp($hash, 'md5:', 4) == 0)
        return password_verify(md5($secret . $password . $secret), substr($hash, 4));
    return password_verify($password, $hash);
}

function make_password_hash($password) {
    if (!function_exists('password_hash'))
        die('Password hashing unavailable.');
    $hash = password_hash($password, PASSWORD_BCRYPT);
    if ($hash === false)
        die('Password hashing unavailable.');
    return $hash;
}

function mksecret($length = 20) {
    if (!is_int($length) || $length < 1)
        die('Invalid secret length.');
    if (!function_exists('openssl_random_pseudo_bytes'))
        die('Secure random source unavailable.');
    $strong = false;
    $bytes = openssl_random_pseudo_bytes((int)ceil($length / 2), $strong);
    if ($bytes === false || !$strong)
        die('Secure random source unavailable.');
    return substr(bin2hex($bytes), 0, $length);
}

function httperr($code = 404) {
	$sapi_name = php_sapi_name();
	if ($sapi_name == 'cgi' OR $sapi_name == 'cgi-fcgi') {
		header('Status: 404 Not Found');
	} else {
		header('HTTP/1.1 404 Not Found');
	}
	exit;
}

function gmtime() {
	return strtotime(get_date_time());
}

function logincookie($id, $passhash, $updatedb = 1, $expires = 0) {
	$id = intval($id);
	if (!$expires)
		$expires = time() + 30 * 86400;
	$token = mksecret(64);
	$token_hash = hash('sha256', $token);
	$fingerprint = hash('sha256', $passhash);
	sql_query('INSERT INTO auth_tokens (token_hash, uid, passhash_fingerprint, expires) VALUES ('.
		implode(', ', array_map('sqlesc', array($token_hash, $id, $fingerprint, $expires))).')') or sqlerr(__FILE__, __LINE__);
	$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||
		(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
	if (session_id()) {
		unset($_SESSION['csrf_token']);
		@session_regenerate_id(true);
	}
	setcookie(COOKIE_UID, $id, $expires, '/', '', $secure, true);
	setcookie(COOKIE_PASSHASH, $token, $expires, '/', '', $secure, true);
	if ($updatedb)
		sql_query('UPDATE users SET last_login = NOW() WHERE id = '.$id);
}

function logoutcookie() {
	if (isset($_COOKIE[COOKIE_PASSHASH]) && is_string($_COOKIE[COOKIE_PASSHASH]) &&
		preg_match('/\A[a-f0-9]{64}\z/D', $_COOKIE[COOKIE_PASSHASH]))
		sql_query('DELETE FROM auth_tokens WHERE token_hash = '.sqlesc(hash('sha256', $_COOKIE[COOKIE_PASSHASH])))
			or sqlerr(__FILE__, __LINE__);
	$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||
		(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
	setcookie(COOKIE_UID, '', time() - 3600, '/', '', $secure, true);
	setcookie(COOKIE_PASSHASH, '', time() - 3600, '/', '', $secure, true);
	unset($_SESSION['csrf_token']);
	if (session_id())
		@session_regenerate_id(true);
}

function loggedinorreturn($nowarn = false) {
	global $CURUSER, $DEFAULTBASEURL;
	if (!$CURUSER) {
		header('Location: '.$DEFAULTBASEURL.'/login.php?returnto=' . urlencode(basename($_SERVER['REQUEST_URI'])).($nowarn ? '&nowarn=1' : ''));
		exit();
	}
}

function deletetorrent($id) {
	global $torrent_dir;
	$images = mysql_fetch_array(sql_query('SELECT image1, image2, image3, image4, image5 FROM torrents WHERE id = '.$id));
	if ($images) { for ($x=1; $x <= 5; $x++) {
			if ($images['image' . $x] != '' && file_exists('torrents/images/' . $images['image' . $x]))
				unlink('torrents/images/' . $images['image' . $x]);
		}
	}
	sql_query('DELETE FROM torrents WHERE id = '.$id);
	sql_query('DELETE FROM snatched WHERE torrent = '.$id);
	sql_query('DELETE FROM bookmarks WHERE torrentid = '.$id);
	sql_query('DELETE FROM readtorrents WHERE torrentid = '.$id);
	foreach(explode('.','peers.files.comments.ratings') as $x)
		sql_query('DELETE FROM '.$x.' WHERE torrent = '.$id);
	sql_query('DELETE FROM torrents_scrape WHERE tid = '.$id);
	sql_query('DELETE FROM torrents_descr WHERE tid = '.$id);
	unlink($torrent_dir.'/'.$id.'.torrent');
}

function pager($rpp, $count, $href, $opts = array()) {
	$pages = ceil($count / $rpp);

	if (!isset($opts['lastpagedefault']))
		$pagedefault = 0;
	else {
		$pagedefault = floor(($count - 1) / $rpp);
		if ($pagedefault < 0)
			$pagedefault = 0;
	}

	if (isset($_GET['page'])) {
		$page = 0 + (int) $_GET['page'];
		if ($page < 0)
			$page = $pagedefault;
	}
	else
		$page = $pagedefault;

	$pager = "<td class=\"pager\">Страницы:</td><td class=\"pagebr\">&nbsp;</td>";
	$pager2 = "";
	$bregs = "";

	$mp = $pages - 1;
	$as = "<b>«</b>";
	if ($page >= 1) {
		$pager .= "<td class=\"pager\">";
		$pager .= "<a href=\"{$href}page=" . ($page - 1) . "\" style=\"text-decoration: none;\">$as</a>";
		$pager .= "</td><td class=\"pagebr\">&nbsp;</td>";
	}

	$as = "<b>»</b>";
	if ($page < $mp && $mp >= 0) {
		$pager2 .= "<td class=\"pager\">";
		$pager2 .= "<a href=\"{$href}page=" . ($page + 1) . "\" style=\"text-decoration: none;\">$as</a>";
		$pager2 .= "</td>$bregs";
	} else
		$pager2 .= $bregs;

	if ($count) {
		$pagerarr = array();
		$dotted = 0;
		$dotspace = 3;
		$dotend = $pages - $dotspace;
		$curdotend = $page - $dotspace;
		$curdotstart = $page + $dotspace;
		for ($i = 0; $i < $pages; $i++) {
			if (($i >= $dotspace && $i <= $curdotend) || ($i >= $curdotstart && $i < $dotend)) {
				if (!$dotted)
				   $pagerarr[] = "<td class=\"pager\">...</td><td class=\"pagebr\">&nbsp;</td>";
				$dotted = 1;
				continue;
			}
			$dotted = 0;
			$start = $i * $rpp + 1;
			$end = $start + $rpp - 1;
			if ($end > $count)
				$end = $count;

			 $text = $i+1;
			if ($i != $page)
				$pagerarr[] = "<td class=\"pager\"><a title=\"$start&nbsp;-&nbsp;$end\" href=\"{$href}page=$i\" style=\"text-decoration: none;\"><b>$text</b></a></td><td class=\"pagebr\">&nbsp;</td>";
			else
				$pagerarr[] = "<td class=\"highlight\"><b>$text</b></td><td class=\"pagebr\">&nbsp;</td>";

				  }
		$pagerstr = join("", $pagerarr);
		$pagertop = "<table class=\"main\"><tr>$pager $pagerstr $pager2</tr></table>\n";
		$pagerbottom = "Всего $count на $i страницах по $rpp на каждой странице.<br /><br /><table class=\"main\">$pager $pagerstr $pager2</table>\n";
	}
	else {
		$pagertop = $pager;
		$pagerbottom = $pagertop;
	}

	$start = $page * $rpp;

	return array($pagertop, $pagerbottom, "LIMIT $start,$rpp");
}

function downloaderdata($res) {
	$rows = array();
	$ids = array();
	$peerdata = array();
	while ($row = mysql_fetch_assoc($res)) {
		$rows[] = $row;
		$id = $row["id"];
		$ids[] = $id;
		$peerdata[$id] = array(downloaders => 0, seeders => 0, comments => 0);
	}

	if (count($ids)) {
		$allids = implode(",", $ids);
		$res = sql_query("SELECT COUNT(*) AS c, torrent, seeder FROM peers WHERE torrent IN ($allids) GROUP BY torrent, seeder");
		while ($row = mysql_fetch_assoc($res)) {
			if ($row["seeder"] == "yes")
				$key = "seeders";
			else
				$key = "downloaders";
			$peerdata[$row["torrent"]][$key] = $row["c"];
		}
		$res = sql_query("SELECT COUNT(*) AS c, torrent FROM comments WHERE torrent IN ($allids) GROUP BY torrent");
		while ($row = mysql_fetch_assoc($res)) {
			$peerdata[$row["torrent"]]["comments"] = $row["c"];
		}
	}

	return array($rows, $peerdata);
}

function genrelist() {
	$ret = array();
	$res = sql_query('SELECT id, name FROM categories ORDER BY sort ASC');
	while ($row = mysql_fetch_array($res))
		$ret[] = $row;
	return $ret;
}

function linkcolor($num) {
	if (!$num)
		return 'red';
//	if ($num == 1)
//		return 'yellow';
	return 'green';
}

function ratingpic($num) {
	global $pic_base_url, $tracker_lang, $ss_uri;
	$r = round($num);
	if ($r < 1 || $r > 5)
		return;
	return "<img src=\"themes/$ss_uri/images/rating/$r.gif\" border=\"0\" alt=\"".$tracker_lang['rating'].": $num / 5\" />";
}

function writecomment($userid, $comment) {
    $userid = intval($userid);
    if (!$userid)
        throw new Exception(E_FATAL_ERROR, 'User ID cannot be 0 or null');
	/*$res = sql_query("SELECT modcomment FROM users WHERE id = $userid") or sqlerr(__FILE__, __LINE__);
	$arr = mysql_fetch_assoc($res);

	$modcomment = date('d-m-Y') . ' - ' . $comment . '' . ($arr['modcomment'] != '' ? "\n" : "") . $arr['modcomment'];
	$modcom = sqlesc($modcomment);

	return sql_query("UPDATE users SET modcomment = $modcom WHERE id = $userid") or sqlerr(__FILE__, __LINE__);*/

    $modcomment = sqlesc(date('d-m-Y') . ' - ' . $comment);
    return sql_query("UPDATE users SET modcomment = CONCAT_WS('\n', $modcomment, modcomment) WHERE id = $userid") or sqlerr(__FILE__,__LINE__);
}

function hash_pad($hash) {
	return str_pad($hash, 20);
}

function get_user_icons($arr, $big = false) {
		if ($big) {
				$donorpic = "starbig.gif";
				$warnedpic = "warnedbig.gif";
				$disabledpic = "disabledbig.gif";
				$style = "style='margin-left: 4pt'";
		} else {
				$donorpic = "star.gif";
				$warnedpic = "warned.gif";
				$disabledpic = "disabled.gif";
				$parkedpic = "parked.gif";
				$style = "style=\"margin-left: 2pt\"";
		}
		$pics = $arr["donor"] == "yes" ? "<img src=\"pic/$donorpic\" alt='Donor' border=\"0\" $style>" : "";
		if ($arr["enabled"] == "yes")
				$pics .= $arr["warned"] == "yes" ? "<img src=pic/$warnedpic alt=\"Warned\" border=0 $style>" : "";
		else
				$pics .= "<img src=\"pic/$disabledpic\" alt=\"Disabled\" border=\"0\" $style>\n";
		$pics .= $arr["parked"] == "yes" ? "<img src=pic/$parkedpic alt=\"Parked\" border=\"0\" $style>" : "";
		return $pics;
}

function parked() {
	   global $CURUSER;
	   if ($CURUSER['parked'] == 'yes')
		  stderr($tracker_lang['error'], 'Ваш аккаунт припаркован.');
}

function magnet($html = true, $info_hash, $name, $size, $announces = array()) {
	$ampersand = $html ? '&amp;' : '&';
	return sprintf('magnet:?xt=urn:btih:%2$s%1$sdn=%3$s%1$sxl=%4$d%1$str=%5$s', $ampersand, $info_hash, urlencode($name), $size, implode($ampersand . 'tr=', $announces));
}

// В этой строке забит копирайт. При его убирании можешь поплатиться рабочим трекером ;) В данном случае - убирая строчки ниже ты не сможешь использовать трекер.
define ('VERSION', '');
define ('NUM_VERSION', '2.1.18');
define ('TBVERSION', 'Powered by <a href="http://www.tbdev.net" target="_blank" style="cursor: help;" title="Бесплатная OpenSource база" class="copyright">TBDev</a> v'.NUM_VERSION.' <a href="http://bit-torrent.kiev.ua" target="_blank" style="cursor: help;" title="Сайт разработчика движка" class="copyright">Yuna Scatari Edition</a> '.VERSION.' Copyright &copy; 2001-'.date('Y'));

function mysql_modified_rows () {
	$info_str = mysql_info();
	$a_rows = mysql_affected_rows();
	preg_match("/Rows matched: ([0-9]*)/", $info_str, $r_matched);
	return ($a_rows < 1)?($r_matched[1]?$r_matched[1]:0):$a_rows;
}

?>