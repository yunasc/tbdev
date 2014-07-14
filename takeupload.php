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

ini_set("upload_max_filesize",$max_torrent_size);

function bark($msg) {
	genbark($msg, $tracker_lang['error']);
}

dbconn(); 

loggedinorreturn();
parked();

if (get_user_class() < UC_UPLOADER)
  die;

foreach(explode(":", "descr:type:name") as $v) {
	if (!isset($_POST[$v]))
		bark("missing form data");
}

if (!isset($_FILES["tfile"]))
	bark("missing form data");

$f = $_FILES["tfile"];
$fname = unesc($f["name"]);
if (empty($fname))
	bark("Файл не загружен. Пустое имя файла!");

$descr = unesc(strval($_POST["descr"]));
if (!$descr)
	bark("Вы должны ввести описание!");

$catid = intval($_POST["type"]);
if (!is_valid_id($catid))
	bark("Вы должны выбрать категорию, в которую поместить торрент!");

if (!validfilename($fname))
	bark("Неверное имя файла!");
if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches))
	bark("Неверное имя файла (не .torrent).");
$shortfname = $torrent = $matches[1];
if (!empty($_POST["name"]))
	$torrent = unesc($_POST["name"]);

$tmpname = $f["tmp_name"];
if (!is_uploaded_file($tmpname))
	bark("eek");
if (!filesize($tmpname))
	bark("Пустой файл!");

$dict = bdecode(file_get_contents($tmpname));
if (!isset($dict))
	bark("Что за хрень ты загружаешь? Это не бинарно-кодированый файл!");

if (get_user_class() >= UC_ADMINISTRATOR && in_array($_POST['free'], array('yes', 'silver', 'no'))) {
	$free = $_POST['free'];
}

if ($_POST['not_sticky'] == 'no' AND get_user_class() >= UC_ADMINISTRATOR)
    $not_sticky = "no";
else
    $not_sticky = "yes";

if ($_POST['multi'] == 'yes')
	$multi_torrent = 'yes';
else
	$multi_torrent = 'no';

//SEO mods
$keywords = htmlspecialchars_uni(strval($_POST["keywords"]));
$description = htmlspecialchars_uni(strval($_POST["description"]));

if (!$keywords)
    $keywords = '';

if (!$description)
    $description = '';
//SEO mods

$info = $dict['info'];
list($dname, $plen, $pieces, $totallen) = array($info['name'], $info['piece length'], $info['pieces'], $info['length']);

/*if (!in_array($ann, $announce_urls, 1))
	bark("Неверный Announce URL! Должен быть ".$announce_urls[0]);*/

$ret = sql_query("SHOW TABLE STATUS LIKE 'torrents'");
$row = mysql_fetch_array($ret);
$next_id = $row['Auto_increment'];

if (strlen($pieces) % 20 != 0)
	bark("invalid pieces");

$filelist = array();
if (isset($totallen)) {
	$filelist[] = array($dname, $totallen);
	$type = "single";
} else {
	$flist = $info['files'];
	if (!isset($flist))
		bark("missing both length and files");
	if (!count($flist))
		bark("no files");
	$totallen = 0;
	foreach ($flist as $fn) {
		list($ll, $ff) = array($fn['length'], $fn['path']);
		$totallen += $ll;
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
	$type = "multi";
}

if ($multi_torrent == 'no') {
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
}

$dict = BDecode(BEncode($dict)); // double up on the becoding solves the occassional misgenerated infohash
$dict['comment'] = "Торрент создан для '$SITENAME'"; // change torrent comment
$dict['created by'] = "$CURUSER[username]"; // change created by
$dict['publisher'] = "$CURUSER[username]"; // change publisher
$dict['publisher.utf-8'] = "$CURUSER[username]"; // change publisher.utf-8
$dict['publisher-url'] = "$DEFAULTBASEURL/userdetails.php?id=$CURUSER[id]"; // change publisher-url
$dict['publisher-url.utf-8'] = "$DEFAULTBASEURL/userdetails.php?id=$CURUSER[id]"; // change publisher-url.utf-8

$infohash = sha1(BEncode($dict['info']));

if ($multi_torrent == 'yes') {
	if (empty($dict['announce-list']) && !empty($dict['announce']))
		$dict['announce-list'][] = array($dict['announce']);
	if (!empty($dict['announce-list'])) {
		$parsed_urls = array();
		foreach ($dict['announce-list'] as $al_url) {
			$al_url[0] = trim($al_url[0]); // Trim url for match below and prevent "Invalid tracker url." error message if URL contains " " before proto://
			if ($al_url[0] == 'http://retracker.local/announce')
				continue;
			if (!preg_match('#^(udp|http)://#si', $al_url[0]))
				continue; // Skip not http:// or udp:// urls
			if (in_array($al_url[0], $parsed_urls))
				continue; // To skip doubled announce urls
			$url_array = parse_url($al_url[0]);
			if (substr($url_array['host'], -6) == '.local')
				continue; // Skip any .local domains
			$parsed_urls[] = $al_url[0];
			// А вдруг в торренте два одинаковых аннонсера? Потому REPLACE INTO
			sql_query('REPLACE INTO torrents_scrape (tid, info_hash, url) VALUES ('.implode(', ', array_map('sqlesc', array($next_id, $infohash, $al_url[0]))).')') or sqlerr(__FILE__,__LINE__);
		}
	} else
		stderr($tracker_lang['error'], "В торрент файле нет announce-list и не указан announce. Такой мультитрекерный торрент использовать нельзя.");
}

/*print_r($infohash);
die;*/

//////////////////////////////////////////////
//////////////Take Image Uploads//////////////

$maxfilesize = $max_image_size; // default 1mb

$allowed_types = array(
"image/gif" => "gif",
"image/pjpeg" => "jpg",
"image/jpeg" => "jpg",
"image/jpg" => "jpg",
"image/png" => "png"
// Add more types here if you like
);

for ($x=0; $x < 5; $x++) {
if (!($_FILES['image'.$x]['name'] == "")) {
	$y = $x + 1;

	// Is valid filetype?
	if (!array_key_exists($_FILES['image'.$x]['type'], $allowed_types))
		bark("Invalid file type! Image $y (".htmlspecialchars_uni($_FILES['image'.$x]['type']).")");

	if (!preg_match('/^(.+)\.(jpg|jpeg|png|gif)$/si', $_FILES['image'.$x]['name']))
		bark("Неверное имя файла (не картинка).");

	// Is within allowed filesize?
	if ($_FILES['image'.$x]['size'] > $maxfilesize)
		bark("Превышен размер файла! Картинка $y - Должна быть меньше ".mksize($maxfilesize));
		//bark("Invalid file size! Image $y - Must be less than 500kb");

	// Where to upload?
	// Update for your own server. Make sure the folder has chmod write permissions. Remember this director
	$uploaddir = "torrents/images/";

	// What is the temporary file name?
	$ifile = $_FILES['image'.$x]['tmp_name'];

	// Calculate what the next torrent id will be
	/*$ret = sql_query("SHOW TABLE STATUS LIKE 'torrents'");
	$row = mysql_fetch_array($ret);
	$next_id = $row['Auto_increment'];*/

	// By what filename should the tracker associate the image with?
	$ifilename = $next_id . $x . '.' . end(explode('.', $_FILES['image'.$x]['name']));

	// Upload the file
	$copy = copy($ifile, $uploaddir.$ifilename);

	if (!$copy)
	    bark("Error occured uploading image! - Image $y");

	$inames[] = $ifilename;

}}

//////////////////////////////////////////////

// Replace punctuation characters with spaces

$torrent = htmlspecialchars_uni(str_replace("_", " ", $torrent));

$ret = sql_query("INSERT INTO torrents (filename, owner, visible, not_sticky, info_hash, name, keywords, description, size, numfiles, type, descr, ori_descr, free, image1, image2, image3, image4, image5, category, save_as, added, last_action, multitracker) VALUES (" . implode(",", array_map("sqlesc", array($fname, $CURUSER["id"], "no", $not_sticky, $infohash, $torrent, $keywords, $description, $totallen, count($filelist), $type, $descr, $descr, $free, $inames[0], $inames[1], $inames[2], $inames[3], $inames[4], $catid, $dname))) . ", '" . get_date_time() . "', '" . get_date_time() . "', ".sqlesc($multi_torrent).")");
if (!$ret) {
	if (mysql_errno() == 1062)
		bark("torrent already uploaded!");
	bark("mysql puked: ".mysql_error());
}
$id = mysql_insert_id();

sql_query('INSERT INTO torrents_descr (tid, descr_hash, descr_parsed) VALUES ('.implode(', ', array_map('sqlesc', array($id, md5($descr), format_comment($descr)))).')') or sqlerr(__FILE__,__LINE__);

sql_query("INSERT INTO checkcomm (checkid, userid, torrent) VALUES ($id, $CURUSER[id], 1)") or sqlerr(__FILE__,__LINE__);
sql_query("DELETE FROM files WHERE torrent = $id");
foreach ($filelist as $file) {
	sql_query("INSERT INTO files (torrent, filename, size) VALUES ($id, ".sqlesc($file[0]).", ".$file[1].")");
}

move_uploaded_file($tmpname, "$torrent_dir/$id.torrent");

$fp = fopen("$torrent_dir/$id.torrent", "w");
if ($fp) {
	$dict_str = BEncode($dict);
    @fwrite($fp, $dict_str, strlen($dict_str));
    fclose($fp);
}

write_log("Торрент номер $id ($torrent) был залит пользователем " . $CURUSER["username"], "5DDB6E", "torrent");

// Этой фигней ваще кто-то пользуется?
/* Email notify */
/*******************

$res = sql_query("SELECT name FROM categories WHERE id=$catid") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_assoc($res);
$cat = $arr["name"];
$res = sql_query("SELECT email FROM users WHERE enabled='yes' AND notifs LIKE '%[cat$catid]%'") or sqlerr(__FILE__, __LINE__);
$uploader = $CURUSER['username'];

$size = mksize($totallen);
$description = ($html ? strip_tags($descr) : $descr);

$body = <<<EOD
A new torrent has been uploaded.

Name: $torrent
Size: $size
Category: $cat
Uploaded by: $uploader

Description
-------------------------------------------------------------------------------
$description
-------------------------------------------------------------------------------

You can use the URL below to download the torrent (you may have to login).

$DEFAULTBASEURL/details.php?id=$id&hit=1

-- 
$SITENAME
EOD;
$to = "";
$nmax = 100; // Max recipients per message
$nthis = 0;
$ntotal = 0;
$total = mysql_num_rows($res);
while ($arr = mysql_fetch_row($res))
{
  if ($nthis == 0)
    $to = $arr[0];
  else
    $to .= "," . $arr[0];
  ++$nthis;
  ++$ntotal;
  if ($nthis == $nmax || $ntotal == $total)
  {
    if (!mail("Multiple recipients <$SITEEMAIL>", "New torrent - $torrent", $body,
    "From: $SITEEMAIL\r\nBcc: $to", "-f$SITEEMAIL"))
	  stderr($tracker_lang['error'], "Your torrent has been been uploaded. DO NOT RELOAD THE PAGE!\n" .
	    "There was however a problem delivering the e-mail notifcations.\n" .
	    "Please let an administrator know about this error!\n");
    $nthis = 0;
  }
}
*******************/

header("Location: $DEFAULTBASEURL/details.php?id=$id");

?>