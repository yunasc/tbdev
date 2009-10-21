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
dbconn(false);
loggedinorreturn();
if (get_user_class() < UC_MODERATOR)
	stderr($tracker_lang["error"], $tracker_lang["access_denied"]);

stdhead("Добавить релиз");

$cats = sql_query("SELECT * FROM categories ORDER BY sort ASC");
$categories = "<select name=\"cat\"><option selected>Выберите категорию</option>";
while ($cat = mysql_fetch_array($cats)) {
	$cat_id = $cat["id"];
	$cat_name = $cat["name"];
	$categories .= "<option value=\"$cat_id\">$cat_name</option>";
}
$categories .= "</select>";
$quality = "<select name=\"quality\"><option value=\"0\">Выберите качество</option>";
foreach ($release_quality as $id => $name)
	$quality .= "<option value=\"$id\">$name</option>";
$quality .= "</select>";
$video = "<select name=\"video_codec\"><option value=\"0\">Выберите кодек</option>";
foreach ($video_codec as $id => $name)
	$video .= "<option value=\"$id\">$name</option>";
$video .= "</select>".
"<input type=\"text\" name=\"video_size\" size=\"20\" value=\"\">".
"<input type=\"text\" name=\"video_kbps\" size=\"20\" value=\"\"> кб/с";
$audio = "<select name=\"audio_lang\"><option value=\"0\">Выберите язык</option>";
foreach ($audio_lang as $id => $name)
	$audio .= "<option value=\"$id\">$name</option>";
$audio .= "</select>".
"<select name=\"audio_trans\"><option value=\"0\">Выберите перевод</option>";
foreach ($audio_trans as $id => $name)
	$audio .= "<option value=\"$id\">$name</option>";
$audio .= "</select>".
"<select name=\"audio_codec\"><option value=\"0\">Выберите кодек</option>";
foreach ($audio_codec as $id => $name)
	$audio .= "<option value=\"$id\">$name</option>";
$audio .= "</select>".
"<input type=\"text\" name=\"audio_kbps\" size=\"20\" value=\"\"> кб/с";

?>

<form action="takeindex.php" method="post">
<table border="0" cellspacing="0" cellpadding="5">
<?
tr("Название релиза", "<input type=\"text\" name=\"name\" size=\"80\" /><br />Пример: Смерть Президента (2006) DVDRip\n", 1);
tr("Постер", "<input type=\"text\" name=\"poster\" size=\"80\" /><br />Залить картинку на <a href=\"http://www.imageshack.us\">ImageShack</a>", 1);
tr("Жанр", "<input type=\"text\" name=\"genre\" size=\"80\" />\n", 1);
tr("Режиссер", "<input type=\"text\" name=\"director\" size=\"80\" />\n", 1);
tr("В ролях", "<input type=\"text\" name=\"actors\" size=\"80\" />\n", 1);
tr("Описание", "<textarea name=\"descr\" rows=\"10\" cols=\"80\"></textarea>", 1);
tr("Качество", $quality, 1);
tr("Видео", $video, 1);
tr("Аудио", $audio, 1);
tr("Продолжительность", "<input type=\"text\" name=\"time\" size=\"30\" value=\"чч:мм:сс\" /><br />Пример: 01:54:00\n", 1);
tr("Номер торрента", "<input type=\"text\" name=\"torrentid\" size=\"60\" /><br />Пример: $DEFAULTBASEURL/details.php?id=<b>6764</b><br />Выделенное жирным - и есть номер торрента\n", 1);
tr("URL IMDB", "<input type=\"text\" name=\"imdb\" size=\"60\" /><br />Пример: http://www.imdb.com/title/tt0408306/\n", 1);
tr("Категория", $categories, 1);
?>
<tr><td align="center" colspan="2"><input type="submit" value="Добавить" /></td></tr>
</table>
</form>

<?
stdfoot();
?>