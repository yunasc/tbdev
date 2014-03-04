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

$id = intval($_GET["id"]);
if (!is_valid_id($id))
	stderr("Ошибка", "А вот этого лучше не делать...");
if (isset($_POST["conusr"]))
	sql_query("UPDATE users SET status = 'confirmed' WHERE id IN (" . implode(", ", array_map("sqlesc", $_POST["conusr"])) . ") AND status = 'pending'".( get_user_class() < UC_SYSOP ? " AND invitedby = $CURUSER[id]" : "")) or sqlerr(__FILE__,__LINE__);
else
	header("Location: invite.php?id=$id");

header("Refresh: 0; url=invite.php?id=$id");

?>