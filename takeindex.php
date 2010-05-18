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

if (get_user_class() < UC_MODERATOR)
	stderr($tracker_lang["error"], $tracker_lang["access_denied"]);

function bark($msg) {
	stdhead();
	stdmsg("Ошибка!", $msg);
	stdfoot();
	exit;
}

$var_list = "name:poster:cat:torrentid:top:center:bottom";
$int_list = "torrentid:cat";

foreach (explode(":", $var_list) as $x)
	if (empty($_POST[$x]))
		stderr($tracker_lang["error"], "Вы не заполнили все поля!");
	else
		$GLOBALS[$x] = $_POST[$x];

foreach (explode(":", $int_list) as $x)
	if (!is_valid_id($GLOBALS[$x]))
		stderr($tracker_lang["error"], "Вы ввели не число в следующее поле: $x");

$imdb = $_POST["imdb"];
$added = sqlesc(TIMENOW);
sql_query("INSERT INTO indexreleases (".implode(", ", explode(":", $var_list)).($imdb ? ", imdb" : "").", added) VALUES (".implode(", ", array_map("sqlesc", array($name, $poster, $cat, $torrentid, $top, $center, $bottom))).($imdb ? ", ".sqlesc($imdb) : "").", $added)") or sqlerr(__FILE__, __LINE__);

header("Refresh: 0; url=index.php");

?>