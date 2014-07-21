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
dbconn(false);
if (!$allow_guests_details)
	loggedinorreturn();

function getagent($httpagent, $peer_id = "") {
	if (preg_match("/^Azureus ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]\_B([0-9][0-9|*])(.+)$)/", $httpagent, $matches))
		return "Azureus/$matches[1]";
	elseif (preg_match("/^Azureus ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]\_CVS)/", $httpagent, $matches))
		return "Azureus/$matches[1]";
	elseif (preg_match("/^Java\/([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
		return "Azureus/<2.0.7.0";
	elseif (preg_match("/^Azureus ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
		return "Azureus/$matches[1]";
	elseif (preg_match("/BitTorrent\/S-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
		return "Shadow's/$matches[1]";
	elseif (preg_match("/BitTorrent\/U-([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
		return "UPnP/$matches[1]";
	elseif (preg_match("/^BitTor(rent|nado)\\/T-(.+)$/", $httpagent, $matches))
		return "BitTornado/$matches[2]";
	elseif (preg_match("/^BitTornado\\/T-(.+)$/", $httpagent, $matches))
		return "BitTornado/$matches[1]";
	elseif (preg_match("/^BitTorrent\/ABC-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
		return "ABC/$matches[1]";
	elseif (preg_match("/^ABC ([0-9]+\.[0-9]+(\.[0-9]+)*)\/ABC-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
		return "ABC/$matches[1]";
	elseif (preg_match("/^Python-urllib\/.+?, BitTorrent\/([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
		return "BitTorrent/$matches[1]";
	elseif (preg_match("/^BitTorrent\/brst(.+)/", $httpagent, $matches))
		return "Burst";
	elseif (preg_match("/^RAZA (.+)$/", $httpagent, $matches))
		return "Shareaza/$matches[1]";
	elseif (preg_match("/Rufus\/([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
		return "Rufus/$matches[1]";
	elseif (preg_match("/^Python-urllib\\/([0-9]+\\.[0-9]+(\\.[0-9]+)*)/", $httpagent, $matches))
		return "G3 Torrent";
	elseif (preg_match("/MLDonkey\/([0-9]+).([0-9]+).([0-9]+)*/", $httpagent, $matches))
		return "MLDonkey/$matches[1].$matches[2].$matches[3]";
	elseif (preg_match("/ed2k_plugin v([0-9]+\\.[0-9]+).*/", $httpagent, $matches))
		return "eDonkey/$matches[1]";
	elseif (preg_match("/uTorrent\/([0-9]+)([0-9]+)([0-9]+)([0-9A-Z]+)/", $httpagent, $matches))
		return "µTorrent/$matches[1].$matches[2].$matches[3].$matches[4]";
	elseif (preg_match("/CT([0-9]+)([0-9]+)([0-9]+)([0-9]+)/", $peer_id, $matches))
		return "cTorrent/$matches[1].$matches[2].$matches[3].$matches[4]";
	elseif (preg_match("/Transmission\/([0-9]+).([0-9]+)/", $httpagent, $matches))
		return "Transmission/$matches[1].$matches[2]";
	elseif (preg_match("/KT([0-9]+)([0-9]+)([0-9]+)([0-9]+)/", $peer_id, $matches))
		return "KTorrent/$matches[1].$matches[2].$matches[3].$matches[4]";
	elseif (preg_match("/rtorrent\/([0-9]+\\.[0-9]+(\\.[0-9]+)*)/", $httpagent, $matches))
		return "rTorrent/$matches[1]";
	elseif (preg_match("/^ABC\/Tribler_ABC-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
		return "Tribler/$matches[1]";
	elseif (preg_match("/^BitsOnWheels( |\/)([0-9]+\\.[0-9]+).*/", $httpagent, $matches))
		return "BitsOnWheels/$matches[2]";
	elseif (preg_match("/BitTorrentPlus\/(.+)$/", $httpagent, $matches))
		return "BitTorrent Plus!/$matches[1]";
	elseif (preg_match("/^Deadman Walking/", $httpagent))
		return "Deadman Walking";
	elseif (preg_match("/^eXeem( |\/)([0-9]+\\.[0-9]+).*/", $httpagent, $matches))
		return "eXeem$matches[1]$matches[2]";
	elseif (preg_match("/^libtorrent\/(.+)$/", $httpagent, $matches))
		return "libtorrent/$matches[1]";
	elseif (substr($peer_id, 0, 12) == "d0c")
		return "Mainline";
	elseif (substr($peer_id, 0, 1) == "M")
		return "Mainline/Decoded";
	elseif (substr($peer_id, 0, 3) == "-BB")
		return "BitBuddy";
	elseif (substr($peer_id, 0, 8) == "-AR1001-")
		return "Arctic Torrent/1.2.3";
	elseif (substr($peer_id, 0, 6) == "exbc\08")
		return "BitComet/0.56";
	elseif (substr($peer_id, 0, 6) == "exbc\09")
		return "BitComet/0.57";
	elseif (substr($peer_id, 0, 6) == "exbc\0:")
		return "BitComet/0.58";
	elseif (substr($peer_id, 0, 4) == "-BC0")
		return "BitComet/0." . substr($peer_id, 5, 2);
	elseif (substr($peer_id, 0, 7) == "exbc\0L")
		return "BitLord/1.0";
	elseif (substr($peer_id, 0, 7) == "exbcL")
		return "BitLord/1.1";
	elseif (substr($peer_id, 0, 3) == "346")
		return "TorrenTopia";
	elseif (substr($peer_id, 0, 8) == "-MP130n-")
		return "MooPolice";
	elseif (substr($peer_id, 0, 8) == "-SZ2210-")
		return "Shareaza/2.2.1.0";
	elseif (preg_match("/^0P3R4H/", $httpagent))
		return "Opera BT Client";
	elseif (substr($peer_id, 0, 6) == "A310--")
		return "ABC/3.1";
	elseif (preg_match("/^XBT Client/", $httpagent))
		return "XBT Client";
	elseif (preg_match("/^BitTorrent\/BitSpirit$/", $httpagent))
		return "BitSpirit";
	elseif (preg_match("/^DansClient/", $httpagent))
		return "XanTorrent";
	else
		return "Unknown";
}

function dltable($name, $arr, $torrent) {
	global $tracker_lang;
	$s = "<b>" . count($arr) . " $name</b>\n";
	if (!count($arr))
		return $s;
	$s .=	"\n";
	$s .=	"<table width=\"100%\" class=\"main\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n";
	$s .=	"<tr><td class=colhead>{$tracker_lang['user']}</td>" .
			"<td class=colhead align=center>{$tracker_lang['port_open']}</td>".
			"<td class=colhead align=right>{$tracker_lang['uploaded']}</td>".
			"<td class=colhead align=right>{$tracker_lang['ul_speed']}</td>".
			"<td class=colhead align=right>{$tracker_lang['downloaded']}</td>" .
			"<td class=colhead align=right>{$tracker_lang['dl_speed']}</td>" .
			"<td class=colhead align=right>{$tracker_lang['ratio']}</td>" .
			"<td class=colhead align=right>{$tracker_lang['completed']}</td>" .
			"<td class=colhead align=right>{$tracker_lang['connected']}</td>" .
			"<td class=colhead align=right>{$tracker_lang['idle']}</td>" .
			"<td class=colhead align=left>{$tracker_lang['client']}</td></tr>\n";
	$now = time();
	//$moderator = (isset($CURUSER) && get_user_class() >= UC_MODERATOR); // Redundant
	$mod = (get_user_class() >= UC_MODERATOR);
	foreach ($arr as $e) {
		// user/ip/port
		// check if anyone has this ip
		$s .= "<tr>\n";
		if ($e["username"])
			$s .= "<td><a href=\"userdetails.php?id=$e[userid]\"><b>".get_user_class_color($e["class"], $e["username"])."</b></a>".($mod ? "&nbsp;[<span title=\"{$e["ip"]}\" style=\"cursor: pointer\">IP</span>]" : "")."</td>\n";
		else
			$s .= "<td>" . ($mod ? $e["ip"] : preg_replace('/\.\d+$/', ".xxx", $e["ip"])) . "</td>\n";
		$secs = max(10, ($e["la"]) - $e["pa"]);
		$s .= "<td align=\"center\">" . ($e['connectable'] == "yes" ? "<span style=\"color: green; cursor: help;\" title=\"{$tracker_lang['peertable_port_open']}\">{$tracker_lang['yes']}</span>" : "<span style=\"color: red; cursor: help;\" title=\"{$tracker_lang['peertable_port_closed']}\">{$tracker_lang['no']}</span>") . "</td>\n";
		$s .= "<td align=\"right\"><nobr>" . mksize($e["uploaded"]) . "</nobr></td>\n";
		$s .= "<td align=\"right\"><nobr>" . mksize($e["uploadoffset"] / $secs) . "/s</nobr></td>\n";
		$s .= "<td align=\"right\"><nobr>" . mksize($e["downloaded"]) . "</nobr></td>\n";
		//if ($e["seeder"] == "no")
		$s .= "<td align=\"right\"><nobr>" . mksize($e["downloadoffset"] / $secs) . "/s</nobr></td>\n";
		/*else
		$s .= "<td align=\"right\"><nobr>" . mksize($e["downloadoffset"] / max(1, $e["finishedat"] - $e["st"])) . "/s</nobr></td>\n";*/
		if ($e["downloaded"]) {
			$ratio = floor(($e["uploaded"] / $e["downloaded"]) * 1000) / 1000;
			$s .= "<td align=\"right\"><font color=" . get_ratio_color($ratio) . ">" . number_format($ratio, 3) . "</font></td>\n";
		} else
			if ($e["uploaded"])
				$s .= "<td align=\"right\">Inf.</td>\n";
			else
				$s .= "<td align=\"right\">---</td>\n";
		$s .= "<td align=\"right\">" . sprintf("%.2f%%", 100 * (1 - ($e["to_go"] / $torrent["size"]))) . "</td>\n";
		$s .= "<td align=\"right\">" . mkprettytime($now - $e["st"]) . "</td>\n";
		$s .= "<td align=\"right\">" . mkprettytime($now - $e["la"]) . "</td>\n";
		$s .= "<td align=\"left\">" . htmlspecialchars_uni(getagent($e["agent"], $e["peer_id"])) . "</td>\n";
		$s .= "</tr>\n";
	}
	$s .= "</table>\n";
	return $s;
}

$id = intval($_GET["id"]);

if (!isset($id) || !$id)
	die();

$res = sql_query("SELECT td.descr_hash, td.descr_parsed, t.multitracker, t.last_mt_update, t.keywords, t.description, t.free, t.seeders, t.banned, t.leechers, t.info_hash, t.filename, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(t.last_action) AS lastseed, t.numratings, t.name, IF(t.numratings < $minvotes, NULL, ROUND(t.ratingsum / t.numratings, 1)) AS rating, t.owner, t.save_as, t.descr, t.visible, t.size, t.added, t.views, t.hits, t.times_completed, t.id, t.type, t.numfiles, t.image1, t.image2, t.image3, t.image4, t.image5, c.name AS cat_name, u.username FROM torrents AS t LEFT JOIN categories AS c ON t.category = c.id LEFT JOIN users AS u ON t.owner = u.id LEFT JOIN torrents_descr AS td ON td.tid = $id WHERE t.id = $id") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);

$keywords = $row['keywords'];
$description = $row['description'];

sql_query("INSERT INTO readtorrents (userid, torrentid) VALUES (".sqlesc($CURUSER["id"]).", ".sqlesc($id).")")/* or sqlerr(__FILE__,__LINE__)*/;

$owned = $moderator = 0;
if (get_user_class() >= UC_MODERATOR)
	$owned = $moderator = 1;
elseif ($CURUSER["id"] == $row["owner"])
	$owned = 1;
//}

if (!$row || ($row["banned"] == "yes" && !$moderator))
        stderr($tracker_lang['error'], $tracker_lang['no_torrent_with_such_id']);

if (isset($_GET["hit"])) {
	sql_query("UPDATE torrents SET views = views + 1 WHERE id = $id");
	if (isset($_GET["tocomm"]))
		header("Location: $DEFAULTBASEURL/details.php?id=$id&page=0#startcomments");
	elseif (isset($_GET["filelist"]))
		header("Location: $DEFAULTBASEURL/details.php?id=$id&filelist=1#filelist");
	elseif (isset($_GET["toseeders"]))
		header("Location: $DEFAULTBASEURL/details.php?id=$id&dllist=1#seeders");
	elseif (isset($_GET["todlers"]))
		header("Location: $DEFAULTBASEURL/details.php?id=$id&dllist=1#leechers");
	else
		header("Location: $DEFAULTBASEURL/details.php?id=$id");
	exit();
}

if (!isset($_GET["page"])) {
	stdhead($tracker_lang['torrent_details']." \"".htmlspecialchars_decode($row["name"])."\"");

	if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
		$owned = 1;
	else
		$owned = 0;

	if ($row['multitracker'] == 'yes') {
		$announces_a = $announces_urls = array();
		$announces_r = sql_query('SELECT url, seeders, leechers, last_update, state, error FROM torrents_scrape WHERE tid = '.$id);
		while ($announce = mysql_fetch_array($announces_r)) {
			$announces_a[] = $announce;
			$announces_urls[] = $announce['url'];
		}
		unset($announce);
	}

	$spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	$s = "";

	print("<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n");
	print("<tr><td class=\"colhead\" colspan=\"2\"><div style=\"float: left; width: auto;\">:: {$tracker_lang['torrent_details']}</div><div align=\"right\"><a href=\"bookmark.php?torrent=$row[id]\"><b>{$tracker_lang['bookmark']}</b></a></div></td></tr>");
	$url = "edit.php?id=" . $row["id"];
	if (isset($_GET["returnto"])) {
		$addthis = "&amp;returnto=" . urlencode($_GET["returnto"]);
		$url .= $addthis;
		$keepget .= $addthis;
	}
	$editlink = "a href=\"$url\" class=\"sublink\"";
	$right_links = array();

	$right_links[] = "<a href=\"download.php?id={$id}\"><img src=\"$pic_base_url/download.gif\" border=\"0\" alt=\"{$tracker_lang['download']}\" title=\"{$tracker_lang['download']}\"></a>";
	if ($row['multitracker'] == 'yes')
		$right_links[] = "<a href=\"".magnet(true, $row['info_hash'], $row['filename'], $row['size'], $announces_urls)."\"><img src=\"$pic_base_url/magnet.png\" border=\"0\" alt=\"{$tracker_lang['magnet']}\" title=\"{$tracker_lang['magnet']}\"></a>";
	$right_links[] = "<a href=\"bookmark.php?torrent={$id}\"><img src=\"$pic_base_url/bookmark.gif\" border=\"0\" alt=\"{$tracker_lang['bookmark']}\" title=\"{$tracker_lang['bookmark']}\"></a>";

	if (count($right_links))
		$s .= '<span style="float: right;">'.implode('&nbsp;', $right_links).'</span>';

	$s .= "<a class=\"index\" href=\"download.php?id=$id\"><b>{$row["name"]}</b></a>";

	if ($owned)
	    $s .= " $spacer<$editlink>[{$tracker_lang['edit']}]</a>";

	switch ($row['free']) {
		case 'yes':
			$freepic = "<img src=\"$pic_base_url/freedownload.gif\" title=\"{$tracker_lang['golden']}\" alt=\"{$tracker_lang['golden']}\">&nbsp;";
			break;
		case 'silver':
			$freepic = "<img src=\"$pic_base_url/silverdownload.gif\" title=\"{$tracker_lang['silver']}\" alt=\"{$tracker_lang['silver']}\">&nbsp;";
			break;
		case 'no':
			$freepic = '';
	}

	tr ("<nobr>{$row["cat_name"]}</nobr>", $freepic.$s, 1, 1, "10%");

	function hex_esc($matches) {
	        return sprintf("%02x", ord($matches[0]));
	}

	tr($tracker_lang['info_hash'], $row["info_hash"]);

	if ($row["image1"] != "") {
		if ($row["image1"] != "")
			$img1 = "<a href=\"viewimage.php?pic={$row['image1']}\"><img border=\"0\" src=\"thumbnail.php?{$row['image1']}\" /></a>";
		tr($tracker_lang['details_poster'], $img1, 1);
	}

	if (!empty($row["descr"])) {
		if (md5($row['descr']) == $row['descr_hash'])
			$descr = $row['descr_parsed'];
		else {
			$descr = format_comment($row['descr']);
			sql_query('INSERT INTO torrents_descr (tid, descr_hash, descr_parsed) VALUES ('.implode(', ', array_map('sqlesc', array($id, md5($row['descr']), $descr))).')') or sqlerr(__FILE__,__LINE__);
		}
		tr($tracker_lang['description'], $descr, 1, 1);
	}

	$images = array();

	for ($i = 2; $i <= 5; $i++) {
		if ($row['image'.$i])
			$images[] = '<a href="torrents/images/' . $row['image'.$i] . '" rel="lightbox" title="'.$tracker_lang['details_screenshot'].' №'.($i - 1).'"><img title="'.$tracker_lang['details_screenshot'].' №'.($i - 1).'" border="0" src="screenshot.php?' . $row['image'.$i] . '" /></a>';
	}
	if (count($images))
		tr($tracker_lang['images'], implode('&nbsp; ', $images), 1);

	if ($row["visible"] == "no")
		tr($tracker_lang['visible'], "<b>{$tracker_lang['no']}</b> ({$tracker_lang['dead']})", 1);
	if ($moderator)
		tr($tracker_lang['banned'], ($row["banned"] == 'no' ? $tracker_lang['no'] : $tracker_lang['yes']) );

	if (isset($row["cat_name"]))
		tr($tracker_lang['type'], $row["cat_name"]);
	else
		tr($tracker_lang['type'], "({$tracker_lang['no_choose']})");

	tr($tracker_lang['seeder'], "{$tracker_lang['seeder_last_seen']} " . mkprettytime($row['lastseed']) . " {$tracker_lang['ago']}");
	tr($tracker_lang['size'], mksize($row['size']) . " (" . number_format($row['size']) . " {$tracker_lang['bytes']})");

	/*$s = "";
	$s .= "<div id=\"ajaxrate\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td valign=\"top\" class=\"embedded\">";
	if (!isset($row["rating"])) {
		if ($minvotes > 1) {
			$s .= sprintf($tracker_lang['not_enough_votes'], $minvotes);
			if ($row["numratings"])
				$s .= sprintf($tracker_lang['only_votes'], $row["numratings"]);
			else
				$s .= $tracker_lang['none_voted'];
			$s .= ")";
		} else
			$s .= $tracker_lang['no_votes'];
	} else {
		$rpic = ratingpic($row["rating"]);
		if (!isset($rpic))
			$s .= "invalid?";
		else
			$s .= "$rpic ({$row["rating"]} {$tracker_lang['from']} 5 {$tracker_lang['with']} {$row["numratings"]} {$tracker_lang['votes']})";
	}
	$s .= "\n";
	$s .= "</td><td class=embedded>$spacer</td><td valign=\"top\" class=embedded>";
	if (!isset($CURUSER))
		$s .= "(<a href=\"login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;nowarn=1\">Log in</a> to rate it)"; else {
		$ratings = array(5 => $tracker_lang['vote_5'],
						 4 => $tracker_lang['vote_4'],
						 3 => $tracker_lang['vote_3'],
						 2 => $tracker_lang['vote_2'],
						 1 => $tracker_lang['vote_1'],);
		if (!$owned || $moderator) {
			$xres = sql_query("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $CURUSER["id"]);
			$xrow = mysql_fetch_array($xres);
			if ($xrow)
				$s .= "({$tracker_lang['you_have_voted_for_this_torrent']} \"{$xrow["rating"]} - {$ratings[$xrow["rating"]]}\")"; else {
				$s .= "<form method=\"post\" action=\"takerate.php\" name=\"ajaxrating\"><input type=\"hidden\" id=\"ratingtid\" name=\"id\" value=\"$id\" />\n";
				$s .= "<select id=\"ratingselect\" name=\"rating\">\n";
				$s .= "<option value=\"0\">{$tracker_lang['vote']}</option>\n";
				foreach ($ratings as $k => $v) {
					$s .= "<option value=\"$k\">$k - $v</option>\n";
				}
				$s .= "</select>\n";
				$s .= "<input type=\"submit\" value=\"{$tracker_lang['vote']}!\" onClick=\"sendrating(); return false;\" />";
				$s .= "</form>\n";
			}
		}
	}
	$s .= "</td></tr></table></div>";

?>
<script type="text/javascript">
function sendrating(){

    var tid = $('#ratingtid').val();
    var rating = $('#ratingselect').val();

	if (rating == 0) {
		alert("Вы не выбрали оценку!");
		return false;
	}

	var ajax = new tbdev_ajax();
	ajax.onShow ('');
	var varsString = "";
	ajax.requestFile = "takerate.php";
	ajax.setVar("id", tid);
	ajax.setVar("rating", rating);
	ajax.method = 'POST';
	ajax.element = 'ajaxrate';
	ajax.sendAJAX(varsString);

	return false;
}
</script>
<?

	tr($tracker_lang['rating'], $s, 1);*/

	if ($CURUSER) {
?>
<script>
$(document).ready(function(){
	$('span.star').click(function (e) {
		e.stopPropagation();
		var tid = $('#ratingtid').val();
		var rate_value = $(e.target).data('value');
		$.ajax({
			url: 'takerate.php',
			type: 'post',
			data: {rating: rate_value, id: tid},
			beforeSend: function () {
				$('div#rating_selector').html('<img src="pic/loading.gif" />');
			},
			success: function (result) {
				$('div#rating_selector').html(result);
			}
		});
	});
});	
</script>
<?

/* Sorry, but heredoc is not possible to indent */
$rating_selector = <<<SELECTOR
<input type="hidden" id="ratingtid" value="{$id}" />
<div id="rating_selector">
	<span class="rating star" title="{$tracker_lang['vote_1']}" data-value="1">
	<span class="rating star" title="{$tracker_lang['vote_2']}" data-value="2">
	<span class="rating star" title="{$tracker_lang['vote_3']}" data-value="3">
	<span class="rating star" title="{$tracker_lang['vote_4']}" data-value="4">
	<span class="rating star" title="{$tracker_lang['vote_5']}" data-value="5">
	</span></span></span></span></span>
</div>
SELECTOR;

		$is_voted = mysql_fetch_array(sql_query('SELECT rating FROM ratings WHERE torrent = ' . $id . ' AND user = ' . $CURUSER['id']));
		if (mysql_error())
			sqlerr();
		if ($is_voted) {
			$stars .= ratingpic($row['rating']) . "(" . $row["rating"] . " " . $tracker_lang['from'] . " 5 ".$tracker_lang['with'] . " " . $row["numratings"] . " " . getWord($row["numratings"], array($tracker_lang['votes_1'], $tracker_lang['votes_2'], $tracker_lang['votes_3'])).")".' Ваша оценка <b>' . $is_voted['rating'] . '</b> - <b>' . $tracker_lang['vote_' . $is_voted['rating']] . '</b>';
		} else {
			$stars .= $rating_selector;
		}
		tr($tracker_lang['rating'], $stars, 1);
	}

	tr($tracker_lang['added'], $row["added"]);
	tr($tracker_lang['views'], $row["views"]);
	tr($tracker_lang['hits'], $row["hits"]);
	tr($tracker_lang['snatched'], $row["times_completed"] . " ".$tracker_lang['times']);

	$keepget = "";
	$uprow = (isset($row["username"]) ? ("<a href=userdetails.php?id={$row["owner"]}>" . htmlspecialchars_uni($row["username"]) . "</a>") : "<i>{$tracker_lang['details_anonymous']}</i>");
/*
if ($owned)
        $uprow .= " $spacer<$editlink><b>[{$tracker_lang['edit']}]</b></a>";
*/

tr($tracker_lang['uploaded'], $uprow.'&nbsp;<a href="simpaty.php?action=add&amp;good&amp;targetid=' . $row["owner"] . '&amp;type=torrent' . $id . '&amp;returnto=' . urlencode($_SERVER["REQUEST_URI"]) . '" title="'.$tracker_lang['respect'].'"><img src="'.$pic_base_url.'/thum_good.gif" border="0" alt="'.$tracker_lang['respect'].'" title="'.$tracker_lang['respect'].'" /></a>&nbsp;&nbsp;<a href="simpaty.php?action=add&amp;bad&amp;targetid='.$row["owner"].'&amp;type=torrent' . $id . '&amp;returnto=' . urlencode($_SERVER["REQUEST_URI"]) . '" title="'.$tracker_lang['antirespect'].'"><img src="'.$pic_base_url.'/thum_bad.gif" border="0" alt="'.$tracker_lang['antirespect'].'" title="'.$tracker_lang['antirespect'].'" /></a>', 1);

if ($row["type"] == "multi") {
	if (!$_GET["filelist"])
		tr($tracker_lang['files'] . "<br /><a href=\"details.php?id=$id&amp;filelist=1$keepget#filelist\" class=\"sublink\">[{$tracker_lang['open_list']}]</a>", $row["numfiles"] . " " . $tracker_lang['files_l'], 1);
	else {
		tr($tracker_lang['files'], $row["numfiles"] . " ".$tracker_lang['files_l'], 1);

		$s = "<table class=\"main\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n";

		$subres = sql_query("SELECT * FROM files WHERE torrent = $id ORDER BY id");
		$s.="<tr><td class=colhead>{$tracker_lang['path']}</td><td class=colhead align=right>{$tracker_lang['size']}</td></tr>\n";
		while ($subrow = mysql_fetch_array($subres)) {
			$s .= "<tr><td>".iconv('utf-8', 'windows-1251', $subrow["filename"])."</td><td align=\"right\">" . mksize($subrow["size"]) . "</td></tr>\n";
		}

		$s .= "</table>\n";
		tr("<a name=\"filelist\">{$tracker_lang['file_list']}</a><br /><a href=\"details.php?id=$id$keepget\" class=\"sublink\">[{$tracker_lang['close_list']}]</a>", $s, 1);
	}
}

if (!isset($_GET["dllist"])) {
	tr($tracker_lang['downloading']."<br /><a href=\"details.php?id=$id&amp;dllist=1$keepget#seeders\" class=\"sublink\">[{$tracker_lang['open_list']}]</a>", $row["seeders"] . " {$tracker_lang['seeders_l']}, {$row["leechers"]} {$tracker_lang['leechers_l']} = " . ($row["seeders"] + $row["leechers"]) . " ".$tracker_lang['peers_l'], 1);
} else {
	$downloaders = array();
	$seeders = array();
	$subres = sql_query("SELECT seeder, finishedat, downloadoffset, uploadoffset, peers.ip, port, peers.uploaded, peers.downloaded, to_go, UNIX_TIMESTAMP(started) AS st, connectable, agent, peer_id, UNIX_TIMESTAMP(last_action) AS la, UNIX_TIMESTAMP(prev_action) AS pa, userid, users.username, users.class FROM peers INNER JOIN users ON peers.userid = users.id WHERE torrent = $id") or sqlerr(__FILE__, __LINE__);
	while ($subrow = mysql_fetch_array($subres)) {
		if ($subrow["seeder"] == "yes")
			$seeders[] = $subrow;
		else
			$downloaders[] = $subrow;
	}

	function leech_sort($a, $b) {
		if (isset($_GET["usort"]))
			return seed_sort($a, $b);
		$x = $a["to_go"];
		$y = $b["to_go"];
		if ($x == $y)
			return 0;
		if ($x < $y)
			return -1;
		return 1;
	}

	function seed_sort($a, $b) {
		$x = $a["uploaded"];
		$y = $b["uploaded"];
		if ($x == $y)
			return 0;
		if ($x < $y)
			return 1;
		return -1;
	}

	usort($seeders, "seed_sort");
	usort($downloaders, "leech_sort");

	tr("<a name=\"seeders\">{$tracker_lang['details_seeding']}</a><br /><a href=\"details.php?id=$id$keepget\" class=\"sublink\">[{$tracker_lang['close_list']}]</a>", dltable($tracker_lang['details_seeding'], $seeders, $row), 1);
	tr("<a name=\"leechers\">{$tracker_lang['details_leeching']}</a><br /><a href=\"details.php?id=$id$keepget\" class=\"sublink\">[{$tracker_lang['close_list']}]</a>", dltable($tracker_lang['details_leeching'], $downloaders, $row), 1);
}

if ($row["multitracker"] == 'yes') {
	if (count($announces_a)) {
		foreach ($announces_a as $announce) {
			if ($announce['state'] == 'ok')
				$anns[] = '<li><b>' . $announce['url'] . '</b> - раздающие: <b>' . $announce['seeders'] . '</b>, качающие: <b>' . $announce['leechers'] . '</b>';
			else
				$anns[] = '<li><font color="red"><b>' . $announce['url'] . '</b></font> - не работает, ошибка: ' . $announce['error'] . '</b>';
		}
		if (strtotime($row['last_mt_update']) < (TIMENOW - 3600) && $CURUSER)
			$update_link = '<br />Данные могли устареть. <a href="update_multi.php?id=' . $id . '" onclick="update_multi(); return false;">' . $tracker_lang['details_update_multitracker'] . '</a>';
		if ($row['last_mt_update'] == '0000-00-00 00:00:00')
			$update_link .= '<br />' . $tracker_lang['details_update_last_mt_update'] . ' <b>' . $tracker_lang['never'] . '</b>';
		else
			$update_link .= '<br />' . $tracker_lang['details_update_last_mt_update'] . ' <b>' . get_et(strtotime($row['last_mt_update'])) . '</b> ' . $tracker_lang['ago'];
		tr($tracker_lang['details_multitracker'], '<div id="update_multi"><ul style="margin: 0;">' . implode($anns) . '</ul>' . $update_link . '</div>', 1);
	} else
		tr($tracker_lang['details_multitracker'], 'WTF? Multitracker = YES, but no announces', 1);
}

if ($row["times_completed"] > 0) {
    $res = sql_query("SELECT users.id, users.username, users.title, users.uploaded, users.downloaded, users.donor, users.enabled, users.warned, users.last_access, users.class, snatched.startdat, snatched.last_action, snatched.completedat, snatched.seeder, snatched.userid, snatched.uploaded AS sn_up, snatched.downloaded AS sn_dn FROM snatched INNER JOIN users ON snatched.userid = users.id WHERE snatched.finished='yes' AND snatched.torrent =" . sqlesc($id) . " ORDER BY users.class DESC $limit") or sqlerr(__FILE__,__LINE__);
	$snatched_full = "<table width=\"100%\" class=\"main\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n";
	$snatched_full .= "<tr><td class=colhead>Юзер</td><td class=colhead>Раздал</td><td class=colhead>Скачал</td><td class=colhead>Рейтинг</td><td class=colhead align=center>Начал / Закончил</td><td class=colhead align=center>Действие</td><td class=colhead align=center>Сидирует</td><td class=colhead align=center>ЛС</td></tr>";

	while ($arr = mysql_fetch_assoc($res)) {
		//start Global
		if ($arr["downloaded"] > 0) {
		        $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
				//  $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
		}
		else if ($arr["uploaded"] > 0)
		$ratio = "Inf.";
		else
		$ratio = "---";
		$uploaded = mksize($arr["uploaded"]);
		$downloaded = mksize($arr["downloaded"]);
		//start torrent
		if ($arr["sn_dn"] > 0) {
				$ratio2 = number_format($arr["sn_up"] / $arr["sn_dn"], 2);
				$ratio2 = "<font color=" . get_ratio_color($ratio2) . ">$ratio2</font>";
		}
		else
			if ($arr["sn_up"] > 0)
				$ratio2 = "Inf.";
			else
				$ratio2 = "---";
		$uploaded2 = mksize($arr["sn_up"]);
		$downloaded2 = mksize($arr["sn_dn"]);
		//end
		//$highlight = $CURUSER["id"] == $arr["id"] ? " bgcolor=#00A527" : "";;
		$snatched_small[] = "<a href=userdetails.php?id=$arr[userid]>".get_user_class_color($arr["class"], $arr["username"])." (<font color=" . get_ratio_color($ratio) . ">$ratio</font>)</a>";
		$snatched_full .= "<tr$highlight><td><a href=userdetails.php?id=$arr[userid]>".get_user_class_color($arr["class"], $arr["username"])."</a>".get_user_icons($arr)."</td><td><nobr>$uploaded&nbsp;Общего<br>$uploaded2&nbsp;Торрент</nobr></td><td><nobr>$downloaded&nbsp;Общего<br>$downloaded2&nbsp;Торрент</nobr></td><td><nobr>$ratio&nbsp;Общего<br>$ratio2&nbsp;Торрент</nobr></td><td align=center><nobr>{$arr["startdat"]}<br />{$arr["completedat"]}</nobr></td><td align=center><nobr>{$arr["last_action"]}</nobr></td><td align=center>" . ($arr["seeder"] == "yes" ? "<b><font color=\"green\">Да</font>" : "<font color=\"red\">Нет</font></b>") .
			"</td><td align=center><a href=\"message.php?action=sendmessage&amp;receiver={$arr['userid']}\"><img src=\"$pic_base_url/button_pm.gif\" border=\"0\"></a></td></tr>\n";
    }
    $snatched_full .= "</table>\n";
	?><script language="javascript" type="text/javascript" src="js/show_hide.js"></script><?
	if ($row["seeders"] == 0 || ($row["leechers"] / $row["seeders"] >= 2))
		$reseed_button = "<form action=\"takereseed.php\"><input type=\"hidden\" name=\"torrent\" value=\"$id\" /><input type=\"submit\" value=\"Позвать скачавших\" /></form>";
	if (!$_GET["snatched"]==1)
		tr("Скачавшие<br /><a href=\"details.php?id=$id&amp;snatched=1#snatched\" class=\"sublink\">[{$tracker_lang['open_list']}]</a>", '<a href="javascript: show_hide(\'s1\')"><img border="0" src="$pic_base_url/plus.gif" id="pics1"><div id="ss1" style="display: none;">'.@implode(", ", $snatched_small).$reseed_button.'</div>', 1);
	else
		tr("Скачавшие<br /><a href=\"details.php?id=$id\" class=\"sublink\" name=\"snatched\">[{$tracker_lang['close_list']}]</a>", $snatched_full,1);
}

tr($tracker_lang['torrent_info'], "<a href=\"torrent_info.php?id={$id}\">{$tracker_lang['show_data']}</a>", 1);


$torrentid = (int) $_GET["id"];
/*$count_sql = sql_query("SELECT COUNT(*) FROM thanks WHERE torrentid = $torrentid");
$count_row = mysql_fetch_array($count_sql);
$count = $count_row[0];*/

$thanked_sql = sql_query("SELECT thanks.userid, users.username, users.class FROM thanks INNER JOIN users ON thanks.userid = users.id WHERE thanks.torrentid = $torrentid");
$count = mysql_num_rows($thanked_sql);

if ($count == 0) {
	$thanksby = $tracker_lang['none_yet'];
} else {
	//$thanked_sql = sql_query("SELECT thanks.userid, users.username FROM thanks INNER JOIN users ON thanks.userid = users.id WHERE thanks.torrentid = $torrentid");
	$thanksby = array();
	while ($thanked_row = mysql_fetch_assoc($thanked_sql)) {
		if ($thanked_row["userid"] == $CURUSER["id"])
			$can_not_thanks = true;
		$userid = $thanked_row["userid"];
		$username = $thanked_row["username"];
		$class = $thanked_row["class"];
		$thanksby[] = "<a href=\"userdetails.php?id={$userid}\">".get_user_class_color($class, $username)."</a>";
	}
	if ($thanksby)
		$thanksby = implode(', ', $thanksby);
}
if ($row["owner"] == $CURUSER["id"] || !$CURUSER)
	$can_not_thanks = true;
$thanksby = "<div id=\"ajax\"><form action=\"thanks.php\" method=\"post\">
<input type=\"submit\" name=\"submit\" onclick=\"send(); return false;\" value=\"{$tracker_lang['thanks']}\"".($can_not_thanks == true ? " disabled" : "").">
<input type=\"hidden\" name=\"torrentid\" value=\"{$torrentid}\">{$thanksby}
</form></div>";
?>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript">
function send() {
	//noinspection JSPotentiallyInvalidConstructorUsage
	var ajax = new tbdev_ajax('thanks.php');
	ajax.onShow ('');
	var varsString = "";
	//ajax.requestFile = "thanks.php";
	ajax.setVar("torrentid", <?=$torrentid;?>);
	ajax.setVar("ajax", "yes");
	ajax.method = 'POST';
	ajax.element = 'ajax';
	ajax.sendAJAX(varsString);
}

function update_multi() {
	//noinspection JSPotentiallyInvalidConstructorUsage
	var ajax = new tbdev_ajax('update_multi.php');
	ajax.onShow ('');
	var varsString = "";
	ajax.setVar("id", <?=$torrentid;?>);
	ajax.setVar("ajax", "yes");
	ajax.method = 'GET';
	ajax.element = 'update_multi';
	ajax.sendAJAX(varsString);
}
</script>
<div id="loading-layer" style="display:none;font-family: Verdana;font-size: 11px;width:200px;height:50px;background:#FFF;padding:10px;text-align:center;border:1px solid #000">
     <div style="font-weight:bold" id="loading-layer-text"><?=$tracker_lang['ajax_loading'];?></div><br />
     <img src="<?=$pic_base_url;?>/loading.gif" border="0" />
</div>
<?

	tr($tracker_lang['said_thanks'], $thanksby, 1);

	print("</table></p>\n");

	} else {
		stdhead($tracker_lang['comments_for']." \"".htmlspecialchars_decode($row["name"])."\"");
		print("<h1>{$tracker_lang['comments_for']} <a href=\"details.php?id={$id}\">{$row["name"]}</a></h1>\n");
	}

	print("<p><a name=\"startcomments\"></a></p>\n");

	$subres = sql_query("SELECT COUNT(*) FROM comments WHERE torrent = $id");
	$subrow = mysql_fetch_array($subres);
	$count = $subrow[0];

	$limited = 10;

if (!$count) {
	print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
	print("<tr><td class=\"colhead\" align=\"left\" colspan=\"2\">");
	print("<div style=\"float: left; width: auto;\" align=\"left\"> :: {$tracker_lang['comments_list']}</div>");
	if ($CURUSER)
		print("<div align=\"right\"><a href=#comments class=\"altlink_white\">{$tracker_lang['comments_add']}</a></div>");
	print("</td></tr><tr><td align=\"center\">");
	print("Комментариев нет.".($CURUSER ? " <a href=\"#comments\">Желаете добавить?</a>" : ""));
	print("</td></tr></table><br>");

	if ($CURUSER) {
		print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
		print("<tr><td class=\"colhead\" align=\"left\" colspan=\"2\"> <a name=\"comments\">&nbsp;</a><b>:: Без комментариев</b></td></tr>");
		print("<tr><td align=\"center\" >");
		//print("<b>Ваше имя:</b> ");
		//print("{$CURUSER['username']}<p>");
		print("<form name=\"comment\" method=\"post\" action=\"comment.php?action=add\">");
		print("<div>");
		textbbcode("comment","text","");
		print("</div>");
		print("</td></tr><tr><td  align=\"center\" colspan=\"2\">");
		print("<input type=\"hidden\" name=\"tid\" value=\"$id\"/>");
		print("<input type=\"submit\" class=btn value=\"Разместить комментарий\" />");
		print("</td></tr></form></table>");
	}
} else {
	list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "details.php?id=$id&", array('lastpagedefault' => 1));

	$subres = sql_query("SELECT cp.text_hash, cp.text_parsed, c.id, c.ip, c.text, c.user, c.added, c.editedby, c.editedat, u.avatar, u.warned, ".
	"u.username, u.title, u.class, u.donor, u.downloaded, u.uploaded, u.gender, u.last_access, e.username AS editedbyname FROM comments AS c LEFT JOIN users AS u ON c.user = u.id LEFT JOIN users AS e ON c.editedby = e.id LEFT JOIN comments_parsed AS cp ON cp.cid = c.id WHERE torrent = " .
	"$id ORDER BY c.id $limit") or sqlerr(__FILE__, __LINE__);
	$allrows = array();
	while ($subrow = mysql_fetch_array($subres))
	    $allrows[] = $subrow;


	print("<table class=\"main\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\" >");
	print("<tr><td class=\"colhead\" align=\"center\" >");
	print("<div style=\"float: left; width: auto;\" align=\"left\"> :: {$tracker_lang['comments_list']}</div>");
	if ($CURUSER)
		print("<div align=\"right\"><a href=#comments class=\"altlink_white\">{$tracker_lang['comments_add']}</a></div>");
	print("</td></tr>");

	print("<tr><td>");
	print($pagertop);
	print("</td></tr>");
	print("<tr><td>");
	commenttable($allrows);
	print("</td></tr>");
	print("<tr><td>");
	print($pagerbottom);
	print("</td></tr>");
	print("</table>");

	if ($CURUSER) {
		print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
		print("<tr><td class=\"colhead\" align=\"left\" colspan=\"2\">  <a name=\"comments\">&nbsp;</a><b>:: Добавить комментарий к торренту</b></td></tr>");
		print("<tr><td width=\"100%\" align=\"center\" >");
		//print("Ваше имя: ");
		//print("{$CURUSER['username']}<p>");
		print("<form name=comment method=\"post\" action=\"comment.php?action=add\">");
		print("<center><table border=\"0\"><tr><td class=\"clear\">");
		print("<div align=\"center\">". textbbcode("comment","text","", 1) ."</div>");
		print("</td></tr></table></center>");
		print("</td></tr><tr><td  align=\"center\" colspan=\"2\">");
		print("<input type=\"hidden\" name=\"tid\" value=\"$id\"/>");
		print("<input type=\"submit\" class=btn value=\"Разместить комментарий\" />");
		print("</td></tr></form></table>");
	}
}

stdfoot();

?>