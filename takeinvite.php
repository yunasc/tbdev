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

function bark($msg) {
	stdhead();
	stdmsg("Ошибка", $msg);
	stdfoot();
	die;
}

$id = intval($_GET["id"]);

if ($id == 0) {
	$id = $CURUSER["id"];
}

if (get_user_class() <= UC_MODERATOR)
	$id = $CURUSER["id"];

$re = sql_query("SELECT invites FROM users WHERE id = $id") or sqlerr(__FILE__,__LINE__);
$tes = mysql_fetch_assoc($re);

if ($tes[invites] <= 0)
	bark("У вас больше не осталось приглашений!");

$hash  = md5(mt_rand(1, 1000000));

sql_query("INSERT INTO invites (inviter, invite, time_invited) VALUES (" . implode(", ", array_map("sqlesc", array($id, $hash, get_date_time()))) . ")") or sqlerr(__FILE__,__LINE__);
sql_query("UPDATE users SET invites = invites - 1 WHERE id = $id") or sqlerr(__FILE__, __LINE__);

header("Refresh: 0; url=invite.php?id=$id");

?>