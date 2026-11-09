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

require_once("include/bittorrent.php");

dbconn();
loggedinorreturn();
parked();

if (@ini_get('output_handler') == 'ob_gzhandler' AND @ob_get_length() !== false)
{	// if output_handler = ob_gzhandler, turn it off and remove the header sent by PHP
	@ob_end_clean();
	header('Content-Encoding:');
}

/*if (!preg_match(':^/(\d{1,10})/(.+)\.torrent$:', $_SERVER["PATH_INFO"], $matches))
	httperr();*/

$id = (int) $_GET["id"];
if (!is_numeric($id))
	stderr($tracker_lang['error'],$tracker_lang['invalid_id']);

/*$id = 0 + $matches[1];
if (!$id)
	httperr();*/

$res = sql_query("SELECT name, filename, owner, banned FROM torrents WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$row = mysqli_fetch_assoc($res);
if (!$row)
	stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

$name = $row['filename'];

$fn = "$torrent_dir/$id.torrent";

if (!$row || !is_file($fn) || !is_readable($fn))
	stderr($tracker_lang['error'], $tracker_lang['unable_to_read_torrent']);

if ($row['banned'] == 'yes' && $row['owner'] != $CURUSER['id'] && get_user_class() < UC_MODERATOR)
	stderr($tracker_lang['error'], 'Упс, а торрентик-то забанен!');

sql_query("UPDATE torrents SET hits = hits + 1 WHERE id = ".sqlesc($id));

$name = str_replace(array(',', ';', "\r", "\n"), '', $name);

require_once __DIR__ . '/include/benc.php';

if (strlen($CURUSER['passkey']) != 32) {
    $CURUSER['passkey'] = bin2hex(random_bytes(16));
    sql_query("UPDATE users SET passkey=" . sqlesc($CURUSER['passkey'], true) . " WHERE id=" . (int) $CURUSER['id'])
        or sqlerr(__FILE__, __LINE__);
}

$dict = bdec(file_get_contents($fn));
if (($dict['type'] ?? '') !== 'dictionary' || ($dict['value']['info']['type'] ?? '') !== 'dictionary')
    stderr($tracker_lang['error'], $tracker_lang['unable_to_read_torrent']);

$entries = $dict['value'];
$announce = array('type' => 'string', 'value' => $announce_urls[0] . '?passkey=' . $CURUSER['passkey']);
if (($entries['announce-list']['type'] ?? '') === 'list' && $entries['announce-list']['value']) {
    $entries['announce-list']['value'][] = array('type' => 'list', 'value' => array($announce));
    unset($entries['announce-list']['string']);
} else {
    $entries['announce'] = $announce;
}
ksort($entries, SORT_STRING);
$payload = 'd';
foreach ($entries as $key => $entry) {
    // The info hash is computed over the stored bencoded bytes, not a re-encoding.
    $payload .= benc_str((string) $key) . ($entry['string'] ?? benc($entry));
}
$payload .= 'e';

header ("Expires: Tue, 1 Jan 1980 00:00:00 GMT");
header ("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header ("Cache-Control: no-store, no-cache, must-revalidate");
header ("Cache-Control: post-check=0, pre-check=0", false);
header ("Pragma: no-cache");
//header ("X-Powered-by: TBDev Yuna Scatari Edition - http://bit-torrent.kiev.ua");
header ("Accept-Ranges: bytes");
header ("Connection: close");
header ("Content-Transfer-Encoding: binary");
header("Content-Disposition: attachment; filename=\"download.torrent\"; filename*=UTF-8''" . rawurlencode($name));
header ("Content-Type: application/x-bittorrent");
ob_implicit_flush(true);

print($payload);

?>