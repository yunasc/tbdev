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

# IMPORTANT: Do not edit below unless you know what you are doing!
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');

function torrenttable($res, $variant = "index") {
		global $pic_base_url, $CURUSER, $use_wait, $use_ttl, $ttl_days, $tracker_lang;

  if ($use_wait)
  if (($CURUSER["class"] < UC_VIP) && $CURUSER) {
		  $gigs = $CURUSER["uploaded"] / (1024*1024*1024);
		  $ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0);
		  if ($ratio < 0.5 || $gigs < 5) $wait = 48;
		  elseif ($ratio < 0.65 || $gigs < 6.5) $wait = 24;
		  elseif ($ratio < 0.8 || $gigs < 8) $wait = 12;
		  elseif ($ratio < 0.95 || $gigs < 9.5) $wait = 6;
		  else $wait = 0;
  }

print("<tr>\n");

// sorting by MarkoStamcar

$count_get = 0;

foreach ($_GET as $get_name => $get_value) {
	$get_name = mysql_real_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_name)));
	$get_value = mysql_real_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_value)));
	if ($get_name != "sort" && $get_name != "type") {
		if ($count_get > 0)
			$oldlink = $oldlink . "&" . $get_name . "=" . $get_value;
		else
			$oldlink = $oldlink . $get_name . "=" . $get_value;
		$count_get++;
	}
}

if ($count_get > 0)
	$oldlink = $oldlink . "&";

if ($_GET['sort'] == "1") {
if ($_GET['type'] == "desc") {
$link1 = "asc";
} else {
$link1 = "desc";
}
}

if ($_GET['sort'] == "2") {
if ($_GET['type'] == "desc") {
$link2 = "asc";
} else {
$link2 = "desc";
}
}

if ($_GET['sort'] == "3") {
if ($_GET['type'] == "desc") {
$link3 = "asc";
} else {
$link3 = "desc";
}
}

if ($_GET['sort'] == "4") {
if ($_GET['type'] == "desc") {
$link4 = "asc";
} else {
$link4 = "desc";
}
}

if ($_GET['sort'] == "5") {
if ($_GET['type'] == "desc") {
$link5 = "asc";
} else {
$link5 = "desc";
}
}

if ($_GET['sort'] == "7") {
if ($_GET['type'] == "desc") {
$link7 = "asc";
} else {
$link7 = "desc";
}
}

if ($_GET['sort'] == "8") {
if ($_GET['type'] == "desc") {
$link8 = "asc";
} else {
$link8 = "desc";
}
}

if ($_GET['sort'] == "9") {
if ($_GET['type'] == "desc") {
$link9 = "asc";
} else {
$link9 = "desc";
}
}

if ($_GET['sort'] == "10") {
if ($_GET['type'] == "desc") {
$link10 = "asc";
} else {
$link10 = "desc";
}
}

if ($link1 == "") { $link1 = "asc"; } // for torrent name
if ($link2 == "") { $link2 = "desc"; }
if ($link3 == "") { $link3 = "desc"; }
if ($link4 == "") { $link4 = "desc"; }
if ($link5 == "") { $link5 = "desc"; }
if ($link7 == "") { $link7 = "desc"; }
if ($link8 == "") { $link8 = "desc"; }
if ($link9 == "") { $link9 = "desc"; }
if ($link10 == "") { $link10 = "desc"; }

$script = "browse.php";
if ($variant == "mytorrents")
	$script = "mytorrents.php";
if ($variant == "bookmarks")
	$script = "bookmarks.php";

?>
<td class="colhead" align="center"><?php echo $tracker_lang['type'];?></td>
<td class="colhead" align="left"><a href="<?php print $script; ?>?<?php print $oldlink; ?>sort=1&type=<?php print $link1; ?>" class="altlink_white"><?php echo $tracker_lang['name'];?></a> / <a href="<?php print $script; ?>?<?php print $oldlink; ?>sort=4&type=<?php print $link4; ?>" class="altlink_white"><?php echo $tracker_lang['added'];?></a></td>
<!--<td class="heading" align="left">DL</td>-->
<?php
if ($wait)
	print("<td class=\"colhead\" align=\"center\">".$tracker_lang['wait']."</td>\n");

if ($variant == "mytorrents")
	print("<td class=\"colhead\" align=\"center\">".$tracker_lang['visible']."</td>\n");


?>
<td class="colhead" align="center"><a href="<?php print $script; ?>?<?php print $oldlink; ?>sort=2&type=<?php print $link2; ?>" class="altlink_white"><?php echo $tracker_lang['files'];?></a></td>
<td class="colhead" align="center"><a href="<?php print $script; ?>?<?php print $oldlink; ?>sort=3&type=<?php print $link3; ?>" class="altlink_white"><?php echo $tracker_lang['comments'];?></a></td>
<?php if ($use_ttl) {
?>
	<td class="colhead" align="center"><?php echo $tracker_lang['ttl'];?></td>
<?php
}
?>
<td class="colhead" align="center"><a href="<?php print $script; ?>?<?php print $oldlink; ?>sort=5&type=<?php print $link5; ?>" class="altlink_white"><?php echo $tracker_lang['size'];?></a></td>
<!--
<td class="colhead" align="right">Views</td>
<td class="colhead" align="right">Hits</td>
-->
<td class="colhead" align="center"><a href="<?php print $script; ?>?<?php print $oldlink; ?>sort=7&type=<?php print $link7; ?>" class="altlink_white"><?php echo $tracker_lang['seeds'];?></a>|<a href="<?php print $script; ?>?<?php print $oldlink; ?>sort=8&type=<?php print $link8; ?>" class="altlink_white"><?php echo $tracker_lang['leechers'];?></a></td>
<?php

if ($variant == "index" || $variant == "bookmarks")
	print("<td class=\"colhead\" align=\"center\"><a href=\"{$script}?{$oldlink}sort=9&type={$link9}\" class=\"altlink_white\">".$tracker_lang['uploadeder']."</a></td>\n");

if ((get_user_class() >= UC_MODERATOR) && $variant == "index")
	print("<td class=\"colhead\" align=\"center\"><a href=\"{$script}?{$oldlink}sort=10&type={$link10}\" class=\"altlink_white\">Изменен</td>");

if ((get_user_class() >= UC_MODERATOR) && $variant == "index")
	print("<td class=\"colhead\" align=\"center\">".$tracker_lang['delete']."</td>\n");

if ($variant == "bookmarks")
	print("<td class=\"colhead\" align=\"center\">".$tracker_lang['delete']."</td>\n");

print("</tr>\n");

print("<tbody id=\"highlighted\">");

if ((get_user_class() >= UC_MODERATOR) && $variant == "index")
	print("<form method=\"post\" action=\"deltorrent.php?mode=delete\">");

	if ($variant == "bookmarks")
		print ("<form method=\"post\" action=\"takedelbookmark.php\">");

	while ($row = mysql_fetch_assoc($res)) {
		$id = $row["id"];
		print("<tr".($row["not_sticky"] == "no" ? " class=\"highlight\"" : "").">\n");

		print("<td align=\"center\" style=\"padding: 0px\">");
		if (isset($row["cat_name"])) {
			print("<a href=\"browse.php?cat=" . $row["category"] . "\">");
			if (isset($row["cat_pic"]) && $row["cat_pic"] != "")
				print("<img border=\"0\" src=\"$pic_base_url/cats/" . $row["cat_pic"] . "\" alt=\"" . $row["cat_name"] . "\" />");
			else
				print($row["cat_name"]);
			print("</a>");
		}
		else
			print("-");
		print("</td>\n");

		$dispname = $row["name"];
        switch ($row['free']) {
            case 'yes':
                $freepic = "<img src=\"$pic_base_url/freedownload.gif\" title=\"".$tracker_lang['golden']."\" alt=\"".$tracker_lang['golden']."\">";
            break;
            case 'silver':
                $freepic = "<img src=\"$pic_base_url/silverdownload.gif\" title=\"".$tracker_lang['silver']."\" alt=\"".$tracker_lang['silver']."\">";
            break;
            case 'no':
                $freepic = '';
        }
		$thisisfree = $freepic;
		print("<td align=\"left\">".($row["not_sticky"] == "no" ? "Важный: " : "")."<a href=\"details.php?");
		if ($variant == "mytorrents")
			print("returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;");
		print("id=$id");
		if ($variant == "index" || $variant == "bookmarks")
			print("&amp;hit=1");
		print("\"><b>$dispname</b></a> $thisisfree\n");

			if ($variant != "bookmarks" && $CURUSER)
				print("<a href=\"bookmark.php?torrent=$row[id]\"><img border=\"0\" src=\"$pic_base_url/bookmark.gif\" alt=\"".$tracker_lang['bookmark_this']."\" title=\"".$tracker_lang['bookmark_this']."\" /></a>\n");

			print("<a href=\"download.php?id=$id\"><img src=\"$pic_base_url/download.gif\" border=\"0\" alt=\"".$tracker_lang['download']."\" title=\"".$tracker_lang['download']."\"></a>\n");

			if ($row['multitracker'] == 'yes') {

			print("<a href=\"".magnet(true, $row['info_hash'], $row['filename'], $row['size'])."\"><img src=\"$pic_base_url/magnet.png\" border=\"0\" alt=\"{$tracker_lang['magnet']}\" title=\"{$tracker_lang['magnet']}\"></a>\n");

				$allow_update = (strtotime($row['last_mt_update']) < (TIMENOW - 3600));
				if ($allow_update)
					$suffix = '_update';
				$multi_image = "<img src=\"$pic_base_url/multitracker.png\" border=\"0\" alt=\"{$tracker_lang['external_torrent' . $suffix]}\" title=\"{$tracker_lang['external_torrent' . $suffix]}\" />\n";
				if ($allow_update)
					$multi_image = "<a href=\"update_multi.php?id=$id\">$multi_image</a>\n";
				echo $multi_image;
			}

		if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
			$owned = 1;
		else
			$owned = 0;

				if ($owned)
			print("<a href=\"edit.php?id=$row[id]\"><img border=\"0\" src=\"$pic_base_url/pen.gif\" alt=\"".$tracker_lang['edit']."\" title=\"".$tracker_lang['edit']."\" /></a>\n");

			   if ($row["readtorrent"] == 0 && $variant == "index")
				   print ("<b><font color=\"red\" size=\"1\">[новый]</font></b>");

			print("<br /><i>".$row["added"]."</i>");

								if ($wait)
								{
								  $elapsed = floor((gmtime() - strtotime($row["added"])) / 3600);
				if ($elapsed < $wait)
				{
				  $color = dechex(floor(127*($wait - $elapsed)/48 + 128)*65536);
				  print("<td align=\"center\"><nobr><a href=\"faq.php#dl8\"><font color=\"$color\">" . number_format($wait - $elapsed) . " h</font></a></nobr></td>\n");
				}
				else
				  print("<td align=\"center\"><nobr>".$tracker_lang['no']."</nobr></td>\n");
		}

	print("</td>\n");

		if ($variant == "mytorrents") {
			print("<td align=\"right\">");
			if ($row["visible"] == "no")
				print("<font color=\"red\"><b>".$tracker_lang['no']."</b></font>");
			else
				print("<font color=\"green\">".$tracker_lang['yes']."</font>");
			print("</td>\n");
		}

		if ($row["type"] == "single")
			print("<td align=\"right\">" . $row["numfiles"] . "</td>\n");
		else {
			if ($variant == "index")
				print("<td align=\"right\"><b><a href=\"details.php?id=$id&amp;hit=1&amp;filelist=1\">" . $row["numfiles"] . "</a></b></td>\n");
			else
				print("<td align=\"right\"><b><a href=\"details.php?id=$id&amp;filelist=1#filelist\">" . $row["numfiles"] . "</a></b></td>\n");
		}

		if (!$row["comments"])
			print("<td align=\"right\">" . $row["comments"] . "</td>\n");
		else {
			if ($variant == "index")
				print("<td align=\"right\"><b><a href=\"details.php?id=$id&amp;hit=1&amp;tocomm=1\">" . $row["comments"] . "</a></b></td>\n");
			else
				print("<td align=\"right\"><b><a href=\"details.php?id=$id&amp;page=0#startcomments\">" . $row["comments"] . "</a></b></td>\n");
		}

//		print("<td align=center><nobr>" . str_replace(" ", "<br />", $row["added"]) . "</nobr></td>\n");
				$ttl = ($ttl_days*24) - floor((gmtime() - sql_timestamp_to_unix_timestamp($row["added"])) / 3600);
				if ($ttl == 1) $ttl .= " час"; else $ttl .= "&nbsp;часов";
		if ($use_ttl)
			print("<td align=\"center\">$ttl</td>\n");
		print("<td align=\"center\">" . str_replace(" ", "<br />", mksize($row["size"])) . "</td>\n");
//		print("<td align=\"right\">" . $row["views"] . "</td>\n");
//		print("<td align=\"right\">" . $row["hits"] . "</td>\n");

		print("<td align=\"center\">");

		if ($row["seeders"]) {
			if ($variant == "index")
			{
			   if ($row["leechers"]) $ratio = $row["seeders"] / $row["leechers"]; else $ratio = 1;
				print("<b><a href=\"details.php?id=$id&amp;hit=1&amp;toseeders=1\"><font color=" .
				  get_slr_color($ratio) . ">" . $row["seeders"] . "</font></a></b>\n");
			}
			else
				print("<b><a class=\"" . linkcolor($row["seeders"]) . "\" href=\"details.php?id=$id&amp;dllist=1#seeders\">" .
				  $row["seeders"] . "</a></b>\n");
		}
		else
			print("<span class=\"" . linkcolor($row["seeders"]) . "\">" . $row["seeders"] . "</span>");

		print(" | ");

		if ($row["leechers"]) {
			if ($variant == "index")
				print("<b><a href=\"details.php?id=$id&amp;hit=1&amp;todlers=1\">" .
				   number_format($row["leechers"]) . ($peerlink ? "</a>" : "") .
				   "</b>\n");
			else
				print("<b><a class=\"" . linkcolor($row["leechers"]) . "\" href=\"details.php?id=$id&amp;dllist=1#leechers\">" .
				  $row["leechers"] . "</a></b>\n");
		}
		else
			print("0\n");

		print("</td>");

		if ($variant == "index" || $variant == "bookmarks")
			print("<td align=\"center\">" . (isset($row["username"]) ? ("<a href=\"userdetails.php?id=" . $row["owner"] . "\"><b>" . get_user_class_color($row["class"], htmlspecialchars_uni($row["username"])) . "</b></a>") : "<i>(unknown)</i>") . "</td>\n");

		if ($variant == "bookmarks")
			print ("<td align=\"center\"><input type=\"checkbox\" name=\"delbookmark[]\" value=\"" . $row[bookmarkid] . "\" /></td>");

		if ((get_user_class() >= UC_MODERATOR) && $variant == "index") {
			if ($row["moderated"] == "no")
				print("<td align=\"center\"><font color=\"red\"><b>Нет</b></font></td>\n");
			else
				print("<td align=\"center\"><a href=\"userdetails.php?id=$row[moderatedby]\"><font color=\"green\"><b>Да</b></font></a></td>\n");
		}

		if ((get_user_class() >= UC_MODERATOR) && $variant == "index")
			print("<td align=\"center\"><input type=\"checkbox\" name=\"delete[]\" value=\"" . $id . "\" /></td>\n");

	print("</tr>\n");

	}

	print("</tbody>");

	if ($variant == "index" && $CURUSER)
		print("<tr><td class=\"colhead\" colspan=\"12\" align=\"center\"><a href=\"markread.php\" class=\"altlink_white\">Все торренты прочитаны</a></td></tr>");

	//print("</table>\n");

	if ($variant == "index") {
		if (get_user_class() >= UC_MODERATOR) {
			print("<tr><td align=\"right\" colspan=\"12\"><input type=\"submit\" value=\"Удалить\"></td></tr>\n");
		}
	}

	if ($variant == "bookmarks")
		print("<tr><td colspan=\"12\" align=\"right\"><input type=\"submit\" value=\"".$tracker_lang['delete']."\"></td></tr>\n");

	if ($variant == "index" || $variant == "bookmarks") {
		if (get_user_class() >= UC_MODERATOR) {
			print("</form>\n");
		}
	}

	return $rows;
}

?>