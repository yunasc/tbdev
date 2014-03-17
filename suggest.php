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

require("include/bittorrent.php");
dbconn(false);
loggedinorreturn();

header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);

if (strlen($_GET['q']) > 3) {
	$q = str_replace(" ",".",sqlesc("%".$_GET['q']."%"));
	$q2 = str_replace("."," ",sqlesc("%".$_GET['q']."%"));
	$result = mysql_query("SELECT name FROM torrents WHERE name LIKE {$q} OR name LIKE {$q2} ORDER BY id DESC LIMIT 0,10;");
	if (mysql_num_rows($result) > 0) {
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$name = mysql_result($result,$i,"name");
			$name = trim(str_replace("\t","",$name));
			print $name;
			if ($i != mysql_num_rows($result)-1) {
				print "\r\n";
			}
		}
	}
}

?>