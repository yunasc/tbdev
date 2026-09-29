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
dbconn(false);
loggedinorreturn();
if ($_SERVER["REQUEST_METHOD"] != "POST")
    stderr("Mark read", '<form method="post" action="markread.php"><input type="hidden" name="csrf_token" value="'.csrf_token().'"><button type="submit">Mark all torrents read</button></form>');
csrf_require_post();
stdhead();

if (mysql_query("INSERT IGNORE INTO readtorrents (userid, torrentid) SELECT ".sqlesc($CURUSER["id"]).", id FROM torrents")) {
	stdmsg("Успешно", "Новые торренты отмечены как прочитаные.");
} else {
	stdmsg("Ошибка", "Отметка новых торрентов произошла с ошибкой: ".'Database error.');
}

stdfoot();

header("Refresh: 5; url=browse.php");

?>