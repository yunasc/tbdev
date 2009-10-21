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
include("include/codecs.php");
dbconn();
loggedinorreturn();
if (get_user_class() <= UC_MODERATOR)
	stderr($tracker_lang["error"], $tracker_lang["access_denied"]);
$action = $_GET["action"];
if ($action == 'edit') {
	$id = (int) $_GET["id"];
	if (!is_valid_id($id))
		stderr($tracker_lang["error"], $tracker_lang["invalid_id"]);
	$res = sql_query("SELECT * FROM indexreleases WHERE id = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
	if (mysql_num_rows($res) != 1)
		stderr($tracker_lang["error"], $tracker_lang["invalid_id"]);
	$release = mysql_fetch_array($res);
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$var_list = "name:poster:genre:director:actors:descr:quality:video_codec:video_size:video_kbps:audio_lang:audio_trans:audio_codec:audio_kbps:time:torrentid:cat";
		$int_list = "quality:video_codec:video_kbps:audio_lang:audio_trans:audio_codec:audio_kbps:torrentid:cat";

		foreach (explode(":", $var_list) as $x)
			if (empty($_POST[$x]))
				stderr($tracker_lang["error"], "Вы не заполнили все поля!");
			else
				$GLOBALS[$x] = $_POST[$x];

		foreach (explode(":", $int_list) as $x)
			if (!is_valid_id($GLOBALS[$x]))
				stderr($tracker_lang["error"], "Вы ввели не число в следующее поле: $x");
		//$video_kbps = $_POST["video_kbps"];
		$time = $_POST["time"];
		$imdb = $_POST["imdb"];
		//$added = sqlesc(get_date_time());
		
		$updateset = array();

		foreach (explode(":", $var_list) as $x)
			$updateset[] = "$x = ".sqlesc($GLOBALS[$x]);
		if (!empty($imdb))
			$updateset[] = "imdb = ".sqlesc($imdb);
		sql_query("UPDATE indexreleases SET " . implode(", ", $updateset) . " WHERE id = $id") or sqlerr(__FILE__, __LINE__);
		//sql_query("UPDATE indexreleases SET (".implode(", ", explode(":", $var_list)).($imdb ? ", imdb" : "").") VALUES (".implode(", ", array_map("sqlesc", array())).($imdb ? ", ".sqlesc($imdb) : "").") WHERE id=$id") or sqlerr(__FILE__,__LINE__);
		$returnto = htmlentities($_POST['returnto']);
		if ($returnto != "")
			header("Location: $returnto");
		else
			stderr("Успешно", "Релиз отредактирован.");
	} else {
		$returnto = $_GET['returnto'];
		stdhead("Редактировать релиз");

$cats = sql_query("SELECT * FROM categories ORDER BY sort ASC");
$categories = "<select name=\"cat\"><option selected>Выберите категорию</option>";
while ($cat = mysql_fetch_array($cats)) {
	$cat_id = $cat["id"];
	$cat_name = $cat["name"];
	$categories .= "<option value=\"$cat_id\"".($release["cat"] == $cat_id ? " selected" : "").">$cat_name</option>";
}
$categories .= "</select>";
$quality = "<select name=\"quality\"><option value=\"0\">Выберите качество</option>";
foreach ($release_quality as $id => $name)
	$quality .= "<option value=\"$id\"".($release["quality"] == $id ? " selected" : "").">$name</option>";
$quality .= "</select>";
$video = "<select name=\"video_codec\"><option value=\"0\">Выберите кодек</option>";
foreach ($video_codec as $id => $name)
	$video .= "<option value=\"$id\"".($release["video_codec"] == $id ? " selected" : "").">$name</option>";
$video .= "</select>".
"<input type=\"text\" name=\"video_size\" size=\"20\" value=\"$release[video_size]\">".
"<input type=\"text\" name=\"video_kbps\" size=\"20\" value=\"$release[video_kbps]\"> кб/с";
$audio = "<select name=\"audio_lang\"><option value=\"0\">Выберите язык</option>";
foreach ($audio_lang as $id => $name)
	$audio .= "<option value=\"$id\"".($release["audio_lang"] == $id ? " selected" : "").">$name</option>";
$audio .= "</select>".
"<select name=\"audio_trans\"><option value=\"0\">Выберите перевод</option>";
foreach ($audio_trans as $id => $name)
	$audio .= "<option value=\"$id\"".($release["audio_trans"] == $id ? " selected" : "").">$name</option>";
$audio .= "</select>".
"<select name=\"audio_codec\"><option value=\"0\">Выберите кодек</option>";
foreach ($audio_codec as $id => $name)
	$audio .= "<option value=\"$id\"".($release["audio_codec"] == $id ? " selected" : "").">$name</option>";
$audio .= "</select>".
"<input type=\"text\" name=\"audio_kbps\" size=\"20\" value=\"$release[audio_kbps]\"> кб/с";

$id = (int) $_GET["id"];

?>

<form action="?action=edit&id=<?=$id;?>" method="post">
<table border="0" cellspacing="0" cellpadding="5">
<?
tr("Название релиза", "<input type=\"text\" name=\"name\" size=\"80\" value=\"$release[name]\" /><br />Пример: Смерть Президента (2006) DVDRip\n", 1);
tr("Постер", "<input type=\"text\" name=\"poster\" size=\"80\" value=\"$release[poster]\" /><br />Залить картинку на <a href=\"http://www.imageshack.us\">ImageShack</a>", 1);
tr("Жанр", "<input type=\"text\" name=\"genre\" size=\"80\" value=\"$release[genre]\" />\n", 1);
tr("Режиссер", "<input type=\"text\" name=\"director\" size=\"80\" value=\"$release[director]\" />\n", 1);
tr("В ролях", "<input type=\"text\" name=\"actors\" size=\"80\" value=\"$release[actors]\" />\n", 1);
tr("Описание", "<textarea name=\"descr\" rows=\"10\" cols=\"80\">$release[descr]</textarea>", 1);
tr("Качество", $quality, 1);
tr("Видео", $video, 1);
tr("Аудио", $audio, 1);
tr("Продолжительность", "<input type=\"text\" name=\"time\" size=\"30\" value=\"$release[time]\" /><br />Пример: 01:54:00\n", 1);
tr("Номер торрента", "<input type=\"text\" name=\"torrentid\" size=\"60\" value=\"$release[torrentid]\" /><br />Пример: $DEFAULTBASEURL/details.php?id=<b>6764</b><br />Выделенное жирным - и есть номер торрента\n", 1);
tr("URL IMDB", "<input type=\"text\" name=\"imdb\" size=\"60\" value=\"$release[imdb]\" /><br />Пример: http://www.imdb.com/title/tt0408306/\n", 1);
tr("Категория", $categories, 1);
?>
<tr><td align="center" colspan="2"><input type="submit" value="Изменить" /></td></tr>
</table>
<? if ($_GET["returnto"])
	print "<input type=\"hidden\" name=\"returnto\" value=\"".htmlentities($_GET["returnto"])."\" />";
?>
</form>

<?
		stdfoot();
	}
}

?>