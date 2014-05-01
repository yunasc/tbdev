<?php

/*
// +--------------------------------------------------------------------------+
// | Project:    TBDevYSE - TBDev Yuna Scatari Edition                        |
// +--------------------------------------------------------------------------+
// | This file is part of TBDevYSE. TBDevYSE is based on TBDev,               |
// | originally by RedBeard of TorrentBits, extensively modified by           |
// | Gartenzwerg.                                                             |
// |                                                                          |
// | TBDevYSE is free software; you can redistribute it and/or modify         |
// | it under the terms of the GNU General Public License as published by     |
// | the Free Software Foundation; either version 2 of the License, or        |
// | (at your option) any later version.                                      |
// |                                                                          |
// | TBDevYSE is distributed in the hope that it will be useful,              |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
// | GNU General Public License for more details.                             |
// |                                                                          |
// | You should have received a copy of the GNU General Public License        |
// | along with TBDevYSE; if not, write to the Free Software Foundation,      |
// | Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA            |
// +--------------------------------------------------------------------------+
// |                                               Do not remove above lines! |
// +--------------------------------------------------------------------------+
*/

// Да простят меня боги за эти уродства, но это всё писалось в 23:59

require_once('include/bittorrent.php');

// Dirty hack to prevent ghost Guests on website
$old_us = $use_sessions;
$use_sessions = 0;
dbconn();
$use_sessions = $old_us;

if (!$_GET['token'])
	loggedinorreturn(); // А вдруг гугл прийдет на такие страницы? Не надо...

require_once('include/scraper/httptscraper.php');
require_once('include/scraper/udptscraper.php');

function scrape($tid, $url, $info_hash) {
	$timeout = 5;
	$udp = new udptscraper($timeout);
	$http = new httptscraper($timeout);

	try {
		if (substr($url, 0, 6) == 'udp://')
			$data = $udp->scrape($url, $info_hash);
		else
			$data = $http->scrape($url, $info_hash);
		$data = $data[$info_hash];
		sql_query('UPDATE torrents_scrape SET state = "ok", error = "", seeders = '.intval($data['seeders']).', leechers = '.intval($data['leechers']).' WHERE tid = '.$tid.' AND url = '.sqlesc($url)) or print(mysql_error()."\n");
		return true;
	} catch (ScraperException $e) {
		sql_query('UPDATE torrents_scrape SET state = "error", error = '.sqlesc($e->getMessage()).', seeders = 0, leechers = 0 WHERE tid = '.$tid.' AND url = '.sqlesc($url)) or print(mysql_error()."\n");
		return false;
	}
}

function generate_token($tid, $url, $info_hash) {
	return md5(implode('', array($tid, $url, $info_hash, COOKIE_SALT)));
}

function check_token($token, $tid, $url, $info_hash) {
	return $token === md5(implode('', array($tid, $url, $info_hash, COOKIE_SALT)));
}

$tid = intval($_GET['id']);

if (!$tid)
	die('WTF?!');

if ($_GET['info_hash'] && $_GET['url']) {
	$token = strval($_GET['token']);
	$url = strval($_GET['url']);
	$info_hash = strval($_GET['info_hash']);
	if (strlen($info_hash) != 40)
		die('Invalid len info_hash supplied');
	if (!check_token($token, $tid, $url, $info_hash))
		die('Invalid token');
	echo scrape($tid, $url, $info_hash);
	exit;
}

list($name, $cur_visible, $multitracker, $last_mt_update) = mysql_fetch_row(sql_query('SELECT name, visible, multitracker, last_mt_update FROM torrents WHERE id = '.$tid));
if ($name == '' || $multitracker == 'no')
	stderr($tracker_lang['error'], "Такого торрента нет, или он не мультитрекерный.");

if (strtotime($last_mt_update) > (TIMENOW - 3600))
	stderr($tracker_lang['error'], "Вы пытаетесь обновить мультитрекер слишком часто. Разрешено это делать не чаще 1 раза в час.");

$anns_r = sql_query('SELECT info_hash, url FROM torrents_scrape WHERE tid = '.$tid);

$s_sum = $l_sum = $errors = $success = 0;
$pids = $works = array();

while ($ann = mysql_fetch_array($anns_r))
	$works[] = $ann;

if (function_exists('curl_multi_init')) {
	$multi = curl_multi_init();
	$channels = array();
	foreach ($works as $work) {
		$url = $work['url'];
		$info_hash = $work['info_hash'];
		$url = $DEFAULTBASEURL.'/update_multi.php?id='.$tid.'&url='.urlencode($url).'&info_hash='.urlencode($info_hash).'&token='.generate_token($tid, $url, $info_hash);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 5000);
		curl_multi_add_handle($multi, $ch);
		$channels[$url] = $ch;
	}

	$running = null;
	do {
	    while (CURLM_CALL_MULTI_PERFORM === curl_multi_exec($multi, $running));
	    if (!$running)
	    	break;
	    while (($res = curl_multi_select($multi)) === 0)
	    	{};
	    if ($res === false) {
	        echo "<h1>select error</h1>";
	        break;
	    }
	} while (true);

	$success = 0;
	foreach ($channels as $url => $channel) {
	    $success += intval(curl_multi_getcontent($channel));
	    curl_multi_remove_handle($multi, $channel);
	}

	curl_multi_close($multi);
} else
	foreach ($works as $work)
		scrape($tid, $work['url'], $work['info_hash']);

sql_query('UPDATE torrents AS t INNER JOIN (SELECT ts.tid, SUM(ts.seeders) AS sum_seeders, SUM(ts.leechers) AS sum_leechers FROM torrents_scrape AS ts WHERE ts.tid = '.$tid.' GROUP BY ts.tid) AS ts ON ts.tid = t.id SET t.remote_seeders = ts.sum_seeders, t.remote_leechers = ts.sum_leechers, t.last_action = NOW(), t.last_mt_update = NOW(), visible = IF(t.remote_seeders > 0, "yes", visible) WHERE t.id = '.$tid) or sqlerr(__FILE__,__LINE__);

$ajax = strval($_GET['ajax']);

if ($ajax !== 'yes') {
	header('Refresh: 3;url=details.php?id='.$tid);
	$errors = count($works) - $success;
	stderr($tracker_lang['success'], "Обновление мультитрекера выполнено успешно. Успешно: $success Ошибок: $errors");
} else {
	header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);

	$announces_a = $announces_urls = array();
	$announces_r = sql_query('SELECT url, seeders, leechers, last_update, state, error FROM torrents_scrape WHERE tid = '.$tid);
	while ($announce = mysql_fetch_array($announces_r)) {
		$announces_a[] = $announce;
		$announces_urls[] = $announce['url'];
	}
	unset($announce);

	$row = mysql_fetch_array(sql_query('SELECT last_mt_update FROM torrents WHERE id = '.$tid));

	if (count($announces_a)) {
		foreach ($announces_a as $announce) {
			if ($announce['state'] == 'ok')
				$anns[] = '<li><b>' . $announce['url'] . '</b> - раздающие: <b>' . $announce['seeders'] . '</b>, качающие: <b>' . $announce['leechers'] . '</b>';
			else
				$anns[] = '<li><font color="red"><b>' . $announce['url'] . '</b></font> - не работает, ошибка: ' . $announce['error'] . '</b>';
		}
		if (strtotime($row['last_mt_update']) < (TIMENOW - 3600) && $CURUSER)
			$update_link = '<br />Данные могли устареть. <a href="update_multi.php?id=' . $tid . '" onclick="update_multi(); return false;">' . $tracker_lang['details_update_multitracker'] . '</a>';
		if ($row['last_mt_update'] == '0000-00-00 00:00:00')
			$update_link .= '<br />' . $tracker_lang['details_update_last_mt_update'] . ' <b>' . $tracker_lang['never'] . '</b>';
		else
			$update_link .= '<br />' . $tracker_lang['details_update_last_mt_update'] . ' <b>' . get_et(strtotime($row['last_mt_update'])) . '</b> ' . $tracker_lang['ago'];
		echo '<ul style="margin: 0;">' . implode($anns) . '</ul>' . $update_link;
	} else
		echo 'WTF? Multitracker = YES, but no announces';
}
?>