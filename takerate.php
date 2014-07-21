<?

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

header("Content-Type: text/html; charset=".$tracker_lang['language_charset']);

function bark($msg) {
	stdmsg($msg, "Rating failed!", 'error');
	die;
}

if (!mkglobal("rating:id"))
	bark("missing form data");

$id = intval($id);
if (!$id)
	bark("invalid id");

$rating = intval($rating);
if ($rating <= 0 || $rating > 5)
	bark("invalid rating");

$res = sql_query("SELECT owner FROM torrents WHERE id = $id");
$row = mysql_fetch_array($res);
if (!$row)
	bark("no such torrent");

/*if ($row["owner"] == $CURUSER["id"])
	bark("You can't vote on your own torrents.");*/

$own = sql_query("SELECT id FROM ratings WHERE torrent = $id AND user = $CURUSER[id]");
if (mysql_num_rows($own) != 0)
    bark("Вы уже голосовали за этот торрент!"); 

$res = sql_query("INSERT INTO ratings (torrent, user, rating, added) VALUES ($id, " . $CURUSER["id"] . ", $rating, NOW())");
if (!$res) {
	if (mysql_errno() == 1062)
		bark("You have already rated this torrent.");
	else
		bark(mysql_error());
}

sql_query("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");

echo 'Ваша оценка <b>' . $rating . '</b> - <b>' . $tracker_lang['vote_' . $rating] . '</b>';

//header("Refresh: 0; url=details.php?id=$id&rated=1");

?>