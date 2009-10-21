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
parked();

$userid = $CURUSER["id"];
$torrentid = (int) $_POST["torrentid"];

if (empty($torrentid)) {
	stdmsg($tracker_lang["error"], "Не пытайся меня взломать!");
}

$ajax = (string) $_POST["ajax"];
if ($ajax == "yes") {
	sql_query("INSERT INTO thanks (torrentid, userid) VALUES ($torrentid, $userid)");// or sqlerr(__FILE__,__LINE__);
	$count_sql = sql_query("SELECT COUNT(*) FROM thanks WHERE torrentid = $torrentid");
	$count_row = mysql_fetch_array($count_sql);
	$count = $count_row[0];

	if ($count == 0) {
		$thanksby = $tracker_lang['none_yet'];
	} else {
		$thanked_sql = sql_query("SELECT thanks.userid, users.username, users.class FROM thanks INNER JOIN users ON thanks.userid = users.id WHERE thanks.torrentid = $torrentid");
		while ($thanked_row = mysql_fetch_assoc($thanked_sql)) {
			if (($thanked_row["userid"] == $CURUSER["id"]) || ($thanked_row["userid"] == $row["owner"]))
			$can_not_thanks = true;
			//list($userid, $username) = $thanked_row;
			$userid = $thanked_row["userid"];
			$username = $thanked_row["username"];
			$class = $thanked_row["class"];
			$thanksby .= "<a href=\"userdetails.php?id=$userid\">".get_user_class_color($class, $username)."</a>, ";
		}
		if ($thanksby)
			$thanksby = substr($thanksby, 0, -2);
	}
	$thanksby = "<div id=\"ajax\"><form action=\"thanks.php\" method=\"post\">
	<input type=\"submit\" name=\"submit\" onclick=\"send(); return false;\" value=\"".$tracker_lang['thanks']."\"".($can_not_thanks ? " disabled" : "").">
	<input type=\"hidden\" name=\"torrentid\" value=\"$torrentid\">".$thanksby."
	</form></div>";
	header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);
	print $thanksby;
} else {
	$res = sql_query("INSERT INTO thanks (torrentid, userid) VALUES ($torrentid, $userid)");// or sqlerr(__FILE__,__LINE__);
	header("Location: $DEFAULTBASEURL/details.php?id=$torrentid&thanks=1");
}
?>