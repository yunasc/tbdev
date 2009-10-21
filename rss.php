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

require "include/bittorrent.php";
dbconn();

$passkey = $_GET["passkey"];
if ($passkey) {
$user = mysql_fetch_row(sql_query("SELECT COUNT(*) FROM users WHERE passkey = ".sqlesc($passkey)));
if ($user[0] != 1)
exit();
} else
loggedinorreturn();

$feed = $_GET["feed"];

// name a category
$res = sql_query("SELECT id, name FROM categories");
while($cat = mysql_fetch_assoc($res))
$category[$cat['id']] = $cat['name'];

// RSS Feed description
$DESCR = "RSS Feeds";

// by category ?
if ($_GET['cat'])
$cats = explode(",", $_GET["cat"]);
if ($cats)
$where = "category IN (".implode(", ", array_map("sqlesc", $cats)).") AND";

// start the RSS feed output
header("Content-Type: application/xml");
print("<?xml version=\"1.0\" encoding=\"windows-1251\" ?>\n<rss version=\"0.91\">\n<channel>\n" .
"<title>" . $SITENAME . "</title>\n<link>" . $DEFAULTBASEURL . "</link>\n<description>" . $DESCR . "</description>\n" .
"<language>en-usde</language>\n<copyright>Copyright © 2006 " . $SITENAME . "</copyright>\n<webMaster>" . $SITEEMAIL . "</webMaster>\n" .
"<image><title><![CDATA[" . $SITENAME . "]]></title>\n<url>" . $DEFAULTBASEURL . "/favicon.gif</url>\n<link>" . $DEFAULTBASEURL . "</link>\n" .
"<width>16</width>\n<height>16</height>\n<description><![CDATA[" . $DESCR . "]]></description>\n<generator><![CDATA[TBDev Yuna Scatari Edition - http://bit-torrent.kiev.ua]]></generator>\n</image>\n");

// get all vars
$res = sql_query("SELECT id,name,descr,filename,size,category,seeders,leechers,added FROM torrents WHERE $where visible='yes' ORDER BY added DESC LIMIT 15") or sqlerr(__FILE__, __LINE__);
while ($row = mysql_fetch_row($res)){
list($id,$name,$descr,$filename,$size,$cat,$seeders,$leechers,$added,$catname) = $row;

// seeders ?
if($seeders != 1){
$s = "их";
$aktivs="$seeders раздающий($s)";
}
else
$aktivs="нет раздающих";

// leechers ?
if ($leechers != 1){
$l = "ий";
$aktivl="$leechers качающих($l)";
}
else
$aktivl="нет качающих";

// ddl or detail ?
if ($feed == "dl")
$link = "$DEFAULTBASEURL/download.php/$id/". ($passkey ? "$passkey/" : "") ."$filename";
else
$link = "$DEFAULTBASEURL/details.php?id=$id&amp;hit=1";

// measure the totalspeed
if ($seeders >= 1 && $leechers >= 1){
$spd = sql_query("SELECT (t.size * t.times_completed + SUM(p.downloaded)) / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS totalspeed FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND p.torrent = '$id' GROUP BY t.id ORDER BY added ASC LIMIT 15") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_assoc($spd);
$totalspeed = mksize($a["totalspeed"]) . "/s";
}
else
$totalspeed = "нет траффика";

// output of all data
echo("<item><title><![CDATA[" . $name . "]]></title>\n<link>" . $link . "</link>\n<description><![CDATA[\nКатегория: " . $category[$cat] . " \n Размер: " . mksize($size) . "\n Статус: " . $aktivs . " и " . $aktivl . "\n Скорость: " . $totalspeed . "\n Добавлен: " . $added . "\n Описание:\n " . format_comment($descr) . "\n]]></description>\n</item>\n");
}

echo("</channel>\n</rss>\n");
?>