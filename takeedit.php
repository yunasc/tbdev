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

require_once("include/BDecode.php");
require_once("include/BEncode.php");
require_once("include/bittorrent.php");
require_once("include/upload_image.php");

function bark($msg) {
	stderr("Ошибка", $msg);
}

////////////////////////////////////////////////
function uploadimage($x, $imgname, $tid) {
	global $max_image_size;

	$maxfilesize = $max_image_size; // default 1mb

	if (!isset($_FILES['image'.$x]) || $_FILES['image'.$x]['name'] == '')
		bark('Missing image upload.');
	$upload = $_FILES['image'.$x];
	$y = $x + 1;
	if ($upload['error'] != UPLOAD_ERR_OK || $upload['size'] > $maxfilesize ||
		!is_uploaded_file($upload['tmp_name']))
		bark("Invalid upload for image $y");
	$ifilename = store_safe_image($upload['tmp_name'], 'torrents/images', $maxfilesize);
	if ($ifilename === false)
		bark("Invalid image format for image $y");
	if ($imgname != '' && preg_match('/\A[a-zA-Z0-9_-]+\.(?:gif|jpe?g|png)\z/iD', $imgname))
		@unlink('torrents/images/'.$imgname);
	return $ifilename;
}
////////////////////////////////////////////////

function dict_check($d, $s) {
	if ($d["type"] != "dictionary")
		bark("not a dictionary");
	$a = explode(":", $s);
	$dd = $d["value"];
	$ret = array();
	foreach ($a as $k) {
		unset($t);
		if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
			$k = $m[1];
			$t = $m[2];
		}
		if (!isset($dd[$k]))
			bark("dictionary is missing key(s)");
		if (isset($t)) {
			if ($dd[$k]["type"] != $t)
				bark("invalid entry in dictionary");
			$ret[] = $dd[$k]["value"];
		}
		else
			$ret[] = $dd[$k];
	}
	return $ret;
}

function dict_get($d, $k, $t) {
	if ($d["type"] != "dictionary")
		bark("not a dictionary");
	$dd = $d["value"];
	if (!isset($dd[$k]))
		return;
	$v = $dd[$k];
	if ($v["type"] != $t)
		bark("invalid dictionary entry type");
	return $v["value"];
}

dbconn();
loggedinorreturn();
csrf_require_post();

if (!mkglobal("id:name:descr:type"))
	bark("missing form data");

$id = intval($id);
if (!$id)
	die();

$res = sql_query("SELECT owner, filename, save_as, image1, image2, image3, image4, image5 FROM torrents WHERE id = $id");
$row = mysql_fetch_array($res);
if (!$row)
	die();

if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)
	bark("You're not the owner! How did that happen?\n");

$updateset = array();

$fname = $row["filename"];
preg_match('/^(.+)\.torrent$/si', $fname, $matches);
$shortfname = $matches[1];
$dname = $row["save_as"];

// picturemod
for ($x=1; $x <= 5; $x++) {
	$_GLOBALS['img'.$x.'action'] = $_POST['img'.$x.'action'];
	if ($_GLOBALS['img'.$x.'action'] == 'update')
		$updateset[] = 'image' . $x . ' = ' .sqlesc(uploadimage($x - 1, $row['image' . $x], $id));
	if ($_GLOBALS['img'.$x.'action'] == 'delete') {
		if ($row['image' . $x]) {
			if (preg_match('/\A[a-zA-Z0-9_-]+\.(?:gif|jpe?g|png)\z/iD', $row['image' . $x]))
				@unlink('torrents/images/' . $row['image' . $x]);
			$updateset[] = 'image' . $x . ' = ""';
		}
	}
}
// picturemod

if (isset($_FILES["tfile"]) && !empty($_FILES["tfile"]["name"]))
	$update_torrent = true;

if ($update_torrent) {
	$f = $_FILES["tfile"];
	$fname = unesc($f["name"]);
	if (empty($fname))
		bark("Файл не загружен. Пустое имя файла!");
	if (!validfilename($fname))
		bark("Неверное имя файла!");
	if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches))
		bark("Неверное имя файла (не .torrent).");
	$tmpname = $f["tmp_name"];
	if (!is_uploaded_file($tmpname))
		bark("eek");
	if (!filesize($tmpname))
		bark("Пустой файл!");
	$dict = bdecode(file_get_contents($tmpname));
	if (!isset($dict))
		bark("Что за хрень ты загружаешь? Это не бинарно-кодированый файл!");
	$info = $dict['info'];
	list($dname, $plen, $pieces, $totallen) = array($info['name'], $info['piece length'], $info['pieces'], $info['length']);
	if (isset($totallen) && (!is_int($totallen) || $totallen < 0))
		bark('invalid file length');
	if (strlen($pieces) % 20 != 0)
		bark("invalid pieces");

	$filelist = array();
	if (isset($totallen)) {
		$filelist[] = array($dname, $totallen);
		$torrent_type = "single";
	} else {
		$flist = $info['files'];
		if (!isset($flist))
			bark("missing both length and files");
		if (!count($flist))
			bark("no files");
		$totallen = 0;
		foreach ($flist as $fn) {
			list($ll, $ff) = array($fn['length'], $fn['path']);
			if (!is_int($ll) || $ll < 0 || !is_array($ff))
				bark('invalid file length or path');
			$totallen += $ll;
			if (!is_int($totallen))
				bark('invalid total length');
			$ffa = array();
			foreach ($ff as $ffe) {
				$ffa[] = $ffe;
			}
			if (!count($ffa))
				bark("filename error");
			$ffe = implode("/", $ffa);
			$filelist[] = array($ffe, $ll);
		if ($ffe == 'Thumbs.db')
	        {
	            stderr("Ошибка", "В торрентах запрещено держать файлы Thumbs.db!");
	            die;
	        }
		}
		$torrent_type = "multi";
	}

	$dict['announce'] = $announce_urls[0];  // change announce url to local
	$dict['info']['private'] = 1;  // add private tracker flag
	$dict['info']['source'] = "[$DEFAULTBASEURL] $SITENAME"; // add link for bitcomet users
	unset($dict['announce-list']); // remove multi-tracker capability
	unset($dict['nodes']); // remove cached peers (Bitcomet & Azareus)
	unset($dict['info']['crc32']); // remove crc32
	unset($dict['info']['ed2k']); // remove ed2k
	unset($dict['info']['md5sum']); // remove md5sum
	unset($dict['info']['sha1']); // remove sha1
	unset($dict['info']['tiger']); // remove tiger
	unset($dict['azureus_properties']); // remove azureus properties
	$dict = BDecode(BEncode($dict)); // double up on the becoding solves the occassional misgenerated infohash
	$dict['comment'] = "Торрент создан для '$SITENAME'"; // change torrent comment
	$dict['created by'] = "$CURUSER[username]"; // change created by
	$dict['publisher'] = "$CURUSER[username]"; // change publisher
	$dict['publisher.utf-8'] = "$CURUSER[username]"; // change publisher.utf-8
	$dict['publisher-url'] = "$DEFAULTBASEURL/userdetails.php?id=$CURUSER[id]"; // change publisher-url
	$dict['publisher-url.utf-8'] = "$DEFAULTBASEURL/userdetails.php?id=$CURUSER[id]"; // change publisher-url.utf-8
	$infohash = sha1(BEncode($dict['info']));

	move_uploaded_file($tmpname, "$torrent_dir/$id.torrent");

	$fp = fopen("$torrent_dir/$id.torrent", "w");
	if ($fp) {
		$dict_str = BEncode($dict);
	    @fwrite($fp, $dict_str, strlen($dict_str));
	    fclose($fp);
	}

	$updateset[] = "info_hash = " . sqlesc($infohash);
	$updateset[] = "filename = " . sqlesc($fname);
	$updateset[] = "save_as = " . sqlesc($dname);
	$updateset[] = "size = " . sqlesc($totallen);
	$updateset[] = "type = " . sqlesc($torrent_type);
	$updateset[] = "numfiles = " . count($filelist);

	@sql_query("DELETE FROM files WHERE torrent = $id");
	foreach ($filelist as $file) {
		@sql_query("INSERT INTO files (torrent, filename, size) VALUES ($id, ".sqlesc($file[0]).", ".intval($file[1]).")");
	}

}

$name = html_uni($name);

$descr = unesc($_POST["descr"]);
if (!$descr)
	bark("Вы должны ввести описание!");

$updateset[] = "name = " . sqlesc($name);

$updateset[] = "descr = " . sqlesc($descr);
$updateset[] = "ori_descr = " . sqlesc($descr);
sql_query('REPLACE INTO torrents_descr (tid, descr_hash, descr_parsed) VALUES ('.implode(', ', array_map('sqlesc', array($id, parsed_comment_hash($descr), format_comment($descr)))).')') or sqlerr(__FILE__,__LINE__);

$updateset[] = "category = " . (intval($type));
if (get_user_class() >= UC_ADMINISTRATOR) {
	if ($_POST["banned"]) {
		$updateset[] = "banned = 'yes'";
		$_POST["visible"] = 0;
	} else
		$updateset[] = "banned = 'no'";
	if ($_POST["not_sticky"] == "no")
	        $updateset[] = "not_sticky = 'no'";
	    else
	        $updateset[] = "not_sticky = 'yes'";
}

if(get_user_class() >= UC_ADMINISTRATOR && in_array($_POST['free'], array('yes', 'silver', 'no')))
       $updateset[] = "free = " . sqlesc($_POST['free']);

$updateset[] = "visible = '" . ($_POST["visible"] ? "yes" : "no") . "'";

$updateset[] = "moderated = 'yes'";
$updateset[] = "moderatedby = ".sqlesc($CURUSER["id"]);

sql_query("UPDATE torrents SET " . join(", ", $updateset) . " WHERE id = $id") or sqlerr(__FILE__,__LINE__);

write_log("Торрент '$name' был отредактирован пользователем {$CURUSER['username']}", "F25B61", "torrent");

$returl = "details.php?id=$id";
if (isset($_POST["returnto"]))
	$returl .= "&returnto=" . urlencode($_POST["returnto"]);

header("Refresh: 0; url=$returl");

?>