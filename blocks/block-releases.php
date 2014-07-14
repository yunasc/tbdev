<?php
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}

global $rootpath;

$count = get_row_count('indexreleases');
$blocktitle = "Релизы".(get_user_class() >= UC_MODERATOR ? "<font class=\"small\"> - [<a class=\"altlink\" href=\"indexadd.php\"><b>Новый</b></a>]</font>" : "");

$content .= "<table cellspacing=\"0\" cellpadding=\"5\" width=\"100%\"><tr><td>";

if (!$count) {
	$content .= "Нет релизов на трекере...";
} else {
	$perpage = 5;
	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] . "?" );
	$content .= $pagertop;
	$content .= "</td></tr>";
	$res = sql_query("SELECT i.*, c.id AS catid, c.name AS catname, c.image AS catimage FROM indexreleases AS i LEFT JOIN categories AS c ON i.cat = c.id ORDER BY id DESC $limit") or sqlerr(__FILE__, __LINE__);
	while ($release = mysql_fetch_array($res)) {
		$catid = $release["catid"];
		$catname = $release["catname"];
		$catimage = $release["catimage"];
		$content .= "<tr><td>";
		$content .= "<table width=\"100%\" class=\"main\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">";
		$content .= "<tr><td class=\"colhead\" colspan=\"2\" align=\"center\">".htmlspecialchars_uni($release["name"]).(get_user_class() >= UC_MODERATOR ? "<font class=\"small\"> - [<a class=\"altlink_white\" href=\"indexedit.php?action=edit&id=$release[id]&returnto=" . urlencode($_SERVER['PHP_SELF']) . "\"><b>Редактировать</b></a>][<a class=\"altlink_white\" href=\"indexdelete.php?action=delete&id=$release[id]&returnto=" . urlencode($_SERVER['PHP_SELF']) . "\"><b>Удалить</b></a>]</font>" : "")."</td></tr>";
		$content .= "<tr valign=\"top\"><td align=\"center\" width=\"160\"><a href=\"details.php?id=$release[torrentid]\" alt=\"$release[name]\" title=\"$release[name]\"><img src=\"$release[poster]\" width=\"160\" border=\"0\" /></a></td>";
		$content .= "<td><div align=\"left\">".(!empty($catimage) ? "<a href=\"browse.php?cat=$catid\"><img src=\"pic/cats/$catimage\" alt=\"$catname\" title=\"$catname\" align=\"right\" border=\"0\" /></a>" : "<span align=\"right\" style=\"float: right;\">$catname</span>").format_comment($release["top"])."<br /></div><div align=\"left\"><hr align=\"left\" width=\"85%\" color=\"#000000\" size=\"1\"></div><div align=\"left\">".format_comment($release["center"])."<br /></div><div align=\"left\"><hr align=\"left\" width=\"85%\" color=\"#000000\" size=\"1\"></div><div align=\"left\">".format_comment($release["bottom"])."</div><div align=\"right\">".($release["imdb"] ? "[<a href=\"$release[imdb]\" class=\"online\">IMDB</a>] " : "")."[<a href=\"details.php?id=$release[torrentid]\" alt=\"$release[name]\" title=\"$release[name]\"><b>Детали</b></a>]</div></td>";
		$content .= "</tr>";
		$content .= "</table>";
		$content .= "</td></tr>";
	}
	$content .= "<tr><td>";
	$content .= $pagerbottom;
	$content .= "</td></tr>";
}

$content .= "</table>";

?>